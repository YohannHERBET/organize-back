<?php

namespace App\Controller\Back;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * 
 * @Route("/back/project")
 */
class ProjectController extends AbstractController {
    

    //! BACK-OFFICE

    /**
     * @Route("/", name="back_project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {

       return $this->render('back/project/index.html.twig', [
           'projects' => $projectRepository->findAll(),
       ]);
    }
    

    /**
     * @Route("/new", name="back_project_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class,  $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           $entityManager->persist($project);
           $entityManager->flush();

           $this->addFlash('success', $project->getTitle() . ', ajouté(e).');

           return $this->redirectToRoute('back_project_show', ['id' =>$project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    /**
     * 
     * Display the project content
     * 
     * @Route("/{id}", name="back_project_show", methods={"GET"})
     * 
     */
    public function show(Project $project = null): Response
    {
        if ($project === null) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }

        return $this->render('back/project/show.html.twig', [
            'project' => $project,
        ]);
    }


    /**
      * 
      * Modification of the project in the back-office
      *
     * @Route("/{id}/edit", name="back_project_edit", methods={"GET", "POST"})
     */
    public function editback(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', $project->getTitle() . ', modifié(e).');

            $this->redirectToRoute('back_project_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('back/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
        
    }

    /**
     * 
     * Delete the project in the back-offic
     * 
     * @Route("/{id}", name="back_project_delete", methods={"POST"})
     */
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        $this->addFlash('success', $project->getTitle() . ', supprimé.');

        return $this->redirectToRoute('back_project_index', [], Response::HTTP_OK);
    }

   
 

}