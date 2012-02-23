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
register the directories for your top-level namespaces and set the `ClassLoader::load()`
method to the `spl_autoload_register()`.  Here's a code example from the [SprayFire](http://github.com/cspray/SprayFire)
project.

```php
<?php

$libsDir = '/whatever/path/to/dir/holding/libs';
include $libsDir . '/ClassLoader/Loader.php';

$Loader = new \ClassLoader\Loader();
$Loader->registerNamespaceDirectory('SprayFire', $libsDir);
\spl_autoload_register(array($Loader, 'load'));

$PrimaryBootstrap = new \SprayFire\Bootstrap\PrimaryBootstrap();
// this is converted to: $libsDir . '/SprayFire/Bootstrap/PrimaryBootstrap.php'
```

And that's pretty much it.  Not a lot else needed!  You could register more apps
if needed.  What's going on is pretty self explanatory but basically we'll look
for any classes with the `SprayFire` namespace in `$libsDir`.  Any other top level
namespaces you register would follow the same pattern.

You may retrieve the namespaces and the directories you have them registered to
by invoking the `getRegisteredNamespaces()` on the ClassLoader object.