<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description <k> tag 
     * @package Codeine
     * @version 8.x
     */

    setFn('Parse', function ($Call)
    {
        $Call = F::Apply(null, 'Key', $Call);
        $Call = F::Apply(null, 'Call', $Call);

        return $Call;
    });

    setFn('Call', function ($Call)
    {
        $Call['Parsed'] = F::Run('Text.Regex', 'All',
        [
            'Pattern' => $Call['Block Call Pattern'],
            'Value' => $Call['Value']
        ]);

        if ($Call['Parsed'])
        {
            $Call['Parsed'][0] = array_unique($Call['Parsed'][0]);
            $Call['Parsed'][2] = array_unique($Call['Parsed'][2]);

            foreach ($Call['Parsed'][2] as $IX => &$Match)
            {
                if (($Matched = F::Live(F::Dot($Call, $Match))) !== null)
                {
                    $Output = '';

                    if ($DotMatched = F::Live(F::Dot($Call, $Match)))
                    {
                        if (is_array($DotMatched))
                        {
                            sort($DotMatched);
                            foreach($DotMatched as $ICV => $cMatch)
                                $Output.= str_replace('<#/>',
                                    $ICV,
                                    str_replace('<call>'.$Match.'</call>', $cMatch,$Call['Parsed'][1][$IX]).
                                    ($cMatch)
                                    .str_replace('<call>'.$Match.'</call>', $cMatch,$Call['Parsed'][3][$IX]));
                        }
                        else
                            $Output = str_replace('<#/>', '', $Call['Parsed'][1][$IX].($DotMatched).$Call['Parsed'][3][$IX]);
                    }

                    $Match = $Output;
                }
                else
                    $Match = '';
            }

            $Call['Value'] = str_replace($Call['Parsed'][0], $Call['Parsed'][2], $Call['Value']);
        }

        return $Call;
    });


    setFn('Key', function ($Call)
    {
        $Call['Parsed'] = F::Run('Text.Regex', 'All',
        [
            'Pattern' => $Call['Block Key Pattern'],
            'Value' => $Call['Value']
        ]);

        if ($Call['Parsed'] && isset($Call['Data']))
        {
            $Call['Parsed'][0] = array_unique($Call['Parsed'][0]);
            $Call['Parsed'][2] = array_unique($Call['Parsed'][2]);

            foreach ($Call['Parsed'][2] as $IX => $Match)
            {
                if (mb_strpos($Match, ',') !== false)
                    $Keys = explode(',', $Match);
                else
                    $Keys = [$Match];

                $Output = '';

                if (isset($Call['Data']))
                    foreach ($Keys as $Key)
                        if ($Matched = F::Live(F::Dot($Call['Data'], $Key)) !== null)
                        {
                            if ($DotMatched = F::Live(F::Dot($Call['Data'], $Key)))
                            {
                                if (is_array($DotMatched))
                                {
                                    sort($DotMatched);
                                    foreach($DotMatched as $ICV => $cData)
                                        if (is_array($Key))
                                            ;
                                        else
                                        {
                                            if (is_array($cData))
                                                $cData = '{}';
                                            
                                            $Output .= str_replace('<#/>',
                                                $ICV,
                                                str_replace('<k>'.$Match.'</k>', $cData, $Call['Parsed'][1][$IX]).
                                                ($cData)
                                                .str_replace('<k>'.$Match.'</k>', $cData, $Call['Parsed'][3][$IX]));
                                        }
                                }
                                else
                                    $Output = str_replace('<#/>', '', $Call['Parsed'][1][$IX].($DotMatched).$Call['Parsed'][3][$IX]);

                                break;
                            }
                        }

                $Call['Parsed'][2][$IX] = $Output;
            }

            $Call['Value'] = str_replace($Call['Parsed'][0], $Call['Parsed'][2], $Call['Value']);
        }

        return $Call;
    });