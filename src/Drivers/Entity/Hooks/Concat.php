<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Output = [];

        foreach ($Call['Keys'] as $Key) {
            $OutputTemp = F::Dot($Call['Data'], $Key);
            $Output[] = is_array($OutputTemp) ? implode($Call['Glue'], $OutputTemp) : $OutputTemp;
        }

        $Output = implode($Call['Glue'], $Output);

        if (isset($Call['Hash']) && $Call['Hash'] === true) {
            $Output = sha1($Output);
        }

        return $Output;
    });
