<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Clean', function ($Call) {
        // Cleanup unused places
        $Call['Output'] = preg_replace('@<place>(.+)</place>@', '', $Call['Output']);
        return $Call;
    });
