<?php

    /* Codeine
     * @author BreathLess
     * @description  
     * @package Codeine
     * @version 7.4
     */

    setFn('Do', function ($Call)
    {
        $Call = F::Merge(F::loadOptions('Entity.'.$Call['Entity']), $Call); // FIXME

        $Call = F::Hook('beforeCatalog', $Call);

        $Call['Layouts'][] = ['Scope' => $Call['Entity'],'ID' => 'Catalog'];

        $Elements = F::Run('Entity', 'Read', $Call, ['Fields' => [$Call['Key']], 'Distinct' => true]);

        $Values = [];

        if (count($Elements) > 0)
        {
            foreach ($Elements as $Element)
                $Values[$Element[$Call['Key']]] = F::Run('Entity', 'Count', $Call,
                    [
                        'Where' =>
                        [
                            $Call['Key'] => $Element[$Call['Key']]
                        ]
                    ]);

            arsort($Values);

            $Call['Output']['Content'][] =
                [
                    'Type'    => 'TagCloud',
                    'Value'   => $Values,
                    'Minimal' => $Call['Minimal'],
                    'Entity'  => $Call['Entity'],
                    'Key'     => $Call['Key']
                ];
        }

        $Call = F::Hook('afterCatalog', $Call);

        return $Call;
    });