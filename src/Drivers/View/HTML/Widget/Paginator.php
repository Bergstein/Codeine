<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Make', function ($Call) {
        if (empty($Call['PageURL'])) {
            $Call['PageURL'] = $Call['FirstURL'] . '/page';
        }

        return F::Run('View.HTML.Widget.Paginator.' . $Call['Paginator']['Driver'], 'Make', $Call);
    });
