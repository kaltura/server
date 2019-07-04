<?php

/**
 *
 * Translation reference can be found http://www.theneitherworld.com/mcpoodle/SCC_TOOLS/DOCS/SCC_FORMAT.HTML and http://www.theneitherworld.com/mcpoodle/SCC_TOOLS/DOCS/CC_CHARS.HTML
 * Class kSCCParser
 *
 */
class kSCCParser
{
    const timingRegex = '/(?P<startHours>\d{2}):(?P<startMinutes>\d{2}):(?P<startSeconds>\d{2})(:|;)(?P<startFrame>\d{2})(?P<content>.+)\r?(\n|$)?\s*\r?(\n|$)/sU';

    const commandCodes = array('94ae','9420','942c','942f','947a','97a1','97a2','9723','9120', '91a1', '91a2', '9123', '91a4', '9125', '9126', '91a7', '91a8', '9129', '912a', '91ab', '912c', '91ad', '91ae', '912f', '94a8');

    private static $rowCodes = array('91','92','15','16','97','13','94');

    private static $columnsCodes = array('d0','51','c2','43','c4','45','46','c7','c8','49','4a','cb','4c','cd','70','f1','62','e3','64','e5','e6','67','68','e9','ea','6b','ec','6d','52','d3','54','d5','d6','57','58','d9','da','5b','dc','5d','5e','df','f2','73','f4','75','76','f7','f8','79','7a','fb','7c','fd','fe','7f');

    private static $singleRowCode = array('10');

    private static $singleColumnCode = array('d0','51','c2','43','c4','45','46','c7','c8','49','4a','cb','4c','cd','52','d3','54','d5','d6','57','58','d9','da','5b','dc','5d','5e','df');

    private static $specialChars = array('91b0'=>'®','9131'=>'°','9132'=>'½','91b3'=>'¿','9134'=>'™','91b5'=>'¢','91b6'=>'£','9137' => '♪','9138'=>'à','91b9'=>' ','91ba'=>'è','913b'=>'â',
                                         '91bc'=>'ê','913d'=>'î','913e'=>'ô','91bf'=>'û');

    private static $extendedChars = array('9220'=>'Á','92a1'=>'É','92a2'=>'Ó','9223'=>'Ú','92a4'=>'Ü','9225'=>'ü','9226'=>'‘','92a7'=>'¡','92a8'=>'*','9229'=>'’','922a'=>'—','92ab'=>'©',
                                          '922c'=>'℠','92ad'=>'•','92ae'=>'“','922f'=>'”','92b0'=>'À','9231'=>'Â','9232'=>'Ç','92b3'=>'È','9234'=>'Ê','92b5'=>'Ë','92b6'=>'ë','9237'=>'Î',
                                          '9238'=>'Ï','92b9'=>'ï','92ba'=>'Ô','923b'=>'Ù','92bc'=>'ù','923d'=>'Û','923e'=>'«','92bf'=>'»','1320'=>'Ã','13a1'=>'ã','13a2'=>'Í','1323'=>'Ì',
                                          '13a4'=>'ì','1325'=>'Ò','1326'=>'ò','13a7'=>'Õ','13a8'=>'õ','1329'=>'{','132a'=>'}','13ab'=>'\\','132c'=>'^','13ad'=>'_','13ae'=>'¦','132f'=>'~',
                                          '13b0'=>'Ä','1331'=>'ä','1332'=>'Ö','13b3'=>'ö','1334'=>'ß','13b5'=>'¥','13b6'=>'¤','1337'=>'|','1338'=>'Å','13b9'=>'å','13ba'=>'Ø','133b'=>'ø',
                                          '9bbc'=>'┌','9b3d'=>'┐','9b3e'=>'└','9bbf'=>'┘');

    private static $twoByteCharset = array('20' =>' ','a1' =>'!','a2' =>'"','23' =>'#','a4' =>'$','25' =>'%','26' =>'&','a7' =>"'",'a8' =>'(','29' =>')','2a' =>'á','ab' =>'+',
                                           '2c' =>',','ad' =>'-','ae' =>'.','2f' =>'/','b0' =>'0','31' =>'1','32' =>'2','b3' =>'3','34' =>'4','b5' =>'5','b6' =>'6','37' =>'7',
                                           '38' =>'8','b9' =>'9','ba' =>':','3b' =>';','bc' =>'<','3d' =>'=','3e' =>'>','bf' =>'?','40' =>'@','c1' =>'A','c2' =>'B','43' =>'C',
                                           'c4' =>'D','45' =>'E','46' =>'F','c7' =>'G','c8' =>'H','49' =>'I','4a' =>'J','cb' =>'K','4c' =>'L','cd' =>'M','ce' =>'N','4f' =>'O',
                                           'd0' =>'P','51' =>'Q','52' =>'R','d3' =>'S','54' =>'T','d5' =>'U','d6' =>'V','57' =>'W','58' =>'X','d9' =>'Y','da' =>'Z','5b' =>'[',
                                           'dc' =>'é','5d' =>']','5e' =>'í','df' =>'ó','e0' =>'ú','61' =>'a','62' =>'b','e3' =>'c','64' =>'d','e5' =>'e','e6' =>'f','67' =>'g',
                                           '68' =>'h','e9' =>'i','ea' =>'j','6b' =>'k','ec' =>'l','6d' =>'m','6e' =>'n','ef' =>'o','70' =>'p','f1' =>'q','f2' =>'r','73' =>'s',
                                           'f4' =>'t','75' =>'u','76' =>'v','f7' =>'w','f8' =>'x','79' =>'y','7a' =>'z','fb' =>'ç','7c' =>'÷','fd' =>'Ñ','fe' =>'ñ');

    /**
     * @param string $content
     * @return array
     */
    public static function parseToSrt($content)
    {
        if (kString::beginsWith($content, "\xff\xfe"))
        {
            $content = iconv('utf-16', 'utf-8', substr($content, 2));
        }

        if (!preg_match_all(self::timingRegex, $content, $matches) || !count($matches) || !count($matches[0]))
        {
            KalturaLog::err("Content regex not found");
            print("Content regex not found");
            return array();
        }
        $text = '';
        $rowCount = 1;
        foreach ($matches[0] as $index => $match)
        {
            $content = $matches['content'][$index];
            $startTime = self::maketime($matches, $index);
			$endTime = $startTime;
			if (isset( $matches['content'][$index + 1]))
            {
                $endTime = self::maketime($matches, $index + 1);
            }
			$translatedContent = self::tranlasteContent($content);
			if ($translatedContent)
            {
                $text .= "$rowCount\n$startTime --> $endTime\n$translatedContent\n\n";
                $rowCount++;
            }
		}
        return $text;
    }

    /**
     * @param $content
     * @return null|string
     */
    private static function tranlasteContent($content)
    {
        //remove unncessary scc commands that are irrelevant to the text.
        $content = str_replace(self::commandCodes, "", $content);

        $parts = preg_split('/\s+/', trim($content));
        $text = null;
        for ($index = 0; $index < count($parts); $index++)
        {
            $code = $parts[$index];
            switch (true)
            {
                case isset(self::$specialChars[$code]):
                {
                    $text .= self::$specialChars[$code];
                    // in case we have double padding for commands we need to ignore next command
                    if (isset($parts[$index + 1]) && $code == $parts[$index + 1])
                    {
                        $index++;
                    }
                    break;
                }
                case isset(self::$extendedChars[$code]):
                {
                    $text .= self::$extendedChars[$code];
                    break;
                }
                default:
                {
                    $codes = str_split($code, 2);
                    //check if codes represent location-cursor move
                    if ((in_array($codes[0], self::$rowCodes) && in_array($codes[1], self::$columnsCodes))
                        || (in_array($codes[0], self::$singleRowCode) && in_array($codes[1], self::$singleColumnCode))
                    )
                    {
                        $text .= ' ';
                        // in case we have double padding for commands we need to ignore next command
                        if (isset($parts[$index + 1]) && $code == $parts[$index + 1])
                        {
                            $index++;
                        }

                    }
                    else
                    {
                        //check if codes represent 2Byte chars and add them to text.
                        foreach ($codes as $charsetCode)
                        {
                            if (isset(self::$twoByteCharset[$charsetCode]))
                            {
                                $text .= self::$twoByteCharset[$charsetCode];
                            }
                        }
                    }
                }
            }
        }
        return trim($text);
    }

    /**
     * @param $matches
     * @param $index
     * @return array
     */
    protected static function maketime($matches, $index)
    {
        $Hours = $matches['startHours'][$index];
        $Minutes = $matches['startMinutes'][$index];
        $Seconds = $matches['startSeconds'][$index];
        $Frame = round($matches['startFrame'][$index] * 41.7);
        return "$Hours:$Minutes:$Seconds,$Frame";

    }
}
