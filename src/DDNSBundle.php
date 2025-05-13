<?php

namespace DDNSBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class DDNSBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\JsonRPCLockBundle\JsonRPCLockBundle::class => ['all' => true],
            \DeviceBundle\DeviceBundle::class => ['all' => true],
            \AccessTokenBundle\AccessTokenBundle::class => ['all' => true],
            \BizUserBundle\BizUserBundle::class => ['all' => true],
        ];
    }
}
