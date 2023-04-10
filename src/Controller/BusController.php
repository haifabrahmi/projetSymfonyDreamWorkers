<?php

namespace App\Controller;

use App\Entity\Bus;
use App\Form\BusType;
use App\Repository\BusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;
/* use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
 */
class BusController extends AbstractController
{

    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    } 

    #[Route('/bus', name: 'app_bus')]
    public function index(): Response
    {
        return $this->render('bus/index.html.twig', [
            'controller_name' => 'BusController',
        ]);
    }

    #[Route('/bus/{id}/delete', name: 'app_delete_bus')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $bus = $entityManager->getRepository(Bus::class)->find($id);
        if (!$bus) {
            throw $this->createNotFoundException('Le bus n\'existe pas.');
        }

        $entityManager->remove($bus);
        $entityManager->flush();
        $this->addFlash('success', 'Le bus a été supprimé avec succès.');

        return $this->redirectToRoute('app_list_bus');
    }

    #[Route('/bus/add', name: 'app_add_bus')]
    public function new(Request $request): Response
    {
        $bus = new Bus();
        $form = $this->createForm(BusType::class, $bus);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Upload de l'image
            $image = $form->get('image')->getData();
            if ($image) {
                $fileUploader = $this->container->get('App\Service\FileUploader');
                $fileName = $fileUploader->upload($image);
                $bus->setImage($fileName);
            }
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bus);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_list_bus');
        }
    
        return $this->render('bus/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/bus/{id}/edit', name: 'app_edit_bus')]
public function edit(Request $request, EntityManagerInterface $entityManager, Bus $bus): Response
{
    $form = $this->createForm(BusType::class, $bus);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Le bus a été modifié avec succès.');
        return $this->redirectToRoute('app_list_bus');
    }

    return $this->render('bus/edit.html.twig', [
        'form' => $form->createView(),
        'bus' => $bus,
    ]);
}

    
    /* #[Route('/bus/{id}/edit', name: 'app_edit_bus')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Bus $bus): Response
    {
        $form = $this->createForm(BusType::class, $bus);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_list_bus');
        }

        return $this->render('bus/edit.html.twig', [
            'form' => $form->createView(),
            'bus' => $bus,
        ]);
    }
 */
    #[Route('/bus/list', name: 'app_list_bus')]
    public function getAllClass(BusRepository $repo, Request $request): Response
    {
        $modele = $request->query->get('modele');

        // Récupérer la liste des bus en fonction de la recherche par modèle
        $buses = $repo->findByModele($modele);

        // Retourner la vue list et envoyer la liste des bus
        return $this->render('bus/list.html.twig', [
            'searchQuery' => $modele,
            'buses' => $buses,
        ]);
    }

    #[Route('/bus/search', name: 'app_search_bus')]
public function search(Request $request, BusRepository $repo): Response
{
    $form = $this->createFormBuilder()
        ->add('modele', TextType::class, [
            'label' => 'Modèle de bus',
            'required' => false,
        ])
        ->getForm();

    $form->handleRequest($request);

    $buses = [];
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $modele = $data['modele'];

        $buses = $repo->findByModele($modele);
    }

    return $this->render('bus/search.html.twig', [
        'form' => $form->createView(),
        'buses' => $buses,
    ]);
}
public static function getSubscribedServices()
{
    return array_merge(parent::getSubscribedServices(), [
        'App\Service\FileUploader' => FileUploader::class,
    ]);
}


        }
    