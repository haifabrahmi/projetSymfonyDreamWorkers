<?php

namespace App\Controller;

use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reponse;
use App\Form\ReponseType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



class ReponseController extends AbstractController
{
    #[Route('/reponse', name: 'app_reponse')]
    public function index(): Response
    {
        return $this->render('reponse/index.html.twig', [
            'controller_name' => 'ReponseController',
        ]);
    }
    #[Route('/reponse/list', name: 'app_list_reponse')]
    public function getAllClass(ReponseRepository $repo) : Response
    {
        //recuperer la liste des classes 
        $rep=$repo->findall();

        //retourner view list et envoyer la liste des classes 
        return $this->render('reponse/list.html.twig', [
            'reponse' => $rep,
        ]);
    }
    #[Route('/reponse/add',name:'app_rep_add')]
    public function new(Request $request) : Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reponse);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_list_reponse');
        }
    
        return $this->render('reponse/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/reponse/{id}/delete', name: 'app_delete_reponse')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);
        if (!$reponse) {
            throw $this->createNotFoundException('La réponse n\'existe pas.');
        }
    
        $entityManager->remove($reponse);
        $entityManager->flush();
        $this->addFlash('success', 'La réponse a été supprimée avec succès.');
    
        return $this->redirectToRoute('app_list_reponse');
    }
    #[Route('/reponse/{id}/edit', name: 'app_edit_reponse')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Reponse $reponse): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_list_reponse');
        }
    
        return $this->render('reponse/edit.html.twig', [
            'form' => $form->createView(),
            'reponse' => $reponse,
        ]);
    }
    

    #[Route('/reponse/list2', name: 'app_list_reponse2')]
    public function afficher(ReponseRepository $repo) : Response
    {
        //recuperer la liste des classes 
        $rep=$repo->findall();

        //retourner view list et envoyer la liste des classes 
        return $this->render('reponse/listB.html.twig', [
            'reponse' => $rep,
        ]);
    }

}
