<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Add', function ($Call) {
        return bcadd($Call['A'], $Call['B']);
    });

    setFn('Substract', function ($Call) {
        return bcsub($Call['A'], $Call['B']);
    });

    setFn('Multiply', function ($Call) {
        return bcmul($Call['A'], $Call['B']);
    });

    setFn('Divide', function ($Call) {
        return bcdiv($Call['A'], $Call['B']);
    });
