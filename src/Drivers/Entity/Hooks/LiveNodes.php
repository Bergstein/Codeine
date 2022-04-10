<?php

    /* Codeine
     * @author bergstein@trickyplan.com
     * @description Default value support
     * @package Codeine
     * @version 8.x
     */

    setFn('Process', function ($Call) {
        // Если модель определена
        if (isset($Call['Nodes']) and !isset($Call['Skip Live'])) {
            if (isset($Call['Data']['ID'])) {
                F::Log('Processing nodes for *' . $Call['Entity'] . ':' . $Call['Data']['ID'] . '*', LOG_DEBUG);
            }

            $Call['Nodes'] = F::Sort($Call['Nodes'], 'Weight', SORT_ASC);

            foreach ($Call['Nodes'] as $Name => $Node) {
                // Если частичная загрузка, то нужно проверить, нужен ли нам этот хук.
                if (isset($Call['Live Fields']) && !in_array($Name, $Call['Live Fields'])) {
                    continue;
                }

                // Если у ноды определён нужный хук
                if (isset($Node['Hooks']) && isset($Node['Hooks'][$Call['On']])) {
                    if (isset($Call['Data']) && ((array)$Call['Data'] === $Call['Data'])) {
                        if (isset($Call['Data']['ID'])) {
                            F::Log(
                                'Processing node ' . $Name . ' for *' . $Call['Entity'] . ':' . $Call['Data']['ID'] . '*',
                                LOG_DEBUG
                            );
                        }

                        if (
                            isset($Node['User Override'])
                            && $Node['User Override']
                            && null != (F::Dot($Call['Data'], $Name))
                        ) {
                            F::Log(function () use ($Call, $Name) {
                                return 'Node *' . $Name . '* overriden by user with *' . j(
                                        F::Dot($Call['Data'], $Name)
                                    ) . '*';
                            }, LOG_DEBUG);
                        } else {
                            $LiveValue = F::Live(
                                $Node['Hooks'][$Call['On']],
                                $Call,
                                [
                                    'Name' => $Name,
                                    'Node' => $Node,
                                    'Data' => $Call['Data'],
                                    'Value' => F::Dot($Call, 'Data.' . $Name)
                                ]
                            );

                            $Call['Data'] =
                                F::Dot($Call['Data'], $Name, $LiveValue);

                            F::Log(function () use ($Name, $LiveValue) {
                                return 'Node *' . $Name . '* executed as ' . j($LiveValue);
                            }, LOG_DEBUG);
                            F::Log(function () use ($Node, $Call) {
                                return 'by ' . j($Node['Hooks'][$Call['On']]);
                            }, LOG_DEBUG);
                        }
                    }
                }
            }
        }

        return $Call;
    });
