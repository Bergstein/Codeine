<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description XCache Data Driver
     * @package Codeine
     * @version 8.x
     */

    setFn('Open', function ($Call) {
        $Memcached = new Memcached();
        $Memcached->addServer($Call['Server'], $Call['Port']);

        return $Memcached;
    });

    setFn('Read', function ($Call) {
        if (is_array($Call['Where']['ID'])) {
            foreach ($Call['Where']['ID'] as &$ID) {
                $ID = $Call['Scope'] . $ID;
            }

            return $Call['Link']->getMulti($Call['Where']['ID']);
        } else {
            if (($Result = $Call['Link']->get($Call['Scope'] . $Call['Where']['ID'])) !== false) {
                return [$Result];
            } else {
                return null;
            }
        }
    });

    setFn('Write', function ($Call) {
        if (isset($Call['Where'])) {
            if (null === $Call['Data']) {
                $Call['Link']->delete($Call['Scope'] . $Call['Where']['ID']);
            } else {
                $Call['Data'] = F::Merge(F::Run(null, 'Read', $Call)[0], $Call['Data']);
                $Call['Link']->set($Call['Scope'] . $Call['Where']['ID'], $Call['Data'], $Call['TTL']);
            }
        } else {
            $Call['Link']->set($Call['Scope'] . $Call['Data']['ID'], $Call['Data'], $Call['TTL']);
        }

        return $Call['Data'];
    });

    setFn('Close', function ($Call) {
        return true;
    });

    setFn('Execute', function ($Call) {
        return true;
    });

    setFn('Exist', function ($Call) {
        return $Call['Link']->get($Call['Scope'] . $Call['Where']['ID']);
    });

    setFn('Status', function ($Call) {
        $Info = $Call['Link']->getStats();
        foreach ($Info as $Key => &$Value) {
            $Value = [$Key, $Value];
        }

        return $Info;
    });