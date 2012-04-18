# ClassLoader

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

```
$libs_dir = '/path/to/your/libs/directory/';
include $libs_dir . 'ClassLoader/Loader.php';
$Loader = new \ClassLoader\Loader();
$Loader->registerNamespaceDirectory('YourNamespace', '/path/to/your/dir/holding/namespace');
$Loader->setAutoloader();

// BAM!  You're good to go with autoloading whatever new classes are needed by
your scripts.

```

## Public API

```
registerNamespaceDirectory($topLevelNamespace, $dir)

- $topLevelNamespace string
- $dir string No trailing slashes and should be one level above $topLevelNamespace
- return void


getRegisteredNamespaces()

- return array An array of $topLevelNamespaces => $dir as set by registerNamespaceDirectory()


load($className)

- $className string
- return boolean True if successfully included class, false if not


setAutoloader()

- return boolean True if ClassLoader\Loader::load was registered as an autoloader, false if not

```

## Changelog

### version 1.0.0

- Initial version, support for registering a top level namespace, getting registered
namespaces and loading a class via ClassLoader\Loader::load()
- Autoloader method needs to be set manually by your calling code.

### version 1.2.0

- Added setAutoloader() to API, allowing only ClassLoader\Loader public methods
to be needed to setup autoloading.  No outside PHP function calls are necessary
to get ClassLoader up and running.