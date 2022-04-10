<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call) {
        $Data = [];

        if (count($Call['Value']) > 0) {
            foreach ($Call['Value'] as $K => $V) {
                if ($K > 0) {
                    $Data[] = [$Call['Name'] => $V];
                }
            }
        }

        $Call['Data'] = j($Data, JSON_UNESCAPED_UNICODE);

        return $Call;
    });
