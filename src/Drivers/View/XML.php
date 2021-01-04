<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description: Simple HTML Renderer
     * @package Codeine
     * @version 8.x
     */

    setFn('Render', function ($Call)
    {
        $XML = new XMLWriter();
        $XML->openMemory();
        $XML->startDocument('1.0', 'UTF-8');
        $XML->setIndent(true);

        if (isset($Call['Output']['Root']))
        {
            $XML->startElement($Call['Output']['Root']);

            if ($NS = F::Dot($Call, 'XML.Namespace'))
            {
                $XML->startAttribute('xmlns');
                    $XML->text($NS);
                $XML->endAttribute();
            }

            if ($Attributes = F::Dot($Call, 'XML.Attributes'))
                foreach ($Attributes as $Key => $Value)
                {
                    if (is_array($Value))
                    {
                        $XML->startAttributeNs($Value['Prefix'], $Value['Key'], null);
                        $XML->text($Value['Value']);
                    }
                    else
                    {
                        $XML->startAttribute($Key);
                        $XML->text($Value);
                    }


                    $XML->endAttribute();
                }

            $Root = '';

            F::Map($Call['Output']['Content'],
               function ($Key, $Value) use ($XML, &$Root)
               {
                   if (substr($Key, 0, 1) == '@')
                   {
                       $XML->startAttribute(substr($Key, 1));
                       $XML->text($Value);
                       $XML->endAttribute();
                   }
                   else
                   {
                       if (is_numeric($Key))
                       {
                           if ($Key > 0) // FIXME Crutch
                               $XML->endElement();
                       }
                       else
                       {
                           $XML->startElement($Key);
                           $Root = $Key;
                       }

                       if (is_array($Value))
                           ;
                       else
                       {
                           $XML->text($Value);
                           $XML->endElement();
                       }
                   }
               }
           );
            $XML->endElement();
        }

        $XML->endDocument();

        $Call['Output'] = $XML->outputMemory(true);
        
        return $Call;
    });
