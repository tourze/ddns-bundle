<?php

namespace DDNSBundle\Tests\EventListener;

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use DDNSBundle\EventListener\DDNSEntityListener;
use DDNSBundle\Service\DDNSAttributeProcessor;
use DDNSBundle\Service\DDNSUpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\DDNSContracts\DTO\ExpectResolveResult;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DDNSEntityListener::class)]
#[RunTestsInSeparateProcesses]
final class DDNSEntityListenerTest extends AbstractIntegrationTestCase
{
    private DDNSAttributeProcessor&MockObject $attributeProcessor;

    private DDNSUpdateService&MockObject $ddnsUpdateService;

    private DDNSEntityListener $listener;

    protected function onSetUp(): void
    {
        $this->setUpMocks();
    }

    private function setUpMocks(): void
    {
        // 使用具体类 DDNSAttributeProcessor 是必要的，因为：
        // 1. 该类是核心业务逻辑处理器，没有对应的接口定义
        // 2. Mock主要为了隔离测试，专注于事件监听器的逻辑验证
        // 3. 实际项目中该服务作为具体实现被直接注入使用
        $this->attributeProcessor = $this->createMock(DDNSAttributeProcessor::class);

        // 使用具体类 DDNSUpdateService 是必要的，因为：
        // 1. 该类是DDNS更新的核心服务，没有对应的接口抽象
        // 2. Mock目的是验证事件监听器调用服务的行为逻辑
        // 3. 测试重点是事件处理流程而非服务的具体实现细节
        $this->ddnsUpdateService = $this->createMock(DDNSUpdateService::class);

        // 将 Mock 服务注册到容器中
        $container = self::getContainer();
        $container->set(DDNSAttributeProcessor::class, $this->attributeProcessor);
        $container->set(DDNSUpdateService::class, $this->ddnsUpdateService);

        // 从容器中获取监听器实例
        $listener = $container->get(DDNSEntityListener::class);
        if (!$listener instanceof DDNSEntityListener) {
            throw new \RuntimeException('Expected DDNSEntityListener instance');
        }
        $this->listener = $listener;
    }

    /**
     * 测试处理带有DDNS属性的实体的创建事件
     */
    public function testPostPersistWithDDNSAttributes(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain = 'example.com';

            #[DdnsIp]
            public string $ip = '192.168.1.1';
        };

        // 使用具体类 ExpectResolveResult 是必要的，因为：
        // 1. 这是DDNS合约中的标准DTO，没有对应的接口
        // 2. 该类主要用于数据传输，Mock主要为了避免依赖外部数据
        // 3. 测试重点是事件监听器逻辑而非DTO的具体实现
        $resolveResult = $this->createMock(ExpectResolveResult::class);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(true)
        ;

        $this->attributeProcessor
            ->expects($this->once())
            ->method('extractResolveResults')
            ->with($entity)
            ->willReturn([$resolveResult])
        ;

        $this->ddnsUpdateService
            ->expects($this->once())
            ->method('updateDNS')
            ->with($resolveResult)
        ;

        $event = new PostPersistEventArgs($entity, $this->createMock(EntityManagerInterface::class));

        $this->listener->postPersist($event);
    }

    /**
     * 测试处理没有DDNS属性的实体的创建事件
     */
    public function testPostPersistWithoutDDNSAttributes(): void
    {
        $entity = new class {
            public string $domain = 'example.com';

            public string $ip = '192.168.1.1';
        };

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(false)
        ;

        $this->attributeProcessor
            ->expects($this->never())
            ->method('extractResolveResults')
        ;

        $this->ddnsUpdateService
            ->expects($this->never())
            ->method('updateDNS')
        ;

        $event = new PostPersistEventArgs($entity, $this->createMock(EntityManagerInterface::class));

        $this->listener->postPersist($event);
    }

    /**
     * 测试处理带有DDNS属性的实体的更新事件
     */
    public function testPostUpdateWithDDNSAttributes(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain = 'example.com';

            #[DdnsIp]
            public string $ip = '192.168.1.1';
        };

        // 使用具体类 ExpectResolveResult 是必要的，因为：
        // 1. 这是DDNS合约中的标准DTO，没有对应的接口
        // 2. 该类主要用于数据传输，Mock主要为了避免依赖外部数据
        // 3. 测试重点是事件监听器逻辑而非DTO的具体实现
        $resolveResult = $this->createMock(ExpectResolveResult::class);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(true)
        ;

        $this->attributeProcessor
            ->expects($this->once())
            ->method('extractResolveResults')
            ->with($entity)
            ->willReturn([$resolveResult])
        ;

        $this->ddnsUpdateService
            ->expects($this->once())
            ->method('updateDNS')
            ->with($resolveResult)
        ;

        $event = new PostUpdateEventArgs($entity, $this->createMock(EntityManagerInterface::class));

        $this->listener->postUpdate($event);
    }

    /**
     * 测试处理多个解析结果
     */
    public function testHandleMultipleResolveResults(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain1 = 'example1.com';

            #[DDNSDomain]
            public string $domain2 = 'example2.com';

            #[DdnsIp]
            public string $ip = '192.168.1.1';
        };

        // 使用具体类 ExpectResolveResult 是必要的，因为：
        // 1. 这是DDNS合约中的标准DTO，没有对应的接口
        // 2. 该类主要用于数据传输，Mock主要为了避免依赖外部数据
        // 3. 测试重点是事件监听器逻辑而非DTO的具体实现
        $resolveResult1 = $this->createMock(ExpectResolveResult::class);

        // 使用具体类 ExpectResolveResult 是必要的，因为：
        // 1. 这是DDNS合约中的标准DTO，没有对应的接口
        // 2. 该类主要用于数据传输，Mock主要为了避免依赖外部数据
        // 3. 测试重点是事件监听器逻辑而非DTO的具体实现
        $resolveResult2 = $this->createMock(ExpectResolveResult::class);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(true)
        ;

        $this->attributeProcessor
            ->expects($this->once())
            ->method('extractResolveResults')
            ->with($entity)
            ->willReturn([$resolveResult1, $resolveResult2])
        ;

        $this->ddnsUpdateService
            ->expects($this->exactly(2))
            ->method('updateDNS')
            ->willReturnCallback(function ($result) use ($resolveResult1, $resolveResult2): void {
                $this->assertContains($result, [$resolveResult1, $resolveResult2]);
            })
        ;

        $event = new PostPersistEventArgs($entity, $this->createMock(EntityManagerInterface::class));

        $this->listener->postPersist($event);
    }
}
