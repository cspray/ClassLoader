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
     * Require the class based on the fully namespaced $className passed.
     *
     * This implementation is fully PSR-0 compliant and any class name fitting the
     * standards will be loaded.
     *
     * @param string $className
     * @return boolean
     */
    public function load($className) {
        list($namespace, $class) = $this->getNamespaceAndClass($className);
        $path = $this->convertToFilePath($namespace, $class);
        if (!isset($path)) {
            return false;
        }

        if (\file_exists($path)) {
            return (boolean) require $path;
        }
        return false;
    }

    /**
     * Returns a numerically indexed array listing the [$namespace, $class] for
     * the passed $className; if the class does not have a namespace a blank string
     * is returned for that value.
     *
     * The $namespace returned will have all trailing and leading slashes removed.
     *
     * @param $className
     * @return array
     */
    protected function getNamespaceAndClass($className) {
        $lastNamespace = \strrpos($className, '\\');
        // we are not checking explicitly because if the last namespace is the
        // first pos then it is in the global namespace and, effectively, isn't in one.
        $namespace = '';
        $class = $className;
        if ($lastNamespace) {
            $namespace = \trim(\substr($className, 0, $lastNamespace), '\\');
            $class = \substr($className, $lastNamespace + 1);
        }

        return array($namespace, $class);
    }

    /**
     * Return the top-level namespace for a namespace.
     *
     * @param string $namespace
     * @return string|null
     */
    protected function getTopLevelNamespace($namespace) {
        $namespace = \ltrim($namespace, '\\ ');
        if (\strpos($namespace, '\\') !== false) {
            $namespaces = \explode('\\', $namespace);
            return $namespaces[0];
        }
        return $namespace;
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
     * @param string $namespace
     * @param string $class
     * @return string
     */
    protected function convertToFilePath($namespace, $class) {
        if (empty($namespace)) {
            list($namespace, $class) = $this->getNamespaceAndClass(\str_replace('_', '\\', $class));
        }

        $topNamespace = $this->getTopLevelNamespace($namespace);
        $path = $this->getDirectoryForTopLevelNamespace($topNamespace);
        $path .= '/' . \str_replace('\\', '/', $namespace) . '/' . \str_replace('_', '/', $class) . '.php';

        return $path;
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
            $this->namespaceMap[$topLevelNamespace] = (string) $dir;
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

    /**
     * @return boolean
     * @see http://www.php.net/spl_autoload_unregister
     */
    public function unsetAutoloader() {
        return \spl_autoload_unregister(array($this, 'load'));
    }

}
