<?php

namespace DDNSBundle\Examples;

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DDNSIP;
use Doctrine\ORM\Mapping as ORM;

/**
 * 示例实体，展示如何使用DDNS属性
 */
#[ORM\Entity]
#[ORM\Table(name: 'server_nodes')]
class ExampleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * 服务器域名 - 使用DDNSDomain属性标注
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[DDNSDomain(provider: 'cloudflare')]
    private string $domain;

    /**
     * 服务器IP地址 - 使用DDNSIP属性标注
     */
    #[ORM\Column(type: 'string', length: 45)]
    #[DDNSIP(provider: 'cloudflare')]
    private string $ipAddress;

    /**
     * 服务器名称 - 普通字段，不会触发DDNS更新
     */
    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
