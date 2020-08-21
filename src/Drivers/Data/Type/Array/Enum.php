<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn ('Write', function ($Call)
    {
        $Call['Node']['Options'] = F::Live($Call['Node']['Options']);

        $Data = [];

        if (is_array($Call['Value']))
            foreach ($Call['Value'] as $Value)
            {
                if (($Value = array_search($Value, $Call['Node']['Options'])) !== false)
                    $Data[] = $Value;
            }

        return $Data;
    });

    setFn('Read', function ($Call)
    {
        $Call['Node']['Options'] = F::Live($Call['Node']['Options']);

        if (is_array($Call['Value']))
            foreach ($Call['Value'] as &$Value)
                if (isset($Call['Node']['Options'][$Value]))
                    $Value = $Call['Node']['Options'][$Value];

        return $Call['Value'];
    });

    setFn('Where', function ($Call)
    {
        $Call['Node']['Options'] = F::Live($Call['Node']['Options']);

        $Data = [];

        foreach ($Call['Value'] as &$Value)
        {
            $Key = array_search($Value, $Call['Node']['Options']);
            if ($Key === false)
                if (isset($Call['Node']['Options'][$Value]))
                    $Key = $Value;

            $Data[] = $Key;
        }

        return $Data;
    });

    setFn('Populate', function ($Call)
    {
        return [array_rand(F::Live($Call['Node']['Options']))];
    });