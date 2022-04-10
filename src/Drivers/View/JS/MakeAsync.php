<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description
     * @package Codeine
     * @version 8.x
     */

    setFn('Do', function ($Call) {
        $Links = '';

        foreach ($Call['JS']['Links'] as $Link => $ScriptTag) {
            $Links .= 'var element = document.createElement("script");
            element.src = "' . $Link . '";
            document.body.appendChild(element);';
        }

        $Call['JS']['Links'] = [
            '<script>

         // Add a script element as a child of the body
         function downloadJSAtOnload() {
         ' . $Links . '
         }

         // Check for browser support of event handling capability
         if (window.addEventListener)
         window.addEventListener("load", downloadJSAtOnload, false);
         else if (window.attachEvent)
         window.attachEvent("onload", downloadJSAtOnload);
         else window.onload = downloadJSAtOnload;

        </script>'
        ];

        return $Call;
    });
