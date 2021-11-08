<?php
class UploadManagerConstants
{
    // 5MB
    const DEFAULT_PART_SIZE_IN_BYTES = 5 * 1024 * 1024;
    const PART_SIZE_IN_BYTES = 'partSizeInBytes';
    const ALLOW_MULTIPART_UPLOADS = 'allowMultipartUploads';
    const ALLOW_PARALLEL_UPLOADS = 'allowParallelUploads';
    const CONCURRENCY = 'concurrency';
    // Total number of promises in any given time
    const DEFAULT_CONCURRENCY = 5;

    const DEFAULT_CONFIG = [
        SELF::PART_SIZE_IN_BYTES => self::DEFAULT_PART_SIZE_IN_BYTES,
        SELF::ALLOW_MULTIPART_UPLOADS => true,
        SELF::ALLOW_PARALLEL_UPLOADS => true,
        SELF::CONCURRENCY => self::DEFAULT_CONCURRENCY
    ];
}
