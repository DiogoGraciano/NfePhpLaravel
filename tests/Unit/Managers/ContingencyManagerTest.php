<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Managers;

use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Mockery;

class ContingencyManagerTest extends TestCase
{
    public function testActivate(): void
    {
        $manager = new ContingencyManager();
        
        $result = $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testActivateWithEmptyType(): void
    {
        $manager = new ContingencyManager();
        
        $result = $manager->activate('SP', 'Teste de contingência');
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testActivateWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao ativar contingência:');
        
        $manager = new ContingencyManager();
        
        // Simular erro passando parâmetros inválidos
        $manager->activate('', '', 'INVALID');
    }

    public function testDeactivateWithExistingContingency(): void
    {
        $manager = new ContingencyManager();
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        $result = $manager->deactivate();
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testDeactivateWithoutExistingContingency(): void
    {
        $manager = new ContingencyManager();
        
        $result = $manager->deactivate();
        
        $this->assertIsString($result);
        $this->assertJson($result);
    }

    public function testIsActiveWhenActive(): void
    {
        $manager = new ContingencyManager();
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        $result = $manager->isActive();
        
        $this->assertTrue($result);
    }

    public function testIsActiveWhenInactive(): void
    {
        $manager = new ContingencyManager();
        
        $result = $manager->isActive();
        
        $this->assertFalse($result);
    }

    public function testIsActiveAfterDeactivate(): void
    {
        $manager = new ContingencyManager();
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        $manager->deactivate();
        
        $result = $manager->isActive();
        
        $this->assertFalse($result);
    }

    public function testGetInfoWhenActive(): void
    {
        $manager = new ContingencyManager();
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        $result = $manager->getInfo();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('motive', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('tpEmis', $result);
    }

    public function testGetInfoWhenInactive(): void
    {
        $manager = new ContingencyManager();
        
        $result = $manager->getInfo();
        
        $this->assertNull($result);
    }

    public function testAdjustXmlWhenActive(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao ajustar XML para contingência:');
        
        $manager = new ContingencyManager();
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?><nfeProc><NFe><infNFe><ide><tpEmis>1</tpEmis></ide></infNFe></NFe></nfeProc>';
        
        $manager->adjustXml($xml);
    }

    public function testAdjustXmlWhenInactive(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Nenhuma contingência ativa. Use activate() primeiro.');
        
        $manager = new ContingencyManager();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?><nfeProc><NFe><infNFe><ide><tpEmis>1</tpEmis></ide></infNFe></NFe></nfeProc>';
        
        $manager->adjustXml($xml);
    }

    public function testAdjustXmlWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao ajustar XML para contingência:');
        
        $manager = new ContingencyManager();
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        // XML inválido para causar exceção
        $xml = 'invalid xml';
        
        $manager->adjustXml($xml);
    }

    public function testLoad(): void
    {
        $manager = new ContingencyManager();
        
        $contingencyJson = '{"type":"SVCAN","motive":"Teste","timestamp":"2024-01-01T00:00:00Z","tpEmis":"9"}';
        
        $manager->load($contingencyJson);
        
        $this->assertTrue($manager->isActive());
    }

    public function testLoadWithException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao carregar contingência:');
        
        $manager = new ContingencyManager();
        
        // JSON inválido para causar exceção
        $contingencyJson = 'invalid json';
        
        $manager->load($contingencyJson);
    }

    public function testGetContingency(): void
    {
        $manager = new ContingencyManager();
        
        $result = $manager->getContingency();
        
        $this->assertNull($result);
        
        $manager->activate('SP', 'Teste de contingência', 'SVCAN');
        
        $result = $manager->getContingency();
        
        $this->assertNotNull($result);
    }
}
