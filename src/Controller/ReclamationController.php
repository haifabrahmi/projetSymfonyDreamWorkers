<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Repository\ReclamationRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\AdministratorRecipient;
use Endroid\QrCode\QrCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ReclamationType;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;

class ReclamationController extends AbstractController
{
   
    #[Route('/reclamation/list', name: 'app_list_reclamation')]
    public function getAllClass(ReclamationRepository $repo) : Response
{
    //recuperer la liste des reclamations
    $reclam = $repo->findAll();

    //trier par etatReclamation (en inversant l'ordre pour afficher 1 avant 0)
    usort($reclam, function($a, $b) {
        return strcmp($b->getEtatReclamation(), $a->getEtatReclamation());
    });

    //retourner view list et envoyer la liste des reclamations triée
    return $this->render('reclamation/list.html.twig', [
        'reclamation' => $reclam,
    ]);
}

   //Notificatiooos
   private $notifier;
   private $flashBag;
   public function __construct(NotifierInterface $notifier, FlashBagInterface $flashBag)
    {
        $this->notifier = $notifier;
        $this->flashBag = $flashBag;
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

            // Ajouter la notification flash
            $this->flashBag->add('success', 'une nouvelle reclamation a été ajoutée.');

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
        //recuperer la liste des reclamations
    $reclam = $repo->findAll();

    //trier par etatReclamation (en inversant l'ordre pour afficher 1 avant 0)
    usort($reclam, function($a, $b) {
        return strcmp($b->getEtatReclamation(), $a->getEtatReclamation());
    });

    //retourner view list et envoyer la liste des reclamations triée
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


/**
 * @Route("/reclamation/{id}/pdf", name="PDF_Reclamation", methods={"GET"})
 */
public function pdf(Reclamation $reclamation)
{
    // Configure Dompdf according to your needs
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf();
    
    // Retrieve the HTML generated in our twig file
    $html = $this->renderView('reclamation/pdf.html.twig', [
        'reclamation' => $reclamation,
    ]);

    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    
    // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser (inline view)
    $output = $dompdf->output();
    $response = new Response($output);
    
    $filename = sprintf('reclamation_%s.pdf', $reclamation->getIdReclamation());
    // Set the Content-Disposition header to force download of the PDF file
    $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
    $response->headers->set('Content-Type', 'application/pdf');

    return $response;
}

#[Route('/reclamation/{id}/signal', name: 'app_signal_reclamation')]
public function signal(Request $request, NotifierInterface $notifier, EntityManagerInterface $entityManager, Reclamation $reclamation): Response
{
    $reclamation->setSignale(true);

    $entityManager->flush();

    $entityManager->remove($reclamation);
    $entityManager->flush();

    $notification = new Notification(
        'La réclamation ' . $reclamation->getIdReclamation() . ' a été signalée.',
        ['email']
    );

    $recipient = new Recipient('nourbrava85@gmail.com');
    $this->notifier->send($notification, $recipient);

    $this->addFlash('success', 'La réclamation a été signalée avec succès et supprimée.');

    return $this->redirectToRoute('app_list_reclamation1');
}

/**
 * @Route("/orderByObjetReclamation", name="orderByObjetReclamation", methods={"GET"})
 */
public function orderByObjetReclamation(Request $request, ReclamationRepository $reclamationRepository): Response
{
    $reclam = $reclamationRepository->findBy([], ['objet_reclamation' => 'ASC']);

    return $this->render('reclamation/listB.html.twig', [
        'reclamation' => $reclam,
    ]);
}
/**
 * @Route("/search_reclamation", name="search_reclamation", methods={"GET"})
 */
public function searchReclamation(Request $request, ReclamationRepository $reclamationRepository): Response
{
    $categorie_reclamation = $request->query->get('categorie_reclamation');
    
    $reclamations = $reclamationRepository->searchReclamation($categorie_reclamation);
    
    return $this->render('reclamation/listB.html.twig', [
        'reclamation' => $reclamations,
    ]);
}







}



 


