<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use DiogoGraciano\Nfephp\NfeContext;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Exception;

class NfeContextTest extends TestCase
{
    private function createContext(): NfeContext
    {
        return new NfeContext();
    }

    // ============================================
    // TESTES BÁSICOS
    // ============================================

    public function testConstructor(): void
    {
        $context = $this->createContext();
        $this->assertInstanceOf(NfeContext::class, $context);
    }

    public function testGetConfigReturnsArray(): void
    {
        $context = $this->createContext();
        $config = $context->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('nfe_config', $config);
    }

    public function testGetNFeConfigReturnsArray(): void
    {
        $context = $this->createContext();
        $nfeConfig = $context->getNFeConfig();

        $this->assertIsArray($nfeConfig);
        $this->assertEquals(2, $nfeConfig['tpAmb']);
        $this->assertEquals('SP', $nfeConfig['siglaUF']);
    }

    public function testGetToolsWithoutCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');

        $context = $this->createContext();
        $context->getTools();
    }

    public function testGetCertificateReturnsNullWithoutCertificate(): void
    {
        $context = $this->createContext();
        $this->assertNull($context->getCertificate());
    }

    public function testGetContingencyManager(): void
    {
        $context = $this->createContext();
        $this->assertInstanceOf(ContingencyManager::class, $context->getContingencyManager());
    }

    // ============================================
    // TESTES DE CONFIGURAÇÃO
    // ============================================

    public function testSetConfigMerges(): void
    {
        $context = $this->createContext();

        $context->setConfig(['custom_key' => 'custom_value']);

        $config = $context->getConfig();
        $this->assertEquals('custom_value', $config['custom_key']);
        $this->assertArrayHasKey('nfe_config', $config);
    }

    public function testSetNFeConfigMerges(): void
    {
        $context = $this->createContext();

        $context->setNFeConfig(['custom_key' => 'value']);

        $nfeConfig = $context->getNFeConfig();
        $this->assertEquals('value', $nfeConfig['custom_key']);
        $this->assertEquals(2, $nfeConfig['tpAmb']);
    }

    // ============================================
    // TESTES DE CONTINGÊNCIA
    // ============================================

    public function testContingencyActivateChangesState(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $this->assertFalse($contingency->isActive());

        $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');

        $this->assertTrue($contingency->isActive());
    }

    public function testContingencyDeactivateChangesState(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');
        $this->assertTrue($contingency->isActive());

        $contingency->deactivate();
        $this->assertFalse($contingency->isActive());
    }

    public function testContingencyGetInfoWhenActive(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');

        $info = $contingency->getInfo();
        $this->assertIsArray($info);
        $this->assertArrayHasKey('type', $info);
        $this->assertArrayHasKey('motive', $info);
        $this->assertArrayHasKey('timestamp', $info);
        $this->assertArrayHasKey('tpEmis', $info);
        $this->assertNotEmpty($info['type']);
        $this->assertNotEmpty($info['motive']);
    }

    public function testContingencyGetInfoWhenInactiveReturnsNull(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $this->assertNull($contingency->getInfo());
    }

    public function testContingencyCallbackIsCalledOnActivate(): void
    {
        $callbackCalled = false;

        $contingencyManager = new ContingencyManager(function () use (&$callbackCalled) {
            $callbackCalled = true;
        });

        $contingencyManager->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');

        $this->assertTrue($callbackCalled);
    }

    public function testContingencyCallbackIsCalledOnDeactivate(): void
    {
        $callCount = 0;

        $contingencyManager = new ContingencyManager(function () use (&$callCount) {
            $callCount++;
        });

        $contingencyManager->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');
        $this->assertEquals(1, $callCount);

        $contingencyManager->deactivate();
        $this->assertEquals(2, $callCount);
    }

    public function testContingencyLoadFromJson(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        // Ativa para obter o JSON
        $json = $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');
        $this->assertTrue($contingency->isActive());

        // Cria novo manager e carrega do JSON
        $newContingency = new ContingencyManager();
        $this->assertFalse($newContingency->isActive());

        $newContingency->load($json);
        $this->assertTrue($newContingency->isActive());
    }

    public function testContingencyAdjustXmlWithoutActiveThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Nenhuma contingência ativa. Use activate() primeiro.');

        $context = $this->createContext();
        $context->getContingencyManager()->adjustXml('<xml>test</xml>');
    }

    public function testContingencyActivateDeactivateActivateCycle(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $this->assertFalse($contingency->isActive());

        $contingency->activate('SP', 'Primeiro motivo de teste contingencia', 'SVCAN');
        $this->assertTrue($contingency->isActive());

        $contingency->deactivate();
        $this->assertFalse($contingency->isActive());

        $contingency->activate('RJ', 'Segundo motivo de teste contingencia', 'SVCRS');
        $this->assertTrue($contingency->isActive());

        $info = $contingency->getInfo();
        $this->assertStringContainsString('Segundo motivo', $info['motive']);
    }

    public function testContingencyActivateReturnsJson(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $result = $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
    }

    public function testContingencyDeactivateReturnsJson(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');
        $result = $contingency->deactivate();

        $this->assertIsString($result);
        $decoded = json_decode($result, true);
        $this->assertIsArray($decoded);
    }

    public function testContingencyDeactivateWithoutPriorActivation(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $result = $contingency->deactivate();

        $this->assertFalse($contingency->isActive());
        $this->assertIsString($result);
    }

    public function testContingencyGetContingencyReturnsNullByDefault(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $this->assertNull($contingency->getContingency());
    }

    public function testContingencyGetContingencyReturnsObjectAfterActivate(): void
    {
        $context = $this->createContext();
        $contingency = $context->getContingencyManager();

        $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');

        $this->assertNotNull($contingency->getContingency());
    }

    public function testContingencyCallbackIsCalledOnLoad(): void
    {
        $callbackCalled = false;

        $source = new ContingencyManager();
        $json = $source->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');

        $target = new ContingencyManager(function () use (&$callbackCalled) {
            $callbackCalled = true;
        });

        $target->load($json);

        $this->assertTrue($callbackCalled);
    }

    public function testContingencyWithoutCallbackDoesNotFail(): void
    {
        $contingency = new ContingencyManager(null);

        $contingency->activate('SP', 'Motivo de teste para ativar a contingencia', 'SVCAN');
        $this->assertTrue($contingency->isActive());

        $contingency->deactivate();
        $this->assertFalse($contingency->isActive());
    }

    // ============================================
    // TESTES DE getCertificateManager
    // ============================================

    public function testGetCertificateManagerWithoutCertificate(): void
    {
        $context = $this->createContext();
        $certManager = $context->getCertificateManager();

        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Managers\CertificateManager::class, $certManager);
        $this->assertNull($certManager->getInfo());
        $this->assertFalse($certManager->isValid());
        $this->assertNull($certManager->getDaysToExpire());
        $this->assertNull($certManager->getCnpj());
        $this->assertNull($certManager->getCpf());
        $this->assertNull($certManager->getCompanyName());
    }

    // ============================================
    // TESTES DE reinitialize
    // ============================================

    public function testReinitializeWithoutCertificateKeepsToolsNull(): void
    {
        $context = $this->createContext();

        $context->reinitialize();

        $this->assertNull($context->getCertificate());

        $this->expectException(Exception::class);
        $context->getTools();
    }

    // ============================================
    // TESTES DE getNFeConfig com config vazia
    // ============================================

    public function testGetNFeConfigReturnsEmptyArrayWhenNotSet(): void
    {
        config(['nfephp' => []]);

        $context = new NfeContext();

        $this->assertIsArray($context->getNFeConfig());
        $this->assertEmpty($context->getNFeConfig());
    }

    public function testSetConfigOverwritesExistingKey(): void
    {
        $context = $this->createContext();

        $context->setConfig(['nfe_config' => ['tpAmb' => 1]]);

        $this->assertEquals(1, $context->getNFeConfig()['tpAmb']);
    }

    public function testSetNFeConfigOverwritesExistingKey(): void
    {
        $context = $this->createContext();

        $context->setNFeConfig(['tpAmb' => 1]);

        $this->assertEquals(1, $context->getNFeConfig()['tpAmb']);
    }

    // ============================================
    // TESTES DE CERTIFICADO INVÁLIDO
    // ============================================

    public function testInitializeWithInvalidCertificateThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao inicializar NFePHP:');

        $certPath = __DIR__ . '/../../fixtures/test.pfx';

        if (!is_dir(dirname($certPath))) {
            mkdir(dirname($certPath), 0755, true);
        }

        file_put_contents($certPath, 'conteudo invalido');

        config([
            'nfephp' => [
                'certificate' => [
                    'path' => $certPath,
                    'password' => 'test123',
                ],
                'nfe_config' => [
                    'atualizacao' => '2024-01-01 00:00:00',
                    'tpAmb' => 2,
                    'razaosocial' => 'Empresa Teste',
                    'cnpj' => '12345678000195',
                    'siglaUF' => 'SP',
                    'schemes' => 'PL_009_V4_00_NT_2020_006_v1.20',
                    'versao' => '4.00',
                    'tokenIBPT' => '',
                    'CSC' => '',
                    'CSCid' => '',
                ],
            ],
        ]);

        try {
            new NfeContext();
        } finally {
            if (file_exists($certPath)) {
                unlink($certPath);
            }
        }
    }

    public function testInitializeWithNonExistentCertificatePathThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao inicializar NFePHP:');

        config([
            'nfephp' => [
                'certificate' => [
                    'path' => '/caminho/inexistente/cert.pfx',
                    'password' => 'test123',
                ],
                'nfe_config' => [
                    'atualizacao' => '2024-01-01 00:00:00',
                    'tpAmb' => 2,
                    'razaosocial' => 'Empresa Teste',
                    'cnpj' => '12345678000195',
                    'siglaUF' => 'SP',
                    'schemes' => 'PL_009_V4_00_NT_2020_006_v1.20',
                    'versao' => '4.00',
                    'tokenIBPT' => '',
                    'CSC' => '',
                    'CSCid' => '',
                ],
            ],
        ]);

        new NfeContext();
    }

    public function testInitializeWithEmptyCertificatePathDoesNotThrow(): void
    {
        config([
            'nfephp' => [
                'certificate' => [
                    'path' => '',
                    'password' => 'test123',
                ],
                'nfe_config' => [
                    'tpAmb' => 2,
                ],
            ],
        ]);

        $context = new NfeContext();
        $this->assertNull($context->getCertificate());
    }

    public function testInitializeWithEmptyPasswordDoesNotThrow(): void
    {
        config([
            'nfephp' => [
                'certificate' => [
                    'path' => '/some/path.pfx',
                    'password' => '',
                ],
                'nfe_config' => [
                    'tpAmb' => 2,
                ],
            ],
        ]);

        $context = new NfeContext();
        $this->assertNull($context->getCertificate());
    }

    // ============================================
    // TESTE DE SINGLETON VIA SERVICE PROVIDER
    // ============================================

    public function testNfeContextIsSingletonInContainer(): void
    {
        $context1 = app(NfeContext::class);
        $context2 = app(NfeContext::class);

        $this->assertSame($context1, $context2);
    }
}
