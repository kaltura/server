<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($entry_name); ?></title>
    <?php if( $entry_id ) { ?>
        <meta property="og:url" content="<?php echo $pageURL; ?>" />
        <meta property="og:title" content="<?php echo addslashes(strip_tags(html_entity_decode($entry_name))); ?>" />
        <meta property="og:description" content="<?php echo addslashes(strip_tags(html_entity_decode($entry_description))); ?>" />
        <meta property="og:type" content="video.other" />
        <meta property="og:image" content="<?php echo $entry_thumbnail_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
        <meta property="og:image:secure_url" content="<?php echo $entry_thumbnail_secure_url; ?>/width/<?php echo $uiConf->getWidth();?>" />

        <?php if( isset($flavor_asset_id) ) { ?>
            <meta property="og:video" content="<?php echo $flavorUrl; ?>" />
            <meta property="og:video:type" content="video/mp4" />
        <?php } ?>
        <meta property="og:video:url" content="<?php echo $playerUrl; ?>">
        <meta property="og:video:secure_url" content="<?php echo $playerUrl; ?>">
        <meta property="og:video:type" content="text/html">
        <meta property="og:video:width" content="<?php echo $uiConf->getWidth();?>" />
        <meta property="og:video:height" content="<?php echo $uiConf->getHeight();?>" />

        <meta name="twitter:card" content="player"/>
        <meta name="twitter:site" content="@kaltura"/>
        <meta name="twitter:creator" content="@kaltura"/>
        <meta name="twitter:title" content="<?php echo htmlspecialchars($entry_name); ?>" />
        <meta name="twitter:description" content="<?php echo htmlspecialchars($entry_description); ?>" />
        <meta name="twitter:image" content="<?php echo $entry_thumbnail_secure_url; ?>/width/<?php echo $uiConf->getWidth();?>" />
        <meta name="twitter:player" content="<?php echo $playerUrl; ?>" />
        <?php if( isset($flavorUrl) ) { ?>
            <meta name="twitter:player:stream" content="<?php echo $flavorUrl; ?>" />
        <?php } ?>
        <meta name="twitter:player:stream:content_type" content="video/mp4"/>
        <meta name="twitter:player:height" content="<?php echo $uiConf->getHeight();?>" />
        <meta name="twitter:player:width" content="<?php echo $uiConf->getWidth();?>" />

        <meta property="og:site_name" content="Kaltura" />
    <?php } ?>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        .header {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }

        .main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .player-container {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            /* 16:9 ratio */
            max-width: 960px; max-height: 540px;
            box-sizing: border-box;
            border-radius: 16px;
            overflow: hidden;
        }

        .player-container div[id^="kaltura_player_"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
            border-radius: 16px;
        }

        .entry-details {
            width: 100%;
            max-width: 960px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        h1.title {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        p.description {
            font-size: 18px;
            line-height: 1.5;
            margin: 0;
        }

        <?php if($framed) { ?>
            html, body {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
            }
            .player-container {
                margin: 0 auto;
                padding-top: 20px;
                text-align: center;
            }
        <?php } ?>
    </style>

    <!--[if lte IE 7]>
    <script src="/lib/js/json2.min.js"></script>
    <![endif]-->
    <script src="/lib/js/jquery-1.8.3.min.js"></script>
    <script src="/lib/js/KalturaEmbedCodeGenerator-1.0.6.min.js"></script>
</head>

<body>
    <header class="header">
        <svg width="125px" height="30px" viewBox="0 0 125 30" version="1.1" xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink">
            <title>logo (2)</title>
            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="logo-(2)">
                    <path
                        d="M41.8410288,4.88048101 C42.084499,4.88048101 42.2818319,5.07746004 42.2818319,5.32042822 L42.2641997,16.5376155 L47.0278117,11.0324094 C47.1115643,10.93895 47.2313158,10.8855991 47.3569447,10.8857604 L49.8915624,10.8857604 C50.0600962,10.8903945 50.2111447,10.9904678 50.2807916,11.1436134 C50.3504385,11.2967591 50.3266351,11.4761549 50.2192261,11.6058073 L44.9839548,17.5788239 L50.5674605,24.0592462 C50.6723716,24.1893239 50.6945587,24.3676492 50.624471,24.5192844 C50.5543833,24.6710661 50.4042164,24.7700543 50.2368582,24.7748964 L47.6596295,24.7748964 C47.5347353,24.7753336 47.4158654,24.7219534 47.3334352,24.6282446 L42.2656691,18.8605367 L42.2818319,24.3408124 C42.2818319,24.4566652 42.2351067,24.5676785 42.1522358,24.6487755 C42.0692178,24.7300191 41.9571069,24.7743071 41.8410288,24.7719607 L39.5679542,24.7719607 C39.3274227,24.7719607 39.1311183,24.5794105 39.1271511,24.3393459 L39.1271511,5.32042822 C39.1271511,5.07746004 39.324484,4.88048101 39.5679542,4.88048101 L41.8410288,4.88048101 Z M78.8464477,7.51283179 C79.0812488,7.51283179 79.2731451,7.69599648 79.2865161,7.92693943 L79.2872508,7.952779 L79.2872508,10.8960258 L82.5859272,10.8960258 C82.8207283,10.8960258 83.0126246,11.0791905 83.0259956,11.3101335 L83.0267303,11.335973 L83.0267303,13.0957618 C83.0267303,13.3300631 82.8432093,13.5215867 82.6117877,13.5349611 L82.5859272,13.535709 L79.2872508,13.535709 L79.2872508,19.9296084 C79.2872508,21.5192844 80.0086985,22.2525297 81.5573867,22.2525297 C81.8628632,22.2515032 82.1665765,22.2070685 82.4595637,22.1205455 C82.5910699,22.08315 82.7325677,22.1089603 82.8424746,22.1902038 C82.944594,22.2657281 83.0086574,22.3811409 83.0193836,22.5065259 L83.0208529,22.5355624 L83.0208529,24.3525444 C83.0190897,24.5286699 82.9121215,24.6869042 82.7490244,24.7543628 C82.0562288,25.0109987 81.3205285,25.1330107 80.5817425,25.113653 C77.8411228,25.113653 76.1979558,23.4348145 76.1653364,20.6236985 L76.1648956,20.5382021 L76.1648956,13.5430415 L74.5001293,13.5430415 C74.2653282,13.5430415 74.0734319,13.3598915 74.0600609,13.1289485 L74.0593262,13.1030943 L74.0593262,11.3433055 C74.0593262,11.1090043 74.2428472,10.9174952 74.4742689,10.9041062 L74.5001293,10.9033583 L76.1619569,10.9033583 L76.1619569,7.952779 C76.1619569,7.72122012 76.3413638,7.53088429 76.5698467,7.51399032 L76.5954133,7.51283179 L78.8464477,7.51283179 Z M58.1566203,10.558733 C60.1112881,10.558733 61.801474,11.3487608 62.809003,12.7277167 L62.8511731,12.7863323 L63.0113316,13.0121719 L63.0113316,11.3374395 C63.0113316,11.1058806 63.1907384,10.9155448 63.4192214,10.8986508 L63.4447879,10.8974923 L65.6679048,10.8974923 C65.9027059,10.8974923 66.0946022,11.0806423 66.1079732,11.3115999 L66.1087079,11.3374395 L66.1087079,24.336413 C66.1056223,24.5663587 65.9262154,24.7534829 65.6992019,24.7694677 L65.6737822,24.7704942 L63.4447879,24.7704942 C63.2127786,24.7666813 63.0251434,24.5843965 63.0120662,24.3560639 L63.0113316,24.330547 L63.0113316,22.5399619 L62.8511731,22.7672679 C61.8138165,24.2337586 60.0579509,25.113653 58.1566203,25.113653 C54.3568977,25.113653 51.3814769,21.9181698 51.3814769,17.8369262 C51.3814769,13.7556827 54.3568977,10.558733 58.1566203,10.558733 Z M121.695887,12.7277167 C120.688358,11.3487608 118.998025,10.558733 117.036158,10.558733 C113.237904,10.558733 110.262484,13.7556827 110.262484,17.8369262 C110.262484,21.9181698 113.237904,25.113653 117.036158,25.113653 C118.946304,25.113653 120.694823,24.2337586 121.738057,22.7672679 L121.896746,22.5399619 L121.896746,24.330547 L121.897481,24.3563572 C121.910852,24.5873295 122.102748,24.7704942 122.337549,24.7704942 L124.566544,24.7704942 L124.591963,24.769321 C124.818389,24.7527497 124.996914,24.5657721 125,24.336413 L125,11.3374395 L124.999265,11.3115999 C124.985894,11.0806423 124.793998,10.8974923 124.559197,10.8974923 L122.337549,10.8974923 L122.311689,10.8982402 C122.080267,10.9116293 121.896746,11.1031383 121.896746,11.3374395 L121.896746,13.0121719 L121.738057,12.7863323 L121.695887,12.7277167 Z M88.4779951,10.8974923 C88.7127962,10.8974923 88.9046925,11.0806423 88.9180635,11.3115999 L88.9187982,11.3374395 L88.9187982,18.6390966 C88.9187982,20.8842939 90.0898651,22.2231999 92.0514388,22.2231999 C94.0704638,22.2231999 95.3868488,20.6795718 95.4128562,18.2822995 L95.413297,18.2094149 L95.413297,11.3374395 C95.413297,11.1031383 95.596818,10.9116293 95.8282396,10.8982402 L95.8541001,10.8974923 L98.1403987,10.8974923 C98.3751998,10.8974923 98.5670961,11.0806423 98.5804671,11.3115999 L98.5812018,11.3374395 L98.5812018,24.336413 C98.5812018,24.5707582 98.3976808,24.7622819 98.1662592,24.7756269 L98.1403987,24.7763602 L95.8541001,24.7763602 C95.6192989,24.7763602 95.4274027,24.5931955 95.4140316,24.3622232 L95.413297,24.336413 L95.413297,22.6998094 L95.2501998,22.9476463 C94.3377374,24.3452119 92.8448843,25.113653 91.0464077,25.113653 C87.8214924,25.113653 85.8046713,22.9189031 85.7589748,19.3774747 L85.7582401,19.2696876 L85.7582401,11.3374395 C85.7582401,11.1031383 85.9417611,10.9116293 86.1731827,10.8982402 L86.1990432,10.8974923 L88.4779951,10.8974923 Z M109.513853,11.0271594 C109.501364,10.8053234 109.32372,10.6257369 109.098763,10.6129931 L108.90672,10.6134331 C107.112652,10.6374395 105.52914,11.6672093 104.856768,13.2526763 L104.686325,13.6544948 L104.686325,11.3374395 L104.68559,11.3115999 C104.672219,11.0806423 104.480323,10.8974923 104.245521,10.8974923 L102.025343,10.8974923 L101.999483,10.8982402 C101.768061,10.9116293 101.58454,11.1031383 101.58454,11.3374395 L101.58454,24.336413 L101.585275,24.3622232 C101.598646,24.5931955 101.790542,24.7763602 102.025343,24.7763602 L104.282255,24.7763602 L104.308115,24.7756269 C104.539537,24.7622819 104.723058,24.5707582 104.723058,24.336413 L104.723058,18.6684265 L104.723499,18.5798504 C104.75171,15.669453 106.322586,13.7938114 108.741713,13.7938114 L108.805629,13.7938994 C108.89041,13.794222 108.972988,13.7958645 109.051744,13.8040768 C109.171202,13.8100601 109.287868,13.7673706 109.375147,13.6858044 C109.462426,13.6042382 109.512824,13.4907758 109.514588,13.3714621 L109.514588,11.0529403 L109.513853,11.0271594 Z M72.0727737,5.31896172 C72.0727737,5.07923449 71.8780856,4.88488048 71.6378479,4.88488048 L69.3530186,4.88488048 C69.1127809,4.88488048 68.9180929,5.07923449 68.9180929,5.31896172 L68.9180929,24.336413 C68.9180929,24.5761842 69.1127809,24.7704942 69.3530186,24.7704942 L71.6378479,24.7704942 C71.8780856,24.7704942 72.0727737,24.5761842 72.0727737,24.336413 L72.0727737,5.31896172 Z M58.8457424,13.3949259 C56.4286722,13.3949259 54.5361576,15.3482915 54.5361576,17.8398592 C54.5361576,20.3064966 56.391057,22.2441707 58.7734507,22.2827394 L58.8457424,22.283326 L58.8457424,22.280393 C61.2613433,22.280393 63.1538579,20.3314269 63.1538579,17.8398592 C63.1538579,15.3482915 61.2628127,13.3949259 58.8457424,13.3949259 Z M113.428919,17.8398592 C113.428919,15.3482915 115.321434,13.3949259 117.737035,13.3949259 C120.152635,13.3949259 122.046619,15.3482915 122.046619,17.8398592 C122.046619,20.3314269 120.154105,22.280393 117.737035,22.280393 L117.737035,22.283326 L117.664743,22.2827394 C115.283818,22.2441707 113.428919,20.3064966 113.428919,17.8398592 Z"
                        id="Shape" fill="#282828"></path>
                    <path
                        d="M15.0284465,8.5819035 C14.3466711,8.5819035 13.7927285,7.89851884 13.7927285,7.21806717 L13.7927285,1.36383634 C13.7927285,0.68191817 14.3466711,0 15.0284465,0 C15.7102219,0 16.2656338,0.68191817 16.2656338,1.36383634 L16.2656338,7.21073471 C16.2656338,7.89118639 15.7116913,8.5819035 15.0284465,8.5819035 Z"
                        id="Path" fill="#FA374B"></path>
                    <path
                        d="M10.47642,10.4560639 C9.99447527,10.9385394 9.11874647,10.8461505 8.63680177,10.363675 L4.49325277,6.23550374 C4.01130807,5.7530283 3.91873942,4.88046634 4.40215347,4.39799091 C4.88556752,3.91551547 5.75982697,4.00790438 6.24177168,4.49037982 L10.3853207,8.62441707 C10.8672654,9.10102654 10.959834,9.9735885 10.47642,10.4560639 Z"
                        id="Path" fill="#FFCD00"></path>
                    <path
                        d="M8.59125212,14.9992668 C8.59125212,15.6811849 7.90800733,16.2340519 7.22476255,16.2340519 L1.36648956,16.2340519 C0.683244781,16.2340519 0,15.6811849 0,14.9992668 C0,14.3173632 0.683244781,13.7659628 1.36648956,13.7659628 L7.22476255,13.7659628 C7.90800733,13.7659628 8.59125212,14.3173632 8.59125212,14.9992668 Z"
                        id="Path" fill="#B4DC00"></path>
                    <path
                        d="M10.4764347,19.5424549 C10.9598487,20.0249303 10.8672801,20.8974923 10.3853354,21.3799677 L6.24178637,25.514005 C5.75984166,25.9964804 4.88411287,26.0888693 4.40216816,25.6063939 C3.92022346,25.1239185 4.01132276,24.2513565 4.49326747,23.7688811 L8.63681646,19.6348438 C9.11876117,19.1523684 9.99448996,19.061446 10.4764347,19.5424549 Z"
                        id="Path" fill="#41BEFF"></path>
                    <path
                        d="M15.0284465,21.4254289 C15.7116913,21.4254289 16.2656338,22.1073471 16.2656338,22.7892653 L16.2656338,28.6361637 C16.2656338,29.3180818 15.7116913,30 15.0284465,30 C14.3452017,30 13.7927285,29.3180818 13.7927285,28.6361637 L13.7927285,22.7892653 C13.7927285,22.1073471 14.3466711,21.4254289 15.0284465,21.4254289 Z"
                        id="Path" fill="#006EFA"></path>
                    <path
                        d="M19.580473,19.5424549 C20.0638871,19.061446 20.9381465,19.1523684 21.4215606,19.6348438 L25.5636402,23.7688811 C26.0470543,24.2513565 26.1396229,25.1239185 25.6562089,25.6063939 C25.1727948,26.0888693 24.2985354,25.9964804 23.8151213,25.514005 L19.6730417,21.3799677 C19.1896276,20.9004253 19.0985283,20.0205309 19.580473,19.5424549 Z"
                        id="Path" fill="#FFAA00"></path>
                    <path
                        d="M21.4671102,14.9992668 C21.4671102,14.3173632 22.150355,13.7659628 22.8335998,13.7659628 L28.6918728,13.7659628 C29.3751175,13.7659628 30.0583623,14.3173632 30.0583623,14.9992668 C30.0583623,15.6811849 29.3751175,16.2340519 28.6918728,16.2340519 L22.8277224,16.2340519 C22.150355,16.2340519 21.4671102,15.6811849 21.4671102,14.9992668 Z"
                        id="Path" fill="#00A078"></path>
                    <path
                        d="M19.580473,10.4560786 C19.0985283,9.97360317 19.1896276,9.10104121 19.6730417,8.61856577 L23.8151213,4.48452852 C24.2985354,4.00205309 25.1727948,3.91113066 25.6562089,4.39213961 C26.1396229,4.87314856 26.0470543,5.74717701 25.5636402,6.22965244 L21.4215606,10.3636897 C20.9381465,10.8461651 20.0638871,10.938554 19.580473,10.4560786 Z"
                        id="Path" fill="#3CD2AF"></path>
                    <ellipse id="Oval" fill="#282828" fill-rule="nonzero" cx="15.0284465" cy="14.9992668"
                        rx="1.98802191" ry="1.9841619"></ellipse>
                </g>
            </g>
        </svg>
    </header>

    <main class="main">
        <div class="player-container" id="framePlayerContainer">
            <script>
                function isObject(item) {
                    return item && typeof item === 'object' && !Array.isArray(item);
                }

                /*!
                 * Merge two or more objects together.
                 * (c) 2017 Chris Ferdinandi, MIT License, https://gomakethings.com
                 * @param   {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
                 * @param   {Object}   objects  The objects to merge together
                 * @returns {Object}            Merged values of defaults and options
                 */
                function extend() {
                    // Variables
                    var extended = {};
                    var deep = false;
                    var i = 0;

                    // Check if a deep merge
                    if ( Object.prototype.toString.call( arguments[0] ) === '[object Boolean]' ) {
                        deep = arguments[0];
                        i++;
                    }

                    // Merge the object into the extended object
                    var merge = function (obj) {
                        for (var prop in obj) {
                            if (obj.hasOwnProperty(prop)) {
                                // If property is an object, merge properties
                                if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
                                    extended[prop] = extend(extended[prop], obj[prop]);
                                } else {
                                    extended[prop] = obj[prop];
                                }
                            }
                        }
                    };

                    // Loop through each object and conduct a merge
                    for (; i < arguments.length; i++) {
                        var obj = arguments[i];
                        merge(obj);
                    }

                    return extended;

                };

                function mergeDeep(target,source) {
                    return extend(true,target,source);
                }

                function getParameterByName(name, url) {
                    if (!url) url = window.location.href;
                    name = name.replace(/[\[\]]/g, '\\$&');
                    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                        results = regex.exec(url);
                    if (!results) return null;
                    if (!results[2]) return '';
                    return decodeURIComponent(results[2].replace(/\+/g, ' '));
                }
                var scriptToEval = '';
                var code = new kEmbedCodeGenerator(<?php echo json_encode($embedParams); ?>).getCode();
                var embedType = '<?php echo $embedType;?>';
                var ltIE10 = $('html').hasClass('lt-ie10');
                var isPlaykit = '<?php echo $isPlaykit?>';
                if (isPlaykit === '1') {
                    var data = <?php echo json_encode($embedParams); ?>;
                    var width = <?php echo $uiConf->getWidth();?>;
                    var height = <?php echo $uiConf->getHeight();?>;
                    var playerConfig = {"provider":{"partnerId": data.partnerId,"uiConfId": data.uiConfId},"targetId":"framePlayerContainer"};
                    var externalConfig = getParameterByName("playerConfig");
                    if (externalConfig){
                        try {
                            var parsedConfig = JSON.parse(externalConfig);
                            playerConfig = mergeDeep(playerConfig,parsedConfig);
                        }
                        catch(ee){}
                    }
                    //default
                    if (!height) {
                        height = 400;
                    }
                    if (!width) {
                        width = 600;
                    }
                    var codeUrl = "//" + data.securedHost + "/p/" + data.partnerId +"/embedPlaykitJs/uiconf_id/"+ data.uiConfId;
                    var iframeURL = codeUrl + "/entry_id/" + data.entryId + "?iframeembed=true";
                    var checkForKs = typeof data.flashVars !== 'undefined' ? data.flashVars.hasOwnProperty('ks') && typeof data.flashVars.ks === 'string' : false;
                    if (checkForKs) {
                        if (data.embedType === 'iframe') {
                            iframeURL += "&ks=" + data.flashVars.ks;
                        } else {
                            playerConfig.provider.ks = data.flashVars.ks;
                        }
                    }
                    var embedCode = '';
                    if (data.playlistId)
                    {
                        iframeURL = codeUrl + "/playlist_id/" + data.playlistId + "?iframeembed=true";
                        embedCode = '<scr' + 'ipt src="' + codeUrl + '"></scr' + 'ipt><scr' + 'ipt> var kalturaPlayer = KalturaPlayer.setup(' + JSON.stringify(playerConfig) + ');	kalturaPlayer.loadPlaylist({playlistId: "' + data.playlistId + '"})</scr' + 'ipt>';
                    }
                    else
                    {
                        embedCode = '<scr' + 'ipt src="' + codeUrl + '"></scr' + 'ipt><scr' + 'ipt> var kalturaPlayer = KalturaPlayer.setup(' + JSON.stringify(playerConfig) + ');	kalturaPlayer.loadMedia({entryId: "' + data.entryId + '"})</scr' + 'ipt>';
                    }

                    code = embedCode;
                    if (data.embedType === 'iframe') {
                        code = '<iframe id="kaltura_player" src="'+iframeURL+'" width="'+ width +'" height="'+height+'" allowfullscreen="" webkitallowfullscreen="" mozallowfullscreen="" allow="autoplay; fullscreen; encrypted-media" frameborder="0" style="width: '+width+'px; height: '+height+'px;" itemprop="video" itemscope="" itemtype="http://schema.org/VideoObject"></iframe>';
                    }
                    else if (data.embedType === 'thumb') {
                        code = `<div id='kaltura_player_1' style='width: 560px;height: 395px'></div>
            <scr`+`ipt type='text/javascript' src='${codeUrl}'></scr`+`ipt>
            <scr`+`ipt src='https://static.kaltura.com/content/static/player-scripts/thumbnail-embed.js'></scr`+`ipt>
            <scr`+`ipt>
                    __thumbnailEmbed({
                        config: {
                            provider: {
                                partnerId: '${data.partnerId}',
                                uiConfId: '${data.uiConfId}'
                            },
                            targetId: 'kaltura_player_1'
                        },
                        mediaInfo: {entryId: '${data.entryId}' }
                    });
            </scr`+`ipt>`;
                    }
                }

                // IE9 and below has issue with document.write script tag
                if( ltIE10 && (embedType == 'dynamic' || embedType == 'thumb') ) {
                    $(code).each(function() {
                        if( ! this.outerHTML ) return true;
                        if( this.nodeName === 'SCRIPT' ) {
                            // If we have external script, append to head
                            if( this.src ) {
                                $.getScript(this.src, function() {
                                    $.globalEval(scriptToEval);
                                });
                            } else {
                                scriptToEval += this.innerHTML;
                            }
                        } else {
                            // Write any other elements
                            document.write(this.outerHTML);
                        }
                    });
                } else {
                    document.write(code);
                }
            </script>
        </div>
        <div class="entry-details">
            <h1 class="title"><?php echo htmlspecialchars($entry_name); ?></h1>
            <p class="description"><?php echo htmlspecialchars($entry_description); ?></p>
        </div>
    </main>
</body>
</html>
