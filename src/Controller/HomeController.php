<?php

namespace App\Controller;

use App\Entity\Gallery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const PER_PAGE = 12;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * @Route("", name="home")
     */
    public function homeAction(): Response
    {
        $galleries = $this->em->getRepository(Gallery::class)->findBy([], ['createdAt' => 'DESC'], self::PER_PAGE);
        $view = $this->renderView('home.html.twig', [
            'galleries' => $galleries,
        ]);

        return new Response($view);
    }

    /**
     * @Route("/galleries-lazy-load", name="home.lazy-load")
     */
    public function homeGalleriesLazyLoadAction(Request $request): Response
    {
        $page = $request->get('page', null);
        if (empty($page)) {
            return new JsonResponse([
                'success' => false,
                'msg' => 'Page param is required',
            ]);
        }

        $offset = ($page - 1) * self::PER_PAGE;
        $galleries = $this->em->getRepository(Gallery::class)->findBy([], ['createdAt' => 'DESC'], 12, $offset);

        $view = $this->renderView('partials/home-galleries-lazy-load.html.twig', [
            'galleries' => $galleries,
        ]);

        return new JsonResponse([
            'success' => true,
            'data' => $view,
        ]);
    }
}
