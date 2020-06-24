<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Query', function ($Call)
    {
        if (isset($Call['Scope']))
            $Call['Scope'] = $Call['Entity'].'/'.$Call['Scope'];
        else
            $Call['Scope'] = $Call['Entity'];

        $Call['Query'] = preg_replace('/[^\w \d]+/SsUu', ' ', $Call['Query']);

        $Call['Query'] = preg_split('/\s/', mb_strtolower($Call['Query']));
        $Results = [];

        $Call = F::Apply('Entity', 'Load', $Call, ['Entity' => $Call['Entity']]);

        $Relevance = [];

        if (isset($Call['Where']))
            $Call['Where'] = F::Live($Call['Where'], $Call);
        
        foreach($Call['Query'] as $Keyword)
        {
            $Call['Where']['Keywords'] = '~'.$Keyword;
            $KeywordResults = F::Run('Entity', 'Read',
                [
                    'Entity' => $Call['Entity'],
                    'Where'  => $Call['Where']
                ]);

            if (is_array($KeywordResults))
                foreach ($KeywordResults as $KeywordResult)
                    $Results[$KeywordResult['ID']] = $KeywordResult;

            $IDs = F::Extract($KeywordResults, 'ID');
            sort($IDs);
            $Relevance = array_merge($Relevance, $IDs);
        }
        $Relevance = array_count_values($Relevance);
        arsort($Relevance);
        $Relevance = array_keys($Relevance); // FIXMEEEEE

        $IDs = [];

        foreach ($Relevance as $RankedID)
        {
            $Result = $Results[$RankedID];
            $Result['From'] = $Call['HTTP']['Host'];
            $Result['URL']  = $Call['HTTP']['Proto'].$Call['HTTP']['Host'].'/'.$Call['Slug']['Entity'].'/'.$Result['ID'];
            $IDs[$Result['ID']] = $Result['ID'];
        }

        $Meta = ['Hits' => [$Call['Scope'] => count($Results)]];
        return ['IDs' => $IDs, 'Meta' => $Meta];
    });