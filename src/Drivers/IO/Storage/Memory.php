<?php

    /* Codeine
     * @author BreathLess
     * @package Codeine
     * @version 7.x
     */

    setFn ('Open', function ($Call)
    {
        return true;
    });

    setFn ('Read', function ($Call)
    {
        if (isset($Call['Where']['ID']))
            return F::Get($Call['Where']['ID']);
        else
            return null;
    });

    setFn ('Write', function ($Call)
    {
        foreach ($Call['Data'] as $Data)
            F::Set($Data['ID'], $Data);
        return $Call['Data'];
    });

    setFn ('Close', function ($Call)
    {
        return true;
    });

    setFn ('Execute', function ($Call)
    {
        return true;
    });