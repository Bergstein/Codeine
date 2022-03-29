<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Elements = F::Run('Entity', 'Read', $Call);

        F::Run('Entity', 'Delete', $Call);

        foreach ($Elements as $Element) {
            $ID = $Element['ID'];
            unset($Element['ID']);
            F::Run('Entity', 'Update', $Call, ['One' => true, 'Where' => $ID, 'Data' => $Element]);
        }

        return $Call;
    });