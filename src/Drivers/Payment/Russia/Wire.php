<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Call['Receiver'] = F::Run(
            'Entity',
            'Read',
            $Call,
            [
                'Entity' => 'Company',
                'Where' => 1,
                'One' => true
            ]
        );

        return $Call;
    });