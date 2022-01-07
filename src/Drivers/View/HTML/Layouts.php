<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call)
    {
        if (isset($Call['Layouts']))
        {
            array_unshift($Call['Layouts'],
            [
                'Scope' => $Call['View']['HTML']['Widget Set'],
                'ID' => 'Main',
                'Context' => $Call['Context']
            ]);

            foreach ($Call['Layouts'] as $Layout) // FIXME I'm fat
            {
                if (($Sublayout = F::Run('View', 'Load', $Call, $Layout)) !== null)
                {
                    if (str_contains($Call['Layout'], '<place>Content</place>'))
                        $Call['Layout'] = preg_replace('/<place>Content<\/place>/', $Sublayout, $Call['Layout']);
                    /*else
                        $Call['Layout'] = $Call['Layout'].$Sublayout;*/
                }
            }
        }

        return $Call;
    });