<?php

function clearCookies($cookiejar = 'cookies') {
 @unlink($cookiejar);
}

function POSTURL(
		$url,
		$referer = "",
		$data = array(),
		$agent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/0427 Firefox/15.0a1",
		$cookiejar = 'cookies') {
 $output = '';

# Um z.B. lokal installiertes TOR zu verwenden:
# $socksproxy = '--socks5-hostname 127.0.0.1:9050';

 $datacmd = '';
 if (count($data) > 0) {
  $dataarr = array();
  foreach ($data as $key=>$value) {
   $dataarr[] = $key.'='.urlencode($value);
  }
  $datacmd = '-d "'. join('&', $dataarr) . '"';
 }

 $curlcommand = 'curl '. $socksproxy. ' -e"'. $referer. ';auto" -L -m 30 -k -s -S -H "Expect:" -b "'.$cookiejar.'" -c "'. $cookiejar. '" -A "'. $agent .'" '.$datacmd.' "'.$url.'"';

# print "Running CURL:\n";
# print $curlcommand . "\n\n";

 # Sleep between 1 and 4 seconds - be nice to the server
 usleep(rand(1000000,4000000));

 exec($curlcommand, $output);

 return join('', $output);
}

?>
