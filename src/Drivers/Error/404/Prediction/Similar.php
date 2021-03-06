<?php

/* Codeine
     * @author bergstein@trickyplan.com
     * @description Similar text & Static Links 
     * @package Codeine
     * @version 8.x
     */

    setFn('Suggest', function ($Call)
    {
        $Keys = array_keys($Call['Request']);
        $Static = F::loadOptions('Code.Routing.Static');
        $Levels = array ();

        if (isset($Keys[0]))
        {
            foreach ($Static['Links'] as $URL => $Link)
                $Levels[$URL] = similar_text($URL, $Keys[0]);
            asort($Levels);

            return array_pop(array_keys($Levels));
        }
        else
            return null;
    });