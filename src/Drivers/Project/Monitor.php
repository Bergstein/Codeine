<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        foreach ($Call['Sensors'] as $Sensor) {
            $Sensors[$Sensor] = F::Run(null, $Sensor, $Call);
        }

        return $Sensors;
    });

    setFn('Version', function ($Call) {
        return F::loadOptions('Version')['Project'];
    });