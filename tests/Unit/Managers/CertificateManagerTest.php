<?php

namespace DiogoGraciano\Nfephp\Tests\Unit\Managers;

use DiogoGraciano\Nfephp\Managers\CertificateManager;
use DiogoGraciano\Nfephp\Tests\TestCase;
use Mockery;
use NFePHP\Common\Certificate;

class CertificateManagerTest extends TestCase
{
    public function testConstructorWithCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $manager = new CertificateManager($certificate);
        
        $this->assertInstanceOf(CertificateManager::class, $manager);
    }

    public function testConstructorWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $this->assertInstanceOf(CertificateManager::class, $manager);
    }

    public function testSetCertificate(): void
    {
        $manager = new CertificateManager();
        $certificate = Mockery::mock(Certificate::class);
        
        $manager->setCertificate($certificate);
        
        $this->assertInstanceOf(CertificateManager::class, $manager);
    }

    public function testGetInfoWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->getInfo();
        
        $this->assertNull($result);
    }

    public function testGetInfoWithCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCnpj')->andReturn('12345678000195');
        $certificate->shouldReceive('getCpf')->andReturn('12345678901');
        $certificate->shouldReceive('getCompanyName')->andReturn('Empresa Teste');
        $certificate->shouldReceive('getValidFrom')->andReturn(new \DateTime('2024-01-01'));
        $certificate->shouldReceive('getValidTo')->andReturn(new \DateTime('2025-01-01'));
        $certificate->shouldReceive('getICP')->andReturn('ICP Teste');
        $certificate->shouldReceive('getCAurl')->andReturn('http://teste.com');
        $certificate->shouldReceive('getCSP')->andReturn('CSP Teste');
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getInfo();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('cnpj', $result);
        $this->assertArrayHasKey('cpf', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('valid_from', $result);
        $this->assertArrayHasKey('valid_to', $result);
        $this->assertArrayHasKey('icp', $result);
        $this->assertArrayHasKey('ca_url', $result);
        $this->assertArrayHasKey('csp', $result);
    }

    public function testGetInfoWithException(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCnpj')->andThrow(new \Exception('Test exception'));
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getInfo();
        
        $this->assertNull($result);
    }

    public function testIsValidWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->isValid();
        
        $this->assertFalse($result);
    }

    public function testIsValidWithValidCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('isExpired')->andReturn(false);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->isValid();
        
        $this->assertTrue($result);
    }

    public function testIsValidWithExpiredCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('isExpired')->andReturn(true);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->isValid();
        
        $this->assertFalse($result);
    }

    public function testIsValidWithException(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('isExpired')->andThrow(new \Exception('Test exception'));
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->isValid();
        
        $this->assertFalse($result);
    }

    public function testGetDaysToExpireWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->getDaysToExpire();
        
        $this->assertNull($result);
    }

    public function testGetDaysToExpireWithCertificate(): void
    {
        $validTo = new \DateTime('+30 days');
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getValidTo')->andReturn($validTo);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getDaysToExpire();
        
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testGetDaysToExpireWithExpiredCertificate(): void
    {
        $validTo = new \DateTime('-30 days');
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getValidTo')->andReturn($validTo);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getDaysToExpire();
        
        $this->assertEquals(0, $result);
    }

    public function testGetDaysToExpireWithNullValidTo(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getValidTo')->andReturn(null);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getDaysToExpire();
        
        $this->assertNull($result);
    }

    public function testGetDaysToExpireWithException(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getValidTo')->andThrow(new \Exception('Test exception'));
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getDaysToExpire();
        
        $this->assertNull($result);
    }

    public function testIsNearExpiration(): void
    {
        $validTo = new \DateTime('+15 days');
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getValidTo')->andReturn($validTo);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->isNearExpiration(30);
        
        $this->assertTrue($result);
    }

    public function testIsNearExpirationNotNear(): void
    {
        $validTo = new \DateTime('+60 days');
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getValidTo')->andReturn($validTo);
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->isNearExpiration(30);
        
        $this->assertFalse($result);
    }

    public function testIsNearExpirationWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->isNearExpiration(30);
        
        $this->assertFalse($result);
    }

    public function testGetCnpjWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->getCnpj();
        
        $this->assertNull($result);
    }

    public function testGetCnpjWithCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCnpj')->andReturn('12345678000195');
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getCnpj();
        
        $this->assertEquals('12345678000195', $result);
    }

    public function testGetCnpjWithException(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCnpj')->andThrow(new \Exception('Test exception'));
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getCnpj();
        
        $this->assertNull($result);
    }

    public function testGetCpfWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->getCpf();
        
        $this->assertNull($result);
    }

    public function testGetCpfWithCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCpf')->andReturn('12345678901');
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getCpf();
        
        $this->assertEquals('12345678901', $result);
    }

    public function testGetCpfWithException(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCpf')->andThrow(new \Exception('Test exception'));
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getCpf();
        
        $this->assertNull($result);
    }

    public function testGetCompanyNameWithoutCertificate(): void
    {
        $manager = new CertificateManager();
        
        $result = $manager->getCompanyName();
        
        $this->assertNull($result);
    }

    public function testGetCompanyNameWithCertificate(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCompanyName')->andReturn('Empresa Teste');
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getCompanyName();
        
        $this->assertEquals('Empresa Teste', $result);
    }

    public function testGetCompanyNameWithException(): void
    {
        $certificate = Mockery::mock(Certificate::class);
        $certificate->shouldReceive('getCompanyName')->andThrow(new \Exception('Test exception'));
        
        $manager = new CertificateManager($certificate);
        
        $result = $manager->getCompanyName();
        
        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
