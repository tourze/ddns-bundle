# ddns-bundle

[![Latest Version](https://img.shields.io/packagist/v/tourze/ddns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/ddns-bundle)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/ddns-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/ddns-bundle)

[English](README.md) | [中文](README.zh-CN.md)

DDNS（动态DNS）服务模块，适用于 Symfony 应用。

## 功能特性

- **自动DNS更新**: 当实体被修改时自动更新DNS记录
- **基于属性的配置**: 使用 PHP 8 属性标记域名和 IP 字段
- **提供商无关**: 通过接口支持多个 DNS 提供商
- **控制台命令**: 手动和定时的 DNS 更新
- **Doctrine集成**: 与 Doctrine ORM 事件无缝集成

## 安装

```bash
composer require tourze/ddns-bundle
```

## 依赖要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM

## 快速开始

### 1. 注册 Bundle

```php
// config/bundles.php
return [
    // ...
    DDNSBundle\DDNSBundle::class => ['all' => true],
];
```

## 使用方法

### 使用DDNS属性

在实体中使用`#[DDNSDomain]`和`#[DdnsIp]`属性来标注域名和IP字段：

```php
<?php

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServerNode
{
    #[ORM\Column(type: 'string')]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $domain;

    #[ORM\Column(type: 'string')]
    #[DdnsIp(provider: 'cloudflare')]
    private string $ipAddress;

    // ... 其他字段和方法
}
```

## 高级用法

### 自动DDNS更新

当实体被创建或更新时，系统会自动：

1. 检查实体是否包含DDNS属性
2. 提取域名和IP地址信息
3. 调用相应的DNS提供者更新解析记录

### 属性参数

- `provider`: 可选参数，指定DNS提供者（如'cloudflare'、'aliyun'等）

### 控制台命令

#### ddns:provider:run

运行DDNS提供商更新任务。该命令会：

1. 遍历所有IP解析器获取需要更新的域名和IP映射
2. 检查每个DNS提供商是否支持该域名
3. 更新相应的DNS记录

```bash
# 手动运行DDNS更新
php bin/console ddns:provider:run
```

该命令已配置为定时任务，每分钟自动执行一次。

## 配置

Bundle会自动注册以下服务：

- `DDNSAttributeProcessor`: 属性处理器
- `DDNSUpdateService`: DDNS更新服务
- `DDNSEntityListener`: 实体变更监听器

## 示例

参考`examples/ExampleEntity.php`文件查看完整的使用示例。

## 工作原理

1. **属性标注**: 在实体字段上使用`#[DDNSDomain]`和`#[DdnsIp]`属性
2. **事件监听**: Doctrine事件监听器监听实体的创建和更新事件
3. **属性解析**: 属性处理器提取带有DDNS属性的字段值
4. **DNS更新**: 调用DNS提供者接口更新解析记录

## 贡献指南

欢迎贡献！请遵循以下准则：

1. **问题反馈**: 通过 [GitHub Issues](https://github.com/tourze/php-monorepo/issues) 报告错误或建议功能
2. **拉取请求**: Fork 仓库并提交带有清晰描述的 PR
3. **代码风格**: 遵循 PSR-12 编码标准
4. **测试**: 确保所有测试通过，并为新功能添加测试
5. **文档**: 为任何更改更新相应文档

## 参考文档

- [Symfony Attributes](https://symfony.com/doc/current/components/dependency_injection/attributes.html)
- [Doctrine Event Listeners](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html)

## 更新日志

查看 [CHANGELOG.md](CHANGELOG.md) 了解版本历史和更新详情。

## 许可证

此Bundle采用MIT许可证。更多信息请参阅 [LICENSE](LICENSE) 文件。
