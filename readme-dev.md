# Jumplead WordPress Plugin 

## Development Environment

* Docker + Crane
* In terminal, from the trunk folder: ```crane lift```
* [http://192.168.59.103](http://192.168.59.103)
* Add ```define('WP_DEBUG', true);``` to wp-config.php











## Coding Standards

We use THREE different coding standards checkers.

### PHP Compatabilty

[https://github.com/wimg/PHPCompatibility](https://github.com/wimg/PHPCompatibility)

Supports PHPCS <2.

* Install: ```composer install```
* Run ```sh codingstandards.sh```
* Fix: Manual (until it supports PHPCS 2+)

### WordPress Coding Standards

[https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards)

Spports PHPCS 2+ only.

* Outside of this project's folder, Install: 

```
composer create-project wp-coding-standards/wpcs:dev-master --no-dev;
phpcs --config-set installed_paths <PATH TO WPCS>;
```

* Run ```sh codingstandards.sh```
* Fix: ``` phpcbf ./ --standard=WordPress```

### Short Array Syntax Checker

* Install: Nothing. Just a ```pcregrep``` command
* Run ```sh codingstandards.sh```
* Fix: Manual











## Integrations

### JetPack

* Add ```define('JETPACK_DEV_DEBUG', true);``` to wp-config.php

#### Sample Form

	[contact-form]
	    [contact-field label='First Name' type='name' required='1'/]
	    [contact-field label='Last Name' type='text'/]
	    [contact-field label='Email' type='email' required='1'/]
	    [contact-field label='Company' type='text'/]
	[/contact-form]

### Contact Form 7

#### Sample Form

	<p>First Name (required)<br />[text* first-name] </p>
	<p>Last Name (required)<br />[text* last-name]</p>
	<p>Your Email (required<br />[email* your-email]</p>
	<p>Company (required)<br />[text* company]</p>
	<p>[submit "Send"]</p>
	

### Formidable

* Create a form inside WordPress admin.

