<?php

namespace DDNSBundle\Attribute;

use Attribute;

/**
 * DDNS IP地址属性
 * 用于标注实体中的IP地址字段，当实体变更时会自动更新DDNS解析
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class DDNSIP
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
