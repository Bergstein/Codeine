<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Check', function ($Call) {
        if (time() % $Call['Period'] == 0) {
            sleep(1);
            return $Call;
        } else {
            return null;
        }
    });