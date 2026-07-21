<?php

namespace App\Controller\Admin;

use App\Form\ChangePasswordType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class AccountController extends AbstractController
{
    #[Route("/backoffice/account/password", name: "admin_account_password")]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException(
                "Utilisateur non connecte.",
            );
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = (string) $form
                ->get("currentPassword")
                ->getData();

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $form
                    ->get("currentPassword")
                    ->addError(new FormError("Mot de passe actuel invalide."));
            } else {
                $newPassword = (string) $form->get("newPassword")->getData();
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $newPassword),
                );
                $entityManager->flush();

                $this->addFlash("success", "Mot de passe mis a jour.");

                return $this->redirectToRoute("admin");
            }
        }

        return $this->render("admin/change_password.html.twig", [
            "title" => "Changer le mot de passe",
            "form" => $form,
        ]);
    }
}
