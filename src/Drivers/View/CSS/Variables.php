<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Process', function ($Call) {
        if (isset($Call['Map']) && is_array($Call['Map'])) {
            if (preg_match_all('@var\("(.*)"\)@SsUu', $Call['Value'], $Pockets)) {
                foreach ($Pockets[1] as $IX => $Match) {
                    if (isset($Call['Map'][$Match])) {
                        $Call['Value'] = str_replace($Pockets[0][$IX], $Call['Map'][$Match], $Call['Value']);
                    }
                }
            }
        }

        return $Call['Value'];
    });