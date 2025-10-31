<?php

declare(strict_types=1);

namespace DDNSBundle\Entity;

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DdnsIp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 示例实体，展示如何使用DDNS属性
 */
#[ORM\Entity]
#[ORM\Table(name: 'server_nodes', options: ['comment' => '服务器节点表'])]
class ExampleEntity implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '服务器域名 - 使用DDNSDomain属性标注'])]
    #[DDNSDomain(provider: 'cloudflare')]
    #[Assert\NotBlank(message: '域名不能为空')]
    #[Assert\Length(max: 255, maxMessage: '域名长度不能超过255个字符')]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9.-]+$/', message: '域名格式不正确')]
    private string $domain = '';

    #[ORM\Column(type: Types::STRING, length: 45, options: ['comment' => '服务器IP地址 - 使用DdnsIp属性标注'])]
    #[DdnsIp(provider: 'cloudflare')]
    #[Assert\NotBlank(message: 'IP地址不能为空')]
    #[Assert\Ip(message: 'IP地址格式不正确')]
    private string $ipAddress = '';

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '服务器名称 - 普通字段，不会触发DDNS更新'])]
    #[Assert\NotBlank(message: '服务器名称不能为空')]
    #[Assert\Length(max: 100, maxMessage: '服务器名称长度不能超过100个字符')]
    private string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, $this->domain);
    }
}
