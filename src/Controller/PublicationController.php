<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Entity\Like;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\PublicationType;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\PublicationRepository;

#[Route('/publication')]
class PublicationController extends AbstractController
{
    #[Route('/', name: 'app_publication_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $publications = $entityManager
            ->getRepository(Publication::class)
            ->findAll();

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    #[Route('/search', name: 'ajax_search_publication', methods: ['GET'])]
    public function searchAction(Request $request,PublicationRepository $publicationRepository)
    {
      $requestString = $request->get('q');
      $publications =  $publicationRepository->findEntitiesByString($requestString);
      
      if(!$publications) {
          $result['publications']['error'] = "No publications found matching your search";
          
      } else {
          $result['publications'] = $this->getRealpublications($publications);
         
      }
      return new Response(json_encode($result));
    }

    public function getRealpublications($publications){

        $realpublications = array();

        foreach ($publications as $publication){
            $realpublications[$publication->getIdPub()] = array(
                'idPub' => $publication->getIdPub(),
                'date' => $publication->getDatePub(),
                'image' => $publication->getImage(),
                'titre' => $publication->getTitre()
                            );
        }
      
        return $realpublications;
    }
    #[Route('/showall', name: 'app_publication_showall', methods: ['GET'])]
    public function showall(EntityManagerInterface $entityManager): Response
    {
        $publications = $entityManager
            ->getRepository(Publication::class)
            ->findAll();

        return $this->render('publication/showall.html.twig', [
            'publications' => $publications,
        ]);
    }
    #[Route('/new', name: 'app_publication_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            // Générer un nom de fichier unique pour éviter les conflits
            $newFilename = uniqid().'.'.$imageFile->guessExtension();

            try {
                // Déplacer le fichier téléchargé dans le répertoire de stockage
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e)
{
                // Gérer les erreurs de téléchargement de fichier
            }

            // Enregistrer le nom de fichier dans l'entité Publication
            $publication->setImage($newFilename);
        }
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{idPub}', name: 'app_publication_show', methods: ['GET'])]
    public function show(Publication $publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }
    #[Route('/show/{idPub}', name: 'app_publication_showFront', methods: ['GET', 'POST'])]
    public function showFront(Publication $publication, Request $request,HttpClientInterface $httpClient): Response
    {
        $likeRepository = $this->getDoctrine()->getRepository(Like::class);

        $like = $likeRepository->findOneBy([
          //  'user' => $user,
            'publication' => $publication,
        ]);

        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $idPub = $publication->getIdPub();
        // Créer un champ masqué pour l'ID de la publication
      /*  $form->add('idPub', HiddenType::class, [
            'data' => $idPub,
        ]);*/
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

           /*  $comment->setCreatedatcomment(new DateTime());
            $comment->setUsername($security->getUser()); */
            //filter for bad words:
                $content = $commentaire->getSujCom();
                $response = $httpClient->request('GET', 'https://neutrinoapi.net/bad-word-filter', [
                    'query' => [
                        'content' => $content
                    ],
                    'headers' => [
                        'User-ID' => '007007',
                        'API-Key' => 'SDP4TUoFlxnmnSHz6kTHBD33OOwgMOO4aWwiE1eaL9MiQ6Aw',
                    ]
                ]);
        
                if ($response->getStatusCode() === 200) {
                    $result = $response->toArray();
                    if ($result['is-bad']) {
                        // Handle bad word found
                        $this->addFlash('danger', 'Your comment contains inappropriate language and cannot be publicationed.');
                        return $this->redirectToRoute('app_publication_showFront', ['idPub' => $publication->getIdPub()], Response::HTTP_SEE_OTHER);
                    } else {
                        // Save comment
                        $this->addFlash('success', 'Your comment has been publicationed.');

                        $commentaire->setIdPub($publication);
        
                        // Sauvegarder le commentaire dans la base de données
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($commentaire);
                        $entityManager->flush();
                        return $this->redirectToRoute('app_publication_showFront', ['idPub' => $idPub]); 

                    }
                } else {
                    // Handle API error
                    
                    return new Response("Error processing request", Response::HTTP_INTERNAL_SERVER_ERROR);
                }




           /*  $commentaire->setIdPub($publication);
        
            // Sauvegarder le commentaire dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commentaire);
            $entityManager->flush();
    
            // Rediriger vers la page de la publication
            return $this->redirectToRoute('app_publication_showFront', ['idPub' => $idPub]); */
        }
        return $this->render('publication/showFront.html.twig', [
            'publication' => $publication,
            'commentaire' => $commentaire,
            'like'=>$like,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{idPub}/edit', name: 'app_publication_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }

    #[Route('/{idPub}', name: 'app_publication_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getIdPub(), $request->request->get('_token'))) {
            $entityManager->remove($publication);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
    }
}
