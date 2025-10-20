<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Helpers;

use DiogoGraciano\Nfephp\Helpers\StringHelper;
use DiogoGraciano\Nfephp\Tests\TestCase;

class StringHelperTest extends TestCase
{
    public function testClean(): void
    {
        $string = "Teste com caracteres especiais: áéíóú";
        $result = StringHelper::clean($string);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testCleanWithEmptyString(): void
    {
        $result = StringHelper::clean('');
        $this->assertEquals('', $result);
    }

    public function testToAscii(): void
    {
        $string = "Teste com acentos: áéíóú";
        $result = StringHelper::toAscii($string);
        
        $this->assertIsString($result);
        $this->assertStringNotContainsString('á', $result);
        $this->assertStringNotContainsString('é', $result);
    }

    public function testEquilizeParameters(): void
    {
        $std = new \stdClass();
        $std->test = 'value';
        $possible = ['test', 'other'];
        
        $result = StringHelper::equilizeParameters($std, $possible);
        
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testEquilizeParametersWithReplaceAccentedChars(): void
    {
        $std = new \stdClass();
        $std->test = 'áéíóú';
        $possible = ['test'];
        
        $result = StringHelper::equilizeParameters($std, $possible, true);
        
        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testFormatCnpj(): void
    {
        $cnpj = '12345678000195';
        $result = StringHelper::formatCnpj($cnpj);
        
        $this->assertEquals('12.345.678/0001-95', $result);
    }

    public function testFormatCnpjWithInvalidLength(): void
    {
        $cnpj = '123456789';
        $result = StringHelper::formatCnpj($cnpj);
        
        $this->assertEquals($cnpj, $result);
    }

    public function testFormatCnpjWithSpecialCharacters(): void
    {
        $cnpj = '12.345.678/0001-95';
        $result = StringHelper::formatCnpj($cnpj);
        
        $this->assertEquals('12.345.678/0001-95', $result);
    }

    public function testFormatCpf(): void
    {
        $cpf = '12345678901';
        $result = StringHelper::formatCpf($cpf);
        
        $this->assertEquals('123.456.789-01', $result);
    }

    public function testFormatCpfWithInvalidLength(): void
    {
        $cpf = '123456789';
        $result = StringHelper::formatCpf($cpf);
        
        $this->assertEquals($cpf, $result);
    }

    public function testFormatCpfWithSpecialCharacters(): void
    {
        $cpf = '123.456.789-01';
        $result = StringHelper::formatCpf($cpf);
        
        $this->assertEquals('123.456.789-01', $result);
    }

    public function testFormatCep(): void
    {
        $cep = '12345678';
        $result = StringHelper::formatCep($cep);
        
        $this->assertEquals('12345-678', $result);
    }

    public function testFormatCepWithInvalidLength(): void
    {
        $cep = '1234567';
        $result = StringHelper::formatCep($cep);
        
        $this->assertEquals($cep, $result);
    }

    public function testFormatPhone10Digits(): void
    {
        $phone = '1234567890';
        $result = StringHelper::formatPhone($phone);
        
        $this->assertEquals('(12) 3456-7890', $result);
    }

    public function testFormatPhone11Digits(): void
    {
        $phone = '12345678901';
        $result = StringHelper::formatPhone($phone);
        
        $this->assertEquals('(12) 34567-8901', $result);
    }

    public function testFormatPhoneWithInvalidLength(): void
    {
        $phone = '123456789';
        $result = StringHelper::formatPhone($phone);
        
        $this->assertEquals($phone, $result);
    }

    public function testUnformatDocument(): void
    {
        $document = '12.345.678/0001-95';
        $result = StringHelper::unformatDocument($document);
        
        $this->assertEquals('12345678000195', $result);
    }

    public function testUnformatCep(): void
    {
        $cep = '12345-678';
        $result = StringHelper::unformatCep($cep);
        
        $this->assertEquals('12345678', $result);
    }

    public function testUnformatPhone(): void
    {
        $phone = '(12) 3456-7890';
        $result = StringHelper::unformatPhone($phone);
        
        $this->assertEquals('1234567890', $result);
    }

    public function testRandom(): void
    {
        $result = StringHelper::random(10);
        
        $this->assertIsString($result);
        $this->assertEquals(10, strlen($result));
    }

    public function testRandomWithCustomCharacters(): void
    {
        $characters = 'ABC123';
        $result = StringHelper::random(5, $characters);
        
        $this->assertIsString($result);
        $this->assertEquals(5, strlen($result));
        
        for ($i = 0; $i < strlen($result); $i++) {
            $this->assertStringContainsString($result[$i], $characters);
        }
    }

    public function testUpper(): void
    {
        $string = 'teste';
        $result = StringHelper::upper($string);
        
        $this->assertEquals('TESTE', $result);
    }

    public function testLower(): void
    {
        $string = 'TESTE';
        $result = StringHelper::lower($string);
        
        $this->assertEquals('teste', $result);
    }

    public function testCapitalize(): void
    {
        $string = 'teste de capitalização';
        $result = StringHelper::capitalize($string);
        
        $this->assertEquals('Teste De Capitalização', $result);
    }
}
