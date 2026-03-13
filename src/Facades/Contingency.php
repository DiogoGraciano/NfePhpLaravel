<?php

namespace DiogoGraciano\Nfephp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Managers\ContingencyManager
 *
 * @method static string activate(string $acronym, string $motive, string $type = '')
 * @method static string deactivate()
 * @method static bool isActive()
 * @method static array|null getInfo()
 * @method static string adjustXml(string $xml)
 * @method static void load(string $contingencyJson)
 * @method static \NFePHP\NFe\Factories\Contingency|null getContingency()
 */
class Contingency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'contingency';
    }
}
