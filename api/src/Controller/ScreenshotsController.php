<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ScreenshotsController extends AbstractController
{
    private $screenshotDirectory;

    public function __construct($screenshotDirectory)
    {
        $this->screenshotDirectory = $screenshotDirectory;
    }

    /**
     * @Route("/api/screenshots/{name}", name="screenshots")
     */
    public function index($name)
    {
        $file = sprintf('%s/%s', $this->screenshotDirectory, $name);

        if (file_exists($file)) {
            return new BinaryFileResponse($file);
        }

        throw new NotFoundHttpException('Screenshot not found.');
    }
}
