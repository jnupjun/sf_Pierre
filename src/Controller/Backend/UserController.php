<?php

namespace App\Controller\Backend;
# Le nom du namespace s'aggrémente automatiquement au namespace quand on l'ajoute

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

# url /admin/ accessible que depuis rôle admin dans le security.yaml
#[Route('/admin/users', name: 'admin.users')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(UserRepository $repo): Response
    {
        return $this->render('Backend/User/index.html.twig', [
            'users' => $repo->findAll(),
        ]);
    }

    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    # ?User because someone can type an id of a non-existent user on the url bar
    public function update(?User $user, Request $request): Response|RedirectResponse
    {
        # dd($user);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable');

            return $this->redirectToRoute('admin.users.index');
        }

        $form = $this->createForm(UserType::class, $user, ['isAdmin' => true]);
        # handleRequest gère toutes les données des requêtes
        # Qu'un formulaire soit validé ou non, ses données sont dans la requête ou non
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: persist user
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur mis à jour avec succès');

            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('Backend/User/update.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?User $user, Request $request): RedirectResponse
    {

        # dd($request->request->get('token'));

        if (!$user) {
            $this->addFlash('error', 'L\'utilisateur n\'existe pas');

            return $this->redirectToRoute('admin.users.index');
        }

        # added 'delete' to the token's id to personate it, present in templates/…/_form.html.twig
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('token'))) {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', 'L\'utilisateur à bien été supprimé');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide');
        }
        return $this->redirectToRoute('admin.users.index');
    }
}
