<?php

    /* Codeine
    * @author bergstein@trickyplan.com
    * @description
    * @package Codeine
    * @version 7.1
    */

    setFn('Open', function ($Call)
    {
        return true;
    });

    setFn('Process.Headers', function ($Call)
    {
        $Headers = [];
        foreach ($Call['CURL']['Headers'] as $Key => $Value)
            $Headers[] = $Key.': '.$Value;

        $Call['CURL']['Headers'] = $Headers;

        return $Call;
    });

    setFn('Select User Agent', function ($Call)
    {
        if (isset($Call['CURL']['Random User Agent']))
        {
            $Call['CURL']['Agent'] = $Call['CURL']['User Agents'][array_rand($Call['CURL']['User Agents'])];
            F::Log('UA: '.$Call['CURL']['Agent'].' selected', LOG_INFO, 'Administrator');
        }

        return $Call;
    });

    setFn('Select Proxy', function ($Call)
    {
        if (isset($Call['CURL']['Random Proxy']) && isset($Call['CURL']['Proxies']))
        {
            $Call['CURL']['Proxies'] = F::Live($Call['CURL']['Proxies']);

            $Random = $Call['CURL']['Proxies'][array_rand($Call['CURL']['Proxies'])];

            if (strpos($Random, ':') === false)
            {
                $Call['CURL']['Proxy']['Host'] = $Random;
                $Call['CURL']['Proxy']['Port'] = 80;
            }
            else
                list($Call['CURL']['Proxy']['Host'], $Call['CURL']['Proxy']['Port']) = explode(':', $Random);
        }

        if ($Call['CURL']['Proxy']['Type'] === 'SOCKS5')
            $Call['CURL']['Proxy']['Type'] = CURLPROXY_SOCKS5;

        if ($Call['CURL']['Proxy']['Type'] === 'HTTP')
            $Call['CURL']['Proxy']['Type'] = CURLPROXY_HTTP;

        if (isset($Call['CURL']['Proxy']['Host']) && !empty($Call['CURL']['Proxy']['Host']))
            F::Log('Proxy: '.$Call['CURL']['Proxy']['Host'].':'.$Call['CURL']['Proxy']['Port'].' ('.$Call['CURL']['Proxy']['Type'].') selected', LOG_INFO, 'Administrator');

        return $Call;
    });

    setFn('Prepare.Cookies', function ($Call)
    {
        if (empty($Call['CURL']['Cookie']))
            $Call['CURL']['Cookie'] = '';
        else
        {
            if (is_array($Call['CURL']['Cookie']))
            {

                $Pairs = [];
                foreach ($Call['CURL']['Cookie'] as $Key => $Value)
                    $Pairs[] = $Key.'='.$Value;

                    $Call['CURL']['Cookie'] = implode(';', $Pairs);
            }
        }

        return $Call;
    });

    setFn('Setup.Options', function ($Call)
    {
        $Call['CURL']['Options'] = [
            CURLOPT_HEADER           => $Call['CURL']['Return Header'],
            CURLOPT_RETURNTRANSFER   => true,
            CURLOPT_COOKIE           => $Call['CURL']['Cookie'],
            CURLOPT_FOLLOWLOCATION   => $Call['CURL']['Follow'],
            CURLOPT_REFERER          => $Call['CURL']['Referer'],
            CURLOPT_PROXYTYPE        => $Call['CURL']['Proxy']['Type'],
            CURLOPT_PROXY            => $Call['CURL']['Proxy']['Host'],
            CURLOPT_PROXYPORT        => $Call['CURL']['Proxy']['Port'],
            CURLOPT_USERAGENT        => $Call['CURL']['Agent'],
            CURLOPT_HTTPHEADER       => $Call['CURL']['Headers'],
            CURLOPT_ENCODING         => $Call['CURL']['Encoding'],
            CURLOPT_SSL_VERIFYPEER   => $Call['CURL']['SSL']['Verify Peer'],
            CURLOPT_SSLVERSION       => $Call['CURL']['SSL']['Version'],
            CURLOPT_TCP_NODELAY      => $Call['CURL']['TCP']['No Delay'],
            CURLOPT_TCP_FASTOPEN     => $Call['CURL']['TCP']['Fast Open'],
            CURLOPT_FAILONERROR      => false,
            CURLINFO_HEADER_OUT      => true
        ];

        if (is_float($Call['CURL']['Connect Timeout']))
            $Call['CURL']['Options'][CURLOPT_CONNECTTIMEOUT_MS] = $Call['CURL']['Connect Timeout'] * 1000;
        else
            $Call['CURL']['Options'][CURLOPT_CONNECTTIMEOUT] = $Call['CURL']['Connect Timeout'];

        if (is_float($Call['CURL']['Overall Timeout']))
            $Call['CURL']['Options'][CURLOPT_TIMEOUT_MS] = $Call['CURL']['Overall Timeout'] * 1000;
        else
            $Call['CURL']['Options'][CURLOPT_TIMEOUT] = $Call['CURL']['Overall Timeout'];

        /*if (isset($Call['CURL']['Options'][CURLOPT_COOKIEJAR]))
            ;
        else
            $Call['CURL']['Options'][CURLOPT_COOKIEJAR] = $Call['CURL']['Cookie Directory'].DS.parse_url($Call['Where']['ID'], PHP_URL_HOST);*/

        if (isset($Call['CURL']['Connect Timeout ms']))
            $Call['CURL']['Options'][CURLOPT_CONNECTTIMEOUT_MS] = $Call['CURL']['Connect Timeout ms'];

        if (isset($Call['CURL']['Overall Timeout ms']))
            $Call['CURL']['Options'][CURLOPT_TIMEOUT_MS] = $Call['CURL']['Overall Timeout ms'];

        if (isset($Call['CURL']['Proxy']['Auth']))
            $Call['CURL']['Options'][CURLOPT_PROXYUSERPWD] = $Call['CURL']['Proxy']['Auth'];



        return $Call;
    });

    setFn('Read', function ($Call)
    {
        $Return = null;

        $Call = F::Run(null, 'Delay', $Call);
        $Call = F::Run(null, 'Select User Agent', $Call);
        $Call = F::Run(null, 'Select Proxy', $Call);
        $Call = F::Run(null, 'Process.Headers', $Call);
        $Call = F::Run(null, 'Prepare.Cookies', $Call);
        $Call = F::Run(null, 'Setup.Options', $Call);


        if (is_array($Call['Where']['ID']))
        {
            $Call['Link'] = curl_multi_init();

            $Links = [];

            foreach ($Call['Where']['ID'] as $cID)
            {
                if (isset($Call['Data']))
                    $cID.= '?'.http_build_query($Call['Data']);

                $Links[$cID] = curl_init($cID);

                F::Log('CURL GET Request Headers: *'.j($Call['CURL']['Headers']).'*', LOG_INFO, 'Administrator');

                curl_setopt_array($Links[$cID], $Call['CURL']['Options']);

                curl_multi_add_handle($Call['Link'], $Links[$cID]);
            }

            $Running = null;
            do
                curl_multi_exec($Call['Link'], $Running);
            while ($Running > 0);

            foreach ($Links as $ID => $Link)
            {
                $Return[$ID] = curl_multi_getcontent($Link);

                if ($Call['CURL']['Return Header'] && isset($Call['CURL']['Only Header']))
                {
                    $Size = curl_getinfo($Link, CURLINFO_HEADER_SIZE);
                    $Return[$ID] = substr($Return[$ID], 0, $Size);
                }

                if (curl_errno($Link))
                {
                    F::Log('CURL GET error: '.curl_error($Link).'*'.$ID.'*', LOG_NOTICE, 'Administrator');
                    F::Log($Return, LOG_DEBUG, 'Administrator');
                    F::Log(curl_getinfo($Call['Link']), LOG_INFO, 'Administrator');
                }
                else
                    F::Log('CURL GET fetched *'.$ID.'*', LOG_INFO, 'Administrator');

                curl_multi_remove_handle($Call['Link'], $Link);
            }

            curl_multi_close($Call['Link']);
        }
        else
        {
            if (isset($Call['Data']))
            {
                if (empty($Call['Data']))
                    ;
                else
                    $Call['Where']['ID'].= '?'.http_build_query($Call['Data']);
            }

            $Call['Link'] = curl_init($Call['Where']['ID']);

            F::Log('CURL GET Request Headers: *'.j($Call['CURL']['Headers']).'*', LOG_INFO, 'Administrator');

            curl_setopt_array($Call['Link'], $Call['CURL']['Options']);

            $Return = [curl_exec($Call['Link'])];

            $Call = F::Apply(null, 'Info', $Call);
            
            if ($Call['CURL']['Return Header'])
            {
                $Size = curl_getinfo($Call['Link'], CURLINFO_HEADER_SIZE);
                $Headers = mb_substr($Return[0], 0, $Size);
                $Body = mb_substr($Return[0], $Size);

                $HTTPStatus = curl_getinfo($Call['Link'], CURLINFO_HTTP_CODE);
                $Return = [$Body, '_Status' => $HTTPStatus, '_0' => $Headers];
                F::Log('CURL GET Response Headers: '.j(explode("\r\n", $Headers)), LOG_INFO, 'Administrator');
            }

            if ($Call['CURL']['Return Header'] && isset($Call['CURL']['Only Header']))
            {
                $Size = curl_getinfo($Call['Link'], CURLINFO_HEADER_SIZE);
                $Return[0] = substr($Return[0], 0, $Size);
            }
            
            if (curl_errno($Call['Link']))
            {
                F::Log('CURL GET error: '.curl_error($Call['Link']).' *'.$Call['Where']['ID'].'*', LOG_NOTICE, 'Administrator');
                F::Log(curl_getinfo($Call['Link']), LOG_INFO, 'Administrator');
                F::Log($Return, LOG_DEBUG, 'Administrator');
            }
            else
                F::Log('CURL GET fetched '.$Call['Where']['ID'], LOG_INFO, 'Administrator');

            curl_close($Call['Link']);
        }

        return $Return;
    });

    setFn('Write', function ($Call)
    {
        $Call = F::Run(null, 'Delay', $Call);
        $Call = F::Run(null, 'Select User Agent', $Call);
        $Call = F::Run(null, 'Select Proxy', $Call);
        $Call = F::Run(null, 'Process.Headers', $Call);
        $Call = F::Run(null, 'Prepare.Cookies', $Call);
        $Call = F::Run(null, 'Setup.Options', $Call);

        $Call['CURL']['Options'][CURLOPT_POST] = true;

        if (is_array($Call['Where']['ID']))
        {
            $Links = [];

            $Call['Link'] = curl_multi_init();

            foreach($Call['Where']['ID'] as $cIndex => $cID)
            {
                $Links[$cID.'_'.$cIndex] = curl_init($cID);
                
                F::Log('CURL POST Request Headers: *'.j($Call['CURL']['Headers']).'*', LOG_INFO, 'Administrator');
                // F::Log('CURL POST Request Parameters: *'.j($Call['Data'][$cIndex]).'*', LOG_INFO, 'Administrator');
                F::Log('CURL POST Request URL: *'.$cID.'*', LOG_INFO, 'Administrator');

                    if (F::Dot($Call, 'Headers.'.$cIndex))
                        $Call['CURL']['Options'][CURLOPT_HTTPHEADER] = F::Merge($Call['CURL']['Headers'], $Call['Headers'][$cIndex]);
                    
                    if (F::Dot($Call, 'Data.'.$cIndex))
                        $Call['CURL']['Options'][CURLOPT_POSTFIELDS] = is_string($Call['Data'][$cIndex]) ? $Call['Data'][$cIndex] : http_build_query($Call['Data'][$cIndex]);

                curl_setopt_array($Links[$cID.'_'.$cIndex], $Call['CURL']['Options']);
                curl_multi_add_handle($Call['Link'], $Links[$cID.'_'.$cIndex]);
            }

            $Running = null;
            $Wait = 0;

            do
            {
                $Status = curl_multi_exec($Call['Link'], $Running);
                if ($Running)
                    curl_multi_select($Call['Link'], 0.01);
                $Wait++;
            } while ($Running && $Status == CURLM_OK);

            foreach ($Links as $ID => $Link)
            {
                $Result[$ID] = curl_multi_getcontent($Link);

                if ($Call['CURL']['Return Header'] && isset($Call['CURL']['Only Header'])) 
                {
                    $Size = curl_getinfo($Link, CURLINFO_HEADER_SIZE);
                    $Result[$ID] = substr($Result[$ID], 0, $Size);
                }

                F::Log('CURL POST Response: '.j($Result[$ID]), LOG_INFO, 'Administrator');
                
                if (curl_multi_errno($Call['Link'])) 
                {
                    F::Log('CURL POST error: '.curl_error($Link).'*'.$ID.'*', LOG_NOTICE, 'Administrator');
                    F::Log($Result, LOG_NOTICE, 'Administrator');
                }
                else
                    F::Log('CURL POST fetched *'.$ID.'*', LOG_INFO, 'Administrator');

                curl_multi_remove_handle($Call['Link'], $Link);
            }

            curl_multi_close($Call['Link']);
        }
        else
        {
            $Call['Link'] = curl_init($Call['Where']['ID']);

            F::Log('CURL POST Request Headers: *'.j($Call['CURL']['Headers']).'*', LOG_INFO, 'Administrator');

            if (F::Dot($Call, 'Data'))
                $Call['CURL']['Options'][CURLOPT_POSTFIELDS] = is_string($Call['Data']) ? $Call['Data'] : http_build_query($Call['Data']);

            curl_setopt_array($Call['Link'], $Call['CURL']['Options']);
            $Result = [curl_exec($Call['Link'])];

            $Call = F::Apply(null, 'Info', $Call);
            
            if ($Call['CURL']['Return Header'])
            {
                $Size = curl_getinfo($Call['Link'], CURLINFO_HEADER_SIZE);
                $Headers = mb_substr($Result[0], 0, $Size);
                $Body = mb_substr($Result[0], $Size);

                $HTTPStatus = curl_getinfo($Call['Link'], CURLINFO_HTTP_CODE);
                $Result = [$Body, '_Status' => $HTTPStatus, '_0' => $Headers];
                
                F::Log('CURL GET Response Headers: '.j(explode("\r\n", $Headers)), LOG_INFO, 'Administrator');
            }

            if ($Call['CURL']['Return Header'] && isset($Call['CURL']['Only Header']))
            {
                $Size = curl_getinfo($Call['Link'], CURLINFO_HEADER_SIZE);
                $Result[0] = substr($Result[0], 0, $Size);
            }
            
            if (curl_errno($Call['Link']))
            {
                F::Log('CURL POST error: '.curl_error($Call['Link']).' *'.$Call['Where']['ID'].'* '.PHP_EOL, LOG_NOTICE, 'Administrator');
                F::Log($Result, LOG_NOTICE, 'Administrator');
            }
            else
                F::Log('CURL POST fetched *'.$Call['Where']['ID'].'* ', LOG_INFO, 'Administrator');

            curl_close ($Call['Link']);
        }

        return $Result;
    });

    setFn ('Close', function ($Call)
    {
        if (is_resource($Call['Link']))
            return curl_close ($Call['Link']);
        else
            return null;
    });

    setFn('Execute', function ($Call)
    {
        return true;
    });

    setFn('Version', function ($Call)
    {
/*        $Call['Link'] = curl_init($Call['Where']['ID']);
        $Call = F::Run(null, 'Select User Agent', $Call);

        curl_setopt_array($Call['Link'],
                [
                    CURLOPT_HEADER => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_COOKIEJAR => $Call['Cookie Directory'].DS.parse_url($Call['Where']['ID'], PHP_URL_HOST),
                    CURLOPT_FILETIME => true,
                    CURLOPT_NOBODY => true,
                    CURLOPT_FOLLOWLOCATION => $Call['Follow'],
                    CURLOPT_CONNECTTIMEOUT => $Call['Connect Timeout']
                ]);

        return curl_getinfo($Call['Link'])['filetime'];*/

        return 1;
    });

    setFn('Exist', function ($Call)
    {
        return true; // FIXME
        F::Start('IO Curl Exist '.$Call['Where']['ID']);
        $Call['Link'] = curl_init($Call['Where']['ID']);
        $Call = F::Run(null, 'Select User Agent', $Call);
        $Call = F::Run(null, 'Setup.Options', $Call);


        curl_setopt_array($Call['Link'], $Call['CURL']['Options']);
        $Head = curl_exec($Call['Link']);
        F::Log('CURL HEAD fetched *'.$Call['Where']['ID'].'* '.$Head, LOG_INFO, 'Administrator');
        $Result = (curl_getinfo($Call['Link'])['http_code'] == 200);

        F::Stop('IO Curl Exist '.$Call['Where']['ID']);
        return $Result;
    });

    setFn('Size', function ($Call)
    {
        return '∞'; // FIXME
    });

    setFn('Delay', function ($Call)
    {
        if (isset($Call['Random Delay']))
            $Call['Delay'] = rand(0, $Call['Random Delay']);

        if (isset($Call['Delay']))
            usleep($Call['Delay']);

        return $Call;
    });
    
    setFn('Info', function ($Call)
    {
        $CURLInfo = curl_getinfo($Call['Link']);

        if (is_array($CURLInfo))
        {
            if (isset($CURLInfo['request_header']))
                $CURLInfo['request_header'] = explode("\r\n", $CURLInfo['request_header']);
            F::Log($CURLInfo, LOG_INFO, 'Administrator');
        }

        return $Call;
    });