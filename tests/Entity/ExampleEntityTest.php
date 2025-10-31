<?php

declare(strict_types=1);

namespace DDNSBundle\Tests\Entity;

use DDNSBundle\Entity\ExampleEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(ExampleEntity::class)]
final class ExampleEntityTest extends AbstractEntityTestCase
{
    protected function createEntity(): ExampleEntity
    {
        return new ExampleEntity();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'domain' => ['domain', 'example.com'],
            'ipAddress' => ['ipAddress', '192.168.1.1'],
            'name' => ['name', 'Test Server'],
        ];
    }

    public function testImplementsStringable(): void
    {
        $entity = $this->createEntity();
        self::assertIsString((string) $entity);
    }

    public function testPropertyAccessors(): void
    {
        $entity = $this->createEntity();

        // 测试name属性
        $entity->setName('Test Server');
        self::assertSame('Test Server', $entity->getName());

        // 测试domain属性
        $entity->setDomain('test.example.com');
        self::assertSame('test.example.com', $entity->getDomain());

        // 测试ipAddress属性
        $entity->setIpAddress('192.168.1.100');
        self::assertSame('192.168.1.100', $entity->getIpAddress());

        // ID应该初始为null
        self::assertNull($entity->getId());
    }

    public function testToString(): void
    {
        $entity = $this->createEntity();
        $entity->setName('Web Server');
        $entity->setDomain('web.example.com');

        $expectedString = 'Web Server (web.example.com)';
        self::assertSame($expectedString, $entity->__toString());
        self::assertSame($expectedString, (string) $entity);
    }

    public function testToStringWithSpecialCharacters(): void
    {
        $entity = $this->createEntity();
        $entity->setName('测试服务器');
        $entity->setDomain('test.中文域名.com');

        $expectedString = '测试服务器 (test.中文域名.com)';
        self::assertSame($expectedString, $entity->__toString());
    }

    public function testEntityCanBeCreatedWithAllProperties(): void
    {
        $entity = $this->createEntity();

        // 分别设置属性，避免方法链
        $entity->setName('Production Server');
        $entity->setDomain('prod.example.com');
        $entity->setIpAddress('10.0.1.100');

        self::assertSame('Production Server', $entity->getName());
        self::assertSame('prod.example.com', $entity->getDomain());
        self::assertSame('10.0.1.100', $entity->getIpAddress());
        self::assertNull($entity->getId()); // ID is set by database
    }

    public function testEntityStringRepresentation(): void
    {
        $entity = $this->createEntity();
        $entity->setName('API Server');
        $entity->setDomain('api.example.com');

        // Test that __toString method works correctly
        self::assertStringContainsString('API Server', (string) $entity);
        self::assertStringContainsString('api.example.com', (string) $entity);
        self::assertStringContainsString('(', (string) $entity);
        self::assertStringContainsString(')', (string) $entity);
    }

    #[DataProvider('validDomainProvider')]
    public function testValidDomains(string $domain): void
    {
        $entity = $this->createEntity();
        $entity->setDomain($domain);

        self::assertSame($domain, $entity->getDomain());
    }

    /**
     * @return iterable<array{string}>
     */
    public static function validDomainProvider(): iterable
    {
        return [
            'simple domain' => ['example.com'],
            'subdomain' => ['sub.example.com'],
            'deep subdomain' => ['api.v1.example.com'],
            'domain with numbers' => ['test123.example.com'],
            'domain with hyphens' => ['test-server.example.com'],
        ];
    }

    #[DataProvider('validIpAddressProvider')]
    public function testValidIpAddresses(string $ipAddress): void
    {
        $entity = $this->createEntity();
        $entity->setIpAddress($ipAddress);

        self::assertSame($ipAddress, $entity->getIpAddress());
    }

    /**
     * @return iterable<array{string}>
     */
    public static function validIpAddressProvider(): iterable
    {
        return [
            'localhost' => ['127.0.0.1'],
            'private network' => ['192.168.1.1'],
            'another private' => ['10.0.0.1'],
            'public ip' => ['8.8.8.8'],
            'ipv6' => ['2001:db8::1'],
        ];
    }

    public function testEntityPropertiesAreInitializedCorrectly(): void
    {
        $entity = $this->createEntity();

        // 只有ID应该为null，其他属性需要通过setter设置
        self::assertNull($entity->getId());
    }
}
