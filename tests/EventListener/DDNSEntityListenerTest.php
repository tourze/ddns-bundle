<?php

namespace DDNSBundle\Tests\EventListener;

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DDNSIP;
use DDNSBundle\EventListener\DDNSEntityListener;
use DDNSBundle\Service\DDNSAttributeProcessor;
use DDNSBundle\Service\DDNSUpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\DDNSContracts\ExpectResolveResult;

class DDNSEntityListenerTest extends TestCase
{
    private DDNSAttributeProcessor&MockObject $attributeProcessor;
    private DDNSUpdateService&MockObject $ddnsUpdateService;
    private EntityManagerInterface&MockObject $entityManager;
    private DDNSEntityListener $listener;

    protected function setUp(): void
    {
        $this->attributeProcessor = $this->createMock(DDNSAttributeProcessor::class);
        $this->ddnsUpdateService = $this->createMock(DDNSUpdateService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new DDNSEntityListener($this->attributeProcessor, $this->ddnsUpdateService);
    }

    /**
     * 测试处理带有DDNS属性的实体的创建事件
     */
    public function testPostPersistWithDDNSAttributes(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain = 'example.com';

            #[DDNSIP]
            public string $ip = '192.168.1.1';
        };

        $resolveResult = $this->createMock(ExpectResolveResult::class);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(true);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('extractResolveResults')
            ->with($entity)
            ->willReturn([$resolveResult]);

        $this->ddnsUpdateService
            ->expects($this->once())
            ->method('updateDNS')
            ->with($resolveResult);

        $event = new PostPersistEventArgs($entity, $this->entityManager);

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
            ->willReturn(false);

        $this->attributeProcessor
            ->expects($this->never())
            ->method('extractResolveResults');

        $this->ddnsUpdateService
            ->expects($this->never())
            ->method('updateDNS');

        $event = new PostPersistEventArgs($entity, $this->entityManager);

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

            #[DDNSIP]
            public string $ip = '192.168.1.1';
        };

        $resolveResult = $this->createMock(ExpectResolveResult::class);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(true);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('extractResolveResults')
            ->with($entity)
            ->willReturn([$resolveResult]);

        $this->ddnsUpdateService
            ->expects($this->once())
            ->method('updateDNS')
            ->with($resolveResult);

        $event = new PostUpdateEventArgs($entity, $this->entityManager);

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

            #[DDNSIP]
            public string $ip = '192.168.1.1';
        };

        $resolveResult1 = $this->createMock(ExpectResolveResult::class);
        $resolveResult2 = $this->createMock(ExpectResolveResult::class);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('hasAnyDDNSAttribute')
            ->with($entity)
            ->willReturn(true);

        $this->attributeProcessor
            ->expects($this->once())
            ->method('extractResolveResults')
            ->with($entity)
            ->willReturn([$resolveResult1, $resolveResult2]);

        $this->ddnsUpdateService
            ->expects($this->exactly(2))
            ->method('updateDNS')
            ->willReturnCallback(function (ExpectResolveResult $result) use ($resolveResult1, $resolveResult2) {
                $this->assertContains($result, [$resolveResult1, $resolveResult2]);
            });

        $event = new PostPersistEventArgs($entity, $this->entityManager);

        $this->listener->postPersist($event);
    }
}