# tdt/start

If you want to start working on The DataTank, start here! This is an installer, a config reader and a router. If you have an empty LAMP stack, this is the right way to go. If you already have a Symfony, Drupal, WordPress, Zend... system installed, you might want to look into the separate packages.

## Installation

### Composer

Open a terminal, and install [composer](http://getcomposer.org/download/):

``` bash
$ curl -s https://getcomposer.org/installer | php

# Move to your path
$ sudo mv composer.phar /usr/local/bin/composer
```

### Create-project

If you have composer installed, you can get your copy of tdt/start by running:

``` bash
$ composer create-project tdt/start -s dev
```

(Alternatively you could clone the repository and run: `composer install` to get the dependencies)

## Getting started

There are two folders in this repository: public/ and app/. Public contain all the publicly accessible files, such as html, javascript and css files. App contains all the bootstrap code which configures and initiates the right packages.

### index.php

Copy the example index.php file from public/index.example.php &rarr; public/index.php. Change environment setting if desired.

### Configuration

Copy the config example files in app/config/ (e.g. app/config/general.example.json &rarr; app/config/general.json)

The most important things to configurate are:

+ Your database configuration in **app/config/db.json**
+ *hostname* and *subdir* in **app/config/general.json**

### Virtual host

* Make the *public* folder the root/documentRoot, when configuring your virtual host.
