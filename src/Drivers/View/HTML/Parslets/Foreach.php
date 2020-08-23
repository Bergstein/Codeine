<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 7.0
     */

    setFn('Parse', function ($Call)
    {
        $Replaces = [];

        foreach ($Call['Parsed']['Value'] as $IX => $Cludge)
        {
            if (($Except = F::Dot($Call, 'Parsed.Options.'.$IX.'.except')) === null)
                $Except = [];
            else
            {
                if (mb_strpos($Except, ',') !== false)
                    $Except = explode(',', $Except);
                else
                    $Except = [$Except];
            }

            $Output = [];
            if (isset($Call['Parsed']['Options'][$IX]['key']))
            {
                $Key = $Call['Parsed']['Options'][$IX]['key'];

                if (mb_strpos($Key, ',') !== false)
                    $Key = explode(',', $Key);
                else
                    $Key = [$Key];

                foreach ($Key as $CMatch)
                {
                    $Value = F::Live(F::Dot($Call['Data'], $CMatch));

                    if ($Value === null)
                        ;
                    else
                        break;
                }
            }

            if (isset($Call['Parsed']['Options'][$IX]['call']))
            {
                $Key = $Call['Parsed']['Options'][$IX]['call'];

                if (mb_strpos($Key, ',') !== false)
                    $Key = explode(',', $Key);
                else
                    $Key = [$Key];

                foreach ($Key as $CMatch)
                {
                    $Value = F::Live(F::Dot($Call, $CMatch)); // FIXME?

                    if ($Value === null)
                        ;
                    else
                        break;
                }
            }

            if (is_array($Value))
            {
                foreach ($Value as $CKey => $CValue)
                {
                    if (in_array($CKey, $Except))
                        ;
                    else
                    {
                        if ($CValue === null)
                            $CValue = 'null';
                        else
                        {
                            if (is_array($CValue))
                                $CValue = array_shift($CValue);

                            if ($CValue === 0)
                                $CValue = '0';

                            if ($CValue === false)
                                $CValue = 'false';

                            if ($CValue === true)
                                $CValue = 'true';
                        }

                        $Output[$CKey] = str_replace(['<fe-key/>', '<fe-value/>'], [$CKey, $CValue], $Cludge);
                    }
                }
            }
            else
                $Output = [];
            
            $Output = implode('', $Output);
            
            $Replaces[$IX] = $Output;
        }

        return $Replaces;
     });