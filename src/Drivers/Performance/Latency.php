<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Audit', function ($Call) {
        if (F::Dot($Call, 'Performance.Latency.Enabled')) {
            $Total = round(microtime(true) - Started, 4) * 1000;

            $Decision = 'Green';

            $Limits = F::Dot($Call, 'Performance.Latency.Limits');

            if (empty($Limits)) {
            } else {
                foreach ($Limits as $Limit => $Value) {
                    if ($Total >= $Value) {
                        $Decision = $Limit;
                    }
                }

                $Verbose = LOG_DEBUG;

                switch ($Decision) {
                    case 'Green':
                        $Verbose = LOG_DEBUG;
                        break;

                    case 'Yellow':
                        $Verbose = LOG_INFO;
                        break;

                    case 'Orange':
                        $Verbose = LOG_NOTICE;
                        break;

                    case 'Red':
                        $Verbose = LOG_WARNING;
                        break;

                    case 'Black':
                        $Verbose = LOG_ERR;
                        break;
                }

                F::Log(
                    'Latency level is *' . $Decision . '*, because total page time *' . $Total . '* ms',
                    $Verbose,
                    'Performance',
                    -1
                );

                if ($Verbose <= LOG_NOTICE) {
                    if (F::$_Performance) {
                    } else {
                        F::$_Performance = 'Latency';
                    }
                    F::Log('Limits: ' . j($Limits), LOG_INFO, 'Performance');
                    F::Log('Performance Analysis is enabled', LOG_INFO, 'Performance', -1);
                }

                $Call = F::Hook('Latency.Audit.' . $Decision, $Call);
            }
        }

        return $Call;
    });
