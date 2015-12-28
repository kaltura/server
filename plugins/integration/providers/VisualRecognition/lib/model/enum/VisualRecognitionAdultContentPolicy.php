<?php
/**
 * @package plugins.integration
 * @subpackage lib.enum
 */
interface VisualRecognitionAdultContentPolicy extends BaseEnum
{
        const AUTO_REJECT = 1;
        const AUTO_FLAG = 2;
        const IGNORE = 3;
}
