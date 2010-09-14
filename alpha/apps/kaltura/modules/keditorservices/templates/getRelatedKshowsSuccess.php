<kshows>
<?php 

function array_xml($array, $num_prefix = "num_")
{
    if(!is_array($array)) // text
    {
        return $array;
    }
    else
    {
        $return = null;
    	foreach($array as $key=>$val) // subnode
        {
            $key = (is_numeric($key)? $num_prefix : $key);
            $return.="<".$key.">".array_xml($val, $num_prefix)."</".$key.">";
        }
    }

    return $return;
}


echo array_xml( $kshowdataarray, 'kshow' );

?>
</kshows>
