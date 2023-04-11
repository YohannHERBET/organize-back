<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\Project;
use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class TaskController extends AbstractController
{

    /**
     * methode to get the 5 next tasks after today
     * @Route("api/task", name="api_task_get_all", methods={"GET"})
     * @return void
     */
    public function getTasks(TaskRepository $taskRepository)
    {
        $user = $this->getUser();

        $tasksList = $taskRepository->taskByDueDate($user);

        return $this->json($tasksList, Response::HTTP_OK, [], ['groups' => ['get_task', 'get_for_task' ]]);
    }


    /**
     * methode to get one task by id
     * @Route("api/task/{id}", name="api_task_get_one", methods={"GET"})
     * @return void
     */
    public function getOneTask(Task $task = null)
    {
        //if the task is not found (ex: task/999)
        if ($task === null) {
            return $this->json(['error' => 'Tache non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($task, Response::HTTP_OK, [], ['groups' => 'get_task']);
    }


    /**
     * methode to create a new task
     * @Route("api/task", name="api_task_add", methods={"POST"})
     * @return void
     */
    public function createTask(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine task
            $task = $serializer->deserialize($jsonContent, Task::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        //to validate entity (task)
        $errors = $validator->validate($task);

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
        $task->setCreatedAt(new DateTime());
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->json(
            $task,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_task_get_one', ['id' => $task->getId()])
            ],
            ['groups' => 'get_task']
        );
    }


    /**
     * methode to edit a task
     * @Route("api/task/{id}", name="api_task_edit", methods={"PUT"})
     * @return void
     */
    public function editTask(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, TaskRepository $taskRepository, Task $task)
    {
        // recover JSON content
        $jsonContent = $request->getContent();

        try {
            // Deserialize JSON in entity Doctrine task
            $taskRequest = $serializer->deserialize($jsonContent, Task::class, 'json');
        } catch (NotEncodableValueException $e) {
            // if JSON is missing or bad format =>retunr error message
            return $this->json(
                ['error' => 'JSON invalide'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        //to validate entity (task)
        $errors = $validator->validate($task);

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

        //recover the task and modify with the request content
        $taskToUpdate = $taskRepository->find(['id' => $task->getId()]);
        $taskToUpdate->setName($taskRequest->getName());
        $taskToUpdate->setPriority($taskRequest->getPriority());
        $taskToUpdate->setDescritpionTask($taskRequest->getDescritpionTask());
        $taskToUpdate->setStartingDate($taskRequest->getStartingDate());
        $taskToUpdate->setDueDate($taskRequest->getDueDate());

        // save the entity
        $entityManager = $doctrine->getManager();
        $entityManager->persist($taskToUpdate);
        $entityManager->flush();

        return $this->json(
            $task,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('api_task_get_one', ['id' => $task->getId()])
            ],
            ['groups' => 'get_task']
        );
    }

    
    /**
     * methode to delete a task
     * @Route("api/task/{id}", name="api_task_delete", methods={"DELETE"})
     */
    public function deleteTask(Request $request, ManagerRegistry $doctrine, TaskRepository $taskRepository, Task $task = null)
    {
        //if the task is not found (ex: task/999)
        if ($task === null) {
            return $this->json(['error' => 'Tâche non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        $taskToDelete = $taskRepository->find($request->get('id'));

        $entityManager = $doctrine->getManager();
        $entityManager->remove($taskToDelete);
        $entityManager->flush();

        return $this->json("Tâche supprimée", Response::HTTP_OK);
    }
}
