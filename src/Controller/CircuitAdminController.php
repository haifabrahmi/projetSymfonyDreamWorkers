<?php

namespace App\Controller;

use App\Entity\Circuit;
use App\Form\CircuitType;
use App\Repository\CircuitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/circuit')]
class CircuitAdminController extends AbstractController
{
    #[Route('/sortByAscDate', name: 'sort_by_asc_date')]
    public function sortAscDate(PaginatorInterface $paginator, EntityManagerInterface $entityManager, CircuitRepository $circuitRepository, Request $request)
    {
        $circuits = $entityManager
            ->getRepository(Circuit::class)
            ->findAll();

        $query = $request->query->get('q');
        $circuits = $this->getDoctrine()
            ->getRepository(Circuit::class)
            ->searchCircuit($query);

        $circuits = $circuitRepository->sortByAscDate();
            
        $pagination = $paginator->paginate(
            $circuits, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );
    
        return $this->render("circuitAdmin/index.html.twig",[
            'circuits' => $pagination,
            'query' => $query,
        ]);
    }
    
    #[Route('/sortByDescDate', name: 'sort_by_desc_date')]
    public function sortDescDate(PaginatorInterface $paginator, EntityManagerInterface $entityManager, CircuitRepository $circuitRepository, Request $request)
    {
        $circuits = $entityManager
            ->getRepository(Circuit::class)
            ->findAll();

        $query = $request->query->get('q');
        $circuits = $this->getDoctrine()
            ->getRepository(Circuit::class)
            ->searchCircuit($query);

        $circuits = $circuitRepository->sortByDescDate();
            
        $pagination = $paginator->paginate(
            $circuits, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );
    
        return $this->render("circuitAdmin/index.html.twig",[
            'circuits' => $pagination,
            'query' => $query,
        ]);
    }

    #[Route("/search", name:"circuit_search")]
    public function search(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $request->query->get('q');
        $circuit = $this->getDoctrine()
            ->getRepository(Circuit::class)
            ->searchCircuit($query);
            
        $pagination = $paginator->paginate(
            $circuit, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );

        return $this->render('circuitAdmin/search.html.twig', [
            'circuits' => $pagination,
            'query' => $query,
        ]);
    }

    #[Route('/', name: 'admin_circuit_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $circuits = $entityManager
            ->getRepository(Circuit::class)
            ->findAll();

        $query = $request->query->get('q');
        $circuit = $this->getDoctrine()
            ->getRepository(Circuit::class)
            ->searchCircuit($query);
            
        $pagination = $paginator->paginate(
            $circuits, // Requête contenant les données à paginer (ici nos products)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );

        return $this->render('circuitAdmin/index.html.twig', [
            'circuits' => $pagination,
            'query' => $query,
            'circuit' => $circuit,
        ]);
    }

    #[Route('/new', name: 'admin_circuit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $circuit = new Circuit();
        $form = $this->createForm(CircuitType::class, $circuit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($circuit);
            $entityManager->flush();

            $this->addFlash('notice','Ajout avec success!');

            return $this->redirectToRoute('admin_circuit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('circuitAdmin/new.html.twig', [
            'circuit' => $circuit,
            'form' => $form,
        ]);
    }

    #[Route('/{idC}', name: 'admin_circuit_show', methods: ['GET'])]
    public function show(Circuit $circuit): Response
    {
        return $this->render('circuitAdmin/show.html.twig', [
            'circuit' => $circuit,
        ]);
    }

    #[Route('/{idC}/edit', name: 'admin_circuit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Circuit $circuit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CircuitType::class, $circuit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('notice','Update avec success!');

            return $this->redirectToRoute('admin_circuit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('circuitAdmin/edit.html.twig', [
            'circuit' => $circuit,
            'form' => $form,
        ]);
    }

    #[Route('/{idC}', name: 'admin_circuit_delete', methods: ['POST'])]
    public function delete(Request $request, Circuit $circuit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$circuit->getIdC(), $request->request->get('_token'))) {
            $entityManager->remove($circuit);
            $entityManager->flush();

            $this->addFlash('notice','Delete avec success!');
        }

        return $this->redirectToRoute('admin_circuit_index', [], Response::HTTP_SEE_OTHER);
    }
}
