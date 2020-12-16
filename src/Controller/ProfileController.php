<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Profile\ProfileType;
use App\Form\Profile\EditPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/mon-compte", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="show")
     */
    public function show(): Response
    {
        return $this->render('site/profile/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/modifier", name="edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $username = $user->getUsername();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre compte a bien été modifié');
            return $this->redirectToRoute('profile_show');
        } else {
            $user->setUsername($username);
        }
        return $this->render('site/profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/modifier-mot-de-passe", name="edit_password")
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(EditPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $password = $encoder->encodePassword($user, $form->get('password')->getData());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été modifié');
            return $this->redirectToRoute('profile_show');
        }
        return $this->render('site/profile/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/supprimer/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $session = new Session();
            $session->invalidate();

            return $this->redirectToRoute('site_account_deleted');

        }
        $this->addFlash('danger', 'Erreur lors de la suppression du compte. Veuillez réessayer plus tard.');
        return $this->redirectToRoute('profile_show');
    }
}
