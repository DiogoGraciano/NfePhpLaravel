<?php

namespace DiogoGraciano\Nfephp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Nfse
 *
 * @method static string createDps(\stdClass $std)
 * @method static string createDpsEvento(\stdClass $std)
 * @method static string|array sendDps(string $xml)
 * @method static string consultNfseByKey(string $chave, bool $encoding = true)
 * @method static string consultDpsByKey(string $chave)
 * @method static string consultNfseEvents(string $chave, ?int $tipoEvento = null, ?int $nSequencial = null)
 * @method static string getDanfse(string $chave)
 * @method static string cancelNfse(\stdClass $std)
 * @method static array getConfig()
 * @method static void setConfig(array $config)
 */
class Nfse extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'nfse';
    }
}
