<?php

namespace App\Message;

class GalleryCreated
{
    /** @var  string */
    private $galleryId;

    public function __construct(string $galleryId)
    {
        $this->galleryId = $galleryId;
    }

    public function getGalleryId(): string
    {
        return $this->galleryId;
    }
}
