<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;

class ReclamationController extends AbstractController
{
   
    #[Route('/reclamation/list', name: 'app_list_reclamation')]
    public function getAllClass(ReclamationRepository $repo) : Response
    {
        //recuperer la liste des classes 
        $reclam=$repo->findall();

        //retourner view list et envoyer la liste des classes 
        return $this->render('reclamation/list.html.twig', [
            'reclamation' => $reclam,
        ]);
    }
    #[Route('/reclamation/add',name:'app_list_add')]
    public function new(Request $request) : Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_list_reclamation');
        }
    
        return $this->render('reclamation/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/{id}/delete', name: 'app_delete_reclamation')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);
        if (!$reclamation) {
            throw $this->createNotFoundException('La réclamation n\'existe pas.');
        }
    
        $entityManager->remove($reclamation);
        $entityManager->flush();
        $this->addFlash('success', 'La réclamation a été supprimée avec succès.');
    
        return $this->redirectToRoute('app_list_reclamation');
    }
    #[Route('/reclamation/{id}/edit', name: 'app_edit_reclamation')]
public function edit(Request $request, EntityManagerInterface $entityManager, Reclamation $reclamation): Response
{
    $form = $this->createForm(ReclamationType::class, $reclamation);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_list_reclamation');
    }

    return $this->render('reclamation/edit.html.twig', [
        'form' => $form->createView(),
        'reclamation' => $reclamation,
    ]);
}
#[Route('/reclamation/list1', name: 'app_list_reclamation1')]
    public function afficher(ReclamationRepository $repo) : Response
    {
        //recuperer la liste des classes 
        $reclam=$repo->findall();

        //retourner view list et envoyer la liste des classes 
        return $this->render('reclamation/listB.html.twig', [
            'reclamation' => $reclam,
        ]);
    }

    #[Route('/reclamation/{id}/reponse/add', name: 'app_add_reponse')]
public function addReponse(Request $request, $id)
{
    // Get the Reclamation object from the database
    $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

    // Create a new Reponse object
    $reponse = new Reponse();

    // Create a form for the Reponse object
    $form = $this->createForm(ReponseType::class, $reponse);

    // Handle the form submission
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        // Set the Reclamation object for the Reponse
        $reponse->setReclamation($reclamation);

        // Save the Reponse object to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reponse);
        $entityManager->flush();

        // Update the Reclamation object's etatReclamation to 1
        $reclamation->setEtatReclamation(1);
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Redirect to the list of Reponses
        return $this->redirectToRoute('app_list_reponse');
    }

    // Render the template with the form variable
    return $this->render('reclamation/add_reponse.html.twig', [
        'form' => $form->createView(),
    ]);
}








}
