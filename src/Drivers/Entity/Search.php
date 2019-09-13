<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 7.4
     */

    setFn('Do', function ($Call)
    {
        $Call['Provider'] = $Call['Entity'];
        $Call = F::Dot($Call,'Search.Index', $Call['Entity']);

        $Call = F::loadOptions($Call['Entity'].'.Entity', null, $Call);
        $Call = F::Hook('beforeEntitySearch', $Call);

        $Call = F::Apply( 'Search', 'Query', $Call);
        
/*        if (isset($Call['Search']['Provider']['Available'][$Call['Entity']]['Nodes']))
            $Fields = $Call['Search']['Provider']['Available'][$Call['Entity']]['Nodes'];
        else
            $Fields = ['ID', 'Title'];*/
        
        $Call['Elements'] = F::Run('Entity', 'Read', $Call,
            [
                'Limit!'  => null,
                'Where!'  =>
                [
                    'ID' =>
                    [
                        '$in' => array_keys($Call['Elements'])
                    ]
                ],
                'Sort'  => F::Dot($Call['Search']['Provider']['Available'][$Call['Entity']], 'List.Sort')
            ]
        );
        
        $Call['Scope'] = isset($Call['Scope'])? strtr($Call['Entity'], '.', '/').'/'.$Call['Scope'] : strtr($Call['Entity'], '.', '/');
        
        $Call['Layouts'][] =
            [
                'Scope' => $Call['Scope'],
                'ID' => isset($Call['Custom Templates']['List'])? $Call['Custom Templates']['List'] :'List',
                'Context' => $Call['Context']
            ];

        $Empty = false;

        $Call['Template'] = (isset($Call['Template'])? $Call['Template']: 'Short');
        
        F::Log('Search template is *'.$Call['Template'].'*', LOG_INFO);

        if (sizeof($Call['Elements']) == 0)
            $Empty = true;

        if (isset($Call['Where']) && $Call['Where'] === [])
            $Empty = true;

        if (null === $Call['Elements'])
            $Empty = true;

        if (isset($Call['NoEmpty']))
            $Empty = false;

        if ($Empty)
        {
            $Empty = isset($Call['Custom Templates']['Empty'])? $Call['Custom Templates']['Empty']: 'Empty';

            $Call['Output']['Content'][]
                = ['Type' => 'Template', 'Scope' => $Call['Scope'], 'Entity' => $Call['Entity'],  'ID' => $Empty];

            $Call = F::Hook('Empty', $Call);
        }
        else
        {
            $Call['Layouts'][] =
                [
                    'Scope' => $Call['Scope'],
                    'ID' => (isset($Call['Custom Templates']['Table'])? $Call['Custom Templates']['Table']: 'Table'),
                    'Context' => $Call['Context']
                ];

            if (isset($Call['Reverse']))
                $Call['Elements'] = array_reverse($Call['Elements'], true);

            if (is_array($Call['Elements']))
                foreach ($Call['Elements'] as $IX => $Element)
                {
                    if (!isset($Element['ID']))
                        $Element['ID'] = $IX;

                    if (isset($Call['Page']) && isset($Call['EPP']))
                        $Element['IX'] = $Call['EPP']*($Call['Page']-1)+$IX+1;
                    else
                        $Element['IX'] = $IX+1;

                    if (isset($Call['Show Redirects']) or !isset($Element['Redirect']) or empty($Element['Redirect']))
                    {
                        $Call['Output']['Content'][] =
                            [
                                'Type'  => 'Template',
                                'Scope' => $Call['Scope'],
                                'ID' => 'Show/'
                                    .$Call['Template'],
                                // FIXME Strategy of selecting templates
                                'Data'  => $Element
                            ];
                    }
                }
        }
        $Call = F::Hook('afterEntitySearch', $Call);

        return $Call;
    });