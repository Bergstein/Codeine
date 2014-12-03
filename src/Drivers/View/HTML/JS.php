<?php

    /* Codeine
     * @author BreathLess
     * @description Media includes support 
     * @package Codeine
     * @version 7.x
     */

    setFn('Process', function ($Call)
    {
        if (preg_match('/<place>JS<\/place>/SsUu', $Call['Output']))
        {
            if (preg_match_all('/<jsrun>(.*)<\/jsrun>/SsUu', $Call['Output'], $Parsed))
            {
                $JSInline = implode(';', $Parsed[1]);
                $Call['Output'] = str_replace($Parsed[0], '', $Call['Output']);
            }
            else
                $JSInline = '';

            $Parsed = F::Run('Text.Regex', 'All',
                [
                    'Pattern' => $Call['JS']['Pattern'],
                    'Value' => $Call['Output']
                ]);

            if ($Parsed)
            {
                $Call['JS']['Input'] = $Parsed[1];

                $Call = F::Hook('beforeJSInput', $Call);

                // JS Input
                foreach ($Call['JS']['Input'] as $JS)
                {
                    if (preg_match('/^http:/SsUu', $JS))
                    {
                        $JS2 = parse_url($JS, PHP_URL_HOST).sha1($JS);
                        $Call['JS']['Scripts'][$JS2] = F::Run('IO', 'Read',
                        [
                            'Storage' => 'Web',
                            'Where'   =>
                            [
                                'ID' => $JS
                            ],
                            'IO TTL' => $Call['JS']['Remote']['TTL']
                        ])[0];
                        $JS = $JS2;
                    }
                    else
                    {
                        list($Asset, $ID) = F::Run('View', 'Asset.Route', ['Value' => $JS]);

                        $Call['JS']['Scripts'][$JS] = F::Run('IO', 'Read',
                            [
                                'Storage' => 'JS',
                                'Scope'   => [$Asset, 'js'],
                                'Where'   => $ID
                            ])[0];
                    }

                    if ($Call['JS']['Scripts'][$JS])
                        F::Log('JS loaded: '.$JS, LOG_INFO);
                    else
                        F::Log('JS cannot loaded: '.$JS, LOG_ERR);
                }

                if (!empty($JSInline))
                {
                    $JSInline = $Call['JS']['Inline']['Prefix'].
                                $JSInline.
                                $Call['JS']['Inline']['Postfix'];
                    $Call['JS']['Scripts']['DomReady'] = $JSInline;
                }

                $Call = F::Hook('afterJSInput', $Call);

                $Call = F::Hook('beforeJSOutput', $Call);

                // JS Output
                if (isset($Call['JS']['Host']) && !empty($Call['JS']['Host']))
                    $Host = $Call['JS']['Host'];
                else
                    $Host = $Call['HTTP']['Host'];

                foreach ($Call['JS']['Scripts'] as $JS => $JSSource)
                {
                    $JS = sha1($JSSource).'_'.strtr($JS, ':', '_');

                    $Write = true;

                    if ($Call['JS']['Caching'])
                    {
                        if (F::Run('IO', 'Execute',
                        [
                            'Storage' => 'JS Cache',
                            'Scope'   => [$Host, 'js'],
                            'Execute' => 'Exist',
                            'Where'   =>
                            [
                                'ID' => $JS
                            ]
                        ]))
                        {
                            F::Log('Cache *hit* '.$JS, LOG_GOOD);
                            $Write = false;
                        }
                        else
                        {
                            F::Log('Cache *miss* *'.$JS.'*', LOG_BAD);
                        }
                    }

                    if ($Write)
                    {
                        $Call = F::Hook('beforeJSWrite', $Call);

                            F::Run ('IO', 'Write',
                            [
                                 'Storage' => 'JS Cache',
                                 'Scope'   => [$Host, 'js'],
                                 'Where'   => $JS,
                                 'Data' => $JSSource
                            ]);

                        $Call = F::Hook('afterJSWrite', $Call);
                    }

                    if (isset($Call['JS']['Host']) && !empty($Call['JS']['Host']))
                        $JSFilename = $Call['HTTP']['Proto']
                                .$Call['JS']['Host']
                                .$Call['JS']['Pathname']
                                .$JS
                                .$Call['JS']['Extension'];
                    else
                        $JSFilename = $Call['JS']['Pathname']
                                .$JS
                                .$Call['JS']['Extension'];

                    $Call['JS']['Links'][$JSFilename] = '<script src="'.$JSFilename.'" type="'.$Call['JS']['Type'].'"></script>';
               }

                $Call = F::Hook('afterJSOutput', $Call);

                $Call['Output'] = str_replace('<place>JS</place>', implode(PHP_EOL, $Call['JS']['Links']), $Call['Output']);
            }

            $Call['Output'] = str_replace($Parsed[0], '', $Call['Output']);
        }

        unset($Call['JS']);

        return $Call;
    });