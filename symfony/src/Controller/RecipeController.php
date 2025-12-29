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
#[IsGranted('IS_EMAIL_VERIFIED')]
final class RecipeController extends AbstractController
{
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
            return $this->redirectToRoute('app_profile_show', ['id' => $this->getUser()->getId()]);
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form->createView(),
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

            $this->addFlash('success', 'Uaktualniono przepis!');
            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $em): Response {
        $this->denyAccessUnlessGranted(RecipeVoter::DELETE, $recipe);


        $token = $request->request->get('_token');
        $userId = $this->getUser()->getId();

        if($this->isCsrfTokenValid('delete'.$recipe->getId(), $token)) {
            $filename = $recipe->getImageFilename();

            $em->remove($recipe);
            $em->flush();

            if($filename) {
                $fileUploader->remove($filename, 'recipes');
            }

            $this->addFlash('success', 'Przepis został usunięty!');
            return $this->redirectToRoute('app_profile_show', ['id' => $userId]);
        }

        return $this->redirectToRoute('app_profile_show', ['id' => $userId]);
    }

    private function handleImageUpload(FormInterface $form, Recipe $recipe, FileUploader $fileUploader): void {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {

            $oldFilename = $recipe->getImageFilename();

            $imageFilename = $fileUploader->upload($imageFile, 'recipes');
            $recipe->setImageFilename($imageFilename);

            if($oldFilename) {
                $fileUploader->remove($oldFilename, 'recipes');
            }
        }
    }
}
