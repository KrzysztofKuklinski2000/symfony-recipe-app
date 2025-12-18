<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/follow')]
#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class FollowController extends AbstractController
{
    #[Route('/{id}', name: 'app_follow', methods: ['POST'])]
    public function follow(User $userToFollow, EntityManagerInterface $em, Request $request): Response {

        if($this->isCsrfTokenValid('follow'.$userToFollow->getId(), $request->request->get('_token'))) {
            /** @var User $currentUser*/
            $currentUser = $this->getUser();

            $currentUser->addFollowing($userToFollow);
            $em->flush();

        }

        return $this->redirectToRoute('app_profile_show', ['id' => $userToFollow->getId()]);
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow', methods: ['POST'])]
    public function unfollow(User $userToUnfollow, EntityManagerInterface $em, Request $request): Response{
        /** @var User $currentUser*/
        $currentUser = $this->getUser();

        if ($this->isCsrfTokenValid('unfollow' . $userToUnfollow->getId(), $request->request->get('_token'))){
            $currentUser->removeFollowing($userToUnfollow);
            $em->flush();
        }



        return $this->redirectToRoute('app_profile_show', ['id' => $userToUnfollow->getId()]);
    }
}
