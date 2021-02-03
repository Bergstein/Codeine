<?php
    
    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Skype Parslet
     * @package Codeine
     * @version 6.0
     */
    
    setFn('Parse', function ($Call)
    {
        $Replaces = [];
        
        foreach ($Call['Parsed']['Value'] as $IX => $Match)
            $Replaces[$Call['Parsed']['Match'][$IX]] = '<a class="skype" href="skype:' . $Match . '">Skype: ' . $Match . '</a>';
        
        return $Replaces;
    });