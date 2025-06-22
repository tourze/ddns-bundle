<?php

namespace DDNSBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Tourze\DDNSContracts\DNSProviderInterface;
use Tourze\DDNSContracts\ExpectResolveResult;
use Tourze\DDNSContracts\IPResolverInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
#[AsCommand(name: self::NAME, description: '运行DDNS提供商更新任务')]
class ProviderRunCommand extends Command
{
    public const NAME = 'ddns:provider:run';

    public function __construct(
        #[TaggedIterator(IPResolverInterface::TAG_NAME)] private readonly iterable  $ipResolvers,
        #[TaggedIterator(DNSProviderInterface::TAG_NAME)] private readonly iterable $dnsProviders,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->ipResolvers as $ipResolver) {
            $this->resolveIP($ipResolver);
        }

        return Command::SUCCESS;
    }

    private function resolveIP(IPResolverInterface $ipResolver): void
    {
        foreach ($ipResolver->resolveAll() as $result) {
            foreach ($this->dnsProviders as $provider) {
                $this->resolveDNS($provider, $result);
            }
        }
    }

    private function resolveDNS(DNSProviderInterface $dnsProvider, ExpectResolveResult $result): void
    {
        if ($dnsProvider->check($result)) {
            $dnsProvider->resolve($result);
        }
    }
}
