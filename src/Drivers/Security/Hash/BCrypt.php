<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description: Standart MD5
     * @package Codeine
     * @version 8.x
     * @date 22.11.10
     * @time 4:40
     */

    setFn('Get', function ($Call) {
        return crypt($Call['Value'], F::Dot($Call, 'Security.Hash.Salt'));
    });
