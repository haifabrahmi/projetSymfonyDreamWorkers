<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Reservation;
use App\Entity\Ticket;
use App\Form\ReservationType;
use App\Form\TicketType;
use App\Repository\ReservationRepository;
use App\Repository\TicketRepository;
use Dompdf\Dompdf;
use App\Response\PdfResponse;

class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back_index')]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('back/index.html.twig',  [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/back/new', name: 'app_back_new')]
    public function add(Request $request, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('app_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back/new.html.twig',  [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('back/{id}', name: 'app_back_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('back/show.html.twig', [
            'reservation' => $reservation,  
        ]);
    }

    #[Route('back/{id}/edit', name: 'app_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('app_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_back_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('app_back_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_back_search')]
    public function search(Request $request, ReservationRepository $reservationRepository) : Response
    {
        $id = $request->query->get('id');
        $reservation = $reservationRepository->find($id);

        return $this->render('back/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    #[Route('/searchticket', name: 'app_ticket_search')]
    public function searchticket(Request $request, TicketRepository $ticketRepository) : Response
    {
        $id = $request->query->get('id');
        $ticket = $ticketRepository->find($id);

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
    
    #[Route('/back/{id}/pdf', name: 'generate_pdf')]
    public function generatePdfAction($id , ReservationRepository $reservationRepository) : Response
    {
        // Get the reservation object from the database
        $reservation = $reservationRepository->find($id);

        // Generate the HTML content for the PDF file
        $html = $this->renderView('back/pdf.html.twig', [
            'reservation' => $reservation,
        ]);

        // Instantiate the Dompdf class and set the options
        $pdf = new Dompdf();
       

        // Load the HTML content into the Dompdf class and render the PDF file
        $pdf->loadHtml($html);
        $pdf->render();

        // Output the generated PDF file
        return new PdfResponse($pdf->output(), 'reservation.pdf');
    }


}
