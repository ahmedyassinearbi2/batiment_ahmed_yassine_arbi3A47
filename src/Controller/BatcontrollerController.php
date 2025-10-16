<?php

namespace App\Controller;

use App\Entity\Batiment;
use App\Form\BatimentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/batiment')]
class BatcontrollerController extends AbstractController
{
    // LIST ALL
    #[Route('/', name: 'batiment_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $batiments = $em->getRepository(Batiment::class)->findAll();

        return $this->render('batiment/index.html.twig', [
            'batiments' => $batiments,
        ]);
    }

    // ADD NEW BATIMENT
    #[Route('/add', name: 'batiment_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $batiment = new Batiment();
        $form = $this->createForm(BatimentType::class, $batiment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($batiment);
            $em->flush();
            return $this->redirectToRoute('batiment_index');
        }

        return $this->render('batiment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // EDIT/UPDATE EXISTING BATIMENT
    #[Route('/edit/{id}', name: 'batiment_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $batiment = $em->getRepository(Batiment::class)->find($id);

        if (!$batiment) {
            throw $this->createNotFoundException('Bâtiment non trouvé.');
        }

        $form = $this->createForm(BatimentType::class, $batiment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('batiment_index');
        }

        return $this->render('batiment/edit.html.twig', [
            'form' => $form->createView(),
            'batiment' => $batiment,
        ]);
    }

    // DELETE CONFIRMATION PAGE
    #[Route('/delete/{id}', name: 'batiment_delete')]
    public function deletePage(int $id, EntityManagerInterface $em): Response
    {
        $batiment = $em->getRepository(Batiment::class)->find($id);

        if (!$batiment) {
            throw $this->createNotFoundException('Bâtiment non trouvé.');
        }

        return $this->render('batiment/delete.html.twig', [
            'batiment' => $batiment,
        ]);
    }

    // DELETE ACTION
    #[Route('/delete/confirm/{id}', name: 'batiment_delete_confirm')]
    public function deleteConfirm(int $id, EntityManagerInterface $em): Response
    {
        $batiment = $em->getRepository(Batiment::class)->find($id);

        if ($batiment) {
            $em->remove($batiment);
            $em->flush();
        }

        return $this->redirectToRoute('batiment_index');
    }
}
