<?php

namespace App\Controller;

use App\Entity\Station;
use App\Form\StationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/station')]
class StationController extends AbstractController
{
    #[Route('/', name: 'app_station_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $stations = $entityManager
            ->getRepository(Station::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $stations, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );

        return $this->render('station/index.html.twig', [
            'stations' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_station_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();

            return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('station/new.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        return $this->render('station/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_station_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('station/edit.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_station_index', [], Response::HTTP_SEE_OTHER);
    }
}
