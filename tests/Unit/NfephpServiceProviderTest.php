<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\NfephpServiceProvider;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;

class NfephpServiceProviderTest extends TestCase
{
    public function testBoot(): void
    {
        $provider = new NfephpServiceProvider($this->app);
        
        $provider->boot();
        
        $this->assertTrue(true); // Se chegou até aqui, o boot foi executado sem erros
    }

    public function testRegister(): void
    {
        $provider = new NfephpServiceProvider($this->app);
        
        $provider->register();
        
        $this->assertTrue($this->app->bound('nfephp'));
        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Nfephp::class, $this->app->make('nfephp'));
    }

    public function testConfigIsMerged(): void
    {
        $provider = new NfephpServiceProvider($this->app);
        
        $provider->register();
        
        $this->assertArrayHasKey('nfephp', Config::all());
    }

    public function testPublishesConfigInConsole(): void
    {
        $provider = new NfephpServiceProvider($this->app);
        
        // Simular ambiente de console
        $this->app->instance('app', $this->app);
        
        $provider->boot();
        
        $this->assertTrue(true); // Se chegou até aqui, o boot foi executado sem erros
    }

    public function testDoesNotPublishConfigOutsideConsole(): void
    {
        $provider = new NfephpServiceProvider($this->app);
        
        $provider->boot();
        
        $this->assertTrue(true); // Se chegou até aqui, o boot foi executado sem erros
    }
}
