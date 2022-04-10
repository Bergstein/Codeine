<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Get', function ($Call) {
        $Index = [];

        foreach ($Call['Nodes'] as $Name => $Node) {
            if (isset($Node['Index']) && $Node['Index']) {
                $Data[$Name]
                    = preg_replace('/<[\/\!]*?[^<>]*?>/Ssi', '.', F::Dot($Call['Data'], $Name));

                if (is_string($Data[$Name]) && preg_match_all('/([^\W]+)/Ssu', $Data[$Name], $Pockets)) {
                    foreach ($Pockets[1] as $Pocket) {
                        $IDX = F::Run('Text.Index.Metaphone.Russian', 'Get', ['Value' => mb_strtolower($Pocket)]);

                        $Index[] = $IDX;
                    }
                }
            }
        }
        $Index = array_unique($Index); // TODO Relevancy
        sort($Index);

        return $Index;
    });
