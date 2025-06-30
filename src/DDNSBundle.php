<?php

namespace DDNSBundle;

use AccessTokenBundle\AccessTokenBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\JsonRPCLockBundle\JsonRPCLockBundle;

class DDNSBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            JsonRPCLockBundle::class => ['all' => true],
            AccessTokenBundle::class => ['all' => true],
        ];
    }
}
