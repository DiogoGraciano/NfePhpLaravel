<?php

namespace DiogoGraciano\Nfephp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DiogoGraciano\Nfephp\Nfephp
 */
class NfephpFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'nfephp';
    }
}
