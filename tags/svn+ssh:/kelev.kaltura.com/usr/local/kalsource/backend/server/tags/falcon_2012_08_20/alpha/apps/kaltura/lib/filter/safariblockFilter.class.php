<?php

class safariblockFilter extends sfFilter
{
  public function execute($filterChain)
  {
    // Execute this filter only once
    if ( $this->isFirstCall())
    {
    	$s = @$_SERVER["HTTP_USER_AGENT"];
    	
    	if ($s && strpos( strToLower( $s), "safari/" ) )
    		$this->getContext()->getResponse()->addStylesheet("safari.css", "last");
    		//$this->getContext()->getController()->forward( 'home' , 'safari' );
    }
 
    // Execute next filter
    $filterChain->execute();
  }
}

