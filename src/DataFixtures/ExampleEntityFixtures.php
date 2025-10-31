<?php

declare(strict_types=1);

namespace DDNSBundle\DataFixtures;

use DDNSBundle\Entity\ExampleEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * ExampleEntity测试数据
 *
 * 为DDNS功能演示创建示例服务器节点数据
 */
class ExampleEntityFixtures extends Fixture
{
    public const EXAMPLE_WEB_SERVER = 'ddns-example-web-server';
    public const EXAMPLE_API_SERVER = 'ddns-example-api-server';
    public const EXAMPLE_DB_SERVER = 'ddns-example-db-server';

    public function load(ObjectManager $manager): void
    {
        // Web服务器节点
        $webServer = new ExampleEntity();
        $webServer->setName('主Web服务器');
        $webServer->setDomain('web-server.test');
        $webServer->setIpAddress('192.168.1.10');

        $manager->persist($webServer);
        $this->addReference(self::EXAMPLE_WEB_SERVER, $webServer);

        // API服务器节点
        $apiServer = new ExampleEntity();
        $apiServer->setName('API网关服务器');
        $apiServer->setDomain('api-gateway.test');
        $apiServer->setIpAddress('192.168.1.20');

        $manager->persist($apiServer);
        $this->addReference(self::EXAMPLE_API_SERVER, $apiServer);

        // 数据库服务器节点
        $dbServer = new ExampleEntity();
        $dbServer->setName('主数据库服务器');
        $dbServer->setDomain('database.internal.test');
        $dbServer->setIpAddress('10.0.1.5');

        $manager->persist($dbServer);
        $this->addReference(self::EXAMPLE_DB_SERVER, $dbServer);

        $manager->flush();
    }
}
