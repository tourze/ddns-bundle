<?php

namespace DDNSBundle\Tests\Attribute;

use DDNSBundle\Attribute\DDNSDomain;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DDNSDomain::class)]
final class DDNSDomainTest extends TestCase
{
    /**
     * 测试DDNSDomain注解的基本功能
     */
    public function testDDNSDomainAttribute(): void
    {
        $attribute = new DDNSDomain();
        $this->assertNull($attribute->getProvider());
    }

    /**
     * 测试DDNSDomain注解带提供者参数
     */
    public function testDDNSDomainAttributeWithProvider(): void
    {
        $provider = 'cloudflare';
        $attribute = new DDNSDomain($provider);
        $this->assertEquals($provider, $attribute->getProvider());
    }
}
