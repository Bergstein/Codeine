<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description: Random integer
     * @package Codeine
     * @version 8.x
     * @date 04.12.10
     * @time 14:56
     */

    setFn('Get', function ($Call) {
        return rand(0, 2147483647);
    });
