<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Age', function ($Call) {
        if (isset($Call['Data']['DOB']) && $Call['Data']['DOB'] != 0) {
            return floor((time() - $Call['Data']['DOB']) / (86400 * 365));
        } else {
            return 0;
        }
    });

    setFn('Location', function ($Call) {
        $Call['Data']['Location'] = null;
        if (isset($Call['Session']['User']['ID'])) {
            if (isset($Call['Data']['ID']) && $Call['Data']['ID'] == $Call['Session']['User']['ID']) {
                return F::Run('System.GeoIP', 'City', $Call);
            }
        }

        return $Call['Data']['Location'];
    });

    setFn('Photo', function ($Call) {
        $Photo = null;

        if (isset($Call['Data']['Photo'])) {
            if (preg_match('/^http.*/', $Call['Data']['Photo'])) {
                $Photo = $Call['Data']['Photo'];
            } elseif (isset($Call['Data']['Photo']) && !empty($Call['Data']['Photo'])) {
                $Photo = $Call['Data']['Photo'];
            }
        }

        return $Photo;
    });

    setFn('Fullname', function ($Call) {
        if (empty($Call['Data']['Title']) && isset($Call['Data']['Fullname'])) {
            return $Call['Data']['Fullname'];
        } else {
            if (isset($Call['Data']['Fullname'])) {
                return $Call['Data']['Title'];
            }
        }

        return null;
    });
