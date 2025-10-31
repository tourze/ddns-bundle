<?php

declare(strict_types=1);

namespace DDNSBundle\Tests\Service;

use DDNSBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    public function testControllerLoaderCreation(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        self::assertInstanceOf(AttributeControllerLoader::class, $loader);
    }

    public function testLoad(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $collection = $loader->load('test');

        self::assertInstanceOf(RouteCollection::class, $collection);
    }

    public function testSupports(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        self::assertFalse($loader->supports('test'));
    }

    public function testAutoload(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $collection = $loader->autoload();

        self::assertInstanceOf(RouteCollection::class, $collection);
    }

    protected function onSetUp(): void
    {
        // 初始化集成测试环境
        // 这个测试类不需要特殊的setUp逻辑，因为我们从容器获取服务
    }
}
