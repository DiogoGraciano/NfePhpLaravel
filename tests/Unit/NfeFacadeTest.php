<?php

namespace DiogoGraciano\Nfephp\Tests\Unit;

use DiogoGraciano\Nfephp\Facades\Nfe;
use DiogoGraciano\Nfephp\Facades\Danfe;
use DiogoGraciano\Nfephp\Facades\Contingency;
use DiogoGraciano\Nfephp\Facades\Certificate;
use DiogoGraciano\Nfephp\Facades\Utils;
use DiogoGraciano\Nfephp\Managers\NfeManager;
use DiogoGraciano\Nfephp\Managers\DanfeManager;
use DiogoGraciano\Nfephp\Managers\ContingencyManager;
use DiogoGraciano\Nfephp\Managers\CertificateManager;
use DiogoGraciano\Nfephp\Managers\UtilsManager;
use DiogoGraciano\Nfephp\Tests\TestCase;

class NfeFacadeTest extends TestCase
{
    public function testNfeFacadeAccessor(): void
    {
        $reflection = new \ReflectionClass(Nfe::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('nfe', $method->invoke(null));
    }

    public function testNfeFacadeResolvesCorrectly(): void
    {
        $this->assertInstanceOf(NfeManager::class, Nfe::getFacadeRoot());
    }

    public function testDanfeFacadeAccessor(): void
    {
        $reflection = new \ReflectionClass(Danfe::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('danfe', $method->invoke(null));
    }

    public function testDanfeFacadeResolvesCorrectly(): void
    {
        $this->assertInstanceOf(DanfeManager::class, Danfe::getFacadeRoot());
    }

    public function testContingencyFacadeAccessor(): void
    {
        $reflection = new \ReflectionClass(Contingency::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('contingency', $method->invoke(null));
    }

    public function testContingencyFacadeResolvesCorrectly(): void
    {
        $this->assertInstanceOf(ContingencyManager::class, Contingency::getFacadeRoot());
    }

    public function testCertificateFacadeAccessor(): void
    {
        $reflection = new \ReflectionClass(Certificate::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('certificate', $method->invoke(null));
    }

    public function testCertificateFacadeResolvesCorrectly(): void
    {
        $this->assertInstanceOf(CertificateManager::class, Certificate::getFacadeRoot());
    }

    public function testUtilsFacadeAccessor(): void
    {
        $reflection = new \ReflectionClass(Utils::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('nfe-utils', $method->invoke(null));
    }

    public function testUtilsFacadeResolvesCorrectly(): void
    {
        $this->assertInstanceOf(UtilsManager::class, Utils::getFacadeRoot());
    }

    public function testUtilsFacadeMethodsWork(): void
    {
        $this->assertTrue(Utils::validateCnpj('12345678000195'));
    }
}
