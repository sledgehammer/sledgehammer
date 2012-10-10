# Sledgehammer Framework

This is the meta repository of the Sledgehammer Framework. It's used as a reference installation with the popular sledgehammer modules installed.

### Other resources

* [API Documentation](http://sledgehammer.github.com/api/)
* [Sledgehammer Wiki](http://github.com/sledgehammer/sledgehammer/wiki)
* [Issue tracker](https://github.com/sledgehammer/sledgehammer/issues)
* [Roadmap / Backlog](https://trello.com/board/sledgehammer-framework/4ec77591eb9c5577726d94fb)


Sledgehammer Framework is a modular framework, with [sledgehammer/core](http://github.com/sledgehammer/core) as its foundation.
Allowing you to build up your appliction with reusable modules.

## Installation
The recommended way is to install sledgehammer modules with [composer](http://getcomposer.org/).

```
$ composer.phar require sledgehammer/core *
```
`include('sledgehamer/core/bootstrap.php');` to activate the framework.

## Why

### Lean and mean
By keeping the number of classes to a minimum (Core currently has 29) it is easier to learn and remember.
For example: It doesn't use multiple Exception clasess that behave exacly the same as \Exception.

### Debugging
Sledgehammer provides excellent error reporting and debugging facilities.

```php
// Generate a notice with addition information to solve the problem.
notice('Failure X', 'You might want to try Y');

// Show the contents of $myVar
dump($myVar);

// Show parsetime, memory usage, database queries and other logs.
\Sledgehammer\statusbar();
```

### AutoLoader
Sledgehammers don't have to comply to the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) standard, because it has an AutoLoader that will autoload anything, from anywhere.
It also plugs some holes in PHP's namespace implementation.

### Plays well with others
Sledgehammer can be used as a framework, as a library or to augment an existing framework.


## Configuration
Sledgehammer is a "Convension over configuration" framework. It's goal is to reduce and simplify your code.

### Error emails
The email address in `$_SERVER['SERVER_ADMIN']` is used by the ErrorHandler to send error reports in non-development environments.

### Environments
Like [other frameworks](http://framework.zend.com/manual/1.12/en/zend.application.quick-start.html) it uses the $_SERVER['APPLICATION_ENV'] to determine the environment or assumes a "production" enviromnent.
The detected value is stored in the `Sledgehammer\ENVIROMENT` constant.
