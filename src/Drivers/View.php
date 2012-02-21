<?php

    /* Codeine
     * @author BreathLess
     * @description: 
     * @package Codeine
     * @version 7.2
     * @issue 30
     */

    self::setFn('Parse', function ($Call)
    {
        if (preg_match_all('@<k>(.*)</k>@SsUu', $Call['Value'], $Pockets))
        {
            foreach (
                $Pockets[1] as $IX => $Match
            )
            {
                if (isset($Call['Data'][$Match]))
                {
                    if (is_array($Call['Data'][$Match]))
                        $Call['Data'][$Match] = implode(' ', $Call[$Match]);

                    $Call['Value'] = str_replace($Pockets[0][$IX], $Call['Data'][$Match], $Call['Value']);
                }
                else
                    $Call['Value'] = str_replace($Pockets[0][$IX], '', $Call['Value']);
            }
        }

        return $Call['Value'];
    });

    self::setFn('Load', function ($Call)
    {
        return $Call['Layout'] = F::Run('IO', 'Read', $Call,
            array (
                  'Storage' => 'Layout',
                  'Where'   => array ('ID' => array (
                      $Call['ID'] . (isset($Call['Context']) ? '.' . $Call['Context'] : '') . (isset($Call['Extension']) ? $Call['Extension'] : '.html'),
                      $Call['ID'] . '.html'))
            ));
    });

    self::setFn('LoadParsed', function ($Call)
    {
        return F::Run('View', 'Parse',
            array (
                  'Data'  => $Call['Data'],
                  'Value' => F::Run('View', 'Load', $Call)
            ));
    });

    self::setFn('Render', function ($Call)
    {
        $Renderer = F::Run($Call['Strategy']['Renderer']['Service'], $Call['Strategy']['Renderer']['Method'], $Call);

        return F::Run($Renderer['Service'], $Renderer['Method'], $Call);
    });

    self::setFn('Asset.Route', function ($Call)
    {
        if (mb_strpos($Call['Value'], ':') !== false)
            return explode(':', $Call['Value']);
        else
            return array ($Call['Value'], $Call['Value']);
    });