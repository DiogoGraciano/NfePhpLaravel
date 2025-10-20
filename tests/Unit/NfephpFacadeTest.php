<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\NfephpFacade;
use DiogoGraciano\Nfephp\Tests\TestCase;

class NfephpFacadeTest extends TestCase
{
    public function testGetFacadeAccessor(): void
    {
        $reflection = new \ReflectionClass(NfephpFacade::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);
        
        $result = $method->invoke(null);
        
        $this->assertEquals('nfephp', $result);
    }

    public function testFacadeResolvesCorrectly(): void
    {
        $result = NfephpFacade::getFacadeRoot();
        
        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Nfephp::class, $result);
    }

    public function testFacadeMethodsWork(): void
    {
        $result = NfephpFacade::validateCnpj('12345678000195');
        
        $this->assertTrue($result);
    }
}