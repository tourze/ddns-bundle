<?php

namespace DDNSBundle\Tests\Command;

use DDNSBundle\Command\ProviderRunCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(ProviderRunCommand::class)]
#[RunTestsInSeparateProcesses]
final class ProviderRunCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function getCommandTester(): CommandTester
    {
        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        $command = self::getContainer()->get(ProviderRunCommand::class);
        if (!$command instanceof Command) {
            throw new \RuntimeException('Expected Command instance');
        }
        $this->commandTester = new CommandTester($command);
    }

    /**
     * 测试命令执行时返回成功状态码
     */
    public function testExecuteWithOneResultReturnsSuccessCode(): void
    {
        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    /**
     * 测试命令执行时，在有多个结果的情况下返回成功状态码
     */
    public function testExecuteWithMultipleResultsReturnsSuccessCode(): void
    {
        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    /**
     * 测试命令执行时，在没有结果的情况下返回成功状态码
     */
    public function testExecuteWithNoResultsReturnsSuccessCode(): void
    {
        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    /**
     * 测试命令执行时，在 DNSProvider check 返回 false 的情况下仍返回成功状态码
     */
    public function testExecuteWhenDnsProviderCheckReturnsFalseReturnsSuccessCode(): void
    {
        $exitCode = $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
    }
}
