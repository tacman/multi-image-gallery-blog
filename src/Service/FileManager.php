<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    private string $path;

    public function __construct(string $path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Target directory %s doesn\'t exist', $path));
        }

        $this->path = $path;
    }

    public function getUploadsDirectory(): string
    {
        return $this->path;
    }

    public function upload(UploadedFile $file, $filename): File
    {
        $file = $file->move($this->getUploadsDirectory(), $filename);

        return $file;
    }

    public function getFilePath($filename): string
    {
        return $this->getUploadsDirectory() . DIRECTORY_SEPARATOR . $filename;
    }

    public function getPlaceholderImagePath(): string
    {
        return $this->getUploadsDirectory() . '/../placeholder.jpg';
    }

}
