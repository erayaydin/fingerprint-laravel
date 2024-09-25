<?php

namespace ErayAydin\Fingerprint\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \ErayAydin\Fingerprint\Fingerprint
 */
class Fingerprint extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fingerprint';
    }
}
