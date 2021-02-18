# Email

[![Awesome9](https://img.shields.io/badge/Awesome-9-brightgreen)](https://awesome9.co)
[![Latest Stable Version](https://poser.pugx.org/awesome9/email/v/stable)](https://packagist.org/packages/awesome9/email)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/awesome9/email.svg)](https://packagist.org/packages/awesome9/email)
[![Total Downloads](https://poser.pugx.org/awesome9/email/downloads)](https://packagist.org/packages/awesome9/email)
[![License](https://poser.pugx.org/awesome9/email/license)](https://packagist.org/packages/awesome9/email)

<p align="center">
	<img src="https://img.icons8.com/nolan/256/email.png"/>
</p>

## 📃 About Email

This package provides ease of designing and sending emails within WordPress.

## 💾 Installation

``` bash
composer require awesome9/email
```

## 🕹 Usage

First, you need to spin out configuration for your email.

```php
Awesome9\Email\Manager::get()
	->set_from_name( 'Awesome9' )
	->set_from_email( 'info@awesome9.co' );
```

Now, let's add and remove some data to be output in admin.

```php
Awesome9\Email\Email::get()
	->add( 'company', 'great' )
	->add( 'remove', 'me' )
	->add( 'array', array(
		'a' => 'test',
		'b' => 'test',
	) );

Awesome9\Email\Email::get()
	->remove( 'remove' )
```

And you can use it in your JavaScript files as
```js
console.log( awesome9.company );
console.log( awesome9.array.a );
```

### Available JSON methods

JSON class methods.

| Method                                                                         | Description              | Returns                                                      |
| ------------------------------------------------------------------------------ | ------------------------ | ------------------------------------------------------------ |
| ```add( (string) $key, (mixed) $value, (string) $object_name )```              | Add the variable         | `$this`                                                      |
| ```remove( (string) $var_name, (string) $object_name ) )```                    | Removes the variable     | `$this`                                                      |
| ```clear_all()```                                                              | Clears all data          | `$this`                                                      |
| ```output()```                                                                 | Outputs the JSON data    |                                                              |

### Helper functions

You can use the procedural approach as well:

```php
Awesome9\JSON\add( $key, $value, $object_name = false );

Awesome9\JSON\remove( $key, $object_name = false );
```

All the parameters remains the same as for the `JSON` class.

## 📖 Changelog

[See the changelog file](./CHANGELOG.md)
