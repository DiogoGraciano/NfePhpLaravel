<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Managers;

use DiogoGraciano\Nfephp\Managers\UtilsManager;
use DiogoGraciano\Nfephp\Tests\TestCase;

class UtilsManagerTest extends TestCase
{
    private function createManager(): UtilsManager
    {
        return new UtilsManager();
    }

    // ============================================
    // VALIDACAO
    // ============================================

    public function testValidateXml(): void
    {
        $manager = $this->createManager();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><root><test>value</test></root>';
        $fixturesDir = __DIR__ . '/../../../fixtures';
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
            $result = $manager->validateXml($xml, $xsd);
            $this->assertIsBool($result);
        } finally {
            if (file_exists($xsd)) {
                unlink($xsd);
            }
        }
    }

    public function testValidateCnpj(): void
    {
        $manager = $this->createManager();

        $this->assertTrue($manager->validateCnpj('12345678000195'));
        $this->assertFalse($manager->validateCnpj('12345678000190'));
    }

    public function testValidateCpf(): void
    {
        $manager = $this->createManager();

        $this->assertTrue($manager->validateCpf('11144477735'));
        $this->assertFalse($manager->validateCpf('12345678900'));
    }

    public function testValidateNFeKey(): void
    {
        $manager = $this->createManager();

        $this->assertIsBool($manager->validateNFeKey('35240112345678000195550010000000011234567801'));
        $this->assertFalse($manager->validateNFeKey('invalid_key'));
    }

    // ============================================
    // STRINGS
    // ============================================

    public function testCleanString(): void
    {
        $manager = $this->createManager();

        $result = $manager->cleanString('Teste com caracteres especiais: áéíóú');

        $this->assertIsString($result);
    }

    public function testStringToAscii(): void
    {
        $manager = $this->createManager();

        $result = $manager->stringToAscii('Teste com acentos: áéíóú');

        $this->assertIsString($result);
    }

    public function testEquilizeParameters(): void
    {
        $manager = $this->createManager();

        $std = new \stdClass();
        $std->test = 'value';
        $possible = ['test', 'other'];

        $result = $manager->equilizeParameters($std, $possible);

        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testFormatCnpj(): void
    {
        $manager = $this->createManager();

        $result = $manager->formatCnpj('12345678000195');

        $this->assertEquals('12.345.678/0001-95', $result);
    }

    public function testFormatCpf(): void
    {
        $manager = $this->createManager();

        $result = $manager->formatCpf('12345678901');

        $this->assertEquals('123.456.789-01', $result);
    }

    // ============================================
    // UF
    // ============================================

    public function testGetUfCode(): void
    {
        $manager = $this->createManager();

        $result = $manager->getUfCode('SP');

        $this->assertEquals(35, $result);
    }

    public function testGetUfByCode(): void
    {
        $manager = $this->createManager();

        $result = $manager->getUfByCode(35);

        $this->assertEquals('SP', $result);
    }

    public function testGetTimezoneByUf(): void
    {
        $manager = $this->createManager();

        $result = $manager->getTimezoneByUf('SP');

        $this->assertIsString($result);
    }

    public function testGenerateNFeKey(): void
    {
        $manager = $this->createManager();

        $result = $manager->generateNFeKey(
            '35',
            '2401',
            '12345678000195',
            '55',
            '1',
            '1',
            '1',
            '12345678'
        );

        $this->assertIsString($result);
        $this->assertGreaterThan(40, strlen($result));
    }
}
