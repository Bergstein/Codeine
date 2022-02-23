<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call)
    {
        $Call['Checks'] = '';

        $Call['Name'] .= '[]';

        foreach ($Call['Options'] as $Key => $Value)
        {
            if (isset($Call['Localized']) && $Call['Localized'])
                $lValue = '<codeine-locale>'.$Call['Entity'].'.Entity:'.$Call['Key'].'.'.$Value.'</codeine-locale>';
            else
                $lValue = $Value;

            $Checked = (
                $Key == $Call['Value']
                ||
                $Value == $Call['Value']
                || (is_array($Call['Value'])
                && in_array($Value, $Call['Value'])));

            $Call['Checks'] .= F::Run('View', 'Load',
                [
                    'Scope' => $Call['View']['HTML']['Widget Set'].'/Widgets',
                    'ID' => 'Form/Checkgroup/Checkbox',
                    'Data' =>
                    F::Merge ($Call,
                        [
                            'Value' => $Value,
                            'Label' => $lValue,
                            'Checked' => $Checked ? 'checked': ''
                        ])
                ]
            );
        }

        return $Call;
     });