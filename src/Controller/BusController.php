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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;



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

    
    #[Route("/AddbusJSON", name: "AddbusJSON")]
    public function AddbusJSON(Request $request, NormalizerInterface $Normalizer)
    {
       
        $em = $this->getDoctrine()->getManager();
        $Bus = new Bus();
        
        $modele = $request->get('modele');
        if ($modele === null || $modele === '') {
            throw new \InvalidArgumentException('The "modele" parameter cannot be null or empty.');
        }
        $Bus->setModele($modele);
        
        
        $numero_de_plaque = $request->get('numero_de_plaque');
        if ($numero_de_plaque !== null) {
            $Bus->setNumero_de_plaque($numero_de_plaque);
        }
        
        $capacite = $request->get('capacite');
        if ($capacite !== null) {
            $Bus->setCapacite($capacite);
        }
        
        $date_depart = $request->get('date_depart');
        if ($date_depart !== null) {
            $Bus->setDateDepart($date_depart);
        }
        
        $date_arrive = $request->get('date_arrive');
        if ($date_arrive !== null) {
            $Bus->setDateArrive($date_arrive);
        }
        
        $destination = $request->get('destination');
        if ($destination !== null) {
            $Bus->setDestination($destination);
        }
        
        $image = $request->get('image');
        if ($image !== null) {
            $Bus->setImage($image);
        }
        
        $em->persist($Bus);
        $em->flush();
        
        $jsonContent = $Normalizer->normalize($Bus, 'json', ['groups' => 'bus:read']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("/AllbusJSON", name:"AllbusJSON")]
     
    public function AllbusJSON(NormalizerInterface $Normalizer)
    {
        $repository= $this->getDoctrine()->getRepository(Bus::class);
        $Bus = $repository->findAll();
        $jsonContent = $Normalizer->normalize($Bus,'json',['groups'=>'bus:read']);
        return new Response(json_encode($jsonContent));
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
}

    