<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Write', function ($Call)
    {
        if (isset($Call['Value']) && !empty($Call['Value']) && $Call['Value']>0)
        {
            if (is_numeric($Call['Value']))
                return (int) $Call['Value'];
            else
            {
                $Date = date_parse_from_format($Call['Date Format'], $Call['Value']);

                return mktime($Date['hour'],$Date['minute'],$Date['second'], $Date['month'], $Date['day'], $Date['year']);
            }
        }
        else
            return null;

    });

    setFn(['Read', 'Where'], function ($Call)
    {
        return (int) $Call['Value']; // Separate Where?
    });

    setFn('Populate', function ($Call)
    {
        return rand(0, time());
    });