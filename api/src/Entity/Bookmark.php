<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Filter\MatchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use App\Message\BookmarkInput;

/**
 * @ApiResource(
 *      mercure=true,
 *      elasticsearch=false,
 *      collectionOperations={
 *          "get",
 *          "create"={
 *              "input"=BookmarkInput::class,
 *              "output"=false,
 *              "status"=202,
 *              "method"="POST",
 *              "messenger"="input"
 *          },
 *          "search"={
 *              "elasticsearch"=true,
 *              "method"="GET",
 *              "path"="/bookmarks/search.{_format}"
 *          }
 *      },
 *      graphql={"create"={"input"=BookmarkInput::class, "messenger"="input"}}
 * )
 * @ApiFilter(MatchFilter::class, properties={"description", "title", "tags"})
 * @ORM\Entity
 */
class Bookmark
{
    const ELASTICSEARCH_INDEX = 'bookmarks';
    const ELASTICSEARCH_TYPE = 'bookmark';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    public $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $screenshot;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    public $tags;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id = null)
    {
        $this->id = $id;
    }
}
