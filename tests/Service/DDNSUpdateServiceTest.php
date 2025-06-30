<?php

namespace DDNSBundle\Tests\Service;

use DDNSBundle\Service\DDNSUpdateService;
use PHPUnit\Framework\TestCase;
use Tourze\DDNSContracts\DNSProviderInterface;
use Tourze\DDNSContracts\ExpectResolveResult;

class DDNSUpdateServiceTest extends TestCase
{
    public function testUpdateDNSCallsProvidersWhenCheckPasses(): void
    {
        $result = $this->createMock(ExpectResolveResult::class);
        
        $provider1 = $this->createMock(DNSProviderInterface::class);
        $provider1->expects($this->once())
            ->method('check')
            ->with($result)
            ->willReturn(true);
        $provider1->expects($this->once())
            ->method('resolve')
            ->with($result);
            
        $provider2 = $this->createMock(DNSProviderInterface::class);
        $provider2->expects($this->once())
            ->method('check')
            ->with($result)
            ->willReturn(false);
        $provider2->expects($this->never())
            ->method('resolve');
            
        $service = new DDNSUpdateService([$provider1, $provider2]);
        $service->updateDNS($result);
    }
    
    public function testUpdateDNSWithNoProviders(): void
    {
        $result = $this->createMock(ExpectResolveResult::class);
        
        $service = new DDNSUpdateService([]);
        $service->updateDNS($result);
        
        $this->assertTrue(true);
    }
}