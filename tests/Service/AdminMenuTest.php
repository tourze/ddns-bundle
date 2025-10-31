<?php

declare(strict_types=1);

namespace DDNSBundle\Tests\Service;

use DDNSBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testMenuCreation(): void
    {
        // 使用容器获取服务
        $menu = self::getService(AdminMenu::class);

        // 测试对象可以被调用
        self::assertIsCallable($menu);
    }

    public function testMenuInvoke(): void
    {
        $menu = self::getService(AdminMenu::class);

        $childMenu = $this->createMock(ItemInterface::class);

        $item = $this->createMock(ItemInterface::class);
        // 直接模拟 'DDNS管理' 子菜单已存在的情况
        $item->method('getChild')->with('DDNS管理')->willReturn($childMenu);
        $item->method('addChild')->with('DDNS管理')->willReturn($childMenu);

        $menu->__invoke($item);
    }

    public function testMenuInvokeWithoutExistingChild(): void
    {
        $menu = self::getService(AdminMenu::class);

        $childMenu = $this->createMock(ItemInterface::class);

        $item = $this->createMock(ItemInterface::class);
        // 模拟 'DDNS管理' 子菜单不存在的情况
        $item->method('getChild')->with('DDNS管理')->willReturn(null);
        $item->method('addChild')->with('DDNS管理')->willReturn($childMenu);

        $menu->__invoke($item);
    }
}
