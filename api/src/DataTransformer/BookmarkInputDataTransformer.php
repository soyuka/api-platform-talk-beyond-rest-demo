<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\BookmarkInput;
use App\Entity\Bookmark;
use Cocur\Slugify\Slugify;

final class BookmarkInputDataTransformer implements DataTransformerInterface {
    public function transform($object, string $to, array $context = [])
    {
        $bookmark = new Bookmark();
        $bookmark->link = $object->link;
        $bookmark->slug = (new Slugify())->slugify($bookmark->link);
        return $bookmark;
    }

    public function supportsTransformation($object, string $to, array $context = []): bool
    {
        if (\is_object($object)) {
            return false;
        }

        return Bookmark::class === $to && BookmarkInput::class === ($context['input']['class'] ?? null);
    }
}
