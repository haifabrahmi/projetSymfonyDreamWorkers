<?php

namespace App\Controller;

use App\Entity\Bus;
use App\Form\BusType;
use App\Repository\BusRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;

use Flashy\Flashy;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
 use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MercurySeries\FlashyBundle\FlashyBundle;
use EasyCorp\Bundle\EasyRatingBundle\Model\Rate;
use App\Entity\Rating;


//use Doctrine\Persistence\ManagerRegistry;




/* use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
 */
/**
 * Summary of BusController
 */
class BusController extends AbstractController
{

    private $fileUploader;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->entityManager = $entityManager;
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
     public function new(Request $request , FlashyNotifier $flashy ): Response
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
            $entityManager->flush();
        $flashy->success('Bus created!', 'http://your-awesome-link.com');
    
            return $this->redirectToRoute('app_list_bus');
        }
    
        return $this->render('bus/add.html.twig', [
            'form' => $form->createView(),
        ]);
    } 


    #[Route('/bus/{id}/edit', name: 'app_edit_bus')]
public function edit(Request $request, EntityManagerInterface $entityManager, Bus $bus , FlashyNotifier $flashy ): Response
{
    $form = $this->createForm(BusType::class, $bus);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Le bus a été modifié avec succès.');
        $flashy->warning('Bus updated!', 'http://your-awesome-link.com');
        return $this->redirectToRoute('app_list_bus');
    }

    return $this->render('bus/edit.html.twig', [
        'form' => $form->createView(),
        'bus' => $bus,
    ]);
}



    

    #[Route("/bus/{modele}/statistics", name:"bus_statistics_by_model")]
    public function busStatisticsByModel(string $modele): Response
    {
        $busStatistics = $entityManager->getRepository(Bus::class)->busStatisticsByModel($modele);
        return $this->render('bus/statistics.html.twig', [
            'busStatistics' => $busStatistics,
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
        $buses = $repo->findByModeleDesc($modele);

        // Retourner la vue list et envoyer la liste des bus
        return $this->render('bus/list.html.twig', [
            'searchQuery' => $modele,
            'buses' => $buses,
        ]);
    }
    #[Route('/showall', name: 'app_bus_showall', methods: ['GET'])]
    public function showall(Request $request,BusRepository $busRepository, PaginatorInterface $paginator): Response
    {
        $buses = $busRepository->findAll();

        $buses = $paginator->paginate(
            $buses, /* query NOT result */
            $request->query->getInt('page', 1),
            3
        );

        return $this->render('bus/showall.html.twig', [
            'buses' => $buses,
        ]);
    }    
    /* 
public function showall(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
{
    $queryBuilder = $entityManager->getRepository(Bus::class)->createQueryBuilder('b')
        ->orderBy('b.id_bus', 'DESC');

    $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
    $buses = $pagination->getItems();

    return $this->render('bus/showall.html.twig', [
        'pagination' => $pagination,
        'buses' => $buses,
    ]);
} */


   /*   public function showall(EntityManagerInterface $entityManager , PaginatorInterface $paginator): Response
    {
          $buses = $entityManager
            ->getRepository(Bus::class)
            ->findAll();

        return $this->render('bus/showall.html.twig', [
            'buses' => $buses,
        ]);
    }  */

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
#[Route('/bus/sort/{field}/{order}', name: 'app_sort_bus')]
public function sort(BusRepository $repo, string $field, string $order): Response
{
    // Vérifier si le champ de tri est valide
    $validFields = ['id_bus', 'modele', 'numero_de_plaque', 'capacite', 'date_depart', 'date_arrive', 'destination', 'image'];
    if (!in_array($field, $validFields)) {
        throw $this->createNotFoundException('Champ de tri invalide.');
    }

    // Vérifier si l'ordre de tri est valide
    if (!in_array($order, ['asc', 'desc'])) {
        throw $this->createNotFoundException('Ordre de tri invalide.');
    }

    // Récupérer la liste des bus triée en fonction du champ et de l'ordre de tri
    $buses = $repo->findBy([], [$field => $order]);

    // Retourner la vue list et envoyer la liste des bus triée
    return $this->render('bus/list.html.twig', [
        'searchQuery' => null,
        'buses' => $buses,
    ]);
}
#[Route('/bus/{id}/rate', name: 'app_bus_rate', methods: ['GET', 'POST'])]
public function rate(Request $request, Bus $bus): Response
{
    $rating = new Rating();
    $rating->setBus($bus);

    $form = $this->createForm(RatingType::class, $rating);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $rating->setUser($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rating);
        $entityManager->flush();

        $this->addFlash('success', 'Your rating has been saved.');

        return $this->redirectToRoute('app_bus_showall');
    }

    return $this->render('bus/rate.html.twig', [
        'bus' => $bus,
        'form' => $form->createView(),
    ]);
}

}

    