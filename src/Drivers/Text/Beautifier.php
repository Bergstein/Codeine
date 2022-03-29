<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        if (isset($Call['Data'][$Call['Key']])) {
            $Call['Value'] = $Call['Data'][$Call['Key']];

            foreach ($Call['Beautifiers'] as $Rule) {
                $Call = F::Apply('Text.Beautifier.' . $Rule, 'Process', $Call);
            }

            return html_entity_decode($Call['Value']);
        }

        return null;
    });