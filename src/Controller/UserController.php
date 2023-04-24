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
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
        $user = $session->get('user');
        return $this->render('templates_Front/user/change.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/changep', name: 'app_user_changep', methods: ['GET', 'POST'])]
    public function changep(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        $user = $session->get('user');
        $mdp = $request->request->get('old_password');
        $mdp1 = $request->request->get('new_password');

        $user1 = $userRepository->findemm($user->getEmail(), $mdp);
        if ($user1 != null) {

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
    public function reglage(Request $request, User $user, UserRepository $userRepository, SessionInterface $session): Response
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
    #[Route('/activation', name: 'app_user_activation', methods: ['GET', 'POST'])]
    public function activation(FlashyNotifier $flashy, Request $request, SessionInterface $session, UserRepository $userRepository): Response
    {
        $user = $session->get('user');
        $user->setIs_verified(1);
        $userRepository->update($user, true);
        $flashy->success('email vérifié avec succès');
        return $this->renderForm('templates_front/user/connexion.html.twig');
    }
    #[Route('/inscription', name: 'app_user_inscription', methods: ['GET', 'POST'])]
    public function inscription(UserPasswordEncoderInterface $PasswordEncoder, Request $request, TokenGeneratorInterface $tokenGenerator, Swift_Mailer $mailer, UserRepository $userRepository, SessionInterface $session): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           /* $password = $user->getPassword();
            $user->setPassword($PasswordEncoder->encodePassword(
                $user,
                $password
            ));*/
            $token = $tokenGenerator->generateToken();
            try {
                $user->setToken($token);
                $userRepository->save($user, true);
                $session->set('user', $user);
                $url = $this->generateUrl('app_user_activation', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

                // Create the Transport
                $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                    ->setUsername('yesmine.guesmi@esprit.tn')
                    ->setPassword('BACMATH2K20');

                // Create the Mailer using your created Transport
                $mailer = new Swift_Mailer($transport);

                // Create a message
                $message = (new Swift_Message('Activation de Compte'))
                    ->setFrom(['yesmine.guesmi@esprit.tn' => 'Tunibus'])
                    ->setTo([$user->getEmail()])
                    ->setBody("<p>Salut! </p>Veuillez cliquer: " . $url, 'text/html');

                // Send the message
                $result = $mailer->send($message);

                return $this->redirectToRoute('app_user_connexion', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $exception) {
                return $this->renderForm('templates_Front/user/inscri.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }
        }

        return $this->renderForm('templates_Front/user/inscri.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
    #[Route('/connexion', name: 'app_user_connexion', methods: ['GET', 'POST'])]
    public function connexion(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {

        return $this->renderForm('templates_Front/user/connexion.html.twig');
    }
    #[Route('/verif', name: 'app_user_verif', methods: ['GET', 'POST'])]
    public function verif(UserPasswordEncoderInterface $passwordEncoder, FlashyNotifier $flashy, Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            if (isset($_POST['g-recaptcha-response'])) {
                $recaptcha = $_POST['g-recaptcha-response'];
                if (!$recaptcha) {
                    return $this->renderForm('templates_Front/user/connexion.html.twig');
                }
                $mdp = $request->request->get('mdp');
                $email = $request->request->get('email');
                $user = new User();
                $user = $userRepository->findemm($email, $mdp);
                if (!$user) {
                    $flashy->error('email ou  mot de passe incorrect');
                    return $this->renderForm('templates_Front/user/connexion.html.twig');
                }
               /* if (!$passwordEncoder->isPasswordValid($user, $mdp)) {
                    $flashy->error('email ou  mot de passe incorrect');
                    return $this->renderForm('templates_Front/user/connexion.html.twig');
                }*/
                if ($user->getIs_verified() === 0) {
                    $flashy->warning('email non vérifié');
                    return $this->renderForm('templates_Front/user/connexion.html.twig', [
                        'user' => $user,
                    ]);
                }
                $session->set('user', $user);
                if ($user->getRole() != "admin")
                    return $this->redirectToRoute('app_user_acceuilconnecte', [], Response::HTTP_SEE_OTHER);
                else
                    return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->render('templates_Front/user/connexion.html.twig');
            }
        }
        return $this->renderForm('templates_Front/user/connexion.html.twig');
    }

    #[Route('/acceuill', name: 'app_user_acceuilconnecte', methods: ['GET'])]
    public function acceuill(UserRepository $userRepository, SessionInterface $session): Response
    {
        $user = new User();
        $user = $session->get('user');
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
        $user = $session->get('user');
        return $this->render('templates_Back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'user' => $user,
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
        if ($this->isCsrfTokenValid('delete' . $user->getIduser(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /*   #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
    
            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user) {
                // Vérification du mot de passe
                if ($passwordEncoder->isPasswordValid($user, $password)) {
                    $token = md5(uniqid());
                    $user->setResetToken($token);
                    // Mise à jour du mot de passe haché
                    $newPasswordHash = $passwordEncoder->encodePassword($user, $password);
                    $user->setPassword($newPasswordHash);
    
                    $userRepository->save($user);
                    $this->sendResetPasswordEmail($mailer, $user);
                    return $this->redirectToRoute('forgot_password_email_sent');
                } else {
                    $this->addFlash('danger', 'Invalid password');
                }
            } else {
                $this->addFlash('danger', 'Email not found');
            }
        }
    
        return $this->render('security/forgot_password.html.twig');
    }

    //envoi de l'email de réinitialisation de mot de passe
private function sendResetPasswordEmail(MailerInterface $mailer, User $user)
{
    $email = (new TemplatedEmail())
        ->from('noreply@example.com')
        ->to($user->getEmail())
        ->subject('Reset Password')
        ->htmlTemplate('emails/reset_password.html.twig')
        ->context([
            'user' => $user,
        ]);

    $mailer->send($email);
}*/
}
