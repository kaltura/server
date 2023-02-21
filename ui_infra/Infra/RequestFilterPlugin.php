<?php

class Infra_RequestFilterPlugin extends Zend_Controller_Plugin_Abstract
{
        public function routeShutdown(Zend_Controller_Request_Abstract $request)
        {
                $params = $request->getParams();
                foreach ($params as $param => $val)
                {
                        if (is_array($val))
                        {
                                foreach ($val as $k => $v)
                                {
                                        $val[$k] = htmlspecialchars($v);
                                }
                                $request->setParam($param, $val);
                        }
                        else
                        {
                                $request->setParam($param, htmlspecialchars($val));
                        }
                }
        }
}
