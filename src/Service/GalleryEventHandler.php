<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Message\GalleryCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GalleryEventHandler
{
    public function __construct(private EntityManagerInterface $entityManager, private FileManager $fileManager, private ImageResizer $imageResizer)
    {
    }

    public function __invoke(GalleryCreated $event): void
    {
        $gallery = $this->entityManager
            ->getRepository(Gallery::class)
            ->find($event->getGalleryId());

        if (empty($gallery)) {
            return;
        }

        foreach ($gallery->getImages() as $image) {
            $fullPath = $this->fileManager->getFilePath($image->getFilename());
            if (empty($fullPath)) {
                continue;
            }

            $cachedPaths = [];
            foreach ($this->imageResizer->getSupportedWidths() as $width) {
                $cachedPaths[$width] = $this->imageResizer->getResizedPath($fullPath, $width, true);
            }
        }
    }
}
