<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description  
     * @package Codeine
     * @version 43.6.1
     */

    setFn('Process', function ($Call)
    {
        if (isset($Call['HTTP']['URI']))
        {
            $Call = F::Apply(null, 'Page', $Call);
            $Call = F::Apply(null, 'Title', $Call);
            $Call = F::Apply(null, 'Keywords', $Call);
            $Call = F::Apply(null, 'Description', $Call);
            $Call = F::Apply(null, 'Header', $Call);
        }

        return $Call;
    });

    setFn('Title', function ($Call)
    {
        if (preg_match($Call['Meta']['Pattern']['Title'], $Call['Output']))
        {
            $Call = F::Live($Call['Meta']['Sources']['Title'], $Call);

            if (isset($Call['View']['HTML']['Title'][count($Call['View']['HTML']['Title'])-1]))
            {
                $Call['View']['HTML']['Header RAW'] = $Call['View']['HTML']['Title'][count($Call['View']['HTML']['Title'])-1];
                $Call['View']['HTML']['Header'] = strip_tags($Call['View']['HTML']['Title'][count($Call['View']['HTML']['Title'])-1], '<l><k>');
            }

            if ($Call['Meta']['Title']['Reverse'])
                $Call['View']['HTML']['Title'] = array_reverse($Call['View']['HTML']['Title']);

            $Call['View']['HTML']['Title'] = implode($Call['Meta']['Title']['Delimiter'], $Call['View']['HTML']['Title']);
            $Call['View']['HTML']['Title'] = html_entity_decode(strip_tags($Call['View']['HTML']['Title'], '<l><k>'));
            $Call['View']['HTML']['Title'] =
                preg_replace('/[\x00-\x1F\x7F]/','',$Call['View']['HTML']['Title']);

            $Call['Output'] = preg_replace(
                        $Call['Meta']['Pattern']['Title'],
                        '<title itemprop="name">'.$Call['View']['HTML']['Title'].'</title>'
                        .'<meta property="og:title" content="'.$Call['View']['HTML']['Title'].'"/>',
                        $Call['Output']);
        }

        return $Call;
    });

    setFn('Keywords', function ($Call)
    {
        if (preg_match($Call['Meta']['Pattern']['Keywords'], $Call['Output']))
        {
            $Call = F::Live($Call['Meta']['Sources']['Keywords'], $Call);

            if (!isset($Call['View']['HTML']['Keywords']))
                $Call['View']['HTML']['Keywords'] = '';
            else
                if (is_array($Call['View']['HTML']['Keywords']))
                    $Call['View']['HTML']['Keywords'] = implode(',',
                        array_unique($Call['View']['HTML']['Keywords']));

            $Call['Output'] = preg_replace(
                        $Call['Meta']['Pattern']['Keywords'],
                        '<meta name="keywords" content="'.strip_tags($Call['View']['HTML']['Keywords'], '<l><k>').'" />',
                        $Call['Output']);
        }

        return $Call;
    });

    setFn('Description', function ($Call)
    {
        if (preg_match($Call['Meta']['Pattern']['Description'], $Call['Output']))
        {
            $Call = F::Live($Call['Meta']['Sources']['Description'], $Call);

            if (!isset($Call['View']['HTML']['Description']))
                $Call['View']['HTML']['Description'] = '';

            if (is_array($Call['View']['HTML']['Description']))
                $Call['View']['HTML']['Description'] = array_pop($Call['View']['HTML']['Description']);

            $Call['Output'] = preg_replace(
                        $Call['Meta']['Pattern']['Description'],
                        '<meta name="description" content="'.strip_tags($Call['View']['HTML']['Description'], '<l><k>').'" />',
                        $Call['Output']);
        }

        return $Call;
    });

    setFn('Header', function ($Call)
    {
        if (isset($Call['View']['HTML']['Header']))
            $Call['Output'] = str_replace('<header/>', $Call['View']['HTML']['Header'], $Call['Output']);
        else
            $Call['Output'] = str_replace('<header/>', '', $Call['Output']);
        
        if (isset($Call['View']['HTML']['Header RAW']))
            $Call['Output'] = str_replace('<headerraw/>', $Call['View']['HTML']['Header RAW'], $Call['Output']);
        else
            $Call['Output'] = str_replace('<headerraw/>', '', $Call['Output']);

        return $Call;
    });

    setFn('Page', function ($Call)
    {
        if (
                preg_match($Call['Meta']['Pattern']['Title'], $Call['Output'])
                ||
                preg_match($Call['Meta']['Pattern']['Keywords'], $Call['Output'])
                ||
                preg_match($Call['Meta']['Pattern']['Description'], $Call['Output'])
        )
        {
            if ($Call['HTTP']['URL'] == '/')
                $Call['HTTP']['URL'] = '//';

            $Page = F::Run('Entity', 'Read', $Call,
                        [
                            'One' => true,
                            'Entity' => 'Page',
                            'Where' => ['Slug' => substr($Call['HTTP']['URL'], 1)]
                        ]);

            if ($Page !== null)
            {
                array_unshift($Call['View']['HTML']['Title'], $Page['Title']);
                $Call['View']['HTML']['Description'] = isset($Page['Description'])? $Page['Description']: '';
                $Call['View']['HTML']['Keywords'] = isset($Page['Keywords'])? $Page['Keywords']: '';
            }
        }

        return $Call;
    });