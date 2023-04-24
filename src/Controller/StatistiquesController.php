<?php

namespace App\Controller;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\Chartjs\Model\ChartDataset;
use Symfony\UX\Chartjs\Model\ChartOptions;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

use App\Repository\ReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/statistiques', name: 'app_statistiques')]
    public function statistiques(ReclamationRepository $reclamationRepository)
    {
        $statistiques = $reclamationRepository->createQueryBuilder('r')
            ->select('r.categorie_reclamation, COUNT(r.id_reclamation) AS nombreReclamations')
            ->groupBy('r.categorie_reclamation')
            ->getQuery()
            ->getResult();

        $chartData = array();
        foreach ($statistiques as $statistique) {
            $chartData['labels'][] = $statistique['categorie_reclamation'];
            $chartData['data'][] = $statistique['nombreReclamations'];
        }

        $chart = new \stdClass();
        $chart->type = 'doughnut';
        $chart->data = new \stdClass();
        $chart->data->labels = $chartData['labels'];
        $chart->data->datasets = array(
            new \stdClass()
        );
        $chart->data->datasets[0]->label = 'Nombre de réclamations';
        $chart->data->datasets[0]->data = $chartData['data'];
        $chart->data->datasets[0]->backgroundColor = array(
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        );
        $chart->data->datasets[0]->borderColor = array(
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        );
        $chart->data->datasets[0]->borderWidth = 1;

        $chart->options = new \stdClass();
        $chart->options->responsive = false;
        $chart->options->maintainAspectRatio = false;
        $chart->options->plugins = new \stdClass();
        $chart->options->plugins->legend = new \stdClass();
        $chart->options->plugins->legend->position = 'right';

        $chartJson = json_encode($chart);

        return new Response(
            '<canvas id="myChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById("myChart").getContext("2d");
                var myChart = new Chart(ctx, ' . $chartJson . ');
            </script>'
        );
    }
}
