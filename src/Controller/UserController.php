<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\UserSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/user')]
class UserController extends AbstractController
{
   
    #[Route('/deconnexion', name: 'app_user_deconnexion', methods: ['GET', 'POST'])]
    public function deconnexion(UserRepository $userRepository, SessionInterface $session): Response
    {
        $session->remove('user');
        return $this->render('templates_Front/user/acceuil.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/change', name: 'app_user_change', methods: ['GET', 'POST'])]
    public function change(UserRepository $userRepository, SessionInterface $session): Response
    {
        $user=$session->get('user');
        return $this->render('templates_Front/user/change.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/changep', name: 'app_user_changep', methods: ['GET', 'POST'])]
    public function changep(Request $request,UserRepository $userRepository, SessionInterface $session): Response
    {   $user=$session->get('user');
        $mdp = $request->request->get('old_password');
        $mdp1 = $request->request->get('new_password');
        
        $user1=$userRepository->findemm($user->getEmail(),$mdp);
        if($user1!=null){
            
            $user->setPassword($mdp1);
            $user1->setPassword($mdp1);
            $session->set('user', $user1);
            $userRepository->update($user, true);
            return $this->redirectToRoute('app_user_acceuilconnecte', [], Response::HTTP_SEE_OTHER);
           
        }
        $warningMessage = "L'ancien mot de passe est incorrect";
        return $this->render('templates_Front/user/change.html.twig', [
            'user' => $user,
            'warning_message' => $warningMessage, // passe le message d'avertissement au template
        ]);
    }
    #[Route('/{iduser}/reglage', name: 'app_user_reglage', methods: ['GET', 'POST'])]
    public function reglage(Request $request, User $user,UserRepository $userRepository, SessionInterface $session): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            $session->set('user', $user);
            
            return $this->redirectToRoute('app_user_acceuilconnecte');
        }

        return $this->renderForm('templates_Front/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/inscription', name: 'app_user_inscription', methods: ['GET', 'POST'])]
    public function inscription(Request $request,UserRepository $userRepository, SessionInterface $session): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);
            $session->set('user', $user);
            if($user->getRole()!="admin")
            return $this->redirectToRoute('app_user_acceuilconnecte', [], Response::HTTP_SEE_OTHER);
            else
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('templates_Front/user/inscri.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);


    }
    #[Route('/connexion', name: 'app_user_connexion', methods: ['GET', 'POST'])]
    public function connexion(Request $request,UserRepository $userRepository, SessionInterface $session): Response
    {
        
        return $this->renderForm('templates_Front/user/connexion.html.twig');


    }
    #[Route('/verif', name: 'app_user_verif', methods: ['GET', 'POST'])]
    public function verif(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        $mdp = $request->request->get('mdp');
        $email = $request->request->get('email');
        $user=$userRepository->findemm($email,$mdp);
        if($user!=null){
            $session->set('user', $user);
            if($user->getRole()!="admin")
            return $this->redirectToRoute('app_user_acceuilconnecte', [], Response::HTTP_SEE_OTHER);
            else
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
           
        }
        $warningMessage = "Email ou mot de passe incorrect";
        return $this->renderForm('templates_Front/user/connexion.html.twig', [
            'user' => $user,
            'warning_message' => $warningMessage, // passe le message d'avertissement au template
        ]);
        
    }
    
    #[Route('/acceuill', name: 'app_user_acceuilconnecte', methods: ['GET'])]
    public function acceuill(UserRepository $userRepository, SessionInterface $session): Response
    {
        $user = new User();
        $user=$session->get('user');
        return $this->render('templates_Front/user/acceuilconnecte.html.twig', [
            'user' => $user,
        ]);
    }
   
    #[Route('/acceuil', name: 'app_user_acceuil', methods: ['GET'])]
    public function acceuil(UserRepository $userRepository): Response
    {
        return $this->render('templates_Front/user/acceuil.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, SessionInterface $session): Response
    {
        $user=$session->get('user');
        return $this->render('templates_Back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/search", name="search", methods={"POST"})
     */
    public function search(Request $request)
    {
        $query = $request->request->get('query');

        // Effectuer la recherche dans la source de données (par exemple, une base de données)
        $users = $this->getDoctrine()->getRepository(User::class)->findByNom($query);

        // Rendre la vue avec les résultats de la recherche
        return $this->render('templates_Back/user/index.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/sort", name="sort_users", methods={"POST"})
     */
    public function sort(Request $request)
    {
        $sortField = $request->request->get('sortField');

        // Récupérer les utilisateurs depuis la source de données (par exemple, une base de données)
        $users = $this->getDoctrine()->getRepository(User::class)->findBy([], [$sortField => 'ASC']);

        // Rendre la vue avec les utilisateurs triés
        return $this->render('templates_Back/user/index.html.twig', ['users' => $users]);
    }

 /*   #[Route('/user/search', name: 'user_search', methods: ['POST'])]
public function searchUsers(Request $request, UserRepository $userRepository): Response
{
    $form = $this->createForm(UserSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $criteria = $form->getData();
        $users = $userRepository->searchUsers($criteria); // Remplacer searchUsers par la méthode de recherche dans votre UserRepository
    } else {
        $users = $userRepository->findAll(); // Remplacer findAll par la méthode de récupération de la liste complète des utilisateurs dans votre UserRepository
    }

    return $this->render('index.html.twig', [
        'form' => $form->createView(),
        'users' => $users,
    ]);
}
*/

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('templates_Back/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{iduser}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('templates_Back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{iduser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('templates_Back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{iduser}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getIduser(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


   
}
