<?php

namespace App\Controller;

use App\Form\MaintenanceType;
use App\Entity\Maintenance;
use App\Entity\Bus;
use App\Repository\MaintenanceRepository;
use App\Repository\BusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Dompdf\Bundle\DompdfBundle;







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
   public function list(Request $request, MaintenanceRepository $repo): Response
{
    $status = $request->query->get('status');

    // Récupérer la liste des maintenances selon le statut s'il est fourni
    if ($status) {
        $maintenances = $repo->findBy(['status' => $status]);
    } else {
        $maintenances = $repo->findAll();
    }

    // Trier la liste des maintenances par date en ordre croissant
    usort($maintenances, function ($a, $b) {
        return $a->getDateEntretien() <=> $b->getDateEntretien();
    });

    // Retourner la vue de la liste filtrée ou non et envoyer la liste des maintenances
    return $this->render('maintenance/list.html.twig', [
        'maintenances' => $maintenances,
        'status' => $status,
    ]);
}
 
       #[Route("/pdf", name:"PDF", methods:"GET")]
      public function pdf(MaintenanceRepository $maintenanceRepository)
{
    // Configure Dompdf according to your needs
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('maintenance/pdf.html.twig', [
        'maintenances' => $maintenanceRepository->findAll(),
    ]);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A3', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();
    // Output the generated PDF to Browser (inline view)
    $dompdf->stream("mypdf.pdf", [
        "maintenances" => true
    ]);
  
    }




     
    /* public function pdf(BusRepository $voyageRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('maintenance/pdf.html.twig', [
            'maintenances' => $MaintenanceRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "maintenances" => true
        ]);
    }
  
  */

   /*  #[Route('/maintenance/list', name: 'app_list_maintenance')]
    public function list(MaintenanceRepository $repo): Response
    {
        // Recupérer la liste des maintenances
        $maintenances = $repo->findAll();
    // Trier la liste des maintenances par date en ordre croissant
    usort($maintenances, function ($a, $b) {
        return $a->getDateEntretien() <=> $b->getDateEntretien();
         });
        // Retourner la vue de la liste et envoyer la liste des maintenances
        return $this->render('maintenance/list.html.twig', [
            'maintenances' => $maintenances,
        ]);
    } */

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


