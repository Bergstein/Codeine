<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.x
     */

    setFn('Identificate', function ($Call)
    {
        /*if ($Call['Loginza']['ID'] == 0)
            $Call = F::Hook('Loginza.NotConfigured', $Call);*/

        $Call['LoginzaURL'] = urlencode($Call['HTTP']['Proto'].$Call['HTTP']['Host'].'/auth/social'); // FIXME
        return $Call;
    });

    setFn('Authenticate', function ($Call)
    {
        if ($Call['Loginza']['ID'] > 0)
            $Auth = '&id='.$Call['Loginza']['ID']
                    .'&sig='.md5($Call['Request']['token'].$Call['Loginza']['Key']);
        else
            $Auth = '';

        $Response = json_decode(F::Run('IO', 'Read',
         [
             'Storage' => 'Web',
             'Where'   =>
                 'http://loginza.ru/api/authinfo?token='
                 .$Call['Request']['token'].
                 $Auth
         ])[0], true);

        if (isset($Response['identity']))
        {
            $Provider = parse_url($Response['provider'], PHP_URL_HOST);

            $UserData = [
                        'External' => $Provider,
                        'EMail' => $Response['identity'],
                        'Status' => 1,
                        'Password' => sha1(rand()),
                        'Title' => isset($Response['name']['full_name'])
                                ? $Response['name']['full_name']
                                : $Response['name']['first_name'].' '.$Response['name']['last_name']];
            // FIXME

            foreach ($Call['Loginza']['Map'] as $Own => $Their)
                $UserData[$Own] = F::Dot($Response, $Their);

            // Проверить, есть ли такой пользователь

            $Call['User'] = F::Run('Entity','Read',
                [
                    'Entity' => 'User',
                    'One'    => true,
                    'Where'  => [
                        'EMail' => $Response['identity']
                    ]
                ]);

            // Если нет, зарегистрировать или прикрепить
            if (empty($Call['User']))
            {
                if (isset($Call['Session']['User']['ID']))
                {
                    $Call['User'] = F::Run('Entity','Update',
                        [
                            'Entity' => 'User',
                            'One' => true,
                            'Data'  =>
                            [
                                strtr($Provider, ['.'=>'']) => $Response
                            ]
                        ]);

                    F::Log('Account attached to user'.$Call['User']['ID'], LOG_INFO, 'Security');
                }
                else
                {
                    $Call['User'] = F::Run('Entity','Create',
                        [
                            'Entity' => 'User',
                            'One'    => true,
                            'Data'   => $UserData
                        ]);

                    F::Log('User registered '.$Call['User']['ID'], LOG_INFO, 'Security');
                }
            }
            else
            {
                $Call['User'] = F::Run('Entity','Update',
                    [
                         'Entity'   => 'User',
                         'Where'    => $Call['User']['ID'],
                         'One'      => true,
                         'Data'     => $UserData
                    ]);
            }
        }
        else
        {
            $Call['Output']['Content'][] =
                [
                    'Type' => 'Template',
                    'Scope' => 'User/Authenticate',
                    'ID' => 'Failed'
                ];

            F::Log('Loginza failed '.$Call['User']['ID'], LOG_ERR, 'Security');
        }

        return $Call;
    });