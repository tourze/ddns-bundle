<?php

namespace DDNSBundle\Tests\Service;

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DDNSIP;
use DDNSBundle\Service\DDNSAttributeProcessor;
use PHPUnit\Framework\TestCase;
use Tourze\DDNSContracts\ExpectResolveResult;

class DDNSAttributeProcessorTest extends TestCase
{
    private DDNSAttributeProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new DDNSAttributeProcessor();
    }

    /**
     * 测试从带有DDNS属性的实体中提取解析结果
     */
    public function testExtractResolveResults(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain = 'example.com';

            #[DDNSIP]
            public string $ip = '192.168.1.1';
        };

        $results = $this->processor->extractResolveResults($entity);

        $this->assertCount(1, $results);
        $this->assertInstanceOf(ExpectResolveResult::class, $results[0]);
        $this->assertEquals('example.com', $results[0]->getDomainName());
        $this->assertEquals('192.168.1.1', $results[0]->getIpAddress());
    }

    /**
     * 测试从没有DDNS属性的实体中提取解析结果
     */
    public function testExtractResolveResultsFromEntityWithoutAttributes(): void
    {
        $entity = new class {
            public string $domain = 'example.com';
            public string $ip = '192.168.1.1';
        };

        $results = $this->processor->extractResolveResults($entity);

        $this->assertEmpty($results);
    }

    /**
     * 测试从有空值的实体中提取解析结果
     */
    public function testExtractResolveResultsWithEmptyValues(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain = '';

            #[DDNSIP]
            public string $ip = '192.168.1.1';
        };

        $results = $this->processor->extractResolveResults($entity);

        $this->assertEmpty($results);
    }

    /**
     * 测试检查实体是否包含DDNS属性
     */
    public function testHasAnyDDNSAttribute(): void
    {
        $entityWithAttributes = new class {
            #[DDNSDomain]
            public string $domain = 'example.com';
        };

        $entityWithoutAttributes = new class {
            public string $domain = 'example.com';
        };

        $this->assertTrue($this->processor->hasAnyDDNSAttribute($entityWithAttributes));
        $this->assertFalse($this->processor->hasAnyDDNSAttribute($entityWithoutAttributes));
    }

    /**
     * 测试多个域名和IP字段的组合
     */
    public function testMultipleDomainAndIPFields(): void
    {
        $entity = new class {
            #[DDNSDomain]
            public string $domain1 = 'example1.com';

            #[DDNSDomain]
            public string $domain2 = 'example2.com';

            #[DDNSIP]
            public string $ip1 = '192.168.1.1';

            #[DDNSIP]
            public string $ip2 = '192.168.1.2';
        };

        $results = $this->processor->extractResolveResults($entity);

        $this->assertCount(4, $results); // 2 domains × 2 IPs = 4 combinations
    }
}
