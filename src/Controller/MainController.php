<?php

namespace App\Controller;

use App\Entity\Crud;
use App\Form\CrudType;
use App\Repository\CrudRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class MainController extends AbstractController
{

    private $entityManager;
    private $crudRepository;

    public function __construct(EntityManagerInterface $entityManager, CrudRepository $crudRepository)
    {
        $this->entityManager = $entityManager;
        $this->crudRepository = $crudRepository;
    }



    /** @Route("/main", name="main")*/
    public function index(): Response
    {
        $data = $this->crudRepository->findAll();
        return $this->render('main/index.html.twig', [
            'data' => $data
        ]);
    }


    /** @Route("/create", name="create") */
    public function create(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $crud = new Crud();
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($crud);
            $entityManagerInterface->flush();

            $this->addFlash("notice", "Data Submitted!");

            return $this->redirectToRoute('main');
        }

        return $this->render('main/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /** @Route("/update/{id}", name="update") */
    public function update(Request $request, EntityManagerInterface $entityManagerInterface, $id): Response
    {

        $crud = $this->crudRepository->find($id);
        $form = $this->createForm(CrudType::class, $crud);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->persist($crud);
            $entityManagerInterface->flush();

            $this->addFlash("notice", "Update Submitted!");

            return $this->redirectToRoute('main');
        }

        return $this->render('main/update.html.twig', [
            'form' => $form->createView(),
            "crud" => $crud
        ]);
    }

    /** @Route("/delete/{id}", name="delete") */
    public function delete(EntityManagerInterface $entityManagerInterface, $id)
    {
        $crud = $this->crudRepository->find($id);
        $entityManagerInterface->persist($crud);
        $entityManagerInterface->remove($crud);
        $entityManagerInterface->flush();

        $this->addFlash("notice", "Deleted!");
        return $this->redirectToRoute('main');
    }
}
