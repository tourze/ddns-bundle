<?php

namespace DDNSBundle\EventListener;

use DDNSBundle\Service\DDNSAttributeProcessor;
use DDNSBundle\Service\DDNSUpdateService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

/**
 * DDNS实体监听器
 * 监听实体的创建和更新事件，自动更新DDNS解析
 */
#[AsEntityListener(event: Events::postPersist)]
#[AsEntityListener(event: Events::postUpdate)]
class DDNSEntityListener
{
    public function __construct(
        private readonly DDNSAttributeProcessor $attributeProcessor,
        private readonly DDNSUpdateService $ddnsUpdateService,
    ) {
    }

    /**
     * 实体创建后处理
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->handleEntityChange($args->getObject());
    }

    /**
     * 实体更新后处理
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->handleEntityChange($args->getObject());
    }

    /**
     * 处理实体变更
     */
    private function handleEntityChange(object $entity): void
    {
        // 检查实体是否包含DDNS属性
        if (!$this->attributeProcessor->hasAnyDDNSAttribute($entity)) {
            return;
        }

        // 提取DDNS解析结果
        $resolveResults = $this->attributeProcessor->extractResolveResults($entity);

        // 更新DDNS解析
        foreach ($resolveResults as $result) {
            $this->ddnsUpdateService->updateDNS($result);
        }
    }
}
