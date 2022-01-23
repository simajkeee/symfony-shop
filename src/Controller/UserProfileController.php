<?php

namespace App\Controller;

use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     */
    public function index(): Response
    {
        return $this->render('main/user_profile/index.html.twig');
    }

    /**
     * @Route("/profile/edit", methods={"GET","POST"}, name="user_profile_edit")
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect('/profile');
        }

        return $this->render('main/user_profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
