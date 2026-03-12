<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Managers;

use DiogoGraciano\Nfephp\Managers\DanfeManager;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Mockery;

class DanfeManagerTest extends TestCase
{
    private function getNfeXml(): string
    {
        return file_get_contents(__DIR__ . '/../../fixtures/nfe.xml');
    }

    public function testConstructorWithoutLogo(): void
    {
        $manager = new DanfeManager();

        $this->assertInstanceOf(DanfeManager::class, $manager);
    }

    public function testConstructorWithLogo(): void
    {
        $manager = new DanfeManager('/path/to/logo.png');

        $this->assertInstanceOf(DanfeManager::class, $manager);
    }

    public function testSetLogo(): void
    {
        $manager = new DanfeManager();
        $manager->setLogo('/path/to/logo.png');

        $this->assertInstanceOf(DanfeManager::class, $manager);
    }

    public function testSetLogoNull(): void
    {
        $manager = new DanfeManager('/path/to/logo.png');
        $manager->setLogo(null);

        $this->assertInstanceOf(DanfeManager::class, $manager);
    }

    // ============================================
    // TESTES DE SUCESSO - GENERATE (XML VÁLIDO)
    // ============================================

    public function testGenerateDanfeWithValidXml(): void
    {
        $manager = new DanfeManager();
        $pdf = $manager->generateDanfe($this->getNfeXml());

        $this->assertNotEmpty($pdf);
        $this->assertIsString($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    public function testGenerateDanfeSimplesWithValidXml(): void
    {
        $manager = new DanfeManager();
        $pdf = $manager->generateDanfeSimples($this->getNfeXml());

        $this->assertNotEmpty($pdf);
        $this->assertIsString($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
    }

    // ============================================
    // TESTES DE SUCESSO - SAVE (Storage do Laravel)
    // ============================================

    public function testSaveDanfeWithValidXml(): void
    {
        Storage::fake('local');

        $manager = new DanfeManager();
        $manager->saveDanfe($this->getNfeXml(), 'danfes/danfe_test.pdf', 'local');

        Storage::disk('local')->assertExists('danfes/danfe_test.pdf');
    }

    public function testSaveDanfeSimplesWithValidXml(): void
    {
        Storage::fake('local');

        $manager = new DanfeManager();
        $manager->saveDanfeSimples($this->getNfeXml(), 'danfes/danfe_simples_test.pdf', 'local');

        Storage::disk('local')->assertExists('danfes/danfe_simples_test.pdf');
    }

    // ============================================
    // TESTES DE SUCESSO - DOWNLOAD (Response HTTP)
    // ============================================

    public function testDownloadDanfeWithValidXml(): void
    {
        $manager = new DanfeManager();
        $response = $manager->downloadDanfe($this->getNfeXml(), 'nota.pdf');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('nota.pdf', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function testDownloadDanfeSimplesWithValidXml(): void
    {
        $manager = new DanfeManager();
        $response = $manager->downloadDanfeSimples($this->getNfeXml());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    // ============================================
    // TESTES DE SUCESSO - RENDER INLINE (Response HTTP)
    // ============================================

    public function testRenderDanfeWithValidXml(): void
    {
        $manager = new DanfeManager();
        $response = $manager->renderDanfe($this->getNfeXml(), 'nota.pdf');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('nota.pdf', $response->headers->get('Content-Disposition'));
    }

    public function testRenderDanfeSimplesWithValidXml(): void
    {
        $manager = new DanfeManager();
        $response = $manager->renderDanfeSimples($this->getNfeXml());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    // ============================================
    // TESTES DE ERRO COM XML INVÁLIDO
    // ============================================

    public function testGenerateDanfeWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $manager = new DanfeManager();
        $manager->generateDanfe('invalid xml');
    }

    public function testGenerateDanfeWithEmptyXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $manager = new DanfeManager();
        $manager->generateDanfe('');
    }

    public function testSaveDanfeWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        Storage::fake('local');

        $manager = new DanfeManager();
        $manager->saveDanfe('invalid xml', 'danfe_test.pdf', 'local');
    }

    public function testDownloadDanfeWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $manager = new DanfeManager();
        $manager->downloadDanfe('invalid xml');
    }

    public function testRenderDanfeWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $manager = new DanfeManager();
        $manager->renderDanfe('invalid xml');
    }

    public function testGenerateDanfceWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFCe:');

        $manager = new DanfeManager();
        $manager->generateDanfce('invalid xml');
    }

    public function testGenerateDanfceWithEmptyXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFCe:');

        $manager = new DanfeManager();
        $manager->generateDanfce('');
    }

    public function testSaveDanfceWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFCe:');

        Storage::fake('local');

        $manager = new DanfeManager();
        $manager->saveDanfce('invalid xml', 'danfce_test.pdf', 'local');
    }

    public function testGenerateDanfeSimplesWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE simplificado:');

        $manager = new DanfeManager();
        $manager->generateDanfeSimples('invalid xml');
    }

    public function testGenerateDanfeSimplesWithEmptyXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE simplificado:');

        $manager = new DanfeManager();
        $manager->generateDanfeSimples('');
    }

    public function testSaveDanfeSimplesWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE simplificado:');

        Storage::fake('local');

        $manager = new DanfeManager();
        $manager->saveDanfeSimples('invalid xml', 'danfe_simples_test.pdf', 'local');
    }

    public function testGenerateDaeventoWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar documento de evento:');

        $manager = new DanfeManager();
        $manager->generateDaevento('invalid xml');
    }

    public function testGenerateDaeventoWithEmptyXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar documento de evento:');

        $manager = new DanfeManager();
        $manager->generateDaevento('');
    }

    public function testSaveDaeventoWithInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar documento de evento:');

        Storage::fake('local');

        $manager = new DanfeManager();
        $manager->saveDaevento('invalid xml', 'daevento_test.pdf');
    }

    public function testGenerateDaeventoWithDadosEmitente(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar documento de evento:');

        $manager = new DanfeManager();
        $manager->generateDaevento('invalid xml', [
            'razao' => 'Empresa Teste',
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'CEP' => '01001000',
            'municipio' => 'São Paulo',
            'UF' => 'SP',
        ]);
    }

    public function testGenerateDanfeWithLogoAndInvalidXml(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao gerar DANFE:');

        $manager = new DanfeManager('/path/to/logo.png');
        $manager->generateDanfe('invalid xml');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
