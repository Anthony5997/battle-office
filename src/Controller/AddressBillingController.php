<?php

namespace App\Controller;

use App\Entity\AddressBilling;
use App\Form\AddressBillingType;
use App\Repository\AddressBillingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/address/billing")
 */
class AddressBillingController extends AbstractController
{
    /**
     * @Route("/", name="address_billing_index", methods={"GET"})
     */
    public function index(AddressBillingRepository $addressBillingRepository): Response
    {
        return $this->render('address_billing/index.html.twig', [
            'address_billings' => $addressBillingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="address_billing_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $addressBilling = new AddressBilling();
        $form = $this->createForm(AddressBillingType::class, $addressBilling);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($addressBilling);
            $entityManager->flush();

            return $this->redirectToRoute('address_billing_index');
        }

        return $this->render('address_billing/new.html.twig', [
            'address_billing' => $addressBilling,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="address_billing_show", methods={"GET"})
     */
    public function show(AddressBilling $addressBilling): Response
    {
        return $this->render('address_billing/show.html.twig', [
            'address_billing' => $addressBilling,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="address_billing_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, AddressBilling $addressBilling): Response
    {
        $form = $this->createForm(AddressBillingType::class, $addressBilling);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('address_billing_index');
        }

        return $this->render('address_billing/edit.html.twig', [
            'address_billing' => $addressBilling,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="address_billing_delete", methods={"POST"})
     */
    public function delete(Request $request, AddressBilling $addressBilling): Response
    {
        if ($this->isCsrfTokenValid('delete'.$addressBilling->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($addressBilling);
            $entityManager->flush();
        }

        return $this->redirectToRoute('address_billing_index');
    }
}
