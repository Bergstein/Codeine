<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */


    setFn('Write', function ($Call) {
        F::Run(
            'Entity',
            'Create',
            [
                'Entity' => 'Journal',
                'Data' =>
                    [
                        'Entity' => $Call['Entity'],
                        'Event' => $Call['Event']
                    ]
            ]
        );

        return $Call;
    });
