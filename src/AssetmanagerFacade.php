<?php
namespace Peltronic\Assetmanager;

use Illuminate\Support\Facades\Facade;

class AssetmanagerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'assetmanager';
    }
}
