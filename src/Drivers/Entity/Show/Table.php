<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Element = F::Run('Entity', 'Read', $Call)[0];

        $Data = [];

        F::Map($Element, function ($Key, $Value, $D, $Fullkey) use ($Call, &$Data) {
            if (!is_array($Value)) {
                $Data[] = [
                    '<codeine-locale>' . $Call['Entity'] . '.Entity:' . substr(
                        $Fullkey,
                        1
                    ) . '</codeine-locale>',
                    $Value
                ];
            }
        });

        $Call['Output']['Content'][]
            =
            [
                'Type' => 'Table',
                'Value' => $Data
            ];

        return $Call;
    });