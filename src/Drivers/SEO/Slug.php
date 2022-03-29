<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Generate', function ($Call) {
        $Call['Value'] = [];

        // if (!isset($Call['Data']['Slug']) or empty($Call['Data']['Slug']))
        {
            if (is_array($Call['Key'])) {
                foreach ($Call['Key'] as $cKey) {
                    if (F::Dot($Call['Data'], $cKey) != null) {
                        $Call['Data'][$cKey] = trim(F::Dot($Call['Data'], $cKey));

                        if (!empty($Call['Data'][$cKey])) {
                            $Call['Value'][] = F::Dot($Call['Data'], $cKey);
                        }
                    }
                }

                if (!empty($Call['Value'])) {
                    $Call['Value'] = implode($Call['Delimiter'], $Call['Value']);
                }
            } else {
                if (isset($Call['Data'][$Call['Key']])) {
                    $Call['Value'] = trim($Call['Data'][$Call['Key']]);
                }
            }

            if (empty($Call['Value'])) {
                return null;
            }

            $Call['Value'] = mb_strtolower($Call['Value']);

            $Call['Value'] = strtr($Call['Value'], ' ', $Call['Delimiter']);

            if (isset($Call['Transliteration'])) {
                $Call['Value'] =
                    F::Live($Call['Transliteration'], ['Value' => $Call['Value']]);
            }

            $Call['Value'] = preg_replace('/([^a-z0-9\-])/', '', $Call['Value']); // FIXME

            $Call['Value'] = preg_replace(
                '/([\\' . $Call['Delimiter'] . ']{2,})/',
                $Call['Delimiter'],
                $Call['Value']
            ); // Remove double delimiters
        }
        /*else
            $Call['Value'] = $Call['Data']['Slug'];*/

        return $Call['Value'];
    });