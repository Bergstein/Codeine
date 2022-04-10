<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        F::Log('CLI Interface Started', LOG_NOTICE);

        $Call = F::Hook('beforeInterfaceRun', $Call);

        $Call['HTTP']['IP'] = F::Live($Call['HTTP']['IP'], $Call);

        if (
            isset($Call['Project']['Hosts'][F::Environment()]) and is_array(
                $Call['Project']['Hosts'][F::Environment()]
            )
        ) {
            $Call['HTTP']['Host'] = array_shift($Call['Project']['Hosts'][F::Environment()]);
            F::Log('CLI Pseudohost: ' . $Call['HTTP']['Host'], LOG_NOTICE);
        } else {
            $Call['HTTP']['Host'] = 'no-cli-pseudohost';
            F::Log('No CLI Pseudohost', LOG_WARNING);
        }

        $Data['Service'] = $Call['Service'];
        $Data['Method'] = $Call['Method'];

        $Call['HTTP']['URI'] = ' ' . json_encode($Data);
        $Call['HTTP']['URL'] = '/';

        if (isset($Call['Skip Run'])) {
            F::Log('Run Skipped, because ' . $Call['Skip Run'], LOG_INFO);
        } else {
            F::Log($Call['Service'] . ':' . $Call['Method'] . ' started', LOG_NOTICE);
            $Call = F::Apply($Call['Service'], $Call['Method'], $Call);
        }

        $Call = F::Hook('afterInterfaceRun', $Call);

        if (is_array($Call) && isset($Call['Output'])) {
            F::Run(
                'IO',
                'Write',
                $Call,
                [
                    'Storage' => 'Output',
                    'Where' => $Call['Service'] . ':' . $Call['Method'],
                    'Data' => $Call['Output']
                ]
            );

            if (isset($Call['Failure']) && $Call['Failure']) {
                $Call['Return Code'] = 1;
            }
        } else {
            echo j($Call);
        }


        F::Log('CLI Finished', LOG_NOTICE);

        return $Call;
    });
