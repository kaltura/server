<?php

return [
    'interfaces' => [
        'google.cloud.dialogflow.v2.Agents' => [
            'SetAgent' => [
                'method' => 'post',
                'uriTemplate' => '/v2/{agent.parent=projects/*}/agent',
                'body' => 'agent',
                'placeholders' => [
                    'agent.parent' => [
                        'getters' => [
                            'getAgent',
                            'getParent',
                        ],
                    ],
                ],
            ],
            'DeleteAgent' => [
                'method' => 'delete',
                'uriTemplate' => '/v2/{parent=projects/*}/agent',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'GetAgent' => [
                'method' => 'get',
                'uriTemplate' => '/v2/{parent=projects/*}/agent',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'SearchAgents' => [
                'method' => 'get',
                'uriTemplate' => '/v2/{parent=projects/*}/agent:search',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'TrainAgent' => [
                'method' => 'post',
                'uriTemplate' => '/v2/{parent=projects/*}/agent:train',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'ExportAgent' => [
                'method' => 'post',
                'uriTemplate' => '/v2/{parent=projects/*}/agent:export',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'ImportAgent' => [
                'method' => 'post',
                'uriTemplate' => '/v2/{parent=projects/*}/agent:import',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
            'RestoreAgent' => [
                'method' => 'post',
                'uriTemplate' => '/v2/{parent=projects/*}/agent:restore',
                'body' => '*',
                'placeholders' => [
                    'parent' => [
                        'getters' => [
                            'getParent',
                        ],
                    ],
                ],
            ],
        ],
        'google.longrunning.Operations' => [
            'GetOperation' => [
                'method' => 'get',
                'uriTemplate' => '/v2/{name=projects/*/operations/*}',
                'placeholders' => [
                    'name' => [
                        'getters' => [
                            'getName',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
