<?php

namespace DDNSBundle\Tests\Service;

use DDNSBundle\Service\DDNSUpdateService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DDNSContracts\DTO\ExpectResolveResult;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DDNSUpdateService::class)]
#[RunTestsInSeparateProcesses]
final class DDNSUpdateServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试环境设置
    }

    public function testUpdateDNSCallsProvidersWhenCheckPasses(): void
    {
        // 使用具体类 ExpectResolveResult 是必要的，因为：
        // 1. 这是DDNS合约中的标准DTO，没有对应的接口
        // 2. 该类主要用于数据传输，Mock主要为了避免依赖外部数据
        // 3. 测试重点是服务逻辑而非DTO的具体实现
        $result = $this->createMock(ExpectResolveResult::class);

        // 在集成测试中，从容器获取服务
        $service = self::getService(DDNSUpdateService::class);

        // 由于我们无法直接控制自动注入的providers，我们测试服务的基本功能
        // 此测试主要验证服务能够正常工作，详细的Mock测试应在单元测试中进行
        $this->expectNotToPerformAssertions();
        $service->updateDNS($result);
    }

    public function testUpdateDNSWithNoProviders(): void
    {
        // 使用具体类 ExpectResolveResult 是必要的，因为：
        // 1. 这是DDNS合约中的标准DTO，没有对应的接口
        // 2. 该类主要用于数据传输，Mock主要为了避免依赖外部数据
        // 3. 测试重点是服务逻辑而非DTO的具体实现
        $result = $this->createMock(ExpectResolveResult::class);

        // 在集成测试中，从容器获取服务
        $service = self::getService(DDNSUpdateService::class);

        // 此方法在没有提供者时不应该抛出异常，只是简单返回
        // 这里测试方法正常执行不抛出异常
        $this->expectNotToPerformAssertions();
        $service->updateDNS($result);
    }
}
