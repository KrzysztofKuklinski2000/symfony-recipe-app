<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Comment;

use App\Form\CommentType;
use Symfony\UX\Turbo\TurboBundle;
use App\Security\Voter\CommentVoter;
use App\Notifier\CommentNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/comment')]
#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class CommentController extends AbstractController
{
    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly EntityManagerInterface $em
    ){}

    #[Route('/add/{id}', name: 'app_comment_add', methods: ['POST'])]
    public function add(Recipe $recipe, Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            assert($user instanceof User);

            $comment->setAuthor($user);
            $comment->setRecipe($recipe);

            $this->em->persist($comment);
            $this->em->flush();

            if($recipe->getAuthor() !== $user){
                $recipient = new Recipient($recipe->getAuthor()->getEmail());

                $this->notifier->send(
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
    public function delete(Comment $comment, Request $request): Response {

        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $recipeId = $comment->getRecipe()->getId();
        $recipe = $comment->getRecipe();

        if($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))){
            $this->em->remove($comment);
            $this->em->flush();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat())
            {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('comment/delete_success.stream.html.twig', [
                    'comment' => $comment,
                    'recipe' => $recipe,
                ]);
            }
        }

        return $this->redirectToRoute('app_show', [
            'id' => $recipeId,
            '_fragment'=>'comments'
        ]);
    }
}
