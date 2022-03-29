<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Exec Parslet
     * @package Codeine
     * @version 8.x
     */

    setFn('Parse', function ($Call) {
        foreach ($Call['Parsed']['Match'] as $IX => $Match) {
            $Call['Run'] = [];

            unset($Call['Weight'], $Call['Decision']);

            foreach ($Call['Parsed']['Options'][$IX] as $Key => $Value) {
                $Call['Run'] = F::Dot($Call['Run'], $Key, $Value);
            }

            if (F::Run('Security.Access', 'Check', $Call, $Call['Run'])) {
                $Call['Replace'][$Call['Parsed']['Match'][$IX]] = $Call['Parsed']['Value'][$IX];
            } else {
                $Call['Replace'][$Call['Parsed']['Match'][$IX]] = '';
            }
        }

        return $Call;
    });