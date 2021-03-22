# Gestion des formulaires

Nous verrons par la suite qu'il existe une méthode pour générer automatiquement un CRUD sur une ressource donnée.

Tout d'abord installez la dépendance suivantes :

```bash
composer require symfony/form
composer require validator security-csrf
```

## Exercice d'application

Vous allez créez un petit formulaire avec un titre et un champ texte pour ajouter des citations sur nos bières.

1. Créez un contrôleur QuoteController 
```bash
symfony console make:controller QuoteController
```

2. Dans ce contrôleur créer une méthode new comme suit. Vous devez créer un formulaire avec FormBuilder de Symfony

```php
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

// ...

  /**
     * @Route("/new", name="quote_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quote = new Quote();

        $form = $this->createFormBuilder($task)
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Quote'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quote);
            $entityManager->flush();

            return $this->redirectToRoute('quotes');
        }

        return $this->render('quote/new.html.twig', [
            'quote' => $quote,
            'form' => $form->createView(),
        ]);
    }
```

3. Affichez le formulaire pour ajouter une citation. Vérifiez que tout fonctionne correctement


## Définition des types de formulaire dans un dossier spécifique

Vous pouvez également créer un dossier Form dans lequel vous définissez vos formulaires dans une classe. Par exemple pour le formulaire pour ajouter une citation vous écrirez :

```php
<?php

namespace App\Form;

use App\Entity\Quote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Quote'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }
}

```

Il vous suffira alors d'importer cette définition dans votre contrôleur de la manière suivante :

```php
 $form = $this->createForm(QuoteType::class, $quote);
 ```