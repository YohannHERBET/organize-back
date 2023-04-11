<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class UserController extends AbstractController
{

    /**
     * methode to get one user
     * @Route("api/user/{id}", name="api_user_get_one", methods={"GET"})
     * @return void
     */
    public function getOneUser(User $user = null)
    {
        //if the user is not found (ex: user/999)
        if ($user === null) {
            return $this->json(['error' => 'utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'get_user']);
    }


    /**
     * methode to create a new user
     * @Route("api/user", name="api_user_add", methods={"POST"})
     * @return void
     */
    public function createUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine User
            $user = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        //to validate entity (user)
        $errors = $validator->validate($user);

        // if errors 
        if (count($errors) > 0) {
            // create array
            $errorsClean = [];

            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                // we push in the array to the key "property"
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        // save the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_user_get_one', ['id' => $user->getId()])
            ],
            ['groups' => 'get_user']
        );
    }


    /**
     * methode to edit a user
     * 
     * @Route("api/user/{id}", name="api_user_edit", methods={"PUT"})
     * @return void
     */
    public function editUser(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, UserRepository $userRepository, User $user, UserPasswordHasherInterface $userPasswordHasher)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine user
            $userRequest = $serializer->deserialize($jsonContent, User::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //to validate entity (user)
        $errors = $validator->validate($user);

        // if errors 
        if (count($errors) > 0) {
            // create array
            $errorsClean = [];

            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                // we push in the array to the key "property"
                $errorsClean[$error->getPropertyPath()][] = $error->getMessage();
            };

            return $this->json($errorsClean, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //recover the user and modify with the request content
        $userToUpdate = $userRepository->find(['id' => $user->getId()]);

        if ($userRequest->getEmail() !== "") {
            $userToUpdate->setEmail($userRequest->getEmail());
        }

        if ($userRequest->getPassword() !== "") {
            $hashedPassword = $userPasswordHasher->hashPassword($user, $userRequest->getPassword());
            $userToUpdate->setPassword($hashedPassword);
        }

        // save the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($userToUpdate);
        $entityManager->flush();

        return $this->json(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_user_get_one', ['id' => $user->getId()])
            ],
            ['groups' => 'get_user']
        );
    }

    
    /**
     * methode to delete a user
     * @Route("api/user", name="api_user_delete", methods={"DELETE"})
     */
    public function deleteUser(Request $request, ManagerRegistry $doctrine, UserRepository $userRepository)
    {
        $user = $this->getUser();

        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json("Utilisateur supprimé", Response::HTTP_OK);
    }
}
