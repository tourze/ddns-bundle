<?php

namespace DDNSBundle\Tests\Attribute;

use DDNSBundle\Attribute\DDNSIP;
use PHPUnit\Framework\TestCase;

class DDNSIPTest extends TestCase
{
    /**
     * 测试DDNSIP注解的基本功能
     */
    public function testDDNSIPAttribute(): void
    {
        $attribute = new DDNSIP();
        $this->assertNull($attribute->getProvider());
    }

    /**
     * 测试DDNSIP注解带提供者参数
     */
    public function testDDNSIPAttributeWithProvider(): void
    {
        $provider = 'cloudflare';
        $attribute = new DDNSIP($provider);
        $this->assertEquals($provider, $attribute->getProvider());
    }
}
