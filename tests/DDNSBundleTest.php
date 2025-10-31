<?php

declare(strict_types=1);

namespace DDNSBundle\Tests;

use DDNSBundle\DDNSBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DDNSBundle::class)]
#[RunTestsInSeparateProcesses]
final class DDNSBundleTest extends AbstractBundleTestCase
{
}
