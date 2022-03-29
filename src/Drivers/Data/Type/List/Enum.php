<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Live Enum', function ($Call) {
        if (isset($Call['Skip Enum Live'])) {
            ;
        } else {
            $Call['Node']['Options'] = F::Live(
                $Call['Node']['Options'],
                [
                    'Node' => $Call['Node'],
                    'Name' => $Call['Name'],
                    'Data' => isset($Call['Data']) ? $Call['Data'] : []
                ]
            );
        }

        return $Call;
    });

    setFn('Write', function ($Call) {
        $Call = F::Apply(null, 'Live Enum', $Call);

        if (empty($Call['Node']['Options'])) {
            return null;
        } else {
            return array_search($Call['Value'], $Call['Node']['Options']);
        }
    });

    setFn('Read', function ($Call) {
        $Call = F::Apply(null, 'Live Enum', $Call);

        if (is_scalar($Call['Value']) && isset($Call['Node']['Options'][$Call['Value']])) {
            return $Call['Node']['Options'][$Call['Value']];
        } else {
            return $Call['Value'];
        }
    });

    setFn('Where', function ($Call) {
        $Call = F::Apply(null, 'Live Enum', $Call);
        $Key = array_search($Call['Value'], $Call['Node']['Options']);
        if ($Key === false) {
            if (isset($Call['Node']['Options'][$Call['Value']])) {
                $Key = $Call['Value'];
            }
        }

        return $Key;
    });

    setFn('Populate', function ($Call) {
        $Call = F::Apply(null, 'Live Enum', $Call);

        return array_rand($Call['Node']['Options']);
    });