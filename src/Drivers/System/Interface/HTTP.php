<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Web Interface 
     * @package Codeine
     * @version 7.1
     */

    setFn ('Do', function ($Call)
    {
        $Call['Started'] = Started;
        
        F::Stop('Preheat');
        F::Start('Payload');
        
        $Call = F::Hook('beforeInterfaceRun', $Call);
            F::Log('Interface *HTTP* started', LOG_INFO);

            $Call = F::Apply(null, 'Method', $Call);
            $Call = F::Apply(null, 'Server', $Call);
            $Call = F::Apply(null, 'Files', $Call);
            $Call = F::Apply(null, 'Request Headers', $Call);
            $Call = F::Apply(null, 'Request', $Call);
            $Call = F::Apply(null, 'Cookie', $Call);

            $Call = F::Hook('beforeRequestRun', $Call);

                try
                {
                    $Call = F::Apply($Call['Service'], $Call['Method'], $Call);
                }
                catch (Exception $e)
                {
                    F::Log($e->getMessage(), LOG_CRIT, 'Developer');

                    switch ($_SERVER['Environment'])
                    {
                        case 'Development':
                            d(__FILE__, __LINE__, $e->getMessage());
                        break;

                        default:
                            header('HTTP/1.1 503 Service Temporarily Unavailable');
                            header('Status: 503 Service Temporarily Unavailable');

                            header('X-Reason: '.$e->getMessage());

                            if (file_exists(Root.'/Public/down.html'))
                                readfile(Root.'/Public/down.html');
                            else
                                readfile(Codeine.'/down.html');

                            $Call = F::Dot($Call, 'HTTP.Output.Suppress', true);
                        break;
                    }
                }

            $Call = F::Hook('afterRequestRun', $Call);

            F::Stop('Payload');
            F::Start('Cooldown');
    /*        if (isset($Call['Output']))
                $Call['HTTP']['Headers']['Content-Length:'] = strlen($Call['Output']);*/

            $Call = F::Apply(null, 'Send.Response.Headers', $Call);

            F::Stop('Cooldown');
            
            if (F::Dot($Call, 'HTTP.Output.Suppress') === true)
                ;
            else
                F::Run('IO', 'Write', $Call,
                    [
                        'Storage' => 'Output',
                        'Where' => isset($Call['HTTP']['URL'])? $Call['HTTP']['URL']: '',
                        'Data' => $Call['Output']
                    ]);

            F::Log('Interface *Web* finished', LOG_INFO);

        $Call = F::Hook('afterInterfaceRun', $Call);

        return $Call;
    });

    setFn('Redirect', function ($Call)
    {
        if (is_string($Call['Redirect']))
        {
            if (isset($Call['HTTP']['Headers']['Location:']))
                F::Log('Already was redirected to '.$Call['HTTP']['Headers']['Location:'].', skipping redirect to '.$Call['Redirect'], LOG_INFO);
            else
            {
                $URL = $Call['Redirect'];

                if (preg_match_all('@\$([\.\w]+)@', $URL, $Vars))
                {
                    foreach ($Vars[0] as $IX => $Key)
                    {
                        $Value = F::Dot($Call,$Vars[1][$IX]);

                        if (is_scalar($Value))
                            $URL = str_replace($Key, $Value , $URL);
                    }
                }

                if (isset($Call['HTTP']['Redirect']) && $Call['HTTP']['Redirect'] == 'Permanent')
                    $Call['HTTP']['Headers']['HTTP/1.1'] = ' 301 Moved Permanently';
                else
                    $Call['HTTP']['Headers']['HTTP/1.1'] = ' 302 Moved Temporarily';

                $Call['HTTP']['Headers']['Location:'] = $URL;
                $Call['HTTP']['Headers']['X-Redirected-By:'] = 'Codeine';
                $Call['HTTP']['Headers']['Cache-Control:'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';

                F::Log('Redirected to '.$URL, LOG_INFO);
            }
        }

        return $Call;
    });

    setFn('Remote Redirect', function ($Call)
    {
        $URL = $Call['Redirect'];

        if (preg_match_all('@\$([\.\w]+)@', $URL, $Vars))
            foreach ($Vars[0] as $IX => $Key)
                $URL = str_replace($Key, F::Dot($Call,$Vars[1][$IX]) , $URL);

        if (preg_match('/^http/', $URL))
            ;
        else
            $URL = 'http://'.$URL;

        if (isset($Call['HTTP']['Redirect']) && $Call['HTTP']['Redirect'] == 'Permanent')
            $Call['HTTP']['Headers']['HTTP/1.1'] = ' 301 Moved Permanently';
        else
            $Call['HTTP']['Headers']['HTTP/1.1'] = ' 302 Found';

        if ($URL === $Call['HTTP']['URL'])
            F::Log('Infinite redirect loop supressed', LOG_WARNING);
        else
        {
            $Call['HTTP']['Headers']['Location:'] = $URL;
            $Call['HTTP']['Headers']['X-Redirected-By:'] = 'Codeine';
            $Call['HTTP']['Headers']['Cache-Control:'] = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';
            F::Log('Redirected to '.$URL, LOG_INFO);
        }
        
        return $Call;
    });

    setFn('StoreURL', function ($Call)
    {
        if (isset($Call['Request']['BackURL']))
            $Call['BackURL'] = $Call['Request']['BackURL'];
        elseif (isset($_SERVER['HTTP_REFERER']))
            $Call['BackURL'] = $_SERVER['HTTP_REFERER'];

        if (isset($Call['BackURL']))
            F::Log('Back URL set to *'.$Call['BackURL'].'*', LOG_INFO);

        return $Call;
    });

    setFn('RestoreURL', function ($Call)
    {
        if (isset($Call['Request']['BackURL']) && !empty($Call['Request']['BackURL']))
            $Call = F::Apply('System.Interface.HTTP', 'Redirect', $Call, ['Redirect' => $Call['Request']['BackURL']]);

        return $Call;
    });

    setFn('Method', function ($Call)
    {
        // HTTP Method determining
        $Call['HTTP']['Method'] =
            in_array($_SERVER['REQUEST_METHOD'], $Call['HTTP']['Methods']['Allowed'])?
            $_SERVER['REQUEST_METHOD']:
            $Call['HTTP']['Methods']['Default'];

        F::Log('HTTP Method: *'.$Call['HTTP']['Method'].'*', LOG_INFO);

        return $Call;
    });

    setFn('Server', function ($Call)
    {
        empty($_SERVER) ? F::Log('Empty $_SERVER', LOG_DEBUG): F::Log(function () use ($Call) { return $_SERVER;}, LOG_INFO);

        $Call['HTTP']['RAW']['Server'] = $_SERVER;

        foreach ($_SERVER as &$Request)
            $Request = str_replace(chr(0), '', $Request);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
        {
            F::Log('XMLHttpRequest detected, enabling App Context', LOG_INFO);
            $Call['Context'] = 'app';
        }

        return $Call;
    });

    setFn('Files', function ($Call)
    {
        empty($_FILES) ? F::Log('Empty $_FILES', LOG_INFO): F::Log($_FILES, LOG_INFO);

        // Merge FILES to REQUEST.
        if (isset($_FILES['Data']))
            foreach ($_FILES['Data']['tmp_name'] as $IX => $Value)
                if (isset($_FILES['Data']['error'][$IX]) && $_FILES['Data']['error'][$IX] == 0)
                {
                    if (is_array($Value) && count($Value) > 0 && !empty($Value))
                        foreach ($Value as $K2 => $V2)
                        {
                            if (!empty($V2))
                                $_REQUEST['Data'][$IX][$K2] = $V2;
                        }
                    else
                        $_REQUEST['Data'][$IX] = $Value;
                }

        return $Call;
    });

    setFn('Request', function ($Call)
    {
        $Call['Request'] = [];
        
        foreach ($_REQUEST as $Key => $Value)
            $Call['Request'] = F::Dot($Call['Request'], strtr($Key, '_', '.'), str_replace(chr(0), '', $Value));

        empty($_REQUEST) ? F::Log('Empty $_REQUEST', LOG_INFO): F::Log('Request: '.j($_REQUEST), LOG_INFO);

        if ($Call['HTTP']['Method'] == 'POST')
            $Call['HTTP']['RAW'] = file_get_contents("php://input");
        else if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') 
            $Call = F::Dot($Call, 'HTTP.Output.Suppress', true);

        if (isset($Call['HTTP']['Request']['Headers']['Content-Type']))
        {
            $ContentType = mb_strtolower($Call['HTTP']['Request']['Headers']['Content-Type']);
            if (mb_strpos($ContentType, 'application/json') !== false)
            {
                if (isset($Call['HTTP']['RAW']))
                {
                    $RAW = jd($Call['HTTP']['RAW']);
                    if (is_array($RAW))
                        $Call['Request'] = F::Merge($Call['Request'], $RAW);
                    else
                        F::Log('Incorrect JSON Request', LOG_WARNING);
                }
            }
        }
        
        if (isset($_SERVER['REQUEST_URI']))
        {
            $Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if ($Path === null)
                ;
            else
                unset($Call['Request'][$Path]);
        }
        
        return $Call;
    });

    setFn('Cookie', function ($Call)
    {
        empty($_COOKIE) ? F::Log('Empty $_COOKIE', LOG_INFO): F::Log('Cookie: '.j($_COOKIE), LOG_INFO);
        $Call['HTTP']['Cookie'] = $_COOKIE;

        return $Call;
    });

    setFn('Request Headers', function ($Call)
    {
        foreach ($_SERVER as $Key => $Value)
            if (preg_match('/HTTP_(.*)/', $Key, $Pockets))
                $Call['HTTP']['Request']['Headers'][strtr(ucwords(mb_strtolower(strtr($Pockets[1], '_', ' ' ))), ' ', '-')] = $Value;

        return $Call;
    });

    setFn('Send.Response.Headers', function ($Call)
    {
        if (headers_sent())
            F::Log('Headers already sent', LOG_WARNING);
        else
        {
            if (isset($Call['HTTP']['Headers']))
                foreach ($Call['HTTP']['Headers'] as $Key => $Value)
                {
                    if (is_array($Value))
                        ;
                    else
                        $Value = (array) $Value;

                    foreach ($Value as $cValue)
                    {
                        $Header = preg_replace('/\s+/', ' ', $Key . ' ' . $cValue);
                        header ($Header, true);
                    }
                }
        }
        return $Call;
    });

    setFn('Finish', function ($Call)
    {
        if (function_exists('fastcgi_finish_request'))
            fastcgi_finish_request();
        return $Call;
    });