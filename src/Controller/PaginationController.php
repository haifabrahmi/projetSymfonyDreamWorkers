<?php
// src/PaginationBundle/Controller/PaginationController.php

namespace App\PaginationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaginationController extends AbstractController
{
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('App\Entity\Bus');
        $queryBuilder = $repository->createQueryBuilder('bus')
            ->orderBy('bus.date_depart', 'DESC');

        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $page,
            $limit
        );

        return $this->render('pagination/showall.html.twig', [
            'pagination' => $pagination,

      
        ]);
    }
}
