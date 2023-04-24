<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NotificationRepository;

class NotificationController extends AbstractController
{

     /**
     * @Route("/notifications", name="notifications")
     */
    public function index(NotificationRepository $notificationRepository)
    {
  // Récupérer le nombre de réclamations créées au cours des dernières 24 heures
  $count = $notificationRepository->countNewNotificationsLast24h();

  // Récupérer toutes les notifications
  $notifications = $notificationRepository->findAll();

  return $this->render('notification/index.html.twig', [
      'notifications' => $notifications,
      'count' => $count,
  ]);

    }
    
}
