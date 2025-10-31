<?php

namespace DDNSBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Tourze\DDNSContracts\DNSProviderInterface;
use Tourze\DDNSContracts\DTO\ExpectResolveResult;
use Tourze\DDNSContracts\IPResolverInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '运行DDNS提供商更新任务')]
#[Autoconfigure(public: true)]
class ProviderRunCommand extends Command
{
    public const NAME = 'ddns:provider:run';

    /**
     * @param iterable<IPResolverInterface>  $ipResolvers
     * @param iterable<DNSProviderInterface> $dnsProviders
     */
    public function __construct(
        #[AutowireIterator(tag: IPResolverInterface::TAG_NAME)] private readonly iterable $ipResolvers,
        #[AutowireIterator(tag: DNSProviderInterface::TAG_NAME)] private readonly iterable $dnsProviders,
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
