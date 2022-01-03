<?php

namespace Polynar\Billingo;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Polynar\Billingo\
 */
class BillingoFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        //return 'billingo';
        return 'billingo'; // in composer.json the extra laravel aliases is: BillingoApiV3Wrapper
    }
}
