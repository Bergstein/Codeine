<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call)
    {
        F::Log('Performance: '.self::$_Performance, LOG_NOTICE, 'Performance');

        if (self::$_Performance == 'Request' or (F::Dot($Call, 'Performance.Enabled') and self::$_Performance == 'Latency'))
        {
            $Call['Performance']['Summary']['Time'] = round((microtime(true)-Started)*1000);
            $Call['Performance']['Summary']['Calls'] = array_sum(self::$_Counters['C']);
            $Call['Performance']['Summary']['Core Storage'] = count(self::$_Storage);

            arsort(self::$_Counters['T']);

            F::Log('Max stack size: '.F::Get('MSS'), LOG_NOTICE, 'Performance');
            F::Log('Total time: '.round($Call['Performance']['Summary']['Time']).' ms', LOG_NOTICE, 'Performance');
            F::Log('Total calls: '.$Call['Performance']['Summary']['Calls'], LOG_NOTICE, 'Performance');
            F::Log('Total time per call: '
                .round($Call['Performance']['Summary']['Time'] / $Call['Performance']['Summary']['Calls'], 2).' ms'
                , LOG_NOTICE, 'Performance');

            F::Log('Memory: *'.(memory_get_usage(true)/1024).'Kb* ', LOG_NOTICE, 'Performance');
            F::Log('Peak memory: *'.(memory_get_peak_usage(true)/1024).'Kb*', LOG_NOTICE, 'Performance');
            F::Log('Core Storage: ~*'.(round(mb_strlen(j(self::$_Storage))/1024)).'kb*', LOG_NOTICE, 'Performance');
            
            $ExcludedFromLimiting = F::Dot($Call, 'Performance.Excluded');
            foreach (self::$_Counters['T'] as $Key => $Value)
            {
                if (!isset(self::$_Counters['C']['Call:'.$Key]))
                    self::$_Counters['C']['Call:'.$Key] = 1;

                $Class =
                    [
                        'ATime'         => LOG_INFO,
                        'RTime'         => LOG_INFO,
                        'ACalls'        => LOG_INFO,
                        'RCalls'        => LOG_INFO,
                        'TimePerCall'   => LOG_INFO
                    ];

                $Call['RTime'] = round(($Value / $Call['Performance']['Summary']['Time']) * 100, 2);
                $Call['RCalls'] = round((self::$_Counters['C']['Call:'.$Key] / $Call['Performance']['Summary']['Calls']) * 100, 2);
                $Call['ATime'] = round($Value);
                $Call['ACalls'] = self::$_Counters['C']['Call:'.$Key];
                $Call['TimePerCall'] = round($Value / $Call['ACalls'], 2);

                $Yellow = F::Dot($Call, 'Performance.Limits.Yellow');
                
                if (in_array($Key, $ExcludedFromLimiting))
                    ;
                else
                {
                    if (empty($Yellow))
                        ;
                    else
                        foreach ($Yellow as $Metric => $Limit)
                            if ($Call[$Metric] > $Limit)
                                $Class[$Metric] = LOG_NOTICE;
    
                    $Red = F::Dot($Call, 'Performance.Limits.Yellow');
                    
                    if (empty($Red))
                        ;
                    else
                        foreach ($Red as $Metric => $Limit)
                            if ($Call[$Metric] > $Limit)
                                $Class[$Metric] = LOG_WARNING;
                }
                
                F::Log('*'.$Key.'* time is *'.$Call['ATime'].'* ms',                $Class['ATime'], 'Performance', -1);
                F::Log('*'.$Key.'* time is *'.$Call['RTime'].'%*',                  $Class['RTime'], 'Performance', -1);
                F::Log('*'.$Key.'* calls is *'.$Call['ACalls'].'*',                 $Class['ACalls'], 'Performance', -1);
                F::Log('*'.$Key.'* calls is *'.$Call['RCalls'].'%*',                $Class['RCalls'], 'Performance', -1);
                F::Log('*'.$Key.'* time per call is *'.$Call['TimePerCall'].'* ms', $Class['TimePerCall'], 'Performance', -1);
            }

            arsort(self::$_Counters['C']);
            foreach (self::$_Counters['C'] as $Key => $Value)
                if (mb_substr($Key, 0, 5) != 'Call:')
                    F::Log('Counter *'.$Key.'* is *'.$Value.'*',                LOG_NOTICE, 'Performance', -1);
        }
        return $Call;
    });