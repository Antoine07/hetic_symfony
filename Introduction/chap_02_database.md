# Symfony et Doctrine

Nous allons mettre en place la persistence des données. Nous utiliserons MySQL et Doctrine dans Symfony pour définir nos entités.


- Dépendances : d'une part l'ORM Doctrine et d'autre part les maker que vous connaissez déjà et qui nous permettrons de créer nos entités.

```bash
composer require symfony/orm-pack
composer require --dev symfony/maker-bundle

```

Une fois Doctrine installé, le fichier .env est modifié. Vous devez renseigner les informations demandées dans ce fichier pour rendre effectif la connexion au serveur de base de données.

 Dans le fichier .env - fichier de configuration des constantes de l'application - à la racine du projet renseignez le nom de votre base de données ainsi que les login/password, adaptez le code à vos besoin ci-dessous :

```text
 ###> doctrine/doctrine-bundle ###
# Format described at http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# For an SQLite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
# Configure your db driver and server_version in config/packages/doctrine.yaml
DATABASE_URL="mysql://root:root@127.0.0.1:8889/db_bafr?serverVersion=5.7"
###< doctrine/doctrine-bundle ###
```

Puis lancez la commande suivante pour créer effectivement la base de données :

```bash
php bin/console doctrine:database:create
```

Voici le diagramme UML des tables qui explicite les relations que nous devons implémenter entre les tables :

![database schema](images/simplebar_01.png)

Une bière aura 0 à plusieurs catégorie(s) et une catégorie comportera 0 à plusieurs bière(s). Chaque bière sera ratachée à un au plus pays.

Doctrine va nous permettre d'implémenter ces relations en base de données.

## Exercice schéma

Explicitez les relations entres les entités sur papier.

## Exercice Créez l'entité Country

Créez l'entité Country (singulier) à l'aide de la commande suivante, répondez au questions posées pour définir les champs de la table :

```bash
php bin/console make:entity
```

## Exercice Création de l'entité Beer

Tapez la même commande que précédement, créez uniquement les champs : name, description et published_at en respectant les types du diagramme UML ci-dessus. Si vous vous trompez supprimer les fichiers se trouvant dans les dossiers Entity et Repository correspondant à votre entity et recommencez.

Créez la relation entre l'entité Country et Beer, pour se faire ajoutez un champ country à l'entité Beer et indiquez à Doctrine que vous souhaitez faire du **ManyToOne**, en effet plusieurs bières peuvent se trouver dans un même pays.

Une relation **ManyToOne** se traduit par **N..1** c'est-à-dire une relation qui permet à une entité A d'avoir une relation avec plusieurs entités B.

Dans l'entité Beer nous allons relier Beer à Country de la manière suivante, cette entité est appelée entité propriétaire car elle possède la clé secondaire ici **country**. Nous précisons que c'est du **ManyToOne**. En effet, plusieurs bières peuvent se trouver dans au plus un même pays, noté que nous parlerons d'entité inverse pour l'entité Country, n'écrivez pas à la main ce qui suit, vous devez l'implémenter à partir de la console (make:entity) :

```php

// DANS LA CLASSE Beer

/**
* @ORM\ManyToOne(targetEntity="App\Entity\Country",
* inversedBy="beers")
*/
private $country;
```

- Commandes pour créer schéma des données, le SQL et créer la table dans la base de données

Schéma des données

```bash
php bin/console make:migration
```

Création des tables

```bash
php bin/console doctrine:migrations:migrate
```

## Exercice Création de l'entité Category

Créez simplement la table Category avec un name uniquement, puis indiquez à Doctrine que vous souhaitez mettre en relation Beer et Category dans une relation ManyToMany, une bière peut se trouver dans une ou plusieurs catégories et réciproquement une catégorie peut contenir plusieurs bières.

*Remarques : si vous vous trompez vous pouvez modifiez l'entité en donnant le nom de l'entité à modifier.*

## Migration

Pour créer le fichier de migration tapez la ligne de commande suivante, il permet de générer un fichier PHP dans lequel toutes les commandes SQL sont écrites afin de créer effectivement les tables en base de données, bien sûr il faudra exécuter une autre commande pour passer à la création des tables en base de données :

```bash
php bin/console make:migration
```

Par la suite vous taperez la ligne suivante afin de créer les tables en base de données :

```bash
php bin/console doctrine:migrations:migrate
```

## Insertion de données dans la table Beer

Nous allons maintenant créer quelques bières et les afficher en page d'accueil, nous allons pour se faire créer une méthode spécifique dans notre contrôleur BarController.

Créez maintenant dans le contrôleur BarController la méthode suivante **createBeer**, nous l'utiliserons pour insérer des données dans la table Beer :

```php

    /**
     * @Route("/newbeer", name="create_beer")
    */
    public function createBeer(){
        $entityManager = $this->getDoctrine()->getManager();

        $beer = new Beer();
        $beer->setname('Super Beer');
        $beer->setPublishedAt(new \DateTime());
        $beer->setDescription('Ergonomic and stylish!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($beer);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new beer with id '.$beer->getId());
    }

```

Ajoutez maintenant des catégories dans la table category de votre base de données.


## Exercice bières et catégories

1. Créez la catégorie blonde et assoiez cette catégorie à vos bière déjà créées.

2. Créez une bière Ardèche et associez cette bière à des catégories, pensez à créer quelques catégories au préalable.

## Exercice Fixtures Doctrine, insertion de données dans les tables

Nous allons maintenant utiliser des fixtures pour insérer des données d'exemple dans la ressource Beer en base de données, en ligne de commande tapez :

```bash
composer require --dev doctrine/doctrine-fixtures-bundle
```

Vous devriez maintenant avoir dans le dossier src le dossier **DataFixtures** ainsi qu'une classe **AppFixtures**. C'est cette dernière classe qui va nous permettre d'insérer des données automatiquement en base de données.

Installez également **Faker** pour le développement c'est un component permettant d'insérer des données d'exemple. Il permet d'avoir des patterns d'exemple comme des numéros de téléphone, adresse, nom, texte, ...

Vous trouverez la documentation officiel à l'adresse suivante :
[https://github.com/fzaninotto/Faker](https://github.com/fzaninotto/Faker)

```bash
composer require --dev fzaninotto/faker
```

Dans le fichier AppFixtures.php précisez le namespace de la dépendance Faker comme suit (autoloader), précisez également le nom des entités avec lesquelles vous souhaitez travailler :

```php

<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Beer;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        // ICI vous devez coder l'insertion des données
        // avec les patterns Faker
    }
}

```

## Exercice Faker et AppFixtures

Insérez des bières à l'aide de AppFixture et de Faker, puis tapez la ligne de commande suivante :

```bash
php bin/console doctrine:fixtures:load
```

Indication, vous pouvez créer des données personnelles (noms de bière) puis utilisez Faker pour donner une description de chacune de vos bière.

Pour plus d'information sur ces commandes reportez-vous à la documentation officiel :
[Fixture](https://symfony.com/doc/master/bundles/DoctrineFixturesBundle/index.html)


```php

$repository = $this->getDoctrine()->getRepository(Beer::class);
$beers = $repository->findAll();

```

Affichez maintenant les bières sur la page beer. Notez que la syntaxe dans le template Twig pour afficher les données est différentes du PHP habituel. Utilisez l'affichage HTML/CSS que l'on a déjà mis en place sur cette page.

```html

{% for beer in beers %}
    {{ beer.name }}
    {# attention la date est retournée dans un objet
    il faut donc appliquer un pipe | et une fonction
    dessus pour afficher la date correctement :
     #}
    {{ beer.publishedAt | date('d/m/Y')}}
{% endfor %}

```

## Exercice Ajoutez un champ price et un champ degree

Reprenez l'entité Beer et ajouter un price et degree à celle-ci. Pour se faire il suffit de relancer la commande suivante, notez que pour un décimale vous préciserez que ce dernier est sur 5 chiffres significatifs avec 2 chiffres après la virgule pour price et degree.

Attention le degree est un champ obligatoire, le prix est facultatif.

```bash

# Ajoutez le nouveau champ ...
php bin/console make:entity

# Créez le fichier de migration en tenant compte de
# l'état de la base de données et des entités => elle se connecte à la base de données
# et étudie la différence
php bin/console doctrine:migrations:diff

```

Mettez à jour les fixtures en ajoutant à l'aide de faker des prix et des degrées à vos bières.

![database schema](images/simplebar_02.png)