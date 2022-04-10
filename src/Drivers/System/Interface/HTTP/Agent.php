<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Call['HTTP']['Agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        F::Log($Call['HTTP']['Agent'], LOG_INFO);

        return $Call;
    });
