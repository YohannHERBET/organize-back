<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProjectController extends AbstractController
{

    /**
     * methode to get all the projects for the connected user
     * @Route("api/project", name="api_project_get_all", methods={"GET"})
     * @return void
     */
    public function getProject(ProjectRepository $projectRepository)
    {
        //get the connected user
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $projectsList = $projectRepository->getProjectsWithCategoriesAndTasks($user);



        // format the data for the front
        $connectedUser = [];
        $connectedUser[] = $user;

        $data = [
            $connectedUser,
            $projectsList,
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'get_all']);
    }

    
    /**
     * methode to get one project
     * @Route("api/project/{id}", name="api_project_get_one", methods={"GET"})
     * @return void
     */
    public function getOneProject(Project $project = null)
    {
        //if the project is not found (ex: project/999)
        if ($project === null) {
            return $this->json(['error' => 'Projet non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($project, Response::HTTP_OK, [], ['groups' => 'get_project']);
    }


    /**
     * methode to create a new project
     * @Route("api/project", name="api_project_add", methods={"POST"})
     * @return void
     */
    public function createProject(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        $user = $this->getUser();

        if ($user === null) {
            return $this->json(['error' => 'Utilisateur non trouvé.'], Response::HTTP_NOT_FOUND);
        }
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine Project
            $project = $serializer->deserialize($jsonContent, Project::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        //to validate entity (project)
        $errors = $validator->validate($project);

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
        //associate the user to the project
        $project->setUser($user);
        $entityManager->persist($project);
        $entityManager->flush();

        return $this->json(
            $project,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_project_get_one', ['id' => $project->getId()])
            ],
            ['groups' => 'get_project']
        );
    }


    /**
     * methode to edit a project
     * @Route("api/project/{id}", name="api_project_edit", methods={"PUT"})
     * @return void
     */
    public function editProject(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, ProjectRepository $projectRepository, Project $project)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine Project
            $projectRequest = $serializer->deserialize($jsonContent, Project::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //to validate entity (project)
        $errors = $validator->validate($project);

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

        //recover the project and modify with the request content
        $projectToUpdate = $projectRepository->find(['id' => $project->getId()]);
        $projectToUpdate->setTitle($projectRequest->getTitle());
        $projectToUpdate->setDescriptionProject($projectRequest->getDescriptionProject());

        // save the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($projectToUpdate);
        $entityManager->flush();

        return $this->json(
            $project,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_project_get_one', ['id' => $project->getId()])
            ],
            ['groups' => 'get_project']
        );
    }


    /**
     * methode to delete a project
     * @Route("api/project/{id}", name="api_project_delete", methods={"DELETE"})
     * 
     */
    public function deleteProject(Request $request, ManagerRegistry $doctrine, ProjectRepository $projectRepository, Project $project = null)
    {
        //if the project is not found (ex: project/999)
        if ($project === null) {
            return $this->json(['error' => 'Projet non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $projectToDelete = $projectRepository->find($request->get('id'));

        $entityManager = $doctrine->getManager();
        $entityManager->remove($projectToDelete);
        $entityManager->flush();

        return $this->json("Projet supprimé", Response::HTTP_OK);
    }
}
