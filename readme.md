# Simple role-based access control

[![Build Status](https://travis-ci.org/jaceju/goez-acl.svg)](https://travis-ci.org/jaceju/goez-acl) [![Code Climate](https://codeclimate.com/github/jaceju/goez-acl/badges/gpa.svg)](https://codeclimate.com/github/jaceju/goez-acl) [![Test Coverage](https://codeclimate.com/github/jaceju/goez-acl/badges/coverage.svg)](https://codeclimate.com/github/jaceju/goez-acl)

## Requirement

- PHP 7.4+

## Installation

Goez/Acl is an independent library for access control, you can use it in any PHP project with composer:

```shell
composer require goez/acl
```

### Laravel

Goez/Acl also supports Laravel 5.4, just follow the steps below:

1. Install from composer.

2. Publish configuration after composer require.

   ```shell
   php artisan vendor:publish --tag=acl-config
   ```

3. Edit `app/config/packages/goez/acl/config.php`:

    ```php
    <?php
    return [
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
    ];
    ```

4. Use methods of Acl in your code:

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

For the examples below, you need to create an instance of `Acl` first:

```php
use Goez\Acl\Acl;

$acl = new Acl();
```

### Add Role

```php
$acl->addRole('admin');
$acl->addRole('member');
$acl->addRole('guest');

var_dump($acl->hasRole('admin')); // true
var_dump($acl->hasRole('member')); // true
var_dump($acl->hasRole('notExists')); // false
```

### Create rules for role

```php
$acl->allow('guest', 'read', 'article');
$acl->deny('guest', 'write', 'article');

var_dump($acl->can('guest', 'read', 'article')); // true
var_dump($acl->can('guest', 'write', 'article')); // false
```

*Note 1: Methods `allow` and `deny` will add role automatically.*

*Note 2: It's always denied by default unless you allowed it.*

### Override rule

```php
$acl->allow('author', 'read', 'article'); // rule 1
$acl->allow('author', 'write', 'article'); // rule 2
$acl->deny('author', 'read', 'article'); // rule 3, override rule 1
$acl->deny('author', 'write', 'article'); // rule 4, override rule 2

var_dump($acl->can('author', 'read', 'article')); // false
var_dump($acl->can('author', 'write', 'article')); // false
```

### Full privileges

```php
$acl->fullPrivileges('admin');

var_dump($acl->can('admin', 'create', 'page')); // true
var_dump($acl->can('admin', 'create', 'site')); // true
var_dump($acl->can('admin', 'read', 'article')); // true
var_dump($acl->can('admin', 'write', 'article')); // true
```

*Note: Method `fullPrivileges ` will add role automatically.*

### Multiple actions or resources

```php
$actions = ['create', 'read', 'write'];
$resources = ['page', 'site', 'article'];

$acl->allow('guest', 'read', $resources);
$acl->allow('author', $actions, 'article');
$acl->allow('admin', $actions, $resources);
```

### Wildcard support for action

```php
$acl->allow('author', '*', 'article');

var_dump($acl->can('author', 'read', 'article')); // true
var_dump($acl->can('author', 'write', 'article')); // true

var_dump($acl->can('author', 'read', 'news')); // false
var_dump($acl->can('author', 'write', 'news')); // false
```

### Wildcard support in `can` method.

```php
$acl->allow('guest', 'write', 'article:comment');

var_dump($acl->can('guest', '*', 'article')); // true
var_dump($acl->can('guest', '*', 'article:content')); // false
var_dump($acl->can('guest', '*', 'news:*')); // false
```

### Sub resource

Use `:` to define the sub-resource.

In this example, `article` as same as `article:*`.

```php
$acl->allow('guest', 'read', 'article');
$acl->allow('guest', 'write', 'article:comment');
$acl->allow('author', '*', 'article:*');

var_dump($acl->can('author', 'read', 'article:title')); // true
var_dump($acl->can('author', 'read', 'article:content')); // true
var_dump($acl->can('author', 'read', 'article:comment')); // true
var_dump($acl->can('author', 'write', 'article:title')); // true
var_dump($acl->can('author', 'write', 'article:content')); // true
var_dump($acl->can('author', 'write', 'article:comment')); // true

var_dump($acl->can('guest', 'read', 'article:title')); // true
var_dump($acl->can('guest', 'read', 'article:content')); // true
var_dump($acl->can('guest', 'read', 'article:comment')); // true
var_dump($acl->can('guest', 'write', 'article:title')); // false
var_dump($acl->can('guest', 'write', 'article:content')); // false
var_dump($acl->can('guest', 'write', 'article:comment')); // true
```

## License

MIT
