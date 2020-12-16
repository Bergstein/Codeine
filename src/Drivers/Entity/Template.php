<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 8.x
     */

    setFn('Table', function ($Call)
    {
        $Call = F::Apply('Entity', 'Load', $Call);

        $Call['Nodes'] = F::Sort($Call['Nodes'], 'Weight', SORT_ASC);

        foreach ($Call['Nodes'] as $Name => $Node)
            $Rows[] = '<block><tr><th><l2>'.$Call['Entity'].'.Entity:'.$Name.'</l2></th><td><k>'.$Name.'</k></td></tr></block>';

        $Call['Output']['Content'][] = '<table class="table">'.implode("\n", $Rows).'</table>';

        return $Call;
    });