<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        if (isset($Call['Data']['AgeLimit']) && isset($Call['Session']['User']['Age']) && $Call['Session']['User']['Age'] < $Call['Data']['AgeLimit']) {
            $Call['Value'] = F::Run(
                'View',
                'Load',
                ['Scope' => 'Error', 'ID' => 'Age', 'Data' => ['Level' => $Call['Data']['AgeLimit']]]
            );
        }

        return $Call;
    });
