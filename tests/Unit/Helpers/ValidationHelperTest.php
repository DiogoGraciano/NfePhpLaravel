<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Helpers;

use DiogoGraciano\Nfephp\Helpers\ValidationHelper;
use DiogoGraciano\Nfephp\Tests\TestCase;

class ValidationHelperTest extends TestCase
{
    public function testValidateXml(): void
    {
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
            $result = ValidationHelper::validateXml($xml, $xsd);
            $this->assertIsBool($result);
        } finally {
            if (file_exists($xsd)) {
                unlink($xsd);
            }
        }
    }

    public function testValidateXmlWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro na validação XML:');
        
        ValidationHelper::validateXml('invalid xml', 'nonexistent.xsd');
    }

    public function testValidateNFeKey(): void
    {
        $validKey = '35240112345678000195550010000000011234567801';
        $invalidKey = 'invalid_key';
        
        $this->assertIsBool(ValidationHelper::validateNFeKey($validKey));
        $this->assertFalse(ValidationHelper::validateNFeKey($invalidKey));
    }

    public function testValidateCnpj(): void
    {
        // CNPJ válido
        $this->assertTrue(ValidationHelper::validateCnpj('12345678000195'));
        $this->assertTrue(ValidationHelper::validateCnpj('12.345.678/0001-95'));
        
        // CNPJ inválido
        $this->assertFalse(ValidationHelper::validateCnpj('12345678000190'));
        $this->assertFalse(ValidationHelper::validateCnpj('11111111111111'));
        $this->assertFalse(ValidationHelper::validateCnpj('123456789'));
        $this->assertFalse(ValidationHelper::validateCnpj(''));
    }

    public function testValidateCpf(): void
    {
        // CPF válido
        $this->assertTrue(ValidationHelper::validateCpf('11144477735'));
        $this->assertTrue(ValidationHelper::validateCpf('111.444.777-35'));
        
        // CPF inválido
        $this->assertFalse(ValidationHelper::validateCpf('12345678900'));
        $this->assertFalse(ValidationHelper::validateCpf('11111111111'));
        $this->assertFalse(ValidationHelper::validateCpf('123456789'));
        $this->assertFalse(ValidationHelper::validateCpf(''));
    }

    public function testValidateCep(): void
    {
        // CEP válido
        $this->assertTrue(ValidationHelper::validateCep('12345678'));
        $this->assertTrue(ValidationHelper::validateCep('12345-678'));
        
        // CEP inválido
        $this->assertFalse(ValidationHelper::validateCep('1234567'));
        $this->assertFalse(ValidationHelper::validateCep('123456789'));
        $this->assertFalse(ValidationHelper::validateCep(''));
    }

    public function testValidateEmail(): void
    {
        // Email válido
        $this->assertTrue(ValidationHelper::validateEmail('test@example.com'));
        $this->assertTrue(ValidationHelper::validateEmail('user.name@domain.co.uk'));
        
        // Email inválido
        $this->assertFalse(ValidationHelper::validateEmail('invalid-email'));
        $this->assertFalse(ValidationHelper::validateEmail('test@'));
        $this->assertFalse(ValidationHelper::validateEmail('@example.com'));
        $this->assertFalse(ValidationHelper::validateEmail(''));
    }

    public function testValidatePhone(): void
    {
        // Telefone válido (10 dígitos)
        $this->assertTrue(ValidationHelper::validatePhone('1234567890'));
        $this->assertTrue(ValidationHelper::validatePhone('(12) 3456-7890'));
        
        // Telefone válido (11 dígitos)
        $this->assertTrue(ValidationHelper::validatePhone('12345678901'));
        $this->assertTrue(ValidationHelper::validatePhone('(12) 34567-8901'));
        
        // Telefone inválido
        $this->assertFalse(ValidationHelper::validatePhone('123456789'));
        $this->assertFalse(ValidationHelper::validatePhone('123456789012'));
        $this->assertFalse(ValidationHelper::validatePhone(''));
    }
}
