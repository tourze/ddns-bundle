<?php

namespace DDNSBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Tourze\DDNSContracts\DNSProviderInterface;
use Tourze\DDNSContracts\DTO\ExpectResolveResult;

/**
 * DDNS更新服务
 * 负责调用DNS提供者更新解析
 */
#[Autoconfigure(public: true)]
readonly class DDNSUpdateService
{
    /**
     * @param iterable<DNSProviderInterface> $dnsProviders
     */
    public function __construct(
        #[AutowireIterator(tag: DNSProviderInterface::TAG_NAME)] private iterable $dnsProviders,
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
