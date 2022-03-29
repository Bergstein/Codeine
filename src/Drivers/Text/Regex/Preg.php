<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Match', function ($Call) {
        if (preg_match('@' . $Call['Pattern'] . '@SsUu', $Call['Value'], $Pockets)) {
            ;
        } else {
            $Pockets = false;
        }

        return $Pockets;
    });

    setFn('All', function ($Call) {
        if (preg_match_all('@' . $Call['Pattern'] . '@Ssu', $Call['Value'], $Pockets)) {
            return $Pockets;
        } else {
            return false;
        }
    });

