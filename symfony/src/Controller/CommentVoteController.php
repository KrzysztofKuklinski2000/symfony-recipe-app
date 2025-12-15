<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentVote;
use App\Security\Voter\CommentVoter;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentVoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class CommentVoteController extends AbstractController
{
    #[Route('/comment/like/{id}', name: 'app_comment_like', methods: ['POST'])]
    public function vote(
        Comment $comment,
        EntityManagerInterface $em,
        Request $request,
        CommentVoteRepository $commentVoteRepository): Response {

        $this->denyAccessUnlessGranted(CommentVoter::VOTE, $comment);

        /** @var User $user*/
        /** @var CommentVote $vote*/
        /** @var CommentVote $existingVote)*/
        $user = $this->getUser();

        $vote = (int) $request->request->get('vote');
        $token = $request->request->get('_token');


        if(!$this->isCsrfTokenValid('vote'.$comment->getId(), $token) || !in_array($vote, [1, -1])){
            return $this->redirectToRoute('app_show', ['id' => $comment->getRecipe()->getId()]);
        }


        $existingVote = $commentVoteRepository->findOneBy([
            'author' => $user,
            'comment' => $comment
        ]);


        if($existingVote) {
            if($existingVote->getValue() === $vote) {
                $em->remove($existingVote);
            }else {
                $existingVote->setValue($vote);
            }
        }else {
            $commentVote = new CommentVote();
            $commentVote->setComment($comment);
            $commentVote->setAuthor($user);
            $commentVote->setValue($vote);
            $em->persist($commentVote);

        }
        $em->flush();

        return $this->render('comment/_vote_form.html.twig', [
            'comment' => $comment,
        ]);
    }
}
