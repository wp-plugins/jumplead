# Jumplead WordPress Plugin 

## Development Environment

* Docker + Crane
* In terminal, from the trunk folder: ```crane lift```
* [http://192.168.59.103](http://192.168.59.103)
* Add ```define('WP_DEBUG', true);``` to wp-config.php


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

