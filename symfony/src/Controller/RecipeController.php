<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Service\FileUploader;
use App\Security\Voter\RecipeVoter;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

#[Route('/recipe')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'app_recipe_index')]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $recipeRepository): Response
    {
        $currentUser = $this->getUser();
        $recipes = $recipeRepository->findBy(['author' => $currentUser]);


        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/dashboard', name: 'app_recipe_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(): Response {
        return $this->render('recipe/dashboard.html.twig');
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    #[IsGranted(RecipeVoter::CREATE)]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->handleImageUpload($form, $recipe, $fileUploader);

            $recipe->setAuthor($this->getUser());
            $em->persist($recipe);
            $em->flush();

            $this->addFlash('success', 'Przepis został dodany!');
            return $this->redirectToRoute('app_recipe_index');
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', requirements: ['id' => '\d+']) ]
    public function show(Recipe $recipe): Response {
        $this->denyAccessUnlessGranted(RecipeVoter::VIEW, $recipe);

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $em, FileUploader $fileUploader): Response {
        $this->denyAccessUnlessGranted(RecipeVoter::EDIT, $recipe);

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->handleImageUpload($form, $recipe, $fileUploader);

            $em->flush();
            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $em): Response {

        // if($recipe->getAuthor() !== $this->getUser()) {
        //     $this->addFlash('error', 'Nie masz uprawnień do usuwania tego przepisu !!!');
        //     return $this->redirectToRoute('app_recipe_index');
        // }

        $this->denyAccessUnlessGranted(RecipeVoter::DELETE, $recipe);


        $token = $request->request->get('_token');

        if($this->isCsrfTokenValid('delete'.$recipe->getId(), $token)) {
            $em->remove($recipe);
            $em->flush();
            $this->addFlash('success', 'Przepis został usunięty!');
            return $this->redirectToRoute('app_recipe_index');
        }else {
            $this->addFlash('error', 'Nieprawidłowy token CSRF');
        }

        return $this->redirectToRoute('app_recipe_index');
    }

    private function handleImageUpload(FormInterface $form, Recipe $recipe, FileUploader $fileUploader): void {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $imageFilename = $fileUploader->upload($imageFile, 'recipes');
            $recipe->setImageFilename($imageFilename);
        }
    }
}
