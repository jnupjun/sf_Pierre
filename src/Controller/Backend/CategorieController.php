<?php

namespace App\Controller\Backend;

use App\Entity\Category;
use App\Form\CategorieType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Regex;

#[Route('/admin/categories', name: 'admin.categories')]
class CategorieController extends AbstractController
{
    public function __construct(
        # EM est une entité de Doctrine
        # permet de faire apparaitre ce paramètre dans toutes les méthodes de la classe
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(CategoryRepository $repo): Response
    {
        # $categories = $repo->findAll();
        # dd($categories);

        # la méthode render() permet de réutiliser le tableau categories dans le twig
        # toutes les variables côté twig viennent de la méthode render() d'un controller
        return $this->render('Backend/Categorie/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    # this suffix the global route line 14
    # GET méthode autorisée uniquement pour afficher les vues des templates
    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    # Une classe passée en paramètre est automatiquement instanciée "Auto-wiring"
    # pas besoin d'écrire "$request = new Request();"
    public function create(Request $request): Response
    {
        $categorie = new Category();

        # Création du formulaire en passant l'objet à remplir grace à createForm() de  AbstractController
        $form = $this->createForm(CategorieType::class, $categorie);
        # dd($form);
        # passer la request au formulaire pour récup les données
        $form->handleRequest($request);

        # si le formulaire est soumis et valide, on persiste l'objet en base de données
        if ($form->isSubmitted() && $form->isValid()) {
            // $categorie->setCreatedAt(new \DateTimeImmutable()); # method noobie, real method : autoSetCreatedAt()
            # dd($categorie);
            # mise à la file d'attente
            $this->em->persist($categorie);
            # exécution de la file d'attente
            $this->em->flush();

            # create flash message to inform user
            $this->addFlash('success', 'La catégorie a bien été créée');

            return $this->redirectToRoute('admin.categories.index');
        }

        # dd($request);
        # +request = $_POST       +query = $_GET

        return $this->render('Backend/Categorie/create.html.twig', [
            'form' => $form,
        ]);
    }

    # method GET = paramètre '?<champ>='
    # {id} = paramètre d'URL 
    #[Route('/{id}/update', name: '.update', methods: ['GET', 'POST'])]
    # $repo instancie CategoryRepository le temps de la fonction update 
    # public function update(int $id, CategoryRepository $repo): Response
    public function update(?Category $categorie, Request $request): Response
    # param converter permet de référencer un paramètre d'url à l'entity en paramètre de la fonction
    {
        # dd($repo->find($id));
        # dd($categorie);
        if (!$categorie) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas');

            # redirectToRoute expects a route name
            return $this->redirectToRoute('admin.categories.index');
        }
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # persist not mandatory for an update, doctrine has already persisted the data
            $this->em->persist($categorie);
            $this->em->flush();
            $this->addFlash('success', 'La catégorie a bien été créée');

            return $this->redirectToRoute('admin.categories.index');
        }
        return $this->render('Backend/Categorie/update.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Category $categorie, Request $request): RedirectResponse
    {
        if (!$categorie) {
            $this->addFlash('error', 'La catégorie demandée n\'existe pas');

            return $this->redirectToRoute('admin.categories.index');
        }

        # dd($request);
        # l'"id" du token est specifié ligne 2 du component '_form.html.twig' il est 
        # Why the fuck did I have an '!' at if (!$this->isCsrfTokenValid(…))
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('token'))) {
            $this->em->remove($categorie);
            $this->em->flush();

            $this->addFlash('success', 'La catégorie à bien été supprimée');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide');
        }
        return $this->redirectToRoute('admin.categories.index');
    }
}
