<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Dto\BookmarkInput;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\MatchFilter;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\OrderFilter;

/**
 * @ApiResource(
 *   mercure=true,
 *   messenger=true,
 *   collectionOperations={"get",
 *     "create"={"input"=BookmarkInput::class, "output"=false, "status"=202, "method"="POST"}
 *   }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\BookmarkRepository")
 * @ApiFilter(MatchFilter::class, properties={"description", "title", "keywords"})
 * @ApiFilter(OrderFilter::class, properties={"created"="desc"}, arguments={"orderParameterName"="order"})
 */
class Bookmark
{
    /**
     * @ORM\Id()
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
     * @Assert\Url
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $screenshot;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    public $keywords;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
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
}
