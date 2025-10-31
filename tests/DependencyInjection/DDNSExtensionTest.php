<?php

namespace DDNSBundle\Tests\DependencyInjection;

use DDNSBundle\DependencyInjection\DDNSExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DDNSExtension::class)]
final class DDNSExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private DDNSExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new DDNSExtension();
        $this->container = new ContainerBuilder(new ParameterBag([
            'kernel.debug' => false,
            'kernel.bundles' => [],
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__ . '/../../',
        ]));
    }

    /**
     * 测试扩展加载资源和命令
     */
    public function testLoadRegistersCommandServices(): void
    {
        $this->extension->load([], $this->container);

        // 验证命令服务是否被加载
        $this->assertTrue(
            $this->container->has('DDNSBundle\Command\ProviderRunCommand')
            || $this->container->hasDefinition('DDNSBundle\Command\ProviderRunCommand')
        );
    }

    /**
     * 测试扩展正确加载了服务配置文件
     */
    public function testLoadLoadsServiceConfiguration(): void
    {
        $this->extension->load([], $this->container);

        // 检查是否有任何服务定义被注册
        $this->assertGreaterThan(0, count($this->container->getDefinitions()));

        // 验证命令服务是否正确配置
        if ($this->container->hasDefinition('DDNSBundle\Command\ProviderRunCommand')) {
            $commandDefinition = $this->container->getDefinition('DDNSBundle\Command\ProviderRunCommand');
            $this->assertTrue($commandDefinition->isAutowired());
            $this->assertTrue($commandDefinition->isAutoconfigured());
        }
    }
}
