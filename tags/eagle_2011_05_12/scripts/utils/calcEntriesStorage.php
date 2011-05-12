<?php

ini_set("memory_limit","256M");

define('ROOT_DIR', realpath(dirname(__FILE__) . '/../../'));
require_once(ROOT_DIR . '/infra/bootstrap_base.php');
require_once(ROOT_DIR . '/infra/KAutoloader.php');

KAutoloader::addClassPath(KAutoloader::buildPath(KALTURA_ROOT_PATH, "vendor", "propel", "*"));
KAutoloader::setClassMapFilePath('../cache/classMap.cache');
KAutoloader::register();

error_reporting(E_ALL);
KalturaLog::setLogger(new KalturaStdoutLogger());

$dbConf = array (
  'datasources' => 
  array (
    'default' => 'propel',
  
    'propel' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
      	'classname' => 'KalturaPDO',
        'phptype' => 'mysql',
        'database' => 'kaltura',
        'hostspec' => '192.168.192.4',
        'user' => 'kaltura',
        'password' => 'kaltura',
		'dsn' => 'mysql:host=192.168.192.4;dbname=kaltura;user=kaltura;password=kaltura;',
      ),
    ),
    
  
    'propel2' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
      	'classname' => 'KalturaPDO',
        'phptype' => 'mysql',
        'database' => 'kaltura',
        'hostspec' => '192.168.192.4',
        'user' => 'kaltura_read',
        'password' => 'kaltura_read',
		'dsn' => 'mysql:host=192.168.192.4;dbname=kaltura;user=kaltura_read;password=kaltura_read;',
      ),
    ),
    
  
    'propel3' => 
    array (
      'adapter' => 'mysql',
      'connection' => 
      array (
      	'classname' => 'KalturaPDO',
        'phptype' => 'mysql',
        'database' => 'kaltura',
        'hostspec' => '192.168.192.4',
        'user' => 'kaltura_read',
        'password' => 'kaltura_read',
		'dsn' => 'mysql:host=192.168.192.4;dbname=kaltura;user=kaltura_read;password=kaltura_read;',
      ),
    ),
  ),
  'log' => 
  array (
    'ident' => 'kaltura',
    'level' => '7',
  ),
);

//$dbConf = kConf::getDB();
DbManager::setConfig($dbConf);
DbManager::initialize();

$entryIds = array(
	"0_dborm9lg",
	"0_imoflf6p",
	"0_3wm9a6t5",
	"0_cic44kil",
	"0_t4yew63l",
	"0_j1w2l0uo",
	"0_hmfl6yqm",
	"0_j4ciu5i8",
	"0_9exkfida",
	"0_t8i28aed",
	"0_mfzy6fk8",
	"0_8iy78nc9",
	"0_k8ymic7i",
	"0_kebpfp7z",
	"0_f4wb8f7o",
	"0_21hgvyre",
	"0_yc4frw3n",
	"0_xcrrzimo",
	"0_ttu09fn8",
	"0_nv77ncna",
	"0_dx18o48f",
	"0_ssuzmxjb",
	"0_7o9752ad",
	"0_pdzuunaa",
	"0_tymuie23",
	"0_vt8j64df",
	"0_dn3be3s1",
	"0_1naibde2",
	"0_0215mb05",
	"0_l1qu67u2",
	"0_0qpt6fbl",
	"0_o9xrvyv3",
	"0_rjlbesk0",
	"0_0riz850n",
	"8ix7p4nf6s",
	"0_3bpvujdf",
	"0_ddhyy406",
	"0_jdzti6gj",
	"g940g7miui",
	"0_o6m9rxsx",
	"0_qbujfju1",
	"s5qjpsjdnq",
	"s1m3dg09dw",
	"4tl281rhe8",
	"0_r6ehikfb",
	"0_s1ymi20f",
	"0_c087s9fw",
	"0_0jxtpm3o",
	"0_bi1oond7",
	"0_26xr3ht9",
	"0_tt5bc5xi",
	"0_3vxvcx0c",
	"0_336gw1eq",
	"0_a7nnunh6",
	"0_bs3jytlv",
	"0_r5abi3sz",
	"0_107z8t15",
	"0_5y5jlxga",
	"8z8ejn2c05",
	"0_ls0qsskt",
	"scs2tb20nc",
	"kuag5odijy",
	"c3o8gn8zoy",
	"0_pjmp0pk5",
	"0_qepjemqa",
	"0_phw09gy2",
	"0_byhgfhm2",
	"0_cgdx8az5",
	"0_jyfskrz1",
	"0_taz94tkf",
	"0_tpg7dxfz",
	"0_jdnikam7",
	"0_faxgi5zh",
	"0_8q6agy4v",
	"0_za7d1dlv",
	"0_9rq9j3ue",
	"0_90x6alrf",
	"0_sgj0jpbp",
	"0_dzqp2hve",
	"0_2vo8i3zm",
	"0_pbj0mewb",
	"0_ecjqlvcx",
	"0_qwx5tnnh",
	"0_csow76ak",
	"0_ippmvqxb",
	"0_5haayn3s",
	"0_scx59ihb",
	"0_e3c218nb",
	"0_tsokmp6i",
	"0_6ffche90",
	"0_qo5oa2aa",
	"0_grjmi99x",
	"0_c43tio3q",
	"0_wdp2nukp",
	"0_im7bqzvs",
	"0_8znu0fzn",
	"0_r88o5k28",
	"0_lm0ctgv4",
	"0_861czc6n",
	"0_fxybu3tb",
	"0_dl09yagk",
	"fgh8g7uc4c",
	"0_3ruyl4so",
	"0_uz9wwbi0",
	"0_i0wm8ylp",
	"0_gj2hfml9",
	"0_k5fttrmg",
	"0_ucqrdlm7",
	"0_semkx9jy",
	"0_js9xbszt",
	"0_oho170pf",
	"kzawobh3ty",
	"0_a2ph89tt",
	"irp1nztkts",
	"66r541rpjs",
	"0_lrrvlb7w",
	"0_0k002875",
	"0_vx5hb6jn",
	"0_l4liidjf",
	"0_g3nzwjdz",
	"0_uqxgy01f",
	"0_ugw90c4t",
	"0_tf1dd91a",
	"0_30eeu66b",
	"mov5ips91k",
	"6ir3dui0d0",
	"0_opitrvgr",
	"0_oc5vhd33",
	"0_plefn9cl",
	"0_0stl9gvz",
	"0_vth1aakn",
	"0_eyqbhsq9",
	"0_52na77mr",
	"0_7yi05auv",
	"0_dh11mjm2",
	"0_4evhzdbl",
	"dz727fptf0",
	"0_xqncw5pv",
	"0_2rl97bry",
	"0_x8uxq77g",
	"0_9784h9p5",
	"0_hwhdajmr",
	"0_fuqs22j4",
	"4la8ruega0",
	"0_zoq8xc3k",
	"0_beiaainf",
	"0_v0agxi9k",
	"umuobsfqos",
	"sfmrr36r5c",
	"0_63sprljk",
	"0_ktgi5sdv",
	"0_j5w9grsg",
	"0_z4rvq5m3",
	"sns6g8rp40",
	"0_szhtu39v",
	"wkwm07e2mw",
	"0_3mp68oqn",
	"0_un7q4ujm",
	"0_k9yu4jt2",
	"h2a4caf7nk",
	"fvjac6zlmk",
	"36ko97bsy4",
	"0_6pqen4pp",
	"gmvioznyq0",
	"gkiq0jsp90",
	"q6ojro0zhg",
	"0_fwectuev",
	"0_n04gvx36",
	"0_5aot3gzv",
	"p21ippxs70",
	"ncrww3l4j4",
	"q3fucnyueo",
	"58bjik2zbc",
	"3ck2qf1rnk",
	"dk2957a76g",
	"heo5zst99c",
	"ri5waxv6hs",
	"gtthmlm78s",
	"yzcgjotw0o",
	"6o4z2tp314",
	"0_d90mqlli",
	"qfnu55tt7k",
	"ybugoe78ai",
	"6h1pb6f3to",
	"1k9vxhvpk0",
	"2lvaw1odvm",
	"2i8t6vxyuo",
	"3nkrks76w4",
	"0_xjdcym4v",
	"0_ez86zub3",
	"0_xjnit0g7",
	"0_njp5gk8s",
	"dfoymlus6t",
);

$sizeFile = fopen('size.csv', 'a');
$entries = entryPeer::retrieveByPKs($entryIds);
foreach($entries as $entry)
{
	$size = myEntryUtils::calcStorageSize($entry);
	fputcsv($sizeFile, array($size, $entry->getId()));
}
fclose($sizeFile);


echo 'Done';
