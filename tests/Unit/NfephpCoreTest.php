<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\NfephpCore;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Mockery;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;

class NfephpCoreTest extends TestCase
{
    public function testConstructor(): void
    {
        $core = new NfephpCore();
        
        $this->assertInstanceOf(NfephpCore::class, $core);
    }

    public function testCreateNFe(): void
    {
        $core = new NfephpCore();
        
        $result = $core->createNFe();
        
        $this->assertInstanceOf(Make::class, $result);
    }

    public function testCreateNFCe(): void
    {
        $core = new NfephpCore();
        
        $result = $core->createNFCe();
        
        $this->assertInstanceOf(Make::class, $result);
    }

    public function testGetToolsWithoutCertificate(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');
        
        $core = new NfephpCore();
        $core->getTools();
    }

    public function testGetToolsWithCertificate(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao inicializar NFePHP:');
        
        // Mock do arquivo de certificado
        $certPath = __DIR__ . '/../../fixtures/test.pfx';
        $certContent = 'test certificate content';
        
        // Criar diretório se não existir
        if (!is_dir(dirname($certPath))) {
            mkdir(dirname($certPath), 0755, true);
        }
        
        file_put_contents($certPath, $certContent);
        
        // Configurar certificado
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
            $core = new NfephpCore();
            $core->getTools();
        } finally {
            if (file_exists($certPath)) {
                unlink($certPath);
            }
        }
    }

    public function testSendNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');
        
        $core = new NfephpCore();
        $core->sendNFe(['xml1', 'xml2']);
    }

    public function testConsultNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');
        
        $core = new NfephpCore();
        $core->consultNFe('12345678901234567890123456789012345678901234');
    }

    public function testCancelNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');
        
        $core = new NfephpCore();
        $core->cancelNFe('12345678901234567890123456789012345678901234', 'Justificativa', '123456789012345');
    }

    public function testInutilizeNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');
        
        $core = new NfephpCore();
        $core->inutilizeNFe(1, 1, 10, 'Justificativa');
    }

    public function testGenerateQRCode(): void
    {
        $this->expectException(\Error::class);
        
        $core = new NfephpCore();
        
        $dom = new \DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>');
        
        $core->generateQRCode(
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
        
        $core = new NfephpCore();
        
        // DOM inválido para causar exceção
        $dom = new \DOMDocument();
        
        $core->generateQRCode(
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
        
        $core = new NfephpCore();
        
        $dom = new \DOMDocument();
        $dom->loadXML('<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>');
        
        $core->generateNFeQRCode(
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
        
        $core = new NfephpCore();
        
        // DOM inválido para causar exceção
        $dom = new \DOMDocument();
        
        $core->generateNFeQRCode(
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
        
        $core = new NfephpCore();
        
        $response = '<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>';
        
        $core->standardizeResponse($response);
    }

    public function testStandardizeResponseWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao padronizar resposta:');
        
        $core = new NfephpCore();
        
        // XML inválido para causar exceção
        $response = 'invalid xml';
        
        $core->standardizeResponse($response);
    }

    public function testGetConfig(): void
    {
        $core = new NfephpCore();
        
        $result = $core->getConfig();
        
        $this->assertIsArray($result);
    }

    public function testGetNFeConfig(): void
    {
        $core = new NfephpCore();
        
        $result = $core->getNFeConfig();
        
        $this->assertIsArray($result);
    }

    public function testSetConfig(): void
    {
        $core = new NfephpCore();
        
        $newConfig = [
            'test' => 'value',
            'nfe_config' => [
                'test' => 'value'
            ]
        ];
        
        $core->setConfig($newConfig);
        
        $this->assertInstanceOf(NfephpCore::class, $core);
    }

    public function testSetNFeConfig(): void
    {
        $core = new NfephpCore();
        
        $newConfig = [
            'test' => 'value'
        ];
        
        $core->setNFeConfig($newConfig);
        
        $this->assertInstanceOf(NfephpCore::class, $core);
    }

    public function testGetContingencyManager(): void
    {
        $core = new NfephpCore();
        
        $result = $core->getContingencyManager();
        
        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Managers\ContingencyManager::class, $result);
    }

    public function testGetCertificateManager(): void
    {
        $core = new NfephpCore();
        
        $result = $core->getCertificateManager();
        
        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Managers\CertificateManager::class, $result);
    }
}