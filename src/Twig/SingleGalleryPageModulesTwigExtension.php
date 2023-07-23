<?php

namespace App\Twig;

use App\Entity\Gallery;
use App\Repository\GalleryRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SingleGalleryPageModulesTwigExtension extends AbstractExtension
{
    /** @var  Environment */
    private $twig;

    /** @var  GalleryRepository */
    private $repository;

    public function __construct(Environment $twig, GalleryRepository $repository)
    {
        $this->twig = $twig;
        $this->repository = $repository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('renderRelatedGalleries', [$this, 'renderRelatedGalleries'], ['is_safe' => ['html']]),
            new TwigFunction('renderNewestGalleries', [$this, 'renderNewestGalleries'], ['is_safe' => ['html']]),
        ];
    }

    public function renderRelatedGalleries(Gallery $gallery, int $limit = 5)
    {
        return $this->twig->render('gallery/partials/_related-galleries.html.twig', [
            'galleries' => $this->repository->findRelated($gallery, $limit),
        ]);
    }

    public function renderNewestGalleries(int $limit = 5)
    {
        return $this->twig->render('gallery/partials/_newest-galleries.html.twig', [
            'galleries' => $this->repository->findNewest($limit),
        ]);
    }

}