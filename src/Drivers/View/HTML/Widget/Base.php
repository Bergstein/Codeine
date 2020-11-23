<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 43.10.2
     */

    setFn('Make', function ($Call)
    {
        if (isset($Call['Attributes']['String']) && !empty($Call['Attributes']['String']))
            foreach ($Call['Attributes']['String'] as $Attribute => $Value)
            {
                if (isset($Call[$Attribute]))
                {
                    $Call[$Attribute] = F::Live($Call[$Attribute], $Call);

                    if ($Call[$Attribute] !== null)
                    {
                        if (is_array($Call[$Attribute]))
                            $Call[$Attribute] = implode(' ', F::Merge($Value, $Call[$Attribute]));

                        $Attributes[] = strtolower($Attribute).'="'.$Call[$Attribute].'"';
                    }
                    else
                    {
                        if (empty($Value))
                            ;
                        else
                        {
                            if (is_array($Value))
                                $Value = implode(' ', $Value);

                            $Attributes[] = strtolower($Attribute).'="'.$Value.'"';
                        }
                    }
                }
            }

        if (isset($Call['Attributes']['Boolean']) && !empty($Call['Attributes']['Boolean']))
            foreach ($Call['Attributes']['Boolean'] as $Attribute => $Value)
            {
                if (isset($Call[$Attribute]))
                {
                    $Call[$Attribute] = F::Live($Call[$Attribute], $Call);

                    if (F::Dot($Call, $Attribute) !== null)
                        $Attributes[] = strtolower($Attribute);
                    else
                        if (!empty($Value) && $Value)
                            $Attributes[] = strtolower($Attribute);
                }
            }

        if (isset($Call['Block']) && $Call['Block'])
            $Call['HTML'] = '<'.$Call['Tag'].' '.implode(' ', $Attributes).'>'.$Call['Value'].'</'.$Call['Tag'].'>';
        else
            $Call['HTML'] = '<'.$Call['Tag'].' '.implode(' ', $Attributes).' />';

        return $Call;
    });