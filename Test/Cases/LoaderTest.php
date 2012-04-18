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
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT);
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
        $ClassLoader->registerNamespaceDirectory('TestApp', \CLASSLOADER_ROOT);
        $ClassLoader->setAutoloader();
        $Controller = new \TestApp\Model\TestModel();
        $this->assertInstanceOf('\\TestApp\\Model\\TestModel', $Controller);
    }

}