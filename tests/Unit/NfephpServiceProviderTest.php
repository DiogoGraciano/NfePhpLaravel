<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Managers\CertificateManager;
use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use DiogoGraciano\Nfephp\Managers\DanfeManager;
use DiogoGraciano\Nfephp\Managers\NfeManager;
use DiogoGraciano\Nfephp\Managers\UtilsManager;
use DiogoGraciano\Nfephp\NfeContext;
use DiogoGraciano\Nfephp\NfephpServiceProvider;
use DiogoGraciano\Nfephp\Nfse;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class NfephpServiceProviderTest extends TestCase
{
    public function testBoot(): void
    {
        $provider = new NfephpServiceProvider($this->app);

        $provider->boot();

        $this->assertTrue(true);
    }

    public function testRegisterNfeBinding(): void
    {
        $this->assertTrue($this->app->bound('nfe'));
        $this->assertInstanceOf(NfeManager::class, $this->app->make('nfe'));
    }

    public function testRegisterDanfeBinding(): void
    {
        $this->assertTrue($this->app->bound('danfe'));
        $this->assertInstanceOf(DanfeManager::class, $this->app->make('danfe'));
    }

    public function testRegisterContingencyBinding(): void
    {
        $this->assertTrue($this->app->bound('contingency'));
        $this->assertInstanceOf(ContingencyManager::class, $this->app->make('contingency'));
    }

    public function testRegisterCertificateBinding(): void
    {
        $this->assertTrue($this->app->bound('certificate'));
        $this->assertInstanceOf(CertificateManager::class, $this->app->make('certificate'));
    }

    public function testRegisterUtilsBinding(): void
    {
        $this->assertTrue($this->app->bound('nfe-utils'));
        $this->assertInstanceOf(UtilsManager::class, $this->app->make('nfe-utils'));
    }

    public function testRegisterNfseBinding(): void
    {
        $this->assertTrue($this->app->bound('nfse'));
        $this->assertInstanceOf(Nfse::class, $this->app->make('nfse'));
    }

    public function testNfeContextIsSingleton(): void
    {
        $context1 = $this->app->make(NfeContext::class);
        $context2 = $this->app->make(NfeContext::class);
        $this->assertSame($context1, $context2);
    }

    public function testConfigIsMerged(): void
    {
        $this->assertArrayHasKey('nfephp', Config::all());
    }

    public function testPublishesConfigInConsole(): void
    {
        $provider = new NfephpServiceProvider($this->app);

        $provider->boot();

        $this->assertTrue(true);
    }
}
