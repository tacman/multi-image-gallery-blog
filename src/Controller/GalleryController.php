<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private UserManager $userManager)
    {
    }

    /**
     * @Route("/gallery/{id}", name="gallery.single-gallery")
     */
    public function homeAction($id): Response
    {
        $gallery = $this->em->getRepository(Gallery::class)->find($id);
        if (empty($gallery)) {
            throw new NotFoundHttpException();
        }

        $canEdit = false;
        $currentUser = $this->userManager->getCurrentUser();
        if (!empty($currentUser)) {
            $canEdit = $gallery->isOwner($currentUser);
        }

        $view = $this->renderView('gallery/single-gallery.html.twig', [
            'gallery' => $gallery,
            'canEdit' => $canEdit,
        ]);

        return new Response($view);
    }
}
