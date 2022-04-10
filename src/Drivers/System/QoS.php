<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Start', function ($Call) {
        if (isset($Call['QoS']['Active']) && $Call['QoS']['Active']) {
            $Call = F::Apply('System.QoS.' . $Call['QoS']['Method'], 'Start', $Call);

            if (isset($Call['QoS']['Classes'][$Call['QoS']['Class']])) {
                foreach ($Call['QoS']['Classes'][$Call['QoS']['Class']] as $Hook) {
                    $Call = F::Live($Hook, $Call);
                }
            }

            F::Log('QoS *Class ' . $Call['QoS']['Class'] . '* selected.', LOG_INFO);
        } else {
            F::Log('Disabled', LOG_INFO);
            $Call['QoS']['Class'] = 0;
        }


        return $Call;
    });

    setFn('Finish', function ($Call) {
        return F::Run('System.QoS.' . $Call['QoS']['Method'], 'Finish', $Call);
    });
