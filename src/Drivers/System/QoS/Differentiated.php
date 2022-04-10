<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Start', function ($Call) {
        foreach ($Call['QoS']['Rules'] as $Name => $Rule) {
            if ($Rule['Weight'] >= $Call['QoS']['Weight']) {
                if (isset($Rule['Run']) && (F::Diff($Rule['Run'], $Call) === null)) {
                    $Call['QoS']['Class'] = $Rule['Class'];
                    $Call['QoS']['Weight'] = $Rule['Weight'];
                }
            }
        }

        return $Call;
    });

    setFn('Finish', function ($Call) {
        return $Call;
    });
