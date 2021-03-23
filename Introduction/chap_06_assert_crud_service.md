# Synthèse

## Modifier l'entité Quote

1. Ajoutez le champ position, il ne pourra prendre que les deux valeurs suivantes ainsi que la valeur null :

- important

- none 


Définissez deux constantes dans votre classe :

```php

const PRIORITY_NONE = 'none';
const PRIORITY_IMPORTANT = 'important';

```

Dans votre setter vérifiez que ces deux valeurs sont bien celles que l'on souhaite enregistrer en base de données :

```php
if (!in_array($position, array(self::PRIORITY_NONE, self::PRIORITY_IMPORTANT))) {
    throw new \InvalidArgumentException("Invalid status");
}
```

2. Créez également le champ created_at un datetime.

3. Hydratez vos tables avec ses nouvelles données (Fixtures). Donnez des dates différentes à vos citations.

## Exercice service & Repository

Vous ne pouvez pas faire facilement d'UNION SQL avec Doctrine, vous pouvez cependant toujours utiliser PDO pour faire une requête native. Faites pour cet exercice deux méthode quoteImportant et quoteNone. Et utilisez ces deux méthodes dans votre services QuoteService pour afficher les citations par ordre d'importance et de date de création.


## Gestion de la validation

Pour cette partie mettez votre formulaire en mode no validate, pour tester les validations côté back.

```html
{{ form_start(form, {'attr': {'novalidate': 'novalidate'}}) }}
    {{ form_widget(form) }}
{{ form_end(form) }}
```

Vous allez utiliser les asserts de doctrine associés à vos formulaires pour gérer les messages d'erreur. Installez tout d'abord la dépendance suivante :

```bash
composer require symfony/validator doctrine/annotations
```

Aidez vous de la documentation suivante choisissez la forme notation pour définir vos contraintes de validation.

[NotBlank](https://symfony.com/doc/current/reference/constraints/NotBlank.html)

1. Le champ titre est obligatoire et le titre doit être compris entre 5 et 10 caractères.

2. Le contenu du markdown ne peut pas être vide.

3. La position ne peut accepter que les trois valeurs suivantes : important, none ou null



## Ajoutez un bouton de suppression

Voici le code pour supprimer une citation. Mettez le en place directement dans la liste des quotes.


```php

<form method="post" action="{{ path('quote_delete', {'id': quote.id}) }}" 
onsubmit="return confirm('Are you sure you want to delete this item?');">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ quote.id) }}">
    <button class="btn btn-danger">Delete</button>
</form>

```