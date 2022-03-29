<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Test', function ($Call) {
        $String = '';

        $Start = microtime(true);

        for ($a = 1; $a < $Call['Cycles']; $a++) {
            $String .= chr(rand(65, 90));
        }

        $Stop = microtime(true);
        return $Call['Cycles'] / ($Stop - $Start);
    });