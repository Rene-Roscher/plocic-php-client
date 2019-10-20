<?php
/**
 * Created by PhpStorm.
 * User: Rene_Roscher
 * Date: 20.10.2019
 * Time: 02:11
 */

namespace Plocic;


class PlocicFacade
{

    /**
     * @return Plocic
     */
    public static function client()
    {
        return new Plocic('authtoken', 'uri');
    }

}