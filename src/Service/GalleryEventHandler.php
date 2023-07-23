<?php

namespace App\Service;

use App\Entity\Gallery;
use App\Message\GalleryCreated;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GalleryEventHandler
{
    /** @var  EntityManagerInterface */
    private $entityManager;

    /** @var FileManager */
    private $fileManager;

    /** @var $imageResizer */
    private $imageResizer;

    public function __construct(EntityManagerInterface $entityManager, FileManager $fileManager, ImageResizer $imageResizer)
    {
        $this->entityManager = $entityManager;
        $this->fileManager = $fileManager;
        $this->imageResizer = $imageResizer;
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
