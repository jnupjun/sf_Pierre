<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app.login', methods: ['GET', 'POST'])]
    # La route, on fait ce qu'on veut avec, on en créé autant qu'on veut !!!
    public function login(AuthenticationUtils $authUtils): Response
    {
        $error = $authUtils->getLastAuthenticationError();
        # session cookie of the last connected username
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('Security/login.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername,
        ]);
    }

    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response|RedirectResponse
    {
        $user = new User;

        # UserType::class === namespace de la classe pour faire appel à ses méthodes sans l'instancier
        $form = $this->createForm(UserType::class, $user);

        # this line is mandatory to pass the if condition below
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # TODO: hash passwd and persist
            # dd($user);
            $user
                ->setPassword(
                    $hasher->hashPassword($user, $form->get('password')->getData())
                    # get password returns all the field (mapped to false in the UserType form)
                );
            # dd($user);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été créé.');

            return $this->redirectToRoute('app.login');
        }

        return $this->render('Security/register.html.twig', [
            'form' => $form,
        ]);
    }
}
