# trusted - SSL certificates manager

[![Latest Stable Version](https://poser.pugx.org/bart/trusted/v/stable.png)](https://packagist.org/packages/bart/trusted) [![License](https://poser.pugx.org/bart/trusted/license.png)](https://packagist.org/packages/bart/trusted)
[![ProjectStatus](http://stillmaintained.com/bart/trusted.png)](http://stillmaintained.com/bart/trusted)

This simple SSL certificates manager includes the following features:
* Individual root CA setup to sign certficate sign requests (CSR)
* Manage users and their permissions based on domains
* Create SSL certficates with private key
* Intuitive and simple to use GUI based on bootstrap
* Built with Laravel 4 and passion in Berlin


## Pre-Requirements

* git
* composer
* openssl
* php5-sqlite


## Installation

* Clone the package: `git clone https://github.com/bart/trusted.git`
* Change into trusted directory: `cd trusted`
* Install composer dependencies: `composer install`
* Set up the app: `php artisan trusted:setup`


## Usage

After creating a virtual host open the app in a browser of your choice.
You will be asked for username and password. Initial credentials are admin/password.

Create a root CA first. Afterwards create users and certificates.
You can determine different domains of a user seperating them by comma.

Enjoy and contribute!


## License

Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
