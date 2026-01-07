<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Service\FileUploader;
use App\Security\Voter\RecipeVoter;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/recipe')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class RecipeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader
    ){}

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    #[IsGranted(RecipeVoter::CREATE)]
    public function new(Request $request): Response {

        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            assert($user instanceof User);

            $recipe->setAuthor($user);

            try {
                $this->handleImageUpload($form, $recipe);
            } catch (FileException $e) {
                $this->addFlash('danger', 'Nie udało się wgrać zdjęcia.');
                return $this->render('recipe/new.html.twig', [
                    'form' => $form,
                    'recipe' => $recipe,
                ]);
            }


            $this->em->persist($recipe);
            $this->em->flush();

            $this->addFlash('success', 'Przepis został dodany!');
            return $this->redirectToRoute('app_profile_show', ['id' => $user->getId()]);
        }

        return $this->render('recipe/new.html.twig', [
            'form' => $form,
            'recipe' => $recipe,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe): Response {
        $this->denyAccessUnlessGranted(RecipeVoter::EDIT, $recipe);

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->handleImageUpload($form, $recipe);
            } catch (FileException $e) {
                $this->addFlash('danger', 'Nie udało się zaktualizować zdjęcia.');
                return $this->redirectToRoute('app_recipe_edit', ['id' => $recipe->getId()]);
            }

            $this->em->flush();

            $this->addFlash('success', 'Uaktualniono przepis!');
            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/edit.html.twig', [
            'form' => $form,
            'recipe' => $recipe,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_recipe_delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recipe): Response {

        $this->denyAccessUnlessGranted(RecipeVoter::DELETE, $recipe);

        $user = $this->getUser();
        assert($user instanceof User);
        $userId = $user->getId();

        if($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $filename = $recipe->getImageFilename();

            $this->em->remove($recipe);
            $this->em->flush();

            if($filename) {
                $this->fileUploader->remove($filename, 'recipes');
            }

            $this->addFlash('success', 'Przepis został usunięty!');
            return $this->redirectToRoute('app_profile_show', ['id' => $userId]);
        }

        return $this->redirectToRoute('app_profile_show', ['id' => $userId]);
    }

    private function handleImageUpload(FormInterface $form, Recipe $recipe): void {
        $imageFile = $form->get('imageFile')->getData();

        if ($imageFile) {
            $imageFilename = $this->fileUploader->upload(
                $imageFile,
                'recipes',
                $recipe->getImageFilename()
            );
            $recipe->setImageFilename($imageFilename);
        }
    }
}
