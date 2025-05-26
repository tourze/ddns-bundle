# ddns-bundle

DDNS服务模块

## 安装

```bash
composer require tourze/ddns-bundle
```

## 使用方法

### 基本用法

在你的Symfony应用中注册Bundle：

```php
// config/bundles.php
return [
    // ...
    DDNSBundle\DDNSBundle::class => ['all' => true],
];
```

### 使用DDNS属性

在实体中使用`#[DDNSDomain]`和`#[DDNSIP]`属性来标注域名和IP字段：

```php
<?php

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DDNSIP;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServerNode
{
    #[ORM\Column(type: 'string')]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $domain;

    #[ORM\Column(type: 'string')]
    #[DDNSIP(provider: 'cloudflare')]
    private string $ipAddress;

    // ... 其他字段和方法
}
```

### 自动DDNS更新

当实体被创建或更新时，系统会自动：

1. 检查实体是否包含DDNS属性
2. 提取域名和IP地址信息
3. 调用相应的DNS提供者更新解析记录

### 属性参数

- `provider`: 可选参数，指定DNS提供者（如'cloudflare'、'aliyun'等）

## 配置

Bundle会自动注册以下服务：

- `DDNSAttributeProcessor`: 属性处理器
- `DDNSUpdateService`: DDNS更新服务
- `DDNSEntityListener`: 实体变更监听器

## 示例

参考`examples/ExampleEntity.php`文件查看完整的使用示例。

## 工作原理

1. **属性标注**: 在实体字段上使用`#[DDNSDomain]`和`#[DDNSIP]`属性
2. **事件监听**: Doctrine事件监听器监听实体的创建和更新事件
3. **属性解析**: 属性处理器提取带有DDNS属性的字段值
4. **DNS更新**: 调用DNS提供者接口更新解析记录

## 参考文档

- [Symfony Attributes](https://symfony.com/doc/current/components/dependency_injection/attributes.html)
- [Doctrine Event Listeners](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html)
