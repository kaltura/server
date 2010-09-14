<?php

require_once ( "kalturaAction.class.php" );

class kmcEmbedAction extends kalturaAction
{
	public function execute ( ) 
	{
		$embed_code_list = array();
		
		$base_embed_code = "";
		
		$k_pl_code = "";
		$i = 0;
		do 
		{
			$embed_code = trim($this->getP ( "embed_{$i}_xml" , "" ));
			$embed_code_list[$i] = $embed_code;
			
			if( ! $base_embed_code )
			{
				// save the $embed_code to point to the first embed_code
				$base_embed_code  = $embed_code;
			}
			
			//$pattern = "/k_pl_[0-9]+_name=(.*)?&k_pl_[0-9]+_url=(.*)?\"/msi";
			$pattern = "/k_pl_0_name=(.*)?&k_pl_0_url=(.*)?\"/msi";
			
			$res = preg_match_all ( $pattern , $embed_code , $match );
			
			$name = @$match[1][0];
//print_r ( $name );			
			$url =  @$match[2][0];
//			print_r ( $match );

			if ( $url ) 
				$k_pl_code .= "k_pl_{$i}_name={$name}&k_pl_{$i}_url=$url&" . "\n" ;
			
			$i++;
		}
		while ( $embed_code != "" );
/*
 * 
 <object height="330" width="640" type="application/x-shockwave-flash" data="http://localhost/kwidget/wid/_1/ui_conf_id/190" id="kaltura_playlist" style="visibility: visible;">		
<param name="allowscriptaccess" value="always"/><param name="allownetworking" value="all"/><param name="bgcolor" value="#000000"/><param name="wmode" value="opaque"/><param name="allowfullscreen" value="true"/>
<param name="movie" value="http://localhost/kwidget/wid/_1/ui_conf_id/190"/>
<param name="flashvars" 
value="autoPlay=false&layoutId=playlistLight&uid=0&partner_id=1&subp_id=100&
k_pl_autoContinue=true&k_pl_autoInsertMedia=true&
k_pl_0_name=test123&k_pl_0_url=http%3A%2F%2Flocalhost%2Findex.php%2Fpartnerservices2%2Fexecuteplaylist%3Fuid%3D%26partner_id%3D1%26subp_id%3D100%26ks%3D%26format%3D8%26playlist_id%3D8nr1l9eoug"
/>
</object>

 */
		
		$this->embed_code_list = $embed_code_list;
		
		$pattern = "/k_pl_0_name=(.*)?&k_pl_0_url=([^\"]*)?/msi";
		$this->embed_merge = preg_replace( $pattern , $k_pl_code , $base_embed_code );
		sfView::SUCCESS;
	}
}
?>