<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class BookmarkInput
{
    /**
     * @var string
     * @Assert\Url
     * @Assert\NotBlank
     */
    public $link;
}
