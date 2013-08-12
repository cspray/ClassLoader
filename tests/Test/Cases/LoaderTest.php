<?php

/**
 * @file
 * @brief
 */

namespace ClassLoader\Test\Cases;

/**
 * @brief
 */
class LoaderTest extends \PHPUnit_Framework_TestCase {

    public function testNamespaceDirectoryLoad() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT . '/tests');
        $this->assertTrue($ClassLoader->load('\\TestApp\\Controller\\TestController'), 'We were unable to load TestApp.Controller.TestController');
        $Controller = new \TestApp\Controller\TestController();
        $this->assertTrue(\is_object($Controller));
    }

    public function testNoNamespaceLoad() {
        $ClassLoader = new \ClassLoader\Loader();
        $this->assertFalse($ClassLoader->load('NoNamespace'));
    }

    public function testNoClassLoad() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('SprayFire', \CLASSLOADER_ROOT . '/libs');
        $this->assertFalse($ClassLoader->load('\\SprayFire\\Core\\NoExist'));
    }

    public function testNoNamespaceClassLoad() {
        $ClassLoader = new \ClassLoader\Loader();
        $this->assertFalse($ClassLoader->load('NoNamespaceClass'));
    }

    public function testGettingRegisteredNamespaces() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('SprayFire', \CLASSLOADER_ROOT);
        $ClassLoader->registerNamespaceDirectory('SomethingElse', \CLASSLOADER_ROOT . '/something_else');
        $ClassLoader->registerNamespaceDirectory('Again', \CLASSLOADER_ROOT . '/again');

        $expected = array();
        $expected['SprayFire'] = \CLASSLOADER_ROOT;
        $expected['SomethingElse'] = \CLASSLOADER_ROOT . '/something_else';
        $expected['Again'] = \CLASSLOADER_ROOT . '/again';
        $actual = $ClassLoader->getRegisteredNamespaces();
        $this->assertSame($expected, $actual);
    }

    public function testSettingAutoloader() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT . '/tests');
        $this->assertTrue($ClassLoader->setAutoloader());
        $Controller = new \TestApp\Model\TestModel();
        $this->assertInstanceOf('\\TestApp\\Model\\TestModel', $Controller);
    }

    public function testLoadingOldStyleNamespacedClass() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT . '/tests');
        $this->assertTrue($ClassLoader->load('TestApp_Controller_Controller'), 'Did not load the old style "namespaced" class');
    }

    public function testLoadingOldStyleAndNewStyleIntermingled() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT . '/tests');
        $this->assertTrue($ClassLoader->load('\TestApp\With_Underscore\Subnamespace_Controller'), 'Could not load "fubar" class name.');
    }

    /**
     * @see https://github.com/cspray/ClassLoader/issues/4
     */
    public function testLoadingNamespacedClassWithOnlyOneSublevel() {
        $ClassLoader = new \ClassLoader\Loader();
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT . '/tests');
        $this->assertTrue($ClassLoader->load('\TestApp\Bootstrap'), 'Could not load the class one sublevel under top namespace');
    }

}
