<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\Circuit;
use App\Form\StationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;

#[Route('/admin/station')]
class StationAdminController extends AbstractController
{
    #[Route('/', name: 'admin_station_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $pieChart = new PieChart();

        $stations = $entityManager
            ->getRepository(Station::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $stations, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );

        $circuits = $entityManager
            ->getRepository(Circuit::class)
            ->findAll();

        $charts = [['Station', 'Number per Circuit']];

        foreach ($circuits as $c) {
            $circuitN = 0;
            foreach ($stations as $s) {
                if ($c == $s->getCircuit()) {
                    $circuitN++;
                }
            }

            array_push($charts, [$c->getNomC(), $circuitN]);
        }
        
        $pieChart->getData()->setArrayToDataTable($charts);

        // dd($pieChart);

        $pieChart->getOptions()->setTitle('Stations Number per Circuits');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(400);
        $pieChart
            ->getOptions()
            ->getTitleTextStyle()
            ->setColor('#07600');
        $pieChart
            ->getOptions()
            ->getTitleTextStyle()
            ->setFontSize(25);

        return $this->render('stationAdmin/index.html.twig', [
            'stations' => $pagination,
            'piechart' => $pieChart,
        ]);
    }

    #[Route('/new', name: 'admin_station_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $station = new Station();
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('station')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $station->setImage($filename);
            $entityManager->persist($station);
            $entityManager->flush();

            $this->addFlash('notice','Ajout avec success!');

            return $this->redirectToRoute('admin_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stationAdmin/new.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_station_show', methods: ['GET'])]
    public function show(Station $station): Response
    {
        return $this->render('stationAdmin/show.html.twig', [
            'station' => $station,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_station_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('station')['image'];
            $uploads_directory = $this->getParameter('uploads_directory');
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploads_directory, $filename);
            $station->setImage($filename);
            $entityManager->persist($station);
            $entityManager->flush();

            $this->addFlash('notice','Update avec success!');

            return $this->redirectToRoute('admin_station_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stationAdmin/edit.html.twig', [
            'station' => $station,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_station_delete', methods: ['POST'])]
    public function delete(Request $request, Station $station, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->request->get('_token'))) {
            $entityManager->remove($station);
            $entityManager->flush();

            $this->addFlash('notice','Delete avec success!');
        }

        return $this->redirectToRoute('admin_station_index', [], Response::HTTP_SEE_OTHER);
    }
}
