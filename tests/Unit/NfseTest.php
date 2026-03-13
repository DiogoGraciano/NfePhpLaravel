<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Facades\Nfse as NfseFacade;
use DiogoGraciano\Nfephp\Managers\NfseManager;
use DiogoGraciano\Nfephp\Nfse;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Exception;

class NfseTest extends TestCase
{
    protected Nfse $nfse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->nfse = app('nfse');
    }

    // ============================================
    // TESTES DE RESOLUÇÃO E INSTÂNCIA
    // ============================================

    public function testNfseCanBeResolvedFromContainer(): void
    {
        $nfse = app('nfse');
        $this->assertInstanceOf(Nfse::class, $nfse);
    }

    public function testNfseFacadeResolves(): void
    {
        $this->assertInstanceOf(Nfse::class, NfseFacade::getFacadeRoot());
    }

    public function testNfseIsSingleton(): void
    {
        $nfse1 = app('nfse');
        $nfse2 = app('nfse');
        $this->assertSame($nfse1, $nfse2);
    }

    public function testGetNfseManager(): void
    {
        $manager = $this->nfse->getNfseManager();
        $this->assertInstanceOf(NfseManager::class, $manager);
    }

    // ============================================
    // TESTES DE CONFIGURAÇÃO
    // ============================================

    public function testGetConfig(): void
    {
        $config = $this->nfse->getConfig();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('nfse_config', $config);
        $this->assertEquals(2, $config['nfse_config']['tpAmb']);
    }

    // ============================================
    // TESTES SEM CERTIFICADO (DEVEM LANÇAR EXCEÇÃO)
    // ============================================

    public function testSendDpsWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $this->nfse->sendDps('<xml>test</xml>');
    }

    public function testConsultNfseByKeyWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $this->nfse->consultNfseByKey('12345678901234567890123456789012345678901234567890');
    }

    public function testConsultDpsByKeyWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $this->nfse->consultDpsByKey('12345678901234567890123456789012345678901234567890');
    }

    public function testConsultNfseEventsWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $this->nfse->consultNfseEvents('12345678901234567890123456789012345678901234567890');
    }

    public function testGetDanfseWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $this->nfse->getDanfse('12345678901234567890123456789012345678901234567890');
    }

    public function testCancelNfseWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $std = new \stdClass();
        $std->chave = '12345678901234567890123456789012345678901234567890';
        $this->nfse->cancelNfse($std);
    }

    // ============================================
    // TESTES DO NFSE MANAGER
    // ============================================

    public function testNfseManagerGetToolsWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('NFSe Tools não inicializado');
        $this->nfse->getNfseManager()->getTools();
    }

    public function testNfseManagerGetConfig(): void
    {
        $config = $this->nfse->getNfseManager()->getConfig();
        $this->assertIsArray($config);
    }
}
