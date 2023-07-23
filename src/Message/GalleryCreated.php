<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

class GalleryCreated
{
    public function __construct(private Uuid $galleryId)
    {
    }

    public function getGalleryId(): Uuid
    {
        return $this->galleryId;
    }
}
