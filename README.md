# README
# Guide d’installation locale — Application Agregateur

## Récupérer le projet
git clone <URL_DU_DEPOT> agregateur
cd agregateur

## Copier l'ancien .env

Copier l'ancien .env , notamment : 

APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:t0gLwwgAXMnGbQVJXvPF60vFoTguz3ybGpZhYkkSwks=
APP_DEBUG=true
# Use a full URL with scheme to avoid bad redirects / asset URLs
APP_URL=http://localhost:8005
# Public base host used for tenant subdomains in local
DISPLAYED_APP_URL=localhost

## Installer les dépendances
composer install
npm install

Voici le récap précis des actions et réglages qui ont permis de faire fonctionner l’accès et la redirection vers l’interface contributeur.

Changements .env

APP_URL=http://localhost:8005 pour générer des URLs correctes via Laravel.
DISPLAYED_APP_URL=localhost sans port pour que le routage par sous‑domaine fonctionne.
TELESCOPE_ENABLED=false pour éviter des erreurs 500 liées à Telescope/MySQL.
Caches vidés: php artisan config:clear, php artisan route:clear, php artisan cache:clear.
Code ajusté

Correction de la détection du sous‑domaine pour ignorer le port:
app/Providers/AppServiceProvider.php → Request::macro('subdomain') passe de getHttpHost() à getHost().
Apache (XAMPP)

Activation des vhosts et écoute du port 8005:
C:\xampp\apache\conf\httpd.conf: Listen 8005, ServerName localhost:8005, Include conf/extra/httpd-vhosts.conf.
VirtualHost du projet:
C:\xampp\apache\conf\extra\httpd-vhosts.conf:
<VirtualHost *:8005>
ServerAlias larochelle.localhost
DocumentRoot "C:/Users/BenjaminGalliot/Desktop/Applicationweb/pre-prod/agregateur-preprod/agregateur-preprod/public"
AllowOverride All
Redémarrage d’Apache après modification.


Ancien README : 


## About

- php 8.2
- apache / ngnix
- composer 2.3
- nodeJS 16
- mySql

## Knowledge

- UUID first terminal : `994c4ef0-4459-475f-8c95-09a47bae81d0`
- UUID first tenant : `98f7f934-9cc7-4e6b-8bef-157b72b3cf88`


### Laravel

[Laravel](https://laravel.com/) 10 is used as a PHP Framework for the project development.

### Laravel Livewire

[Livewire](https://laravel-livewire.com/) comes as front end integration with the laravel Jetstream starter kit.

### Laravel Telescope

[Telescope](https://laravel.com/docs/9.x/telescope) provides metrics and insight to the application.

### Laravel Sanctum

Laravel sanctum provides the authentication system for api routes.
It is mainly used in application for SPA requests from frontend

[Documentation](https://laravel.com/docs/9.x/sanctum)

### Tailwind css

[Tailwind css](https://tailwindcss.com/) is used as css library.

Some useful links:

- https://www.material-tailwind.com
- https://flowbite.com
- https://tailwindui.com
- https://tailwind-elements.com

### Vite.js

By default, Laravel utilizes [Vite](https://vitejs.dev/) to bundle your assets.

### Mobiscroll

[Mobiscroll](https://mobiscroll.com/) is the library for displaying calendars and selects.

### Chart.js

[Chart.js](https://www.chartjs.org/docs/latest/) is a javascript library for generating beautiful charts.


## Installation

### Environment variable

#### Database

DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

### Composer packages

- Installing the composer dependencies:

```bash
$> composer install
```

### Node modules

Installing the node dependencies:

```bash
$> npm install
```

### File storage

Link the [filesystem](https://laravel.com/docs/9.x/filesystem) storage disks in the public directory

```bash
$> php artisan storage:link
```

### Laravel Telescope

Publish the [Laravel telescope](https://laravel.com/docs/9.x/telescope#introduction) views to the public directory

```bash
$> php artisan telescope:publish
```


## Database migrations & seeding

- Running database migrations:

```bash
$> php artisan migrate
```

## Compiling assets

To compile the javascript and css scaffolding run the following command:

```bash
$> npm run build
```

## Laravel Telescope

Telescope pages are accessible from /telescope


## Tailwind

The tailwind default theme has been extended using the [tailwind.config.js](https://tailwindcss.com/docs/configuration) file.

The application CSS also include custom tailwind-components & utilities from the file `resources/sass/tailwind.scss`. For more information about adding custom style using tailwind visit the [documentation page](https://tailwindcss.com/docs/adding-custom-styles#using-css-and-layer)

### Colors

- primary
- secondary
- info
- success
- warning
- Tailwind colors

### Plugins

- [tailwindcss-forms](https://github.com/tailwindlabs/tailwindcss-forms)
- [tailwindcss-typography](https://tailwindcss.com/docs/typography-plugin)
- [tailwindcss-line-clamp](https://github.com/tailwindlabs/tailwindcss-line-clamp)

## Artisan commands

Here are some custom artisan console commands:

- Installing and init the app (command already run from the migration)

```bash
$> php artisan init:app
```

- Creating a new user in the database

## Queues

If you want to dispatch jobs using queues, modify the `.env` file as following:

```yml
QUEUE_CONNECTION=database
```

Once new jobs have been enqueued you should tell the workers to process the incoming jobs.

```bash
# Runs the worker for the default queue
$> php artisan queue:work
