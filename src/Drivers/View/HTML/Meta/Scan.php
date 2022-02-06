<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Title', function ($Call)
    {
        if (preg_match_all('@<subtitle>(.*)<\/subtitle>@SsUu', $Call['Output'], $Pockets))
        {
            F::Log('*title* tag is deprecated. Use *codeine-seo-title* instead', LOG_WARNING, ['Developer', 'Deprecated']);
            $Call['View']['HTML']['Title'] = F::Merge($Call['View']['HTML']['Title'], $Pockets[1]);
            $Call['Output'] = str_replace($Pockets[0], '', $Call['Output']);
        }

        return $Call;
    });

    setFn('Keywords', function ($Call)
    {
        if (preg_match_all('@<keyword>(.*)<\/keyword>@SsUu', $Call['Output'], $Pockets))
        {
            F::Log('*keyword* tag is deprecated. Use *codeine-seo-keyword* instead', LOG_WARNING, ['Developer', 'Deprecated']);
            $Call['View']['HTML']['Keywords'] = F::Merge($Call['View']['HTML']['Keywords'], $Pockets[1]);
            $Call['Output'] = str_replace($Pockets[0], '', $Call['Output']);
        }

        return $Call;
    });

    setFn('Description', function ($Call)
    {
        if (preg_match_all('@<description>(.*)<\/description>@SsUu', $Call['Output'], $Pockets))
            {
                F::Log('*description* tag is deprecated. Use *codeine-seo-description* instead', LOG_WARNING, ['Developer', 'Deprecated']);
                $Call['View']['HTML']['Description'] = F::Merge($Call['View']['HTML']['Description'], $Pockets[1]);
                $Call['Output'] = str_replace($Pockets[0], '', $Call['Output']);
            }

        return $Call;
    });