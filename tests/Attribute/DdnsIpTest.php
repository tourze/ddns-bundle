<?php

namespace DDNSBundle\Tests\Attribute;

use DDNSBundle\Attribute\DdnsIp;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DdnsIp::class)]
final class DdnsIpTest extends TestCase
{
    /**
     * 测试DdnsIp注解的基本功能
     */
    public function testDdnsIpAttribute(): void
    {
        $attribute = new DdnsIp();
        $this->assertNull($attribute->getProvider());
    }

    /**
     * 测试DdnsIp注解带提供者参数
     */
    public function testDdnsIpAttributeWithProvider(): void
    {
        $provider = 'cloudflare';
        $attribute = new DdnsIp($provider);
        $this->assertEquals($provider, $attribute->getProvider());
    }
}
