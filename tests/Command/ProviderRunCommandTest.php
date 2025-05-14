<?php

namespace DDNSBundle\Tests\Command;

use DDNSBundle\Command\ProviderRunCommand;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\DDNSContracts\DNSProviderInterface;
use Tourze\DDNSContracts\ExpectResolveResult;
use Tourze\DDNSContracts\IPResolverInterface;

class ProviderRunCommandTest extends TestCase
{
    private MockObject|IPResolverInterface $ipResolver;
    private MockObject|DNSProviderInterface $dnsProvider;
    private InputInterface|MockObject $input;
    private OutputInterface|MockObject $output;
    private ProviderRunCommand $command;
    private ReflectionMethod $executeMethod;
    
    protected function setUp(): void
    {
        $this->ipResolver = $this->createMock(IPResolverInterface::class);
        $this->dnsProvider = $this->createMock(DNSProviderInterface::class);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        
        // 创建命令并注入依赖
        $this->command = new ProviderRunCommand(
            [$this->ipResolver],
            [$this->dnsProvider]
        );
        
        // 使用反射获取protected方法
        $reflectionClass = new ReflectionClass(ProviderRunCommand::class);
        $this->executeMethod = $reflectionClass->getMethod('execute');
        $this->executeMethod->setAccessible(true);
    }
    
    /**
     * 测试命令执行时，在有一个成功结果的情况下返回成功状态码
     */
    public function testExecute_withOneResult_returnsSuccessCode(): void
    {
        // 创建模拟的 ExpectResolveResult
        $result = $this->createMock(ExpectResolveResult::class);
        
        // 配置 ipResolver 和 dnsProvider 的行为
        $this->setupIpResolverWithResults([$result]);
        $this->setupDnsProviderWithCheckResult(true);
        
        // 使用反射执行protected方法
        $returnCode = $this->executeMethod->invoke($this->command, $this->input, $this->output);
        
        // 验证返回值
        $this->assertEquals(Command::SUCCESS, $returnCode);
    }

    /**
     * 测试命令执行时，在有多个结果的情况下返回成功状态码
     */
    public function testExecute_withMultipleResults_returnsSuccessCode(): void
    {
        // 创建多个模拟的 ExpectResolveResult
        $result1 = $this->createMock(ExpectResolveResult::class);
        $result2 = $this->createMock(ExpectResolveResult::class);
        
        // 配置 ipResolver 和 dnsProvider 的行为
        $this->setupIpResolverWithResults([$result1, $result2]);
        $this->setupDnsProviderWithCheckResult(true);
        
        // 使用反射执行protected方法
        $returnCode = $this->executeMethod->invoke($this->command, $this->input, $this->output);
        
        // 验证返回值
        $this->assertEquals(Command::SUCCESS, $returnCode);
    }

    /**
     * 测试命令执行时，在没有结果的情况下返回成功状态码
     */
    public function testExecute_withNoResults_returnsSuccessCode(): void
    {
        // 配置 ipResolver 返回空结果集
        $this->setupIpResolverWithResults([]);
        
        // 确保不会调用 dnsProvider 的任何方法
        $this->dnsProvider->expects($this->never())
            ->method($this->anything());
        
        // 使用反射执行protected方法
        $returnCode = $this->executeMethod->invoke($this->command, $this->input, $this->output);
        
        // 验证返回值
        $this->assertEquals(Command::SUCCESS, $returnCode);
    }

    /**
     * 测试命令执行时，在 DNSProvider check 返回 false 的情况下仍返回成功状态码
     */
    public function testExecute_whenDnsProviderCheckReturnsFalse_returnsSuccessCode(): void
    {
        // 创建模拟的 ExpectResolveResult
        $result = $this->createMock(ExpectResolveResult::class);
        
        // 配置 ipResolver 和 dnsProvider 的行为
        $this->setupIpResolverWithResults([$result]);
        $this->setupDnsProviderWithCheckResult(false);
        
        // 确保不会调用 dnsProvider 的 resolve 方法
        $this->dnsProvider->expects($this->never())
            ->method('resolve');
        
        // 使用反射执行protected方法
        $returnCode = $this->executeMethod->invoke($this->command, $this->input, $this->output);
        
        // 验证返回值
        $this->assertEquals(Command::SUCCESS, $returnCode);
    }
    
    /**
     * 配置 IPResolver 的行为
     */
    private function setupIpResolverWithResults(array $results): void
    {
        $this->ipResolver->expects($this->once())
            ->method('resolveAll')
            ->willReturn($results);
    }
    
    /**
     * 配置 DNSProvider 的 check 方法行为
     */
    private function setupDnsProviderWithCheckResult(bool $result): void
    {
        $this->dnsProvider->expects($this->any())
            ->method('check')
            ->willReturn($result);
    }
} 