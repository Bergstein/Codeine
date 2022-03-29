<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Entities = F::Run('Entity', 'Read', $Call, ['Sort' => ['ID' => -1], 'No Page' => true]);

        foreach ($Entities as $Entity) {
            $Count = F::Run('Entity', 'Count', $Call, ['Where' => $Entity['ID']]);

            if ($Count > 1) {
                F::Run(
                    'Entity',
                    'Delete',
                    $Call,
                    [
                        'Where' => $Entity['ID'],
                        'Mongo' => ['Just One' => true]
                    ]
                );
            }

            echo $Entity['ID'] . ':' . $Count . PHP_EOL;
        }

        return $Call;
    });