# tdt/start

If you want to start working on The DataTank, start here! This is an installer, a config reader and a router. If you have an empty LAMP stack, this is the right way to go. If you already have a Symfony, Drupal, WordPress, Zend... system installed, you might want to look into the separate packages.

## Installation

### Composer

* Open a terminal, and install [composer](http://getcomposer.org/download/):

``` bash
$ curl -s https://getcomposer.org/installer | php
```

### Create-project

* If you have composer installed, you can get your copy of tdt/start by running:

``` bash
$ composer create-project tdt/start
```
When this is performed on command line, you should be ready to perform the next steps:

* Fill out the config files in app/config/*.json
* Make public the root/documentRoot, when configuring your virtual host

## Flow

There are 2 folders in this repository: public/ and app/. Public contain all the publicly accessible files, such as html, javascript and css files. App contains all the bootstrap code which configures and initiates the right packages.