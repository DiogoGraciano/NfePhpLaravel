<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Managers\NfeManager;
use DiogoGraciano\Nfephp\NfeContext;
use DiogoGraciano\Nfephp\Tests\TestCase;
use NFePHP\NFe\Make;

class NfeTest extends TestCase
{
    private function createManager(): NfeManager
    {
        return new NfeManager(new NfeContext());
    }

    public function testConstructor(): void
    {
        $manager = $this->createManager();

        $this->assertInstanceOf(NfeManager::class, $manager);
    }

    public function testCreateNFe(): void
    {
        $manager = $this->createManager();

        $result = $manager->createNFe();

        $this->assertInstanceOf(Make::class, $result);
    }

    public function testCreateNFCe(): void
    {
        $manager = $this->createManager();

        $result = $manager->createNFCe();

        $this->assertInstanceOf(Make::class, $result);
    }

    public function testGetToolsWithoutCertificate(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->getTools();
    }

    public function testGetToolsWithInvalidCertificate(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao inicializar NFePHP:');

        $certPath = __DIR__ . '/../../fixtures/test.pfx';
        $certContent = 'test certificate content';

        if (!is_dir(dirname($certPath))) {
            mkdir(dirname($certPath), 0755, true);
        }

        file_put_contents($certPath, $certContent);

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
            $this->createManager();
        } finally {
            if (file_exists($certPath)) {
                unlink($certPath);
            }
        }
    }

    public function testSendNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao enviar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->sendNFe(['xml1', 'xml2']);
    }

    public function testConsultNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao consultar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->consultNFe('12345678901234567890123456789012345678901234');
    }

    public function testCancelNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao cancelar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->cancelNFe('12345678901234567890123456789012345678901234', 'Justificativa', '123456789012345');
    }

    public function testInutilizeNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao inutilizar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->inutilizeNFe(1, 1, 10, 'Justificativa');
    }

    public function testDistributionDFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao consultar distribuição DFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->distributionDFe(0);
    }

    public function testConfirmNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao manifestar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->confirmNFe('12345678901234567890123456789012345678901234');
    }

    public function testAcknowledgeNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao manifestar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->acknowledgeNFe('12345678901234567890123456789012345678901234');
    }

    public function testUnknownNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao manifestar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->unknownNFe('12345678901234567890123456789012345678901234');
    }

    public function testNotPerformedNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao manifestar NFe: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $manager->notPerformedNFe('12345678901234567890123456789012345678901234', 'Justificativa');
    }

    public function testManifestNFeBatchWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao manifestar NFe em lote: Tools não inicializado. Verifique se o certificado está configurado.');

        $manager = $this->createManager();
        $std = new \stdClass();
        $std->evento = [];
        $manager->manifestNFeBatch($std);
    }

    public function testGenerateQRCode(): void
    {
        $this->expectException(\Error::class);

        $manager = $this->createManager();

        $dom = new \DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>');

        $manager->generateQRCode(
            $dom,
            'token123',
            'id123',
            '1.0',
            'http://teste.com',
            'http://teste.com/chave'
        );
    }

    public function testGenerateQRCodeWithException(): void
    {
        $this->expectException(\Error::class);

        $manager = $this->createManager();

        $dom = new \DOMDocument();

        $manager->generateQRCode(
            $dom,
            'token123',
            'id123',
            '1.0',
            'http://teste.com',
            'http://teste.com/chave'
        );
    }

    public function testGenerateNFeQRCode(): void
    {
        $this->expectException(\Error::class);

        $manager = $this->createManager();

        $dom = new \DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>');

        $manager->generateNFeQRCode(
            $dom,
            'token123',
            'id123',
            '1.0',
            'http://teste.com',
            'http://teste.com/chave'
        );
    }

    public function testGenerateNFeQRCodeWithException(): void
    {
        $this->expectException(\Error::class);

        $manager = $this->createManager();

        $dom = new \DOMDocument();

        $manager->generateNFeQRCode(
            $dom,
            'token123',
            'id123',
            '1.0',
            'http://teste.com',
            'http://teste.com/chave'
        );
    }

    public function testStandardizeResponse(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao padronizar resposta:');

        $manager = $this->createManager();

        $response = '<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>';

        $manager->standardizeResponse($response);
    }

    public function testStandardizeResponseWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao padronizar resposta:');

        $manager = $this->createManager();

        $response = 'invalid xml';

        $manager->standardizeResponse($response);
    }

    public function testGetConfig(): void
    {
        $manager = $this->createManager();

        $result = $manager->getConfig();

        $this->assertIsArray($result);
    }

    public function testGetNFeConfig(): void
    {
        $manager = $this->createManager();

        $result = $manager->getNFeConfig();

        $this->assertIsArray($result);
    }

    public function testSetConfig(): void
    {
        $manager = $this->createManager();

        $newConfig = [
            'test' => 'value',
            'nfe_config' => [
                'test' => 'value',
            ],
        ];

        $manager->setConfig($newConfig);

        $this->assertInstanceOf(NfeManager::class, $manager);
    }

    public function testSetNFeConfig(): void
    {
        $manager = $this->createManager();

        $newConfig = [
            'test' => 'value',
        ];

        $manager->setNFeConfig($newConfig);

        $this->assertInstanceOf(NfeManager::class, $manager);
    }
}
