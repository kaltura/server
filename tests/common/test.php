<?php

require_once dirname(__FILE__) . '/KalturaGlobalData.php';

$data = KalturaGlobalData::getData('@BASE_ENTRY_DELETE_ENTRY_ID@');

print("data [" . print_r($data, true) . "]\n");
KalturaGlobalData::setData('@BASE_ENTRY_DELETE_ENTRY_ID@', 10);

$data = KalturaGlobalData::getData('@BASE_ENTRY_DELETE_ENTRY_ID@');
print("data [" . print_r($data, true) . "]\n");
print("end!");