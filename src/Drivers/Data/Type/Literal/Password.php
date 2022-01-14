<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Write', function ($Call)
    {
        if (empty($Call['Value']))
            $Call['Value'] = $Call['Old'];
        else
        {
            if ($Call['Value'] == $Call['Old'])
                ;
            else
                $Call['Value'] = F::Run('Security.Hash', 'Get',
                [
                    'Security' =>
                    [
                        'Hash' =>
                        [
                            'Mode' => 'Password'
                        ]
                    ],
                    'Value' => $Call['Value']
                ]);
        }
        
        return $Call['Value'];
    });

    setFn(['Read', 'Where'], function ($Call)
    {
        return $Call['Value'];
    });