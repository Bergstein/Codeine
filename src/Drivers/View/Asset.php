<?php
    
    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */
    
    setFn('Get', function ($Call)
    {
        $Filename = 'Assets/'.strtr($Call['Asset'], '.', DS).DS.$Call['Scope'].DS.$Call['ID'].'.'.$Call['Extension'];

        $Call['Output']['Content'] = F::findFile($Filename);

        if ($Call['Output']['Content'] === null)
        {
            $Call['HTTP']['Headers']['HTTP/1.0'] = '404 Not Found';
            F::Log('Asset not found: '. $Filename, LOG_INFO);
        }
        
        $Call = F::Dot($Call, 'HTTP.CORS.Enabled', true);
        
        if ('Development' === F::Environment())
            $Call['HTTP']['Headers']['X-Codeine-Filename:'] = $Filename;
        
        return $Call;
    });

    setFn('Get.Cached', function ($Call)
    {
        $Filename = '/var/tmp/codeine/'.$Call['Project']['id'].'/'.$Call['Scope'].'/'.$Call['ID'].'.'.$Call['Extension'];

        $Call['Output']['Content'] = $Filename;

        $Call = F::Dot($Call, 'HTTP.CORS.Enabled', true);

        if ('Development' === F::Environment())
            $Call['HTTP']['Headers']['X-Codeine-Filename:'] = $Filename;

        return $Call;
    });