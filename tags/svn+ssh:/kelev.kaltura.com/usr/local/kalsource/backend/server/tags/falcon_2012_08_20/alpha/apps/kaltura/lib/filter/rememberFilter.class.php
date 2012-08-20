<?php

class rememberFilter extends sfFilter
{
  public function execute($filterChain)
  {
    // Execute this filter only once
    if ($this->isFirstCall())
    {
      // Filters don't have direct access to the request and user objects.
      // You will need to use the context object to get them
      $request = $this->getContext()->getRequest();
      $user    = $this->getContext()->getUser();
      
      $screenname = $request->getCookie( 'screenname');
      $id = $request->getCookie( 'id');
      if ( $screenname && $id )
      {
        // sign in
        $user->setAuthenticated(true);
        $user->setAttribute('screenname', $screenname ); 
		$user->setAttribute('id', $id ) ;
      }
      
      // now remember stuff for the crawler tracker
      $stamp = $request->getParameter('stamp', null);
      if( $stamp ) echo $this->getContext()->getResponse()->setCookie( 'stamp', $stamp, time() + 31536000 , '/' );
      
    }
 
    // Execute next filter
    $filterChain->execute();
  }
}

?>