<?php

namespace Oracle\Oci\Common;

class Defer
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    public function __destruct()
    {
        call_user_func($this->callback);
    }
}

function defer(&$context, $callback)
{
    $context[] = new Defer($callback);
}
