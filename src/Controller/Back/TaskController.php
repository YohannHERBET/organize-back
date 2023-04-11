<?php

namespace App\Controller\Back;


use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * 
 * @Route("/back/task")
 */
class TaskController extends AbstractController {


      /**
     * @Route("/", name="back_task_index", methods={"GET"})
     */
    public function index(TaskRepository $taskRepository): Response
    {

       return $this->render('back/task/index.html.twig', [
           'tasks' => $taskRepository->findAll(),
       ]);
    }

     /**
     * @Route("/new", name="back_task_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class,  $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setCreatedAt(new DateTime());
           $entityManager->persist($task);
           $entityManager->flush();

           $this->addFlash('success', $task->getName() . ', ajouté(e).');

           return $this->redirectToRoute('back_task_show', ['id' =>$task->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * 
     * Display the task content
     * 
     * @Route("/{id}", name="back_task_show", methods={"GET"})
     * 
     */
    public function show(Task $task = null): Response
    {
        if ($task === null) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }

        return $this->render('back/task/show.html.twig', [
            'task' => $task,
        ]);
    }

      /**
      * 
      * Modification of the task in the back-office
      *
     * @Route("/{id}/edit", name="back_task_edit", methods={"GET", "POST"})
     */
    public function editback(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', $task->getName() . ', modifié(e).');

            $this->redirectToRoute('back_task_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('back/task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
        
    }

    /**
     * @Route("/{id}", name="back_task_delete", methods={"POST"})
     */
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        $this->addFlash('success', $task->getName() . ', supprimé(e).');

        return $this->redirectToRoute('back_task_index', [], Response::HTTP_OK);
    }
}