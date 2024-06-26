<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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
}
