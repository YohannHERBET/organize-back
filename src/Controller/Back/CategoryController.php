<?php

namespace App\Controller\Back;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * 
 * @Route("/back/category")
 */
class CategoryController extends AbstractController {

     /**
     * @Route("/", name="back_category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {

       return $this->render('back/category/index.html.twig', [
           'categories' => $categoryRepository->findAll(),
       ]);
    }


    /**
     * @Route("/new", name="back_category_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response 
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,  $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           $entityManager->persist($category);
           $entityManager->flush();

           $this->addFlash('success', $category->getNameCategory() . ', ajouté(e).');
           
           return $this->redirectToRoute('back_category_show', ['id' =>$category->getId()], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('back/category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }


    /**
     * 
     * Display the category content
     * 
     * @Route("/{id}", name="back_category_show", methods={"GET"})
     * 
     */
    public function show(Category $category = null): Response
    {
        if ($category === null) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        return $this->render('back/category/show.html.twig', [
            'category' => $category,
        ]);
    }


      /**
      * 
      * Modification of the category in the back-office
      *
     * @Route("/{id}/edit", name="back_category_edit", methods={"GET", "POST"})
     */
    public function editback(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success', $category->getNameCategory() . ', modifié(e).');

            $this->redirectToRoute('back_category_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('back/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
        
    }

      /**
     * @Route("/{id}", name="back_category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        $this->addFlash('success', $category->getNameCategory() . ', supprimé(e).');

        return $this->redirectToRoute('back_category_index', [], Response::HTTP_OK);
    }



}