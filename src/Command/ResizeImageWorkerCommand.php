<?php

namespace App\Command;

use App\Entity\Image;
use App\Service\FileManager;
use App\Service\ImageResizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResizeImageWorkerCommand extends Command
{
    /** @var  OutputInterface */
    private $output;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('app:resize-image-worker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $output->writeln(sprintf('Started worker'));

        try {
            $this->resizeImage($job->getData());
            $queue->delete($job);
        } catch (\Exception $e) {
            $queue->bury($job);
            throw $e;
        }
    }

    protected function resizeImage(string $imageId)
    {
        /** @var Image $image */
        $image = $this->getContainer()->get('doctrine')
            ->getManager()
            ->getRepository(Image::class)
            ->find($imageId);

        if (empty($image)) {
            $this->output->writeln("Image with ID $imageId not found");
        }

        $imageResizer = $this->getContainer()->get(ImageResizer::class);
        $fileManager = $this->getContainer()->get(FileManager::class);

        $fullPath = $fileManager->getFilePath($image->getFilename());
        if (empty($fullPath)) {
            $this->output->writeln("Full path for image with ID $imageId is empty");

            return;
        }

        $cachedPaths = [];
        foreach ($imageResizer->getSupportedWidths() as $width) {
            $cachedPaths[$width] = $imageResizer->getResizedPath($fullPath, $width, true);
        }

        $this->output->writeln("Thumbnails generated for image $imageId");
        $this->output->writeln(json_encode($cachedPaths, JSON_PRETTY_PRINT));
    }
}
