<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call) {
        $Call['HTML'] = '';

        for ($IC = 1; $IC <= $Call['Stars']; $IC++) {
            $StarData = ['Num' => $IC];

            if (isset($Call['Value']) && $Call['Value'] == $IC) {
                $StarData['Checked'] = 'checked';
            }

            $Call['HTML'] .= F::Run(
                'View',
                'Load',
                [
                    'Scope' => $Call['View']['HTML']['Widget Set'] . '/Widgets',
                    'ID' => 'Form/Star',
                    'Data' => F::Merge($Call, $StarData)
                ]
            );
        }

        $Call['Value'] = $Call['HTML'];
        return $Call;
    });
