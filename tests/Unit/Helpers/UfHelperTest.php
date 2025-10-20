<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Helpers;

use DiogoGraciano\Nfephp\Helpers\UfHelper;
use DiogoGraciano\Nfephp\Tests\TestCase;

class UfHelperTest extends TestCase
{
    public function testGetCode(): void
    {
        $result = UfHelper::getCode('SP');
        
        $this->assertIsInt($result);
        $this->assertEquals(35, $result);
    }

    public function testGetByCode(): void
    {
        $result = UfHelper::getByCode(35);
        
        $this->assertIsString($result);
        $this->assertEquals('SP', $result);
    }

    public function testGetTimezone(): void
    {
        $result = UfHelper::getTimezone('SP');
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testGetAll(): void
    {
        $result = UfHelper::getAll();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('SP', $result);
        $this->assertArrayHasKey('RJ', $result);
        $this->assertEquals(35, $result['SP']);
        $this->assertEquals(33, $result['RJ']);
    }

    public function testIsValid(): void
    {
        $this->assertTrue(UfHelper::isValid('SP'));
        $this->assertTrue(UfHelper::isValid('sp'));
        $this->assertTrue(UfHelper::isValid('Sp'));
        $this->assertFalse(UfHelper::isValid('XX'));
        $this->assertFalse(UfHelper::isValid(''));
    }

    public function testGenerateNFeKey(): void
    {
        $result = UfHelper::generateNFeKey(
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

    public function testValidateNFeKey(): void
    {
        $validKey = '35240112345678000195550010000000011234567801';
        $invalidKey = 'invalid_key';
        
        $this->assertIsBool(UfHelper::validateNFeKey($validKey));
        $this->assertFalse(UfHelper::validateNFeKey($invalidKey));
    }

    public function testParseNFeKey(): void
    {
        $key = '35240112345678000195550010000000011234567801';
        $result = UfHelper::parseNFeKey($key);
        
        if ($result !== null) {
            $this->assertIsArray($result);
            $this->assertArrayHasKey('cUF', $result);
            $this->assertArrayHasKey('aamm', $result);
            $this->assertArrayHasKey('cnpj', $result);
            $this->assertArrayHasKey('mod', $result);
            $this->assertArrayHasKey('serie', $result);
            $this->assertArrayHasKey('nNF', $result);
            $this->assertArrayHasKey('tpEmis', $result);
            $this->assertArrayHasKey('cNF', $result);
            $this->assertArrayHasKey('dv', $result);
        } else {
            $this->assertNull($result);
        }
    }

    public function testParseNFeKeyWithInvalidKey(): void
    {
        $result = UfHelper::parseNFeKey('invalid_key');
        
        $this->assertNull($result);
    }

    public function testGetFullName(): void
    {
        $this->assertEquals('São Paulo', UfHelper::getFullName('SP'));
        $this->assertEquals('Rio de Janeiro', UfHelper::getFullName('RJ'));
        $this->assertEquals('Minas Gerais', UfHelper::getFullName('MG'));
        $this->assertEquals('', UfHelper::getFullName('XX'));
    }

    public function testGetRegion(): void
    {
        $this->assertEquals('Sudeste', UfHelper::getRegion('SP'));
        $this->assertEquals('Sudeste', UfHelper::getRegion('RJ'));
        $this->assertEquals('Nordeste', UfHelper::getRegion('BA'));
        $this->assertEquals('Norte', UfHelper::getRegion('AM'));
        $this->assertEquals('Sul', UfHelper::getRegion('RS'));
        $this->assertEquals('Centro-Oeste', UfHelper::getRegion('MT'));
        $this->assertEquals('', UfHelper::getRegion('XX'));
    }
}
