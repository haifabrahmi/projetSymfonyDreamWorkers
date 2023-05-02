<?php

namespace App\Controller;
use App\Entity\Publication;
use App\Entity\Like;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    #[Route('/like', name: 'app_like')]
    public function index(): Response
    {
        return $this->render('like/index.html.twig', [
            'controller_name' => 'LikeController',
        ]);
    }

    /**
     * @Route("/publication/{idPub}/like", name="like_publication")
     */
    public function likePublication(Publication $publication): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find(1);

        $like = new Like();
        $like->setUser($user);
        $like->setType("yes");
        $like->setPublication($publication);
        $publication->setNbReaction($publication->getNbReaction() + 1);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->persist($publication);
        $entityManager->flush();

        return $this->redirectToRoute('app_publication_showFront', ['idPub' => $publication->getIdPub()]);
    }
    /**
     * @Route("/publication/{idPub}/dislike", name="dislike_publication")
     */
    public function dislikePublication(Publication $publication): Response
    {
        //$user = $this->getUser();

        $likeRepository = $this->getDoctrine()->getRepository(Like::class);
        $publication->setNbReaction($publication->getNbReaction() - 1);

        $like = $likeRepository->findOneBy([
          //  'user' => $user,
            'publication' => $publication,
        ]);

        if ($like) {
            $like->setType("no");

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($like);
            $entityManager->persist($publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_publication_showFront', ['idPub' => $publication->getIdPub()]);
    }
}
