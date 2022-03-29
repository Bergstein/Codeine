<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        if ((isset($Call['Session']['Location']) && $Call['Session']['Location'] != 0) && isset($Call['Where']) && is_array(
                $Call['Where']
            )) {
            $Call['Where']['Location'] = $Call['Session']['Location'];
        }

        return $Call;
    });