<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call) {
        return '<div class="Example">' . highlight_string(
                $Call['Value'],
                true
            ) . '<samp>' . $Call['Output'] . '</samp></div>';
    });