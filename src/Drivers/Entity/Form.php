<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Generate', function ($Call)
    {
        $IC = 0;

        if (isset($Call['Data']))
            ;
        else
            $Call['Data'] = [];
        
        $Call = F::Apply('Entity.Form.Layout.'.$Call['FormLayout'], 'Start', $Call);

        $Call['FID'] = F::Live($Call['FID']);

        foreach ($Call['Nodes'] as $Name => $Node)
        {
            $IC++;
            $Widget = null;

            if (isset($Call['Only Required']) &&
                $Call['Only Required'] == true && (!isset($Node['Required']) or !$Node['Required']))
                continue;

            if (isset($Node['Widgets']))
            {
                if (isset($Node['Widgets'][$Call['Purpose']])) // Для нашего случая
                    $Widget = $Node['Widgets'][$Call['Purpose']];
                elseif (isset($Node['Widgets']['Write'])) // Для записи как таковой
                    $Widget = $Node['Widgets']['Write'];

                if (isset($Call['OnlyTag']))
                {
                    if (isset($Node['Scope']))
                        ;
                    else
                    {
                        F::Log('Widget for *'.$Name.'* skipped (scope not set)', LOG_DEBUG);
                        continue;
                    }

                    if (in_array($Node['Scope'], $Call['OnlyTag']))
                    ;
                    else
                    {
                        F::Log('Widget for *'.$Name.'* skipped (scope '.
                            $Node['Scope'][0].' not equal tag '.$Call['OnlyTag'][0].'),', LOG_DEBUG);

                        continue;
                    }
                }
                else
                    if (isset($Node['Scope']) && !in_array($Call['Tag'], (array) $Node['Scope']))
                        continue;

                /*if ($Call['Purpose'] == 'Create' && !empty(F::Dot($Call['Data'], $Name)))
                    continue;*/

                if (null !== $Widget)
                {
                    F::Log('Widget for *'.$Name.'* processing', LOG_DEBUG);

                    $Widget = F::Merge($Node, $Widget);

                    $Widget['Entity'] = $Call['Entity'];
                    $Widget['Node'] = $Name;
                    $Widget['Name'] = 'Data';
                    
                    $Widget['Class']['Node'] = strtolower('Widget_'.str_replace('.', '_', $Widget['Entity'].'.'.$Name));
                    
                    $Widget['Key'] = $Name;
                    $Widget['ID'] = $Call['FID'].'_'.strtr($Name, '.','_');
                    $Widget['Context'] = $Call['Context'];

                    if($IC == 0)
                        $Widget['Autofocus'] = true;

                    if (isset($Node['Options']))
                        $Widget['Options'] = F::Live($Node['Options'],
                        [
                            'Node'      => $Node,
                            'Name'      => $Name,
                            'Action'    => $Call['Action'],
                            'Data'      => $Call['Data']
                        ]);
                    else
                        $Widget['Options'] = [];

                    if (isset($Node['Localized']) && $Node['Localized'])
                        $Widget['Label']  = $Call['Entity'].'.Entity:'.$Name.'.Label';
                    else
                        $Widget['Label']  = $Call['Entity'].'.Entity:'.$Name;

                    if (strpos($Name, '.') !== false)
                    {
                        $Slices = explode('.', $Name);

                        foreach ($Slices as $Slice)
                            $Widget['Name'].= '['.$Slice.']';
                    }
                    else
                        $Widget['Name'] .= '['.$Name.']';

                    if (isset($Call['Data']))
                        $Widget['Data'] = $Call['Data'];
                    
                    if (($Widget['Value'] = F::Dot($Call['Data'], $Name)) === null)
                    {
                        if (isset($Node['Default']))
                            $Widget['Value'] = F::Live($Node['Default'], $Call);
                        else
                            $Widget['Value'] = null;
                    }
                    else
                        $Widget['Value'] = F::Live($Widget['Value']);

                    if (is_scalar($Widget['Value']))
                    {
                        $Widget['RAWValue'] = $Widget['Value'];
                        $Widget['Value'] = htmlspecialchars($Widget['Value']);
                    }
                    
                    if (!isset($Widget['Weight']))
                        $Widget['Weight'] = $IC; // Magic

                    $Call = F::Apply('Entity.Form.Layout.'.$Call['FormLayout'], 'Add', $Call,
                        [
                            'Name' => $Name,
                            'Widget' => $Widget
                        ]);

                    $Call['Widget'] = null;
                }
            }
        }

        $Call = F::Apply('Entity.Form.Layout.'.$Call['FormLayout'], 'Finish', $Call);

        if (isset($Call['Output']['Form']))
            $Call['Output']['Form'] = F::Sort($Call['Output']['Form'], 'Weight', SORT_ASC);

        return $Call;
    });