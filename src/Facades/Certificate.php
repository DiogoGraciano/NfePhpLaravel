<?php

namespace DiogoGraciano\Nfephp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Managers\CertificateManager
 *
 * @method static array|null getInfo()
 * @method static bool isValid()
 * @method static int|null getDaysToExpire()
 * @method static bool isNearExpiration(int $daysThreshold = 30)
 * @method static string|null getCnpj()
 * @method static string|null getCpf()
 * @method static string|null getCompanyName()
 * @method static void setCertificate(?\NFePHP\Common\Certificate $certificate)
 */
class Certificate extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'certificate';
    }
}
