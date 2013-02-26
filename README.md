# ClassLoader

[![Build Status](https://travis-ci.org/cspray/ClassLoader.png?branch=1.3.0)](https://travis-ci.org/cspray/ClassLoader)

A PHP 5.3 class-based autoloader designed to work exclusively with namespaces to
easily autoload your favorite classes.  Originally ClassLoader was simply a part
of [SprayFire](http://github.com/cspray/SprayFire) but the utility proved to be
entirely too useful and was needed in far more projects.  Instead of copying the
class loader object from SprayFire and constantly doing small refactors for each
new project there will simply be the ClassLoader library that all projects can
utilize without needing to actually go in and change the class's namespace.

## How it works

Well, first I assume you have some system set in place for your projects to easily
include third-party libraries.  Stick the repo in whatever directory appropriate
for your project and then include the ClassLoader object.  Instantiate the object,
register the directories for your top-level namespaces and invoke ClassLoader\Loader::setAutoloader().

Here's an example:

```php
<?php

$libs_dir = '/path/to/your/libs/directory/';
include $libs_dir . 'ClassLoader/Loader.php';
$Loader = new \ClassLoader\Loader();
$Loader->registerNamespaceDirectory('YourNamespace', '/path/to/your/dir/holding/namespace');
$Loader->setAutoloader();

// BAM!  You're good to go with autoloading whatever new classes are needed by your scripts.

?>
```

## Public API

```php
<?php

/**
 * Sets a specific top level namespace to include files from a specific directory; all classes
 * autoloaded with the given $topLevelNamespace should be included from $dir.
 *
 * @param string $topLevelNamespace
 * @param string $dir
 */
registerNamespaceDirectory($topLevelNamespace, $dir)

/**
 * @return array
 */
getRegisteredNamespaces()

/**
 * @param string $className
 * @return boolean
 */
load($className)

/**
 * @return boolean
 */
setAutoloader()

/**
 * @return boolean
 */
unsetAutoloader()

?>
```

## Changelog

### version 1.3.0

- Added unsetAutoloader to API
- Added full support for PSR-0 standards

### version 1.2.0

- Added setAutoloader() to API, allowing only ClassLoader\Loader public methods
to be needed to setup autoloading.  No outside PHP function calls are necessary
to get ClassLoader up and running.


### version 1.0.0

- Initial version, support for registering a top level namespace, getting registered
namespaces and loading a class via ClassLoader\Loader::load()
- Autoloader method needs to be set manually by your calling code.

