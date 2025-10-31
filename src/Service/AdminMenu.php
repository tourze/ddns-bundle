<?php

declare(strict_types=1);

namespace DDNSBundle\Service;

use DDNSBundle\Entity\ExampleEntity;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        $ddnsCenter = $item->getChild('DDNS管理');
        if (null === $ddnsCenter) {
            $ddnsCenter = $item->addChild('DDNS管理');
        }

        // 服务器节点管理
        $ddnsCenter->addChild('服务器节点')->setUri($this->linkGenerator->getCurdListPage(ExampleEntity::class));
    }
}
