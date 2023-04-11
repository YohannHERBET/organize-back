<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class CategoryController extends AbstractController
{
    // not useful at the moment
    // /**
    //  * methode to get all the categories
    //  * @Route("api/category", name="api_category_get_all", methods={"GET"})
    //  * @return void
    //  */
    // public function getCategories(CategoryRepository $categoryRepository)
    // {

    //     $categorysList = $categoryRepository->findAll();


    //     return $this->json($categorysList, Response::HTTP_OK, [], ['groups' => 'get_category']);
    // }


    /**
     * methode to get one category
     * @Route("api/category/{id}", name="api_category_get_one", methods={"GET"})
     * @return void
     */
    public function getOneCategory(Category $category = null)
    {
        //if the category is not found (ex: category/999)
        if ($category === null) {
            return $this->json(['error' => 'Catégorie non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'get_category']);
    }


    // not useful at the moment
    // /**
    //  * methode to get all the categories for one project
    //  * @Route("api/project/{id}/category", name="api_category_get_for_project", methods={"GET"})
    //  * @return void
    //  */
    // public function getCategoryByProject(Project $project)
    // {

    //     //if the project is not found (ex: project/999)
    //     if ($project === null) {
    //         return $this->json(['error' => 'Projet non trouvé.'], Response::HTTP_NOT_FOUND);
    //     }

    //     $categorysList = $project->getCategories();

    //     return $this->json($categorysList, Response::HTTP_OK, [], ['groups' => 'get_category']);
    // }


    /**
     * methode to create a new category
     * @Route("api/category", name="api_category_add", methods={"POST"})
     * @return void
     */
    public function createCategory(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine category
            $category = $serializer->deserialize($jsonContent, Category::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(['error' => 'JSON invalide'],Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        //to validate entity (category)
        $errors = $validator->validate($category);

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

        // save the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json(
            $category, 
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_category_get_one', ['id' => $category->getId()])
            ],
            ['groups' => 'get_category']
        );
    }

    /**
     * methode to edit a category
     * @Route("api/category/{id}", name="api_category_edit", methods={"PUT"})
     * @return void
     */
    public function editCategory(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, CategoryRepository $categoryRepository, category $category)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine category
            $categoryRequest = $serializer->deserialize($jsonContent, category::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(['error' => 'JSON invalide'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //to validate entity (category)
        $errors = $validator->validate($category);

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

        //recover the category and modify with the request content
        $categoryToUpdate = $categoryRepository->find(['id' => $category->getId()]);
        $categoryToUpdate->setNameCategory($categoryRequest->getNameCategory());

        // save the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($categoryToUpdate);
        $entityManager->flush();

        return $this->json(
            $category,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_category_get_one', ['id' => $category->getId()])
            ],
            ['groups' => 'get_category']
        );
    }

    
    /**
     * methode to delete a category
     * @Route("api/category/{id}", name="api_category_delete", methods={"DELETE"})
     * 
     */
    public function deleteCategory(Request $request, ManagerRegistry $doctrine, CategoryRepository $categoryRepository, Category $category = null)
    {

        //if the category is not found (ex: category/999)
        if ($category === null) {
            return $this->json(['error' => 'Catégorie non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $categoryToDelete = $categoryRepository->find($request->get('id'));

        $entityManager = $doctrine->getManager();
        $entityManager->remove($categoryToDelete);
        $entityManager->flush();

        return $this->json("Catégorie supprimée", Response::HTTP_OK);
    }
}
