<?php
   
   // Test 0 (php): if you can read this, you do not have PHP installed!
   $o = "";
   
   $p = isset($_REQUEST["page"])? $_REQUEST["page"] : 1;
   if ($p == 2) {
      
      // Test 5 (cookie get)
      $test5 = 1;
      $o .= "<p>Test 5 (cookie get): ";
      if (!isset($_COOKIE["FOOOOOOO"])) {
         $test5 = 2;
      } else {
         if ($_COOKIE["FOOOOOOO"] != "BARRRRRRRRRRR") {
            $test5 = 3;
         }
      }
      switch($test5) {
         case 1:
            $o .= "passed."; break;
         case 2:
            $o .= "failed. Did you accept the cookie I sent you?"; break;
         case 3:
            $o .= "failed. Cookie is corrupted: ".$_COOKIE["FOOOOOOO"].". Please check your browser's setting.";
            break;
         default:
            $o .= "failed. Something's wrong with your PHP...";
      }
      $o .= "</p>";
      
      // Test 6 (PHP session)
      $test6 = 1;
      $o .= "<p>Test 6 (PHP session extension): ";
      if (!extension_loaded('session')) {
         if (!@dl('php_session.dll') && !@dl('session.so')) {
            $test6 = 3;
         }
      }   
      if (!function_exists("session_start")) {         
         $test6 = 2;
      }
      switch ($test6) {
         case 1:
            $o .= "passed."; break;
         case 2:
            $o .= "failed. It seems that you do not have PHP Session installed. You may read <a href='http://www.php.net/manual/en/ref.session.php'>this</a> for more infos on PHP Session.";
            break;
         case 3:
            $o .= "failed. It seems that you have PHP Session installed but failed to load it. Here we assume the library filename is php_session.dll (for WIN) or session.so (for *NIX). If not, you may have to modify the source code for that.";
            break;
         default:
            $o .= "failed. Something's wrong with your PHP...";
            break;
      }
      $o .= "</p>"; 
      
      $o .= "<p>If you have <b>all</b> 6 tests passed. You should have no problem installing and using gmail-lite and libgmailer.</p>";
      
      if ($test6 != 1) {
         $o .= "<p style='color:red'>You did not pass the PHP Session test. You must set <code>\$session_method = (!GM_USE_PHPSESSION | GM_USE_COOKIE);</code> in config.php. </p>";
      } elseif ($test5 != 1) {
         $o .= "<p style='color:red'>You passed the PHP Session test but not the cookie test. You must set <code>\$session_method = (GM_USE_PHPSESSION | !GM_USE_COOKIE);</code> in config.php.</p>";
      }         
   } else {
   
      // Test 1 (curl extension)
      $test1 = 1;
      $o .= "<p>Test 1 (curl extension): ";
      if (!extension_loaded('curl')) {
         if (!dl('php_curl.dll') && !dl('curl.so')) {
            $test1 = 3;
         }
      }   
      if (!function_exists("curl_setopt")) {         
         $test1 = 2;
      }
      switch ($test1) {
         case 1:
            $o .= "passed."; break;
         case 2:
            $o .= "failed. It seems that you do not have CURL installed. You may read <a href='http://www.php.net/manual/en/ref.curl.php'>this</a> for more infos on CURL in PHP.";
            break;
         case 3:
            $o .= "failed. It seems that you have CURL installed but failed to load it. Here we assume the library filename is php_curl.dll (for WIN) or curl.so (for *NIX). If not, you may have to modify the source code for that.";
            break;
         default:
            $o .= "failed. Something's wrong with your PHP...";
            break;
      }
      $o .= "</p>"; 
      
      if ($test1 != 1) {
         // no need to continue...
         echo "<html><body>".$o."</body></html>";
         exit;
      }      
      
      // Test 2 (curl)
      $test2 = 1;
      $o .= "<p>Test 2 (curl http): ";   
      $c = curl_init();
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($c, CURLOPT_URL, "http://www.google.com/");
      curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4b) Gecko/20040612 Mozilla Firebird/0.9");
      curl_setopt($c, CURLOPT_HEADER, 1);
      curl_setopt($c, CURLOPT_REFERER, "http://www.linux.org/");
      $result = curl_exec($c);
      if (strlen($result) == 0) {
         $test2 = 2;
      } else {
         if (strpos(strtolower($result), "google") === false || strpos(strtolower($result), "</html>") === false) {  // www.google.com without google: impossible, right?
            $test2 = 4;
         }
      }
      if (curl_errno($c) != 0) {
         $test2 = 3;
         $cerr = curl_error($c);
      }
      curl_close($c);
      switch($test2) {
         case 1:
            $o .= "passed.";
            break;
         case 2:
            $o .= "failed. Nothing could be obtained by curl. Please check your curl settings.";
            break;
         case 3:
            $o .= "failed. CURL error: ".$cerr.". Please check your curl settings.";
            break;
         case 4:
            $o .= "failed. Failed to connect to the dedicated host: www.google.com. Result: <pre>".$result."</pre>";
            break;
         default:
            $o .= "failed. Something's wrong with your PHP...";
            break;
      }
      $o .= "</p>"; 
      
      if ($test2 != 1) {
         // no need to continue...
         echo "<html><body>".$o."</body></html>";
         exit;
      }      
      
      // Test 3 (curl https)
      $test3 = 1;
      $o .= "<p>Test 3 (SSL via curl): ";   
      $c = curl_init();
      curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($c, CURLOPT_URL, "https://gmail.google.com/gmail");
      curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.4b) Gecko/20040612 Mozilla Firebird/0.9");
      curl_setopt($c, CURLOPT_HEADER, 1);
      curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($c, CURLOPT_SSL_VERIFYHOST,  2);   
      curl_setopt($c, CURLOPT_REFERER, "http://www.linux.org/");
      $result = curl_exec($c);
      if (strlen($result) == 0) {
         $test3 = 2;
      } else {
         if (strpos(strtolower($result), "google") === false || strpos(strtolower($result), "</html>") === false) {  // gmail.google.com without google: impossible, right?
            $test3 = 4;
         }
      }
      if (curl_errno($c) != 0) {
         $test3 = 3;
         $cerr = curl_error($c);
      }
      curl_close($c);
      switch($test3) {
         case 1:
            $o .= "passed.";
            break;
         case 2:
            $o .= "failed. Nothing could be obtained by curl. Please check your curl-ssl settings.";
            exit();
            break;
         case 3:
            $o .= "failed. CURL error: ".$cerr.". Please check your curl-ssl settings.";
            exit();
            break;
         case 4:
            $o .= "failed. Failed to connect to the dedicated host: gmail.google.com. Result: <pre>".$result."</pre>";
            exit();
            break;
         default:
            $o .= "failed. Something's wrong with your PHP...";
            exit();
            break;
      }
      $o .= "</p>"; 
      
      if ($test3 != 1) {
         // no need to continue...
         echo "<html><body>".$o."</body></html>";
         exit;
      }      
      
      // Test 4 (cookie set)
      $test4 = 1;
      $o .= "<p>Test 4 (cookie set): ";
      header("Set-cookie: FOOOOOOO=BARRRRRRRRRRR;");
      $o .= "cookie sent. Note: you have to tell your browser to accept this cookie in order to continue the test. Press <a href='diagnose.php?page=2'>here</a> to continue.</p>";
      
   }
?>
<html>
<body>
<?php echo $o ?>
</body>
</html>