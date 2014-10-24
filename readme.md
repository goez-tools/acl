# Simple role-based access control

[![Build Status](https://travis-ci.org/jaceju/goez-acl.svg)](https://travis-ci.org/jaceju/goez-acl) [![Code Climate](https://codeclimate.com/github/jaceju/goez-acl/badges/gpa.svg)](https://codeclimate.com/github/jaceju/goez-acl) [![Test Coverage](https://codeclimate.com/github/jaceju/goez-acl/badges/coverage.svg)](https://codeclimate.com/github/jaceju/goez-acl)

## Requirement

PHP 5.3+

## Installation

Goez/Acl is an independent library for access control, you can use it in any PHP project with composer:

```bash
composer require goez/acl
```

### Laravel 4

Goez/Acl also supports Laravel 4, just follow the steps below:

1. Install from composer.

2. Publish configuration after composer require.

   ```bash
   php artisan config:publish goez/acl
   ```

3. Register provider in `app/config/app.php` :

	```php
	'providers' => array(
		 // ...
	    'Goez\Acl\AclServiceProvider', // Add this line

	),
	```

4. Edit `app/config/packages/goez/acl/config.php`:

	```php
	<?php
	return array(
	    'init' => function ($acl) {

	        // Initialize your permission here.
	        // Example:
	        //
	        // $acl->fullPrivileges('admin');
	        // $acl->allow('author', 'read', 'article');
	        // $acl->allow('author', 'write', 'article');
	        // $acl->allow('guest', 'read', 'article');
	        // $acl->deny('guest', 'write', 'article');

	    },
	);
	```

4. 	Use methods of Acl in your code:

	```php
	// In controller:
	if ($acl->can('member', 'read', 'article')) {
	    // ...
	}
	```

	In Blade template:

	```html
	@if (app('acl')->can('member', 'read', 'article`))
	<!-- .... -->
	@endif
	```

## More Examples

### Add Role

```php
use Goez\Acl\Acl;

$acl = new Acl();

$acl->addRole('admin');
$acl->addRole('member');
$acl->addRole('guest');

var_dump($acl->hasRole('admin')); // true
var_dump($acl->hasRole('member')); // true
var_dump($acl->hasRole('notExists')); // false
```

### Create rules for role

```php
use Goez\Acl\Acl;

$acl = new Acl();

$acl->allow('guest', 'read', 'article');
$acl->deny('guest', 'write', 'article');

var_dump($acl->can('guest', 'read', 'article')); // true
var_dump($acl->can('guest', 'write', 'article')); // false
```

*Note 1: Methods `allow` and `deny` will add role automatically.*

*Note 2: It's always denied by default unless you allowed it.*

### Override rule

```php
use Goez\Acl\Acl;

$acl = new Acl();

$acl->allow('author', 'read', 'article'); // rule 1
$acl->allow('author', 'write', 'article'); // rule 2
$acl->deny('author', 'read', 'article'); // rule 3, override rule 1
$acl->deny('author', 'write', 'article'); // rule 4, override rule 2

var_dump($acl->can('author', 'read', 'article')); // false
var_dump($acl->can('author', 'write', 'article')); // false
```

### Full privileges

```php
use Goez\Acl\Acl;

$acl = new Acl();

$acl->fullPrivileges('admin');

var_dump($acl->can('admin', 'create', 'page')); // true
var_dump($acl->can('admin', 'create', 'site')); // true
var_dump($acl->can('admin', 'read', 'article')); // true
var_dump($acl->can('admin', 'write', 'article')); // true
```

*Note: Method `fullPrivileges ` will add role automatically.*

## License

MIT
