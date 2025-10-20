<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Nfephp;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Mockery;

class NfephpTest extends TestCase
{
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
        $xsd = __DIR__ . '/../../fixtures/test.xsd';
        
        // Criar um arquivo XSD temporário para teste
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
