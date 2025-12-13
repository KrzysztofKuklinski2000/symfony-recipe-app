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
use Symfony\UX\Turbo\TurboBundle;
use App\Notifier\CommentNotification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[Route('/comment')]
#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class CommentController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_comment_add', methods: ['POST'])]
    public function add(Recipe $recipe, EntityManagerInterface $em, Request $request, NotifierInterface $notifer): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setRecipe($recipe);

            $em->persist($comment);
            $em->flush();

            if($recipe->getAuthor() !== $this->getUser()){
                $recipient = new Recipient($recipe->getAuthor()->getEmail());

                $notifer->send(
                    new CommentNotification($comment, $recipe),
                    $recipient
                );
            }



            $newEmptyForm = $this->createForm(CommentType::class, new Comment());

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
            {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('comment/add_success.stream.html.twig', [
                    'commentForm' => $newEmptyForm->createView(),
                    'recipe' => $recipe,
                    'comment' => $comment,
                ]);
            }

            return $this->redirectToRoute('app_show', ['id' => $recipe->getId()]);
        }

        return $this->render('comment/_form.html.twig', [
            'commentForm' => $form->createView(),
            'recipe' => $recipe,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Comment $comment, EntityManagerInterface $em, Request $request): Response {

        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $token = $request->request->get('_token');

        if($this->isCsrfTokenValid('delete'.$comment->getId(), $token)){
            $em->remove($comment);
            $em->flush();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
            {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('comment/delete_success.stream.html.twig', [
                    'comment' => $comment,
                    'recipe' => $comment->getRecipe(),
                ]);
            }
        }

        return $this->redirectToRoute('app_show', ['id' => $comment->getRecipe()->getId(), '_fragment'=>'comments']);
    }
}
