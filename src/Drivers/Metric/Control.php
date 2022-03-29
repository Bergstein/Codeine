<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Types = F::Run(
            'IO',
            'Execute',
            [
                'Execute' => 'Distinct',
                'Storage' => 'Primary',
                'Scope' => 'Metric',
                'Fields' => ['Type'],
                'No Where' => true
            ]
        );

        $Resolutions = F::Run(
            'IO',
            'Execute',
            [
                'Execute' => 'Distinct',
                'Storage' => 'Primary',
                'Scope' => 'Metric',
                'Fields' => ['Resolution'],
                'No Where' => true
            ]
        );

        $Headers = ['Type', 'Count'];
        foreach ($Resolutions['Resolution'] as $Resolution) {
            $Headers [] = 'Sum R' . $Resolution;
        }

        $Rows = [$Headers];

        foreach ($Types['Type'] as $Type) {
            $Row = [
                $Type,
                F::Run(
                    'Metric.Calc',
                    'Count',
                    $Call,
                    [
                        'Metric' =>
                            [
                                'Type' => $Type
                            ]
                    ]
                )
            ];

            foreach ($Resolutions['Resolution'] as $Resolution) {
                $Row[$Resolution] =
                    F::Run(
                        'Metric.Calc',
                        'Sum',
                        $Call,
                        [
                            'Metric' =>
                                [
                                    'Type' => $Type,
                                    'Resolutions' => [$Resolution]
                                ]
                        ]
                    );
            }

            $Rows[] = $Row;
        }

        $Call['Output']['Content'][] =
            [
                'Type' => 'Table',
                'Value' => $Rows
            ];
        return $Call;
    });

    setFn('Menu', function ($Call) {
        return [
            'Count' =>
                F::Run(
                    'Formats.Number.French',
                    'Do',
                    [
                        'Value' => F::Run(
                            'IO',
                            'Execute',
                            [
                                'Execute' => 'Count',
                                'Storage' => 'Primary',
                                'Scope' => 'Metric',
                                'No Where' => true
                            ]
                        )
                    ]
                )
        ];
    });