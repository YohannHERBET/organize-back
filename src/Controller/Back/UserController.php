<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * 
 * @Route("/back/user")
 */
class UserController extends AbstractController {

   

     //! BACK-OFFICE

     /**
     * @Route("/", name="back_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {

       return $this->render('back/user/index.html.twig', [
           'users' => $userRepository->findAll(),
       ]);
    }

      /**
      * 
      * Modification of the user in the back-office
      *
     * @Route("/{id}/edit", name="back_user_edit", methods={"GET", "POST"})
     */
     public function editback(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
     {
         $form = $this->createForm(UserType::class, $user);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             if ($form->get('password')->getData()) {
                 $hashedPassword = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());

                 $user->setPassword($hashedPassword);
             }

             $entityManager->flush();

             $this->addFlash('success', $user->getNickname() . ', modifié(e).');

             return $this->redirectToRoute('back_user_index', [], Response::HTTP_SEE_OTHER);
         }

         return $this->renderForm('back/user/edit.html.twig', [
             'user' => $user,
             'form' => $form,
         ]);
     }

    /**
     * @Route("/new", name="back_user_new", methods={"GET", "POST"})
     */
     public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response 
     {
         $user = new User();
         $form = $this->createForm(UserType::class,  $user);
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $userPasswordHasher ->hashPassword($user, $user->getPassword());

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', $user->getNickname() . ', ajouté(e).');

            return $this->redirectToRoute('back_user_show', ['id' =>$user->getId()], Response::HTTP_SEE_OTHER);
         }

         return $this->renderForm('back/user/new.html.twig', [
             'user' => $user,
             'form' => $form,
         ]);
     }


      /**
     * @Route("/{id}", name="back_user_show", methods={"GET"})
     * 
     * $user = null permet de récupérer la main sur la 404
     */
     public function show(User $user = null): Response
     {

        if ($user === null) {
            throw $this->createNotFoundException('Utilisateur(trice) non trouvé(e).');
        }

        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
     }


      /**
      * 
      * Delete of the user in the back-office
      *
     * @Route("/{id}/delete", name="back_user_delete", methods={"POST"})
     */
     public function deleteback(Request $request, User $user, EntityManagerInterface $entityManager): Response {

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash('success', $user->getNickname() . ', supprimé(e).');

        return $this->redirectToRoute('back_user_index', [], Response::HTTP_OK);

    }


}