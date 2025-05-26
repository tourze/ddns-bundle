<?php

namespace DDNSBundle\Service;

use DDNSBundle\Attribute\DDNSDomain;
use DDNSBundle\Attribute\DDNSIP;
use ReflectionClass;
use ReflectionProperty;
use Tourze\DDNSContracts\ExpectResolveResult;

/**
 * DDNS属性处理器
 * 负责解析实体中的DDNS属性并构造解析结果
 */
class DDNSAttributeProcessor
{
    /**
     * 从实体中提取DDNS解析结果
     */
    public function extractResolveResults(object $entity): array
    {
        $reflectionClass = new ReflectionClass($entity);
        $domainFields = $this->findFieldsWithAttribute($reflectionClass, DDNSDomain::class);
        $ipFields = $this->findFieldsWithAttribute($reflectionClass, DDNSIP::class);

        $results = [];

        // 为每个域名字段和IP字段的组合创建解析结果
        foreach ($domainFields as $domainField) {
            $domainValue = $this->getPropertyValue($entity, $domainField);
            if (empty($domainValue)) {
                continue;
            }

            foreach ($ipFields as $ipField) {
                $ipValue = $this->getPropertyValue($entity, $ipField);
                if (empty($ipValue)) {
                    continue;
                }

                $results[] = new ExpectResolveResult($domainValue, $ipValue);
            }
        }

        return $results;
    }

    /**
     * 查找具有指定属性的字段
     */
    private function findFieldsWithAttribute(ReflectionClass $reflectionClass, string $attributeClass): array
    {
        $fields = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes($attributeClass);
            if (!empty($attributes)) {
                $fields[] = $property;
            }
        }

        return $fields;
    }

    /**
     * 获取属性值
     */
    private function getPropertyValue(object $entity, ReflectionProperty $property): mixed
    {
        $property->setAccessible(true);
        return $property->getValue($entity);
    }

    /**
     * 检查实体是否包含DDNS属性
     */
    public function hasAnyDDNSAttribute(object $entity): bool
    {
        $reflectionClass = new ReflectionClass($entity);

        foreach ($reflectionClass->getProperties() as $property) {
            $domainAttributes = $property->getAttributes(DDNSDomain::class);
            $ipAttributes = $property->getAttributes(DDNSIP::class);

            if (!empty($domainAttributes) || !empty($ipAttributes)) {
                return true;
            }
        }

        return false;
    }
}
