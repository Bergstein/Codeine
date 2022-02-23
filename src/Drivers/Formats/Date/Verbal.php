<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Date() engine 
     * @package Codeine
     * @version 8.x
     */

    setFn('Format', function ($Call)
    {
        $Values = file(F::findFile('Assets/Formats/Date/Verbal/Locale/ru/values.csv')); // FIXME Correct locale

        $H = date('H',$Call['Value']);
        $M = date('m',$Call['Value']);

        $IX = ($H*60).$M;

        if (isset($Values[$IX]))
        {
            $Variants = explode(',', $Values[$IX]);
            return mb_strtolower($Variants[array_rand($Variants)]);
        }
        else
             return null;
    });