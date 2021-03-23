<?php

namespace App\Controller;

use App\Entity\Quote;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quote")
 */
class QuoteController extends AbstractController
{
    /**
     * @Route("/", name="quote_index", methods={"GET"})
     */
    public function index(QuoteRepository $quoteRepository): Response
    {
        return $this->render('quote/index.html.twig', [
            'quotes' => $quoteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quote_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quote = new Quote();

        $form = $this->createFormBuilder($quote)
            ->add('title', TextType::class, ['required' => true, 'label' => 'Titre de la citation '])
            ->add('content', TextareaType::class, ['required' => true, 'label' => 'Markdown '])
            ->add('save', SubmitType::class, ['label' => 'Create Quote'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // dd($quote); // l'hydratation du formulaire par le formeBuilder
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quote);
            $entityManager->flush(); // commit pour faire persiter la citation dans la bd

            return $this->redirectToRoute('quotes');
        }

        return $this->render('quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form->createView(),
        ]);
    }

}
