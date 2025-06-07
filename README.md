Pour build le Dockerfile:
docker-compose build

Pour lancer les différents conteneurs: 
docker-compose up -d

Pour arrêter docker :
docker-compose stop

Pour supprimer les containers :
docker-compose down


Pour supprimer les containers avec les volumes :
docker-compose down --volumes

Pour accéder en local:
Symfony : http://127.0.0.1:8080
PhpMyAdmin : http://127.0.0.1:8081

Pour éxec les commandes symfony:
docker exec -it php bash

Accéder à la documentation: /api
Pour récupérer un token JWT, il faut s'authentifier sur le micro-service custsomer. Et ensuite pour tester la documention API Mettre en haut à gauche "Bearer + VOTRE_JWT_TOKEN

Le covering est mis avec place avec PHPUnit et les commandes: run: vendor/bin/phpunit --coverage-clover clover.xml
puis ./vendor/bin/coverage-check clover.xml 95
dans phpunit.yml (github actions)
La branche main est protégée dans le cas d'un pull request ayant un coverage de -95%

La dette technique et l'analyse de sécurité etc est mis en place avec sonarqube cloud


Pour le message broker on utilise rabbitmq, et les librairies
composer require symfony/messenger
composer require symfony/amqp-messenger

Pour consommer les validations ou annulations de commandes avec RabbitMQ: php bin/console messenger:consume orders_in -vvv