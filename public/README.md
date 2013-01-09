# tdt/start

If you want to start working on The DataTank, start here! This is an installer, a config reader and a router. If you have an empty LAMP stack, this is the right way to go. If you already have a symfony, drupal, wordpress, Zend... system installed, you might want to look into the separate packages.

## Installation

* Download the latest version or clone this repository.

* If you are an end-user who wants to use the latest build of The DataTank to open up your organisation's data, you can use our installer.php as follows:

``` bash
$ php install.php
```

it will start asking you questions according to what you want to do. You're done!

* If you are a developer that wants to develop on a full blown The DataTank, you can install all dependencies and packages using composer:

``` bash
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

When this is performed on command line, you should be ready to perform the next step:

* fill out your config in app/config.json

## Flow

There are 2 folders in this repository: public/ and app/. Public contain all the publicly accessible files, such as html, javascript and css files. App contains all the bootstrap code which configure and initiate the right packages.