<?php
interface BulkUploadResultStatus extends BaseEnum
{
    const ERROR = 1;
    
    const OK = 2;
    
    const IN_PROGRESS = 3;
}