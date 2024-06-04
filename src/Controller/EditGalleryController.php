<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\EditGalleryType;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class EditGalleryController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private FormFactoryInterface   $formFactory,
        private RouterInterface        $router,
        private UserManager            $userManager
    )
    {
    }

    #[Route(path: '/gallery/{id}/delete', name: 'gallery.delete')]
    public function deleteImageAction($id): Response
    {
        $gallery = $this->em->getRepository(Gallery::class)->find($id);
        if (empty($gallery)) {
            throw new NotFoundHttpException('Gallery not found');
        }

        $currentUser = $this->userManager->getCurrentUser();
        if (empty($currentUser) || false === $gallery->isOwner($currentUser)) {
            throw new AccessDeniedHttpException();
        }

        $this->em->remove($gallery);
        $this->em->flush();

        $this->addFlash('success', 'Gallery deleted');

        return new RedirectResponse($this->router->generate('home'));
    }

    #[Route(path: '/gallery/{id}/edit', name: 'gallery.edit')]
    public function editGalleryAction(Request $request, $id): Response
    {
        $gallery = $this->em->getRepository(Gallery::class)->find($id);
        if (empty($gallery)) {
            throw new NotFoundHttpException('Gallery not found');
        }

        $currentUser = $this->userManager->getCurrentUser();
        if (empty($currentUser) || false === $gallery->isOwner($currentUser)) {
            throw new AccessDeniedHttpException();
        }

        $galleryDto = [
            'name' => $gallery->getName(),
            'description' => $gallery->getDescription(),
        ];

        $form = $this->formFactory->create(EditGalleryType::class, $galleryDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gallery->setDescription($form->get('description')->getData());
            $gallery->setName($form->get('name')->getData());
            $this->em->flush();

            $this->addFlash('success', 'Gallery updated');

            return new RedirectResponse($this->router->generate('gallery.edit', ['id' => $gallery->getId()]));
        }

        return $this->render('gallery/edit-gallery.html.twig', [
            'gallery' => $gallery,
            'form' => $form->createView(),
        ]);
    }
}
