<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Do', function ($Call)
    {
        $Call['Output']['Content'] =
            [
                [
                    'Type'  => 'Block',
                    'Value' => 'Codeine Version: '
                        .self::$_Options['Version']['Codeine']['Major'].'.'.self::$_Options['Version']['Codeine']['Minor']
                ],
                [
                    'Type'  => 'Block',
                    'Value' => 'Project Version: '
                        .self::$_Options['Version']['Project']['Major'].'.'.self::$_Options['Version']['Project']['Minor']
                ]
            ];
        return $Call;
    });