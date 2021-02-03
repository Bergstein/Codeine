<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Exec Parslet 
     * @package Codeine
     * @version 6.0
     */

     setFn('Parse', function ($Call)
     {
         $Replaces = [];

         foreach ($Call['Parsed']['Value'] as $IX => $Match)
              $Replaces[$Call['Parsed']['Match'][$IX]] = '<a class="email" href="mailto:'. $Match.'">'. $Match.'</a>';

         return $Replaces;
     });