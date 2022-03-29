<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Elements = F::Run(
            'Entity',
            'Count',
            [
                'Entity' => $Call['Entity']
            ]
        );

        $Call['Output']['Content'] = $Call['Entity'] . ':' . $Elements;
        F::Run(
            'IO',
            'Write',
            $Call,
            ['Storage' => 'Metrics', 'Scope' => 'Total', 'Where' => $Call['Entity'], 'Data' => $Elements]
        );

        return $Call;
    });