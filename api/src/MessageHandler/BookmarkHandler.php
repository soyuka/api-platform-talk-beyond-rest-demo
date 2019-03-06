<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Bookmark;
use App\Message\BookmarkInput;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Panther\Client as Panther;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

final class BookmarkHandler implements MessageHandlerInterface
{
    private $registry;
    private $logger;
    private $screenshotDirectory;
    private $browser;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger, $screenshotDirectory)
    {
        $this->registry = $registry;
        $this->logger = $logger;
        $this->screenshotDirectory = $screenshotDirectory;
        $this->browser = Panther::createChromeClient();
    }

    public function __invoke(BookmarkInput $message)
    {
        $manager = $this->registry->getManagerForClass(Bookmark::class);
        $repository = $manager->getRepository(Bookmark::class);

        $this->logger->info("Processing {$message->link}");

        if (null === $bookmark = $repository->findOneBy(['link' => $message->link])) {
            $bookmark = new Bookmark();
            $bookmark->link = $message->link;
            $bookmark->slug = (new Slugify())->slugify($bookmark->link);
        }

        // Persist with the link only, the rest can take more time
        try {
            $manager->persist($bookmark);
            $manager->flush();
        } catch (UniqueConstraintViolationException $e) {
            $this->logger->error("Error while persisting the link {$message->link}");
            $this->logger->error($e->getMessage());

            return;
        }

        $crawler = $this->browser->request('GET', $bookmark->link);

        // Meta tags
        try {
            $this->logger->info("Read metadata for {$bookmark->link}");
            $title = $crawler->filter('title');
            $bookmark->title = 0 === $title->count() ? null : html_entity_decode(strip_tags($title->html()));
            $description = $crawler->filter('meta[name="description"]');
            $bookmark->description = 0 === $description->count() ? null : $description->attr('content');
            $image = $crawler->filter('meta[name="og:image"]');
            $bookmark->image = 0 === $image->count() ? null : $image->text();

            $manager->persist($bookmark);
            $manager->flush();
        } catch (ProcessFailedException $e) {
            $this->logger->error("Error while reading the link {$bookmark->link}");
            $this->logger->error($e->getMessage());
        }

        // Screenshot
        try {
            $bookmark->screenshot = $bookmark->slug.'.png';
            $path = sprintf('%s/%s', $this->screenshotDirectory, $bookmark->screenshot);
            $this->browser->takeScreenshot($path);

            list($width, $height) = getimagesize($path);
            $width = $width - 20; // remove the scrollbar
            $source = imagecreatefrompng($path);
			$resized = imagecreatetruecolor(1024, 768);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, 1024, 768, $width, $height);
            imagepng($resized, $path);
            imagedestroy($source);
            imagedestroy($resized);

            $this->logger->info("Took a screenshot for {$message->link}");

            $manager->persist($bookmark);
            $manager->flush();
        } catch (ProcessFailedException $e) {
            $this->logger->error("Error while reading the link {$message->link}");
            $this->logger->error($e->getMessage());
        }
    }
}
