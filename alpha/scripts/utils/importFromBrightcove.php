<?php
ini_set("memory_limit","1024M");
$ini_file = "brightcove.ini";

if ( $argc == 2)
{
	$token = $argv[1];	
}
else
{
	die ( 'usage: php ' . $_SERVER ['SCRIPT_NAME'] . " [Brightcove API token]" . PHP_EOL ); 
}

$extras = parse_ini_file($ini_file);

$page_num = 0;
$run = TRUE;
$header = "*title,description,tags,url,category,contentType";
if(!empty($extras)) {
	foreach($extras as $key=>$value) {
		$header .= ",".$key;
	}
}
echo $header, PHP_EOL;
while($run) {
	$url = "http://api.brightcove.com/services/library?command=find_all_videos&media_delivery=http&token=".$token."&page_number=".$page_num++;
	$json = file_get_contents($url);
	if($json === FALSE) {
		die("Wrong URL: ".$url);
	}
	$data = json_decode($json, true);
	foreach ($data["items"] as $item) {
		if(strpos($item["videoFullLength"]["url"], "rtmp:") === FALSE) {
			echo '"',addslashes($item["name"]),'",';
			echo '"',addslashes($item["shortDescription"]),'",';
			$tags = "";
			if(!empty($item["tags"])) {
				$tags .= '"'.implode(",", $item["tags"]).'"';
			}
			echo $tags,",";
			echo addslashes($item["videoFullLength"]["url"]);
			echo ",,Video,",addslashes($item["videoStillURL"]);
			if(!empty($extras)) {
				foreach($extras as $key=>$value) {
					if(strpos($value, ',') === FALSE) {
						echo ",".addslashes($item[$value]);
					} else {
						$indices = explode(',', $value, 2);
						echo ",".addslashes($item[$indices[0]][$indices[1]]);
					}
				}
			}
			echo PHP_EOL;
		}
	}
	if(count($data["items"]) < 100) {
		$run = FALSE;
	}
}
?>