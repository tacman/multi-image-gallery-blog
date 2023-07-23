<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'images')]
class Image
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public ?Uuid $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $originalFilename;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $filename;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: Gallery::class, inversedBy: 'images')]
    #[ORM\JoinColumn(name: 'gallery_id', referencedColumnName: 'id', nullable: true)]
    private Gallery $gallery;

    public function __construct(Uuid $id, $originalFilename, $filename)
    {
        $this->id = $id;
        $this->originalFilename = $originalFilename;
        $this->filename = $filename;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * @param string $originalFilename
     */
    public function setOriginalFilename(string $originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function canEdit(User $user): bool
    {
        return $this->getGallery()->isOwner($user);
    }

    public function getGallery(): Gallery
    {
        return $this->gallery;
    }

    public function setGallery(Gallery $gallery): void
    {
        $this->gallery = $gallery;
    }

}
