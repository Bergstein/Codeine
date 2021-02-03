<?php
    
    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Exec Parslet 
     * @package Codeine
     * @version 8.x
     */
    
    setFn('Parse', function ($Call)
    {
        $Replaces = [];
        foreach ($Call['Parsed']['Value'] as $IX => $Match)
        {
            $Replaces[$Call['Parsed']['Match'][$IX]] = $Match;
        }
        
        return $Replaces;
    });