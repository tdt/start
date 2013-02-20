# tdt/start

[![Build Status](https://travis-ci.org/tdt/start.png)](https://travis-ci.org/tdt/start)

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

Caveat lector! If you cloned the repository onto your machine you will only have to run the command: `composer install` to get the dependencies)

After that the command: `composer update` is adviced.

## Getting started

There are two folders in this repository: public/ and app/. Public contains all the publicly accessible files, such as html, javascript and css files. App contains all the bootstrap code which configures and initiates the correct packages.

### index.php

Copy the example index.php file from public/index.example.php &rarr; public/index.php. Change the environment setting if desired. The default environment setting is production, but if you're looking to develop or test the software you want to change the environment setting to _development_ or  _testing_ respectively.

### Configuration

Copy the config example files in app/config/ (e.g. app/config/general.example.json &rarr; app/config/general.json)

Below we cover each of the configuration files and what their configuration parameters are:

#### general.json

1. hostname - The hostname of the server it's installed on e.g. http://localhost/ .
2. subdir - The subdirectory of the host where you installed The DataTank. Mostly this is the filestructure relative to your _www_ folder. Don't forget the trailing slash if you're filling this in!
3. timezone - The timezone you're in.
4. defaultlanguage - The abbreviation of your language e.g. en, nl,...
5. defaultformat - The default format in which queried data will be presented in if no format is specified with the request.
6. accesslogapache - The absolute path to the apache log access file. This will be used to perform some statistical queries.
7. cache - Contains three parameters to perform caching:
	+ system - Choose from NoCache or MemCache. If you opt for MemCache, be sure that Memcached is installed.
	+ host - The host that the caching system is running on. Note that if NoCache has been used, host and port don't really matter.
	+ port - The port to which we have to connect in order to communicate with the caching system.
8. auth - Authentication parameters:
	+ enabled - true/false. Caveat lector, if this is put to false, anyone can delete or create data resources!
	+ api_user - The username that is allowed to perform admin actions.
	+ api_passwd - The password for the api_user.
9. logging - Contains parameters to log actions that happen within the software:
	+ enabled - true/false.
	+ path - The absolute path to a directory where we can put the logging files. Make sure PHP has write access to this directory!

#### db.json

1. system - The system name of the database. MySQL is strongly advised, although we use an ORM that handles different kinds of databases, we have not tested this thoroughly enough.
2. host - The host on which the database runs. e.g. localhost
3. user - The username to connect to the database. Make sure this user has write privileges.
4. name - The name of the database you want to use to let The DataTank write its meta-data in.
5. password - The password for the user to connect to the database.

#### cores.json

This file will contain the regular expressions that will route an HTTP-request to the correct destination.
Uncomment the routes in the _core_ entry of the json file, and that should get you going.

### Virtual host

* Make the *public* folder the root/documentRoot, when configuring your virtual host.