<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call) {
        $Call['Value'] = F::Run('Code.Flow.Application', 'Run', ['Run' => $Call['Run']])['Output'];

        return $Call;
    });