<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description: Simple HTML Renderer
     * @package Codeine
     * @version 8.x
     */

    setFn('Render', function ($Call) {
        $Call['HTTP']['Headers']['Content-Type:'] = mime_content_type($Call['Output']['Content']);

        $pi = pathinfo($Call['Output']['Content']);

        if (isset($Call['Output']['Title'])) {
        } else {
            $Call['Output']['Title'] = $pi['filename'];
        }

        $Call['HTTP']['Headers']['Content-Disposition:'] =
            'attachment;filename="' . $Call['Output']['Title'] . '.' . $pi['extension'] . '"';

        readfile($Call['Output']['Content']);

        $Call['No Print Anymore'] = true;

        return $Call;
    });
