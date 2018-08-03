<?php
namespace sorokinmedia\user\tests;

use yii\console\Application;

/**
 * Class TestCase
 * @package sorokinmedia\user\tests
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->destroyApplication();
        parent::tearDown();
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    protected function mockApplication()
    {
        new Application([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'runtimePath' => __DIR__ . '/runtime',
            'aliases' => [
                '@tests' => __DIR__,
            ],
        ]);
    }

    /**
     *
     */
    protected function destroyApplication()
    {
        \Yii::$app = null;
    }
}