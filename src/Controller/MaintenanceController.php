<?php

namespace App\Controller;

use App\Form\MaintenanceType;
use App\Entity\Maintenance;
use App\Entity\Bus;
use App\Repository\MaintenanceRepository;
use App\Repository\BusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MaintenanceController extends AbstractController
{
    #[Route('/maintenance', name: 'app_maintenance')]
    public function index(): Response
    {
        return $this->render('maintenance/index.html.twig', [
            'controller_name' => 'MaintenanceController',
        ]);
    }

    #[Route('/maintenance/list', name: 'app_list_maintenance')]
    public function list(MaintenanceRepository $repo): Response
    {
        // Recupérer la liste des maintenances
        $maintenances = $repo->findAll();
    
        // Retourner la vue de la liste et envoyer la liste des maintenances
        return $this->render('maintenance/list.html.twig', [
            'maintenances' => $maintenances,
        ]);
    }

    #[Route('/maintenance/{id}/edit', name: 'app_edit_maintenance')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Maintenance $maintenance): Response
    {
        $form = $this->createForm(MaintenanceType::class, $maintenance);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_list_maintenance');
        }

        return $this->render('maintenance/edit.html.twig', [
            'form' => $form->createView(),
            'maintenance' => $maintenance,
        ]);
    }
    #[Route('/maintenance/{id}/delete', name: 'app_delete_maintenance')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $maintenance = $entityManager->getRepository(Maintenance::class)->find($id);
        if (!$maintenance) {
            throw $this->createNotFoundException('La réclamation n\'existe pas.');
        }
    
        $entityManager->remove($maintenance);
        $entityManager->flush();
        $this->addFlash('success', 'La réclamation a été supprimée avec succès.');
    
        return $this->redirectToRoute('app_list_maintenance');
    }
    #[Route('/maintenance/add',name:'app_list_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $maintenance = new Maintenance();
    
        $form = $this->createForm(MaintenanceType::class, $maintenance);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the Bus entity object based on the busId value submitted in the form
            $id_bus = $form->get('bus')->getData();
            $bus = $entityManager->getRepository(Bus::class)->find($id_bus);
    
            // Set the Bus entity object to the bus field of your Maintenance entity
            $maintenance->setBus($bus);
    
            $entityManager->persist($maintenance);
            $entityManager->flush();
    
            $this->addFlash('success', 'La maintenance a été ajoutée avec succès !');
    
            return $this->redirectToRoute('app_list_maintenance');
        }
    
        return $this->render('maintenance/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

}


