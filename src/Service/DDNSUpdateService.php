<?php

namespace DDNSBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Tourze\DDNSContracts\DNSProviderInterface;
use Tourze\DDNSContracts\ExpectResolveResult;

/**
 * DDNS更新服务
 * 负责调用DNS提供者更新解析
 */
class DDNSUpdateService
{
    public function __construct(
        #[TaggedIterator(tag: DNSProviderInterface::TAG_NAME)] private readonly iterable $dnsProviders,
    ) {
    }

    /**
     * 更新DNS解析
     */
    public function updateDNS(ExpectResolveResult $result): void
    {
        foreach ($this->dnsProviders as $provider) {
            assert($provider instanceof DNSProviderInterface);

            if ($provider->check($result)) {
                $provider->resolve($result);
            }
        }
    }
}
