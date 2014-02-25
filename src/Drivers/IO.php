<?php

    /* Codeine
     * @author BreathLess
     * @description  New IO Date
     * @package Codeine
     * @version 7.x
     */

    setFn ('Open', function ($Call)
    {
        if (isset($Call['Storage']))
        {
            $StorageID = $Call['Storage'];

            if (isset($Call['Storages'][$StorageID]))
            {
                if (is_string($Call['Storages'][$StorageID])
                    && isset($Call['Storages'][$Call['Storages'][$StorageID]]))
                $Call['Storages'][$StorageID] = $Call['Storages'][$Call['Storages'][$StorageID]];

                $Call = F::Merge($Call, $Call['Storages'][$StorageID]);
            }
            else
                F::Log($Call['Storage'].' not found'); // FIXME

            if (($Call['Link'] = F::Get($StorageID)) === null)
                $Call['Link'] = F::Set($StorageID, F::Apply($Call['Driver'], 'Open', $Call));

            return $Call;
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_ERR);
            return null;
        }
     });

    setFn ('Read', function ($Call)
    {
        if (isset($Call['Result']))
            unset($Call['Result']);

        if (isset($Call['Storage']))
        {
            $Call = F::Apply('IO', 'Open', $Call);

            if ($Call['Link'] === null)
                return null;

            $Call = F::Hook('beforeIORead', $Call);

            if (!isset($Call['Result']))
            {
                // Если в Where простая переменная - это ID.
                if (isset($Call['Where']) && is_scalar($Call['Where']))
                    $Call['Where'] = ['ID' => $Call['Where']];

                if (isset($Call['Driver']))
                    $Call['Result'] = F::Run ($Call['Driver'], 'Read', $Call);
                else
                    $Call['Result'] = null;

                if (isset($Call['Format']) && is_array($Call['Result']))
                {
                    foreach($Call['Result'] as &$Element)
                        $Element = F::Run($Call['Format'], null, $Call, ['Value' => $Element]);
                }

                $Call = F::Hook('afterIORead', $Call);
            }

            if (isset($Call['Return Key']) && $Call['Result'][$Call['Return Key']])
                return $Call['Result'][$Call['Return Key']];
            else
                return $Call['Result'];
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_CRIT);
            return null;
        }
    });

    setFn ('Write', function ($Call)
    {
        if (isset($Call['Storage']))
        {
            $Call = F::Apply('IO', 'Open', $Call);

            $Call = F::Hook('beforeIOWrite', $Call);

                if ($Call['Link'] === null)
                    return null;

                // Если в Where простая переменная - это ID.
                if (isset($Call['Where']) && is_scalar($Call['Where']))
                    $Call['Where'] = ['ID' => $Call['Where']];

                if (isset($Call['Format']))
                {
                    if (isset($Call['Data']) && is_array($Call['Data']) && isset($Call['Data']['ID']))
                        $Call['ID'] = $Call['Data']['ID'];

                    $Call['Data'] = F::Run ($Call['Format'], null, $Call, ['Value!' => $Call['Data']]);
                }

                if (isset($Call['Driver']))
                    $Call['Data'] = F::Run ($Call['Driver'], null, $Call);
                else
                    F::Log('IO Driver not set.', LOG_CRIT);

            $Call = F::Hook('afterIOWrite', $Call);

            return $Call['Data'];
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_ERR);
            return null;
        }
    });

    setFn ('Close', function ($Call)
    {
        if (isset($Call['Storage']))
        {
            $Call = F::Apply('IO', 'Open', $Call);

            if ($Call['Link'] === null or !$Call['Link'])
                return null;

            return F::Run ($Call['Driver'], 'Close', $Call);
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_ERR);
            return null;
        }
    });

    setFn ('Execute', function ($Call)
    {
        if (isset($Call['Storage']))
        {
            $Call = F::Apply('IO', 'Open', $Call);

            if ($Call['Link'] === null)
                return null;

            if (isset($Call['Where']))
                $Call['Where'] = F::Live($Call['Where']);

            if (isset($Call['Where']) && is_scalar($Call['Where']))
                $Call['Where'] = ['ID' => $Call['Where']];

            return F::Run ($Call['Driver'], $Call['Execute'], $Call);
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_ERR);
            return null;
        }
    });

    setFn ('Commit', function ($Call)
    {
        if (isset($Call['Storage']))
        {
            $Call = F::Apply('IO', 'Open', $Call);

            if ($Call['Link'] === null)
                return null;

            return F::Run ($Call['Driver'], null, $Call);
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_ERR);
            return null;
        }
    });

    setFn ('Rollback', function ($Call)
    {
        if (isset($Call['Storage']))
        {
            $Call = F::Apply('IO', 'Open', $Call);

            if ($Call['Link'] === null)
                return null;

            return F::Run ($Call['Driver'], null, $Call);
        }
        else
        {
            F::Log('IO Null Storage: ', LOG_ERR);
            return null;
        }
    });

    setFn('Shutdown', function ($Call)
    {
        foreach ($Call['Storages'] as $StorageName => $Storage)
            F::Run('IO', 'Close', $Call, ['Storage' => $StorageName]);

        return $Call;
    });