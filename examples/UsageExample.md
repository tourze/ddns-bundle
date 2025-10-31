# DDNS Bundle 使用示例

## 基本使用场景

### 1. 服务器节点管理

当你有一个服务器节点管理系统，需要在服务器IP变更时自动更新DNS解析：

```php
<?php

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'server_nodes')]
class ServerNode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $hostname;

    #[ORM\Column(type: 'string', length: 45)]
    #[DdnsIp(provider: 'cloudflare')]
    private string $publicIp;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    #[DdnsIp(provider: 'cloudflare')]
    private ?string $backupIp = null;

    // 当更新服务器IP时，会自动触发DDNS更新
    public function updateIp(string $newIp): void
    {
        $this->publicIp = $newIp;
        // Doctrine flush时会自动触发DDNS更新
    }
}
```

### 2. CDN节点管理

管理CDN节点的域名解析：

```php
<?php

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CDNNode
{
    #[ORM\Column(type: 'string')]
    #[DDNSDomain]
    private string $subdomain; // 如: cdn1.example.com

    #[ORM\Column(type: 'string')]
    #[DdnsIp]
    private string $nodeIp;

    #[ORM\Column(type: 'string')]
    private string $region; // 普通字段，不会触发DDNS

    // 当CDN节点IP变更时自动更新DNS
    public function migrateToNewIp(string $newIp): void
    {
        $this->nodeIp = $newIp;
    }
}
```

### 3. 多域名多IP场景

一个实体可以有多个域名和IP字段：

```php
<?php

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class LoadBalancer
{
    // 主域名
    #[ORM\Column(type: 'string')]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $primaryDomain;

    // 备用域名
    #[ORM\Column(type: 'string')]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $backupDomain;

    // 主IP
    #[ORM\Column(type: 'string')]
    #[DdnsIp(provider: 'cloudflare')]
    private string $primaryIp;

    // 备用IP
    #[ORM\Column(type: 'string')]
    #[DdnsIp(provider: 'cloudflare')]
    private string $backupIp;

    // 这会创建4个DNS解析记录：
    // primaryDomain -> primaryIp
    // primaryDomain -> backupIp
    // backupDomain -> primaryIp
    // backupDomain -> backupIp
}
```

## 控制器中的使用

```php
<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class ServerController extends AbstractController
{
    #[Route('/api/server/{id}/update-ip', methods: ['POST'])]
    public function updateServerIp(
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $server = $em->getRepository(ServerNode::class)->find($id);
        $newIp = $request->get('ip');

        // 更新IP - 这会自动触发DDNS更新
        $server->updateIp($newIp);

        $em->flush(); // 在这里触发DDNS更新

        return new JsonResponse(['status' => 'success']);
    }
}
```

## 事件流程

1. **实体更新**: 调用`$em->flush()`时
2. **事件触发**: Doctrine触发`postUpdate`事件
3. **属性检查**: `DDNSEntityListener`检查实体是否有DDNS属性
4. **数据提取**: `DDNSAttributeProcessor`提取域名和IP信息
5. **DNS更新**: `DDNSUpdateService`调用DNS提供者更新解析

## 注意事项

1. **性能考虑**: DNS更新是同步操作，可能影响响应时间
2. **错误处理**: DNS更新失败不会回滚数据库事务
3. **批量操作**: 大量实体更新时会产生大量DNS请求
4. **提供者配置**: 确保DNS提供者服务已正确配置

## 扩展功能

如果需要异步处理或更复杂的逻辑，可以：

1. 使用消息队列异步处理DNS更新
2. 添加重试机制
3. 实现DNS更新日志记录
4. 支持条件性更新（只在特定字段变更时更新）
