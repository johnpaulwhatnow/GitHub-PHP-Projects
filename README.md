Popular PHP GitHub Repos

# Overview

This small web application uses the GitHub V3 API, Symfony 2.8 and Angular 1.3 to create simple list and detail views of the most stared libraries on GitHub. To understand more about the application architecture, check out the Architecture section. If not, below are instructions.

# Screenshots
![Alt text](/screenshots/list-view.PNG?raw=true "List View")

![Alt text](/screenshots/detail-view.PNG?raw=true "Detail View")

# Backend Installation

## Installing Symfony

This documentation assumes you have a new Symfony 2.8 installation and configured it with a database. For instructions on how to install Symfony 2.8, please refer to the Symfony Documentation.

[http://symfony.com/doc/2.8/setup.html](http://symfony.com/doc/2.8/setup.html)

For me, I used Composer with the following command:

```
composer create-project symfony/framework-standard-edition my_project_name "2.8.*"
```

## Install the KNP Labs GitHub API Bundle

This app uses the GitHub API wrapper maintained by KNP Labs. Using Composer, you can add it to your app with this command:

php composer.phar require knplabs/github-api php-http/guzzle6-adapter

* If you have trouble installing Guzzle, make sure your composer.json file has the correct version of php in "config.platofrm.php"

More information can be found here: https://github.com/KnpLabs/php-github-api

## Using AppBundle

This documentation assumes that "AppBundle" is the primary home for this application in your symfony build and that this is the only code you will have in your AppBundle directory. If this is not the case, be sure to adjust the file locations below accordingly.   For simple instructions, replace your application’s src > AppBundle folder with the AppBundle folder provided in this repository.  Then, use the following command to add add this entity to your database:

php app/console doctrine:schema:update --force

* Be sure to clear your app’s cache after this change.

## Changes outside of your AppBundle

### Security

This web app makes light use of Symfony2’s access_control.  The application makes two types of AJAX requests and whitelists the IP addresses that have permission to run these requests. You can find a full example in this repo's app > config folder. These are configured in your application’s security.yml:

```
access_control:
   - { path: ^/ajax, roles: IS_AUTHENTICATED_ANONYMOUSLY, ip: 127.0.0.1 }
   - { path: ^/ajax, roles: ROLE_NO_ACCESS }
```

More information can be found here: http://symfony.com/doc/2.8/security/access_control.html

More information can be found here: http://symfony.com/doc/2.8/security/access_control.html

### Enabling the Serializer

This application also uses Symfony2’s serializer. To enable it, and thus espose it to your controllers, add this to the "framework" portion of your app’s config.yml. You can find a full example in this repo's app > config folder.

```
serializer:
   enabled: true
```

More information about using the serializer can be found here: [https://symfony.com/doc/2.8/serializer.html](https://symfony.com/doc/2.8/serializer.html)

# Frontend Installation

## Symfony2’s Web Folder

This project makes use of the default "web" folder as a webroot. You can find information about changing Symfony2’s directory structure here: [http://symfony.com/doc/2.8/configuration/override_dir_structure.html](http://symfony.com/doc/2.8/configuration/override_dir_structure.html)

## Installing the Frontend

Simply copy the contents of this repo’s "web" folder into your project’s “web” folder.

## Populating the Database

Assuming you’ve set this up at a localhost, and set your apache root to your webfolder,  you can populate the database by visiting this page:

[http://127.0.0.1/get-top-php-repos](http://127.0.0.1/get-top-php-repos)

# Architecture

## Backend

### Controllers

This application uses three controllers.  "MainController.php" serves the frontend application. “AjaxController.php” serves a list and detail route that populate the frontend application. These two routes are IP protected and can only be accessed via AJAX.  “ApiController.php” has one route to get the top php repositories from GitHub, and then inserts / updates the database accordingly.

### Entities

The app only uses one Doctrine entity, Repo, which is located in the Entity folder inside AppBundle. Most of the class was generated from the ORM mapping file in Resources > config > doctrine > Repo.orm.yml. One method, however, "hydrateFromDataRow", is a custom method used to populate an object from an API row.

### Security

As stated in the installation instructions, this app limits the use of the ajax routes (which are designed to only be used by the Angular application) in two ways:

* The app uses Symfony2’s access_control to limit ajax routes by IP

* AjaxController.php conditionally checks if the request is AJAX.

## Frontend

### Bower_components

This folder contains all of the fontend dependencies needed to run the application.

### Bundles

Symfony2’s bundle specific frontend assets.

### Components

This is where more abstract, reusable parts of the frontend application go, such as Models and Services.

### Templates

This directory contains the frontend templates used by Angular. "Repos.html" is used to render the “#/repos” and “repo.html” is used to render “#repo/:id”

### App.js

This is the main application file for the frontend application. The key parts of the file are as follows:

* Config (lines 1-13): The modules and dependencies are loaded.

* Routing (lines 14 - 64): The routes and states are defined. This app uses Angular’s UI Router

* Controllers (lines 68 - 166):  This application uses three controllers. "ReposCtrl" manages the “list” view, “RepoCtrl” manages the detail view, and “DataCtrl” manages the data needed for the list view. This is split out into a separate controller because, usually, multiple AJAX requests must be made to insure the all the data is ready to show a user the application

