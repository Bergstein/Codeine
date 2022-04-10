<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 7.1
     */

    setFn('Check', function ($Call) {
        if (F::isCall($Call['Run'])) {
            $Call = F::Merge($Call, $Call['Run']);
        }

        unset($Call['Decision'], $Call['Weight']);

        $Decision = F::Run('Security.Access', 'Check', $Call);

        if ($Decision === 401) {
            $Call['Run'] =
                [
                    'Service' => 'User.Login',
                    'Method' => 'Do',
                    'Zone' => 'Default'
                ];
        }

        if ($Decision) {
            $Call = F::Hook('onFrontAccessAllowed', $Call);
        } else {
            $Call = F::Hook('onFrontAccessDenied', $Call);
        }

        return $Call;
    });
