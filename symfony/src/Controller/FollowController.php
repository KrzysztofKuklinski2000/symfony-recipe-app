<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/follow')]
#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class FollowController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em){}

    #[Route('/{id}', name: 'app_follow', methods: ['POST'])]
    public function follow(User $userToFollow, Request $request): Response {

        if($this->isCsrfTokenValid('follow'.$userToFollow->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();
            assert($user instanceof User);

            if($user !== $userToFollow){
                $user->addFollowing($userToFollow);
                $this->em->flush();
            }

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('follow/action.stream.html.twig', [
                    'user' => $userToFollow,
                ]);
            }

        }

        return $this->redirectToRoute('app_profile_show', ['id' => $userToFollow->getId()]);
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow', methods: ['POST'])]
    public function unfollow(User $userToUnfollow, Request $request): Response{

        if ($this->isCsrfTokenValid('unfollow' . $userToUnfollow->getId(), $request->request->get('_token'))){
            $user = $this->getUser();
            assert($user instanceof User);

            $user->removeFollowing($userToUnfollow);
            $this->em->flush();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('follow/action.stream.html.twig', [
                    'user' => $userToUnfollow,
                ]);
            }
        }



        return $this->redirectToRoute('app_profile_show', ['id' => $userToUnfollow->getId()]);
    }
}
