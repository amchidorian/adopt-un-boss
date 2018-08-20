# Adopt'Un Boss
## Description
Cette application permet de mettre en relation des postulants et des candidats, via des offres posté par les entreprises.  
Ils ont alors un système de like puis de match et de contact.

## Installation
1. clonez le dépôt a la racine de votre serveur web.

2. installer la base de données avec le fichier sql dans /database

3. mettez en place les virtualhosts (le serveur name doit etre "adopt-un-boss.bwb").

4. installez les dépendances avec la commande : composer install
 
5. configurez les accés en base de données dans le fichier database.json

Template du fichier  database.json
{ 
    "driver" : "mysql", 
    "host" : "localhost", 
    "port" : "3309", 
    "dbname" : "adopt_un_boss", 
    "username" : "", 
    "password" : "" 
}

## Structure
L'application s'appuie sur un framework MVCL afin de s'assurer la séparation des tâches.   
Il est donc livré avec les dossiers et les fichiers permettant de pouvoir travailler sur la logique métier.   
