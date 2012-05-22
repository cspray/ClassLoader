<?php

/**
 * @file
 * @brief An autoloading class that allows the user to register top level namespaces
 * to a specific directory and autoloads classes belonging to that top level namespace.
 *
 * Copyright (c) 2012 Charles Sprayberry
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author Charles Sprayberry
 * @license http://www.opensource.org/licenses/mit-license.php
 */

namespace ClassLoader;

/**
 * @brief Allows for the registering and retrieval of top level namespaces, loading
 * classes based on the directory registered for a top level namespace using a
 * generated absolute path and intends to be compatible with PSR-0 autoloading
 * conventions.
 */
class Loader {

    /**
     * @brief Array holding a top-level namespace as the key and the complete
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
     * @brief Will check to see if the \a $namespace has a directory mapped to it.
     *
     * @param $namespace A top-level namespace that may exist in \a $namespaceMap
     * @return Directory for namespace if registered or null
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
     * Top\Level\ClassName
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
     * $Class = new \Top\Level\ClassName();
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