<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        foreach ($Call['Terms'] as $Term) {
            $Call['Output']['Content'][] =
                [
                    'Type' => 'Heading',
                    'Level' => 3,
                    'Value' => '<codeine-locale>Project.Terms:Rule.' . $Term . '</codeine-locale>'
                ];
        }

        return $Call;
    });

    setFn('RAW', function ($Call) {
        return $Call['Terms'];
    });