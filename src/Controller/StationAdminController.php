<?php

namespace App\Controller;

use App\Entity\Station;
use App\Form\StationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/station')]
class StationAdminController extends AbstractController
{
    #[Route('/', name: 'admin_station_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $stations = $entityManager
            ->getRepository(Station::class)
            ->findAll();

        return $this->render('stationAdmin/index.html.twig', [
            'stations' => $stations,
        ]);
    }

    #[Route('/new', name: 'admin_station_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();

            return $this->redirectToRoute('admin_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stationAdmin/new.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{idS}', name: 'admin_station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        return $this->render('stationAdmin/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{idS}/edit', name: 'admin_station_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stationAdmin/edit.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{idS}', name: 'admin_station_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getIdS(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_station_index', [], Response::HTTP_SEE_OTHER);
    }
}