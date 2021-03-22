# Service

Service est un container de service. Représente le coeur de Symfony.

Notez que dans Symfony il suffit de créer un fichier dans un dossier ou non dans le dossier src pour que Symfony considère cette classe comme un service.

Voyez dans le fichier **service.yaml** dans le dossier config, les deux paramètres suivants permettent l'autoconfiguration des services dans l'application. Laissez ces paramètres à true :

```yaml
services:
    # default configuration for services in *this* file
    _defaults:
        autowire: true      # Automatically injects dependencies in your services.
        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.
```

C'est dans ce fichier que l'on peut voir également que Symfony considérera toutes les fichiers dans src comme des services à l'exception - excludes - de certains dossiers :

```yaml
    # makes classes in src/ available to be used as services
    # this creates a service per class whose id is the fully-qualified class name
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'
            - '../src/Tests/'
```

Notez que les repositories sont considérés comme des services.

## Exercice d'application

Créez la classe Hello dans le dossier Services et injectez cette classe dans la méthode home pour afficher le message de la méthode say sur la page d'accueil.

```php
<?php

namespace App\Services;

class Hello {

    public function say(){

        return "Hello Service";
    }
}
```

Injection de dépendance dans le contrôleur, créez une méthode showService dans le contrôleur BarController ainsi qu'un lien dans le menu principal pour afficher les différents tests que nous allons faire sur les services.

```php

/**
 * @Route("/showService", name="showService")
 */
public function showService(Hello $hello){

}
```

## Paramètres d'un service

Vous pouvez également définir des paramètres pour vos services dans le fichier service.yam à l'aide du tag arguments, voyez ci-arpès :

```php
<?php

namespace App\Services;

class Hello {

    private $message;

    public function __construct($message){
        $this->message = $message;
    }
    // ...
}
```

### Exercice d'application

Ajoutez la configuration suivante à votre service Hello et essayez maintenant de l'afficher en page d'accueil, notez que message est le nom de la variable que vous avez passer dans le constructeur de la classe Hello.

```yaml
App\Services\Hello:
        arguments:
            - message: "Hello yaml configuration service"
```

Vous pouvez aller plus loin dans la configuration des paramètres, écrivez maintenant dans le fichier .env le texte suivant :

```txt
APP_BAR_MESSAGE="Hello yaml configuration service environment"
```

Puis dans votre service.yaml récupèrez ce texte comme suit, dans la partie parameters :

```yaml
parameters:
    app_hello_message: '%env(APP_BAR_MESSAGE)%'
```

Et maintenant dans la partie argument vous pouvez écrire ce paramètre variable comme suit :

```yaml
App\Services\Hello:
        arguments:
            - message: "%app_hello_message%"
```

## Exercice Markdown

Installez la dépendance suivante dans votre projet Bar, vous trouverez de l'information sur ce package sur le site package.json

```bash
composer require cebe/markdown
```

1. Essayez maintenant d'injecter cette classe comme un service dans la méthode home de votre contrôleur BarController. Quel est le message d'erreur que Symfony affiche ?

```php
use cebe\markdown\Markdown; // <-  pensez à définir le namespace dans le contrôleur

// ...

/**
 * @Route("/showService", name="showService")
 */
public function showService(Hello $hello, Markdown $parser){
    dd($parser);
}
```

2. Dans la partie service ajoutez maintenant la ligne suivante pour que cette classe soit considérée maintenant comme un service, puis re-testez l'injection. Cette fois Symfony connait cette classe et sait l'injecter.

```yaml
services:
    cebe\markdown\Markdown: ~
```

Vous pouvez dans la console vérifier que vous avez bien ce nouveau service, qu'il est bien dans le container de services :

```bash
php bin/console debug:autowiring --all markdown
```

3. Vous allez maintenant utilisez le tableau suivant et afficher sur la page d'accueil les textes markdown en HTML. Attention, cependant Twig échappe les caractères spéciaux, utilisez le pipe raw qu'il ne les échappe pas.

```php

$markdows = [
    'post' => "
* Pommes
* Poires
    * Sous élément avec au moins quatre espaces devant.
"
 ];
```

## Exercice Service Parser

Un service peut injecter un autre service du container de service. Vous pouvez par exemple injecter la classe Markdown dans le contrôleur d'un autre service.

Créez un service HelperParser. Il permettra de transformer un texte markdown en HTML. Testez ce service dans la méthode showService.

## Exercice Création d'un service avec un repository

1. Créez une entité Quote avec un titre et un contenu. Créez également un lien dans le menu vers les citations. Vous créez un page pour afficher 10 citations sur la bière. Créez une fixture pour générer des données d'exemple avec Faker. Utilisez la syntaxe suivante :

Installez la dépendance dev suivante :

```bash
composer require davidbadura/faker-markdown-generator --dev
```

```php
namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class QuoteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($faker));

        for ($i = 0; $i < 10; $i++) {
            $post = new Quote;
            $post->setTitle($faker->catchPhrase)
                ->setContent($faker->markdown);

            $manager->persist($post);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
```

2. Créez le service qui permettra de récupérer les 10 citations et transformera les contenus en HTML. Pensez à utiliser le pipe raw pour que le markdown s'affiche correctement. 