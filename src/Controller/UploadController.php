<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Image;
use App\Message\GalleryCreated;
use App\Service\FileManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Uid\Uuid;

class UploadController extends AbstractController
{
    public function __construct(
        private RouterInterface        $router,
        private FileManager            $fileManager,
        private EntityManagerInterface $em,
        private UserManager            $userManager,
        private MessageBusInterface    $bus
    )
    {
    }

    /**
     * @Route("/private/upload", name="upload")
     */
    public function renderUploadScreenAction(): Response
    {
        $view = $this->renderView('gallery/upload.html.twig');

        return new Response($view);
    }

    /**
     * @Route("/private/upload-process", name="upload.process")
     */
    public function processUploadAction(Request $request): Response
    {
        // @todo access control
        // @todo input validation

        $gallery = new Gallery(Uuid::v7());
        $gallery->setName($request->get('name'));
        $gallery->setDescription($request->get('description'));
        $gallery->setUser($this->userManager->getCurrentUser());
        $files = $request->files->get('file');

        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();
            $filepath = Uuid::v7() . '.' . $file->getClientOriginalExtension();
            $this->fileManager->upload($file, $filepath);

            $image = new Image(
                Uuid::v7(),
                $filename,
                $filepath
            );

            $gallery->addImage($image);
        }

        $this->em->persist($gallery);
        $this->em->flush();

        $this->bus->dispatch(
            new GalleryCreated($gallery->getId())
        );

        $this->addFlash('success', 'Gallery created! Images are now being processed.');

        return new JsonResponse([
            'success' => true,
            'redirectUrl' => $this->router->generate(
                'gallery.single-gallery',
                ['id' => $gallery->getId()]
            ),
        ]);
    }
}
