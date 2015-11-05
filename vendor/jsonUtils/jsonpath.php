<?php
/* JSONPath 0.8.3 - XPath for JSON
 *
 * Copyright (c) 2007 Stefan Goessner (goessner.net)
 * Licensed under the MIT (MIT-LICENSE.txt) licence.
 */

// API function 
function jsonPath($obj, $expr, $args=null) {
   $jsonpath = new JsonPath();
   $jsonpath->resultType = ($args ? $args['resultType'] : "VALUE");
   $x = $jsonpath->normalize($expr);
   $jsonpath->obj = $obj;
   if ($expr && $obj && ($jsonpath->resultType == "VALUE" || $jsonpath->resultType == "PATH")) {
      $jsonpath->trace(preg_replace("/^\\$;/", "", $x), $obj, "$");
      if (count($jsonpath->result))
         return $jsonpath->result;
      else
         return false;
   }
}

// JsonPath class (internal use only)
class JsonPath {
   var $obj = null;
   var $resultType = "Value";
   var $result = array();
   var $subx = array();

   // normalize path expression
   function normalize($x) {
      $x = preg_replace_callback(array("/[\['](\??\(.*?\))[\]']/", "/\['(.*?)'\]/"), array(&$this, "_callback_01"), $x);
      $x = preg_replace(array("/'?\.'?|\['?/", "/;;;|;;/", "/;$|'?\]|'$/"),
                        array(";", ";..;", ""),
                        $x);
      $x = preg_replace_callback("/#([0-9]+)/", array(&$this, "_callback_02"), $x);
      $this->result = array();  // result array was temporarily used as a buffer ..
      return $x;
   }
   function _callback_01($m) { return "[#".(array_push($this->result, $m[1])-1)."]"; }
   function _callback_02($m) { return $this->result[$m[1]]; }

   function asPath($path) {
      $x = explode(";", $path);
      $p = "$";
      for ($i=1,$n=count($x); $i<$n; $i++)
         $p .= preg_match("/^[0-9*]+$/", $x[$i]) ? ("[".$x[$i]."]") : ("['".$x[$i]."']");
      return $p;
   }
   function store($p, $v) {
      if ($p) array_push($this->result, ($this->resultType == "PATH" ? $this->asPath($p) : $v));
      return !!$p;
   }
   function trace($expr, $val, $path) {
      if ($expr !== "") {
         $x = explode(";", $expr);
         $loc = array_shift($x);
         $x = implode(";", $x);

         if (is_array($val) && array_key_exists($loc, $val))
            $this->trace($x, $val[$loc], $path.";".$loc);
         else if ($loc == "*")
            $this->walk($loc, $x, $val, $path, array(&$this, "_callback_03"));
         else if ($loc === "..") {
            $this->trace($x, $val, $path);
            $this->walk($loc, $x, $val, $path, array(&$this, "_callback_04"));
         }
         else if (preg_match("/^\(.*?\)$/", $loc)) // [(expr)]
            $this->trace($this->evalx($loc, $val, substr($path,strrpos($path,";")+1)).";".$x, $val, $path);
         else if (preg_match("/^\?\(.*?\)$/", $loc)) // [?(expr)]
            $this->walk($loc, $x, $val, $path, array(&$this, "_callback_05"));
         else if (preg_match("/^(-?[0-9]*):(-?[0-9]*):?(-?[0-9]*)$/", $loc)) // [start:end:step]  phyton slice syntax
            $this->slice($loc, $x, $val, $path);
         else if (preg_match("/,/", $loc)) // [name1,name2,...]
            for ($s=preg_split("/'?,'?/", $loc),$i=0,$n=count($s); $i<$n; $i++)
                $this->trace($s[$i].";".$x, $val, $path);
      }
      else
         $this->store($path, $val);
   }
   function _callback_03($m,$l,$x,$v,$p) { $this->trace($m.";".$x,$v,$p); }
   function _callback_04($m,$l,$x,$v,$p) { if (is_array($v[$m])) $this->trace("..;".$x,$v[$m],$p.";".$m); }
   function _callback_05($m,$l,$x,$v,$p) { if ($this->evalx(preg_replace("/^\?\((.*?)\)$/","$1",$l),$v[$m])) $this->trace($m.";".$x,$v,$p); }

   function walk($loc, $expr, $val, $path, $f) {
      foreach($val as $m => $v)
         call_user_func($f, $m, $loc, $expr, $val, $path);
   }
   function slice($loc, $expr, $v, $path) {
      $s = explode(":", preg_replace("/^(-?[0-9]*):(-?[0-9]*):?(-?[0-9]*)$/", "$1:$2:$3", $loc));
      $len=count($v);
      $start=(int)$s[0]?$s[0]:0; 
      $end=(int)$s[1]?$s[1]:$len; 
      $step=(int)$s[2]?$s[2]:1;
      $start = ($start < 0) ? max(0,$start+$len) : min($len,$start);
      $end   = ($end < 0)   ? max(0,$end+$len)   : min($len,$end);
      for ($i=$start; $i<$end; $i+=$step)
         $this->trace($i.";".$expr, $v, $path);
   }
   function evalx($x, $v, $vname) {
      $name = "";
      $expr = preg_replace(array("/\\$/","/@/"), array("\$this->obj","\$v"), $x);
      $res = eval("\$name = $expr;");

      if ($res === FALSE)
         print("(jsonPath) SyntaxError: " . $expr);
      else
         return $name;
   }
}
?>