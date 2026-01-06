<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/account')]
#[IsGranted('ROLE_USER')]
#[IsGranted('IS_EMAIL_VERIFIED')]
final class AccountController extends AbstractController
{
    public function __construct(
        private readonly FileUploader $fileUploader,
        private readonly EntityManagerInterface $em
    ){}

    #[Route('/edit', name: 'app_account_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request): Response
    {

        $user = $this->getUser();
        assert($user instanceof User);

        $form  = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                try{
                    $imageFilename = $this->fileUploader->upload(
                        $imageFile,
                        'users',
                        $user->getImageFilename()
                    );

                    $user->setImageFilename($imageFilename);
                }catch(FileException $e) {
                    $this->addFlash('error', 'Wystąpił błąd podczas przesyłania pliku.');
                    return $this->redirectToRoute('app_account_edit');
                }
            }

            $this->em->flush();
            $this->addFlash('success', 'Konto zostało zaktualizowane!');
            return $this->redirectToRoute('app_account_edit');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
