<?php

// Generated using OracleSDKGenerator, API Version: 20160918

namespace Oracle\Oci\ObjectStorage;

use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\IteratorConfig;
use Oracle\Oci\Common\Iterators;

class ObjectStorageIterators extends Iterators
{
    public function __construct(AbstractClient $client)
    {
        parent::__construct($client, [
            'listObjectVersions' => new IteratorConfig([
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'items',
            ]),

            'listObjects' => new IteratorConfig([
                'nextTokenResponseGetterMethod' => 'buildNextTokenResponseGetterFromJson',
                'nextTokenResponseGetterArgs' => 'nextStartWith',
                'pageRequestSetterMethod' => 'buildPageRequestSetterToParams',
                'pageRequestSetterArgs' => 'start',
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'objects',
            ]),

            'listRetentionRules' => new IteratorConfig([
                'responseItemsGetterMethod' => 'buildResponseItemsGetter',
                'responseItemsGetterArgs' => 'items',
            ]),

        ]);
    }
}
