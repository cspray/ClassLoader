<?php

/**
 * @file
 * @brief Holds a class used as the framework autoloader, converting a namespaced
 * class to an absolute directory.
 */

namespace ClassLoader;

/**
 * @brief Responsible for including namespaced framework and application classes,
 * assuming they abide to the rules set forth by the framework.
 *
 * @details
 * Will load any class belonging to a top-level namespace that is registered.  You
 * can register a namespace directory by passing the top-level namespace and the
 * complete path to the directory holding that namespace to registerNamespaceDirectory().
 */
class Loader {

    /**
     * @brief An array holding a top-level namespace as the key and the complete
     * root path for that namespace as the value.
     *
     * @property $namespaceMap
     */
    protected $namespaceMap = array();

    /**
     * @brief Include the class based on the fully namespaced \a $className passed.
     *
     * @param $className The namespaced class to load
     */
    public function load($className) {
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
     * @brief Will return the top-level namespace for a class, given it has a namespace
     *
     * @param $className Fully namespaced name of the class
     * @return mixed
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
     * @brief Will check to see if the \a $namespace has a directory mapped to it,
     * if not we assume that it is in the app path.
     *
     * @param $namespace A top-level namespace that may exist in \a $namespaceMap
     * @return Directory for namespace if registered or null
     * @see SprayFire.Core.Directory
     */
    protected function getDirectoryForTopLevelNamespace($namespace) {
        if (isset($namespace) && \array_key_exists($namespace, $this->namespaceMap)) {
            return $this->namespaceMap[$namespace];
        }
        return null;
    }

    /**
     * @brief Converts the PHP namespace separator to the appropriate directory
     * separator.
     *
     * @param $className Namespaced name of the class to load
     * @return The complete path to the class
     */
    protected function convertNamespacedClassToFilePath($className) {
        return '/' . \str_replace('\\', '/', $className) . '.php';
    }

    /**
     * @brief Allows access to set a given namespace to a given directory.
     *
     * @details
     * The directory should be assigned in such a way that the following occurs:
     *
     * <pre>
     * The class:
     *
     * Top.Level.ClassName
     *
     * The directory for that class:
     *
     * /install_path/app/Top/Level/ClassName.php
     *
     * The proper key and value for this namspace and directory would look like:
     *
     * $namespaceMap['Top'] = '/install_path/app';
     *
     * Thus when you attempt to instantiate the class like so:
     *
     * $Class = new Top.Level.ClassName();
     *
     * The class autoloader will convert the namespace to a directory and then
     * append that directory to the value stored by the 'Top' key.
     * </pre>
     *
     * @param $topLevelNamespace A string representing a top level namespace
     * @param $dir The complete path to the directory holding the top level namespace
     */
    public function registerNamespaceDirectory($topLevelNamespace, $dir) {
        if (!empty($topLevelNamespace) && !empty($dir)) {
            $this->namespaceMap[$topLevelNamespace] = $dir;
        }
    }

    /**
     * @return An array of registered top level namespaces
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