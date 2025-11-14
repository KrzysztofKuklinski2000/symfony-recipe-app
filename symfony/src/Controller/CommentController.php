<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Security\Voter\CommentVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
#[IsGranted('ROLE_USER')]
final class CommentController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_comment_add', methods: ['POST'])]
    public function add(Recipe $recipe, EntityManagerInterface $em, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setRecipe($recipe);

            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Komentarz został dodany!');
        }else {
            $this->addFlash('error', 'Komentarz nie został dodany!');
        }


        return $this->redirectToRoute('app_show', ['id'=> $recipe->getId(), '_fragment'=>'comments']);
    }

    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $em, Request $request): Response {

        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $token = $request->request->get('_token');

        if($this->isCsrfTokenValid('delete'.$comment->getId(), $token)){
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Komentarz został usunięty!');
        }else {
            $this->addFlash('error', 'Nieprawidłowy token CSRF');
        }

        return $this->redirectToRoute('app_show', ['id' => $comment->getRecipe()->getId(), '_fragment'=>'comments']);
    }
}
