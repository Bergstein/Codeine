<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description: Password generator
     * @package Codeine
     * @version 8.x
     * @date 04.12.10
     * @time 14:56
     */

    setFn('Get', function ($Call) {
        $Output = file_get_contents(
            'http://www.random.org/strings/?num=1&len=' . $Call['Size'] . '&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new'
        );

        switch ($Call['Case']) {
            case 'Lower':
                $Output = strtolower($Output);
                break;

            case 'Upper':
                $Output = strtoupper($Output);
                break;
        }

        return $Output;
    });
