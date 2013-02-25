<?php

/**
 * An autoloading class that allows the user to register top level namespaces
 * to a specific directory and autoloads classes belonging to that top level namespace.
 *
 * @author Charles Sprayberry
 * @license http://www.opensource.org/licenses/mit-license.php
 * @version 1.2
 */

namespace ClassLoader;

/**
 * Allows for the registering and retrieval of top level namespaces, loading
 * classes based on the directory registered for a top level namespace using a
 * generated absolute path and intends to be compatible with PSR-0 autoloading
 * conventions.
 *
 * @package ClassLoader
 */
class Loader {

    /**
     * Array map holding a top-level namespace as the key and the complete
     * root path for that namespace as the value.
     *
     * @property $namespaceMap
     */
    protected $namespaceMap = array();

    /**
     * Include the class based on the fully namespaced $className passed.
     *
     * @param string $className
     * @return boolean
     */
    public function load($className) {
        $className = \str_replace('_', '\\', $className);
        $namespace = $this->getTopLevelNamespace($className);
        $path = $this->getDirectoryForTopLevelNamespace($namespace);
        if (!isset($path)) {
            return false;
        }
        $path .= $this->convertNamespacedClassToFilePath($className);
        if (\file_exists($path)) {
            return (boolean) include $path;
        }
        return false;
    }

    /**
     * Return the top-level namespace for a class, given it has a namespace
     *
     * @param string $className
     * @return string|null
     */
    protected function getTopLevelNamespace($className) {
        $className = \ltrim($className, '\\ ');
        if (\strpos($className, '\\') !== false) {
            $namespaces = \explode('\\', $className);
            return $namespaces[0];
        }
        return null;
    }

    /**
     * Get the full directory path for the given $namespace or null if not registered.
     *
     * @param string $namespace
     * @return string|null
     */
    protected function getDirectoryForTopLevelNamespace($namespace) {
        if (isset($namespace) && \array_key_exists($namespace, $this->namespaceMap)) {
            return $this->namespaceMap[$namespace];
        }
        return null;
    }

    /**
     * Converts the PHP namespace separator to the appropriate directory separator.
     *
     * @param string $className
     * @return string
     */
    protected function convertNamespacedClassToFilePath($className) {
        return '/' . \str_replace('\\', '/', $className) . '.php';
    }

    /**
     * Allows access to set classes in a given top level namespace to be loaded
     * from a specific directory; the directory passed should be the full, absolute
     * path with a
     *
     * The directory should be assigned in such a way that the following occurs:
     *
     * <pre>
     * The class:
     *
     * Top\Level\ClassName
     *
     * The directory for that class:
     *
     * /install_path/app/Top/Level/ClassName.php
     *
     * The proper call for this class and directory would be:
     *
     * <code>
     * $ClassLoader->registerNamespaceDirectory('Top', '/install_path/app');
     * </code>
     *
     * Thus when you attempt to instantiate the class like so:
     *
     * $Class = new \Top\Level\ClassName();
     *
     * The class autoloader will convert the namespace to a directory and then
     * append that directory to the value stored by the 'Top' key.
     * </pre>
     *
     * @param string $topLevelNamespace
     * @param string $dir
     */
    public function registerNamespaceDirectory($topLevelNamespace, $dir) {
        if (!empty($topLevelNamespace) && !empty($dir)) {
            $this->namespaceMap[$topLevelNamespace] = $dir;
        }
    }

    /**
     * @return array
     */
    public function getRegisteredNamespaces() {
        return $this->namespaceMap;
    }

    /**
     * @return boolean
     * @see http://www.php.net/spl_autoload_register
     */
    public function setAutoloader() {
        return \spl_autoload_register(array($this, 'load'));
    }

}
