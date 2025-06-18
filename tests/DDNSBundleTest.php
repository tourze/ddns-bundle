<?php

namespace DDNSBundle\Tests;

use AccessTokenBundle\AccessTokenBundle;
use BizUserBundle\BizUserBundle;
use DDNSBundle\DDNSBundle;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;

class DDNSBundleTest extends TestCase
{
    /**
     * 测试 getBundleDependencies 方法返回正确的依赖数组
     */
    public function testGetBundleDependencies(): void
    {
        $dependencies = DDNSBundle::getBundleDependencies();
        $this->assertArrayHasKey(JsonRPCLockBundle::class, $dependencies);
        $this->assertArrayHasKey(AccessTokenBundle::class, $dependencies);
        $this->assertArrayHasKey(BizUserBundle::class, $dependencies);
        
        $this->assertEquals(['all' => true], $dependencies[JsonRPCLockBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[AccessTokenBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[BizUserBundle::class]);
    }
} 