<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Write', function ($Call) {
        if (is_array($Call['Value'])) {
        } else {
            $Call['Value'] = explode(PHP_EOL, $Call['Value']);
        }

        foreach ($Call['Value'] as $IX => &$Value) {
            if (is_string($Value)) {
                $Value = trim($Value);
            } else {
                unset($Call['Value'][$IX]);
            }

            if (empty($Value)) {
                unset($Call['Value'][$IX]);
            }
        }

        return $Call['Value'];
    });

    setFn('Read', function ($Call) {
        return $Call['Value'];
    });

    setFn('Where', function ($Call) {
        return $Call['Value'];
    });
