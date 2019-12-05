# ToDoList

========

Le projet 8 consiste à intervenir sur le MVP d’une application de gestion de taches quotidiennes. Les missions de développement sont les suivants :

- ’implémentation de nouvelles fonctionnalités
- La correction de quelques anomalies
- L’implémentation de tests automatisés
- Faire un audit de qualité et de performance de l’application

## Spécifications :

- Php : >=5.5.9
- Symfony : 3.4.\*
- Phpunit :^7

## Installation:

1. Cloner le dépôt sur votre machine locale
2. Installer le projet avec composer

```
composer install
```

3. Créer la base de donnes et mettez la à jour

- php bin/console doctrine:database:create
- php bin/console doctrine:schema:update –force

4. Charger les fixtures

- php bin/console doctrine:fixtures:load

## Admin Authentification:

```

	username: user_1
	password: user_1

```
