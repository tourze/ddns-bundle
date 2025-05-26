<?php

namespace DDNSBundle\Attribute;

use Attribute;

/**
 * DDNS域名属性
 * 用于标注实体中的域名字段，当实体变更时会自动更新DDNS解析
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class DDNSDomain
{
    public function __construct(
        private readonly ?string $provider = null,
    ) {
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }
}
