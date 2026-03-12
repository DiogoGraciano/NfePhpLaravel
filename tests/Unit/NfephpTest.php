<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Managers\DanfeManager;
use DiogoGraciano\Nfephp\Managers\NfephpManager;
use DiogoGraciano\Nfephp\Nfephp;
use DiogoGraciano\Nfephp\Tests\TestCase;
use NFePHP\NFe\Make;

class NfephpTest extends TestCase
{
    public function testConstructor(): void
    {
        $core = new Nfephp();

        $this->assertInstanceOf(Nfephp::class, $core);
        $this->assertInstanceOf(NfephpManager::class, $core);
    }

    public function testCreateNFe(): void
    {
        $core = new Nfephp();

        $result = $core->createNFe();

        $this->assertInstanceOf(Make::class, $result);
    }

    public function testCreateNFCe(): void
    {
        $core = new Nfephp();

        $result = $core->createNFCe();

        $this->assertInstanceOf(Make::class, $result);
    }

    public function testGetToolsWithoutCertificate(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');

        $core = new Nfephp();
        $core->getTools();
    }

    public function testGetToolsWithCertificate(): void
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
            $core = new Nfephp();
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

        $core = new Nfephp();
        $core->sendNFe(['xml1', 'xml2']);
    }

    public function testConsultNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');

        $core = new Nfephp();
        $core->consultNFe('12345678901234567890123456789012345678901234');
    }

    public function testCancelNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');

        $core = new Nfephp();
        $core->cancelNFe('12345678901234567890123456789012345678901234', 'Justificativa', '123456789012345');
    }

    public function testInutilizeNFeWithoutTools(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tools não inicializado. Verifique se o certificado está configurado.');

        $core = new Nfephp();
        $core->inutilizeNFe(1, 1, 10, 'Justificativa');
    }

    public function testGenerateQRCode(): void
    {
        $this->expectException(\Error::class);

        $core = new Nfephp();

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

        $core = new Nfephp();

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

        $core = new Nfephp();

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

        $core = new Nfephp();

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

        $core = new Nfephp();

        $response = '<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>';

        $core->standardizeResponse($response);
    }

    public function testStandardizeResponseWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao padronizar resposta:');

        $core = new Nfephp();

        $response = 'invalid xml';

        $core->standardizeResponse($response);
    }

    public function testGetConfig(): void
    {
        $core = new Nfephp();

        $result = $core->getConfig();

        $this->assertIsArray($result);
    }

    public function testGetNFeConfig(): void
    {
        $core = new Nfephp();

        $result = $core->getNFeConfig();

        $this->assertIsArray($result);
    }

    public function testSetConfig(): void
    {
        $core = new Nfephp();

        $newConfig = [
            'test' => 'value',
            'nfe_config' => [
                'test' => 'value',
            ],
        ];

        $core->setConfig($newConfig);

        $this->assertInstanceOf(Nfephp::class, $core);
    }

    public function testSetNFeConfig(): void
    {
        $core = new Nfephp();

        $newConfig = [
            'test' => 'value',
        ];

        $core->setNFeConfig($newConfig);

        $this->assertInstanceOf(Nfephp::class, $core);
    }

    public function testGetContingencyManager(): void
    {
        $core = new Nfephp();

        $result = $core->getContingencyManager();

        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Managers\ContingencyManager::class, $result);
    }

    public function testGetCertificateManager(): void
    {
        $core = new Nfephp();

        $result = $core->getCertificateManager();

        $this->assertInstanceOf(\DiogoGraciano\Nfephp\Managers\CertificateManager::class, $result);
    }

    public function testActivateContingency(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->activateContingency('SP', 'Teste de contingência', 'SVCAN');
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testActivateContingencyWithEmptyType(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->activateContingency('SP', 'Teste de contingência');
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testDeactivateContingency(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->deactivateContingency();
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testIsInContingency(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->isInContingency();
        
        $this->assertIsBool($result);
    }

    public function testGetContingencyInfo(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->getContingencyInfo();
        
        $this->assertNull($result);
        
        $nfephp->activateContingency('SP', 'Teste de contingência', 'SVCAN');
        
        $result = $nfephp->getContingencyInfo();
        
        $this->assertIsArray($result);
    }

    public function testAdjustXmlForContingency(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao ajustar XML para contingência:');
        
        $nfephp = new Nfephp();
        $nfephp->activateContingency('SP', 'Teste de contingência', 'SVCAN');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?><nfeProc><NFe><infNFe><ide><tpEmis>1</tpEmis></ide></infNFe></NFe></nfeProc>';
        
        $nfephp->adjustXmlForContingency($xml);
    }

    public function testLoadContingency(): void
    {
        $nfephp = new Nfephp();
        
        $contingencyJson = '{"type":"SVCAN","motive":"Teste","timestamp":"2024-01-01T00:00:00Z","tpEmis":"9"}';
        
        $nfephp->loadContingency($contingencyJson);
        
        $this->assertTrue($nfephp->isInContingency());
    }

    public function testGetCertificateInfo(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->getCertificateInfo();
        
        $this->assertNull($result);
    }

    public function testIsCertificateValid(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->isCertificateValid();
        
        $this->assertFalse($result);
    }

    public function testGetCertificateDaysToExpire(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->getCertificateDaysToExpire();
        
        $this->assertNull($result);
    }

    public function testValidateXml(): void
    {
        $nfephp = new Nfephp();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>';
        $fixturesDir = __DIR__ . '/../../fixtures';
        $xsd = $fixturesDir . '/test.xsd';

        if (!is_dir($fixturesDir)) {
            mkdir($fixturesDir, 0755, true);
        }

        $xsdContent = '<?xml version="1.0" encoding="UTF-8"?>
        <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
            <xs:element name="root">
                <xs:complexType>
                    <xs:sequence>
                        <xs:element name="test" type="xs:string"/>
                    </xs:sequence>
                </xs:complexType>
            </xs:element>
        </xs:schema>';

        file_put_contents($xsd, $xsdContent);
        
        try {
            $result = $nfephp->validateXml($xml, $xsd);
            $this->assertIsBool($result);
        } finally {
            if (file_exists($xsd)) {
                unlink($xsd);
            }
        }
    }

    public function testValidateCnpj(): void
    {
        $nfephp = new Nfephp();
        
        $this->assertTrue($nfephp->validateCnpj('12345678000195'));
        $this->assertFalse($nfephp->validateCnpj('12345678000190'));
    }

    public function testValidateCpf(): void
    {
        $nfephp = new Nfephp();
        
        $this->assertTrue($nfephp->validateCpf('11144477735'));
        $this->assertFalse($nfephp->validateCpf('12345678900'));
    }

    public function testValidateNFeKey(): void
    {
        $nfephp = new Nfephp();
        
        $this->assertIsBool($nfephp->validateNFeKey('35240112345678000195550010000000011234567801'));
        $this->assertFalse($nfephp->validateNFeKey('invalid_key'));
    }

    public function testCleanString(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->cleanString('Teste com caracteres especiais: áéíóú');
        
        $this->assertIsString($result);
    }

    public function testStringToAscii(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->stringToAscii('Teste com acentos: áéíóú');
        
        $this->assertIsString($result);
    }

    public function testEquilizeParameters(): void
    {
        $nfephp = new Nfephp();
        
        $std = new \stdClass();
        $std->test = 'value';
        $possible = ['test', 'other'];
        
        $result = $nfephp->equilizeParameters($std, $possible);
        
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testFormatCnpj(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->formatCnpj('12345678000195');
        
        $this->assertEquals('12.345.678/0001-95', $result);
    }

    public function testFormatCpf(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->formatCpf('12345678901');
        
        $this->assertEquals('123.456.789-01', $result);
    }

    public function testGetUfCode(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->getUfCode('SP');
        
        $this->assertEquals(35, $result);
    }

    public function testGetUfByCode(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->getUfByCode(35);
        
        $this->assertEquals('SP', $result);
    }

    public function testGetTimezoneByUf(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->getTimezoneByUf('SP');
        
        $this->assertIsString($result);
    }

    private function getNfeXml(): string
    {
        return file_get_contents(__DIR__ . '/../fixtures/nfe.xml');
    }

    public function testGetDanfeManager(): void
    {
        $core = new Nfephp();

        $result = $core->getDanfeManager();

        $this->assertInstanceOf(DanfeManager::class, $result);
    }

    // --- Sucesso: generate ---

    public function testGenerateDanfeWithValidXml(): void
    {
        $core = new Nfephp();
        $pdf = $core->generateDanfe($this->getNfeXml());

        $this->assertNotEmpty($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    public function testGenerateDanfeSimplesWithValidXml(): void
    {
        $core = new Nfephp();
        $pdf = $core->generateDanfeSimples($this->getNfeXml());

        $this->assertNotEmpty($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    // --- Sucesso: save (Storage) ---

    public function testSaveDanfeWithValidXml(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');

        $core = new Nfephp();
        $core->saveDanfe($this->getNfeXml(), 'danfes/test.pdf', 'local');

        \Illuminate\Support\Facades\Storage::disk('local')->assertExists('danfes/test.pdf');
    }

    // --- Sucesso: download ---

    public function testDownloadDanfeWithValidXml(): void
    {
        $core = new Nfephp();
        $response = $core->downloadDanfe($this->getNfeXml(), 'nota.pdf');

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    // --- Sucesso: render inline ---

    public function testRenderDanfeWithValidXml(): void
    {
        $core = new Nfephp();
        $response = $core->renderDanfe($this->getNfeXml(), 'nota.pdf');

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    // --- Erro: XML inválido ---

    public function testGenerateDanfeWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $core = new Nfephp();
        $core->generateDanfe('invalid xml');
    }

    public function testSaveDanfeWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        \Illuminate\Support\Facades\Storage::fake('local');

        $core = new Nfephp();
        $core->saveDanfe('invalid xml', 'test.pdf', 'local');
    }

    public function testDownloadDanfeWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $core = new Nfephp();
        $core->downloadDanfe('invalid xml');
    }

    public function testRenderDanfeWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $core = new Nfephp();
        $core->renderDanfe('invalid xml');
    }

    public function testGenerateDanfceWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFCe:');

        $core = new Nfephp();
        $core->generateDanfce('invalid xml');
    }

    public function testSaveDanfceWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFCe:');

        \Illuminate\Support\Facades\Storage::fake('local');

        $core = new Nfephp();
        $core->saveDanfce('invalid xml', 'test.pdf', 'local');
    }

    public function testGenerateDanfeSimplesWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE simplificado:');

        $core = new Nfephp();
        $core->generateDanfeSimples('invalid xml');
    }

    public function testSaveDanfeSimplesWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE simplificado:');

        \Illuminate\Support\Facades\Storage::fake('local');

        $core = new Nfephp();
        $core->saveDanfeSimples('invalid xml', 'test.pdf', 'local');
    }

    public function testGenerateDaeventoWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar documento de evento:');

        $core = new Nfephp();
        $core->generateDaevento('invalid xml');
    }

    public function testSaveDaeventoWithInvalidXml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao gerar documento de evento:');

        \Illuminate\Support\Facades\Storage::fake('local');

        $core = new Nfephp();
        $core->saveDaevento('invalid xml', 'test.pdf');
    }

    public function testSetDanfeLogo(): void
    {
        $core = new Nfephp();
        $core->setDanfeLogo('/path/to/logo.png');

        $this->assertInstanceOf(Nfephp::class, $core);
    }

    public function testSetDanfeLogoNull(): void
    {
        $core = new Nfephp();
        $core->setDanfeLogo(null);

        $this->assertInstanceOf(Nfephp::class, $core);
    }

    public function testGenerateNFeKey(): void
    {
        $nfephp = new Nfephp();
        
        $result = $nfephp->generateNFeKey(
            '35', // cUF
            '2401', // aamm
            '12345678000195', // cnpj
            '55', // mod
            '1', // serie
            '1', // nNF
            '1', // tpEmis
            '12345678' // cNF
        );
        
        $this->assertIsString($result);
        $this->assertGreaterThan(40, strlen($result));
    }
}
