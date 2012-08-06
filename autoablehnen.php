<?

###########################
##
## LQFB 2.0 Autoablehnen
##
## Howto:
##  1. Benutzernamen eingeben:

$username = 'herrbert2012';

##  2. Passwort eingeben:

$password = '12345678';

##  3. URL zur Loginseite:

$lqfb = 'https://lfpp.de/lf/index/login.html?redirect_view=index&redirect_module=index';

## (Momentan ist das die Adresse zur Testinstanz, da kann man sich auch Testlogins einrichten)

##  4. CURL installieren
##  5. Um TOR oder andere Proxies zu verwenden die Datei curl-exec.php anpassen
##  6. Testen:
##        php autoablehnen.php
##  7. Wenn erfolgreich: Cronjob einrichten.
##  8. Herzlichen Glückwunsch! Wer nicht dafür ist, ist dagegen
##
##########################
##
##  Version 1.0 - release  06.08.2012
##
##    Erste öffentliche Version. Macht was draus
##
##########################

require('curl-exec.php');
require('parser.php');
require('url_to_absolute.php');

clearCookies();

print "Lade LQFB-Startseite...\n";

$loginpage = POSTURL($lqfb);
$replyform = parse_loginpage($loginpage);

if ($replyform['action'] == '' || $replyform['method'] == '' || $replyform['username'] == '' || $replyform['password'] == '')  {
 print 'Konnte Startseite nicht parsen.'. "\n\n";
 print 'Debuginfo: ';
 print_r($replyform);
 die();
}

print "Anmeldung...\n";

$loginurl = url_to_absolute($lqfb, $replyform['action']);
$data = $replyform['post'];
$data[$replyform['username']] = $username;
$data[$replyform['password']] = $password;

$commencelogin = POSTURL($loginurl, $lqfb, $data);
$logout = parse_logoutform($commencelogin);

if ($logout['action'] == '' || $logout['method'] == '') {
 print 'Anmeldung fehlgeschlagen.'. "\n\n";
 file_put_contents('debug', $commencelogin);
 print 'Debuginfo in Datei "debug"'. "\n";
 die();
}

print "Anmeldung erfolgreich.\n";

$listurl = parse_getlistingsurl($commencelogin);
if ($listurl == '' || strpos($listurl, '?') === false) {
 print 'Konnte Link auf Uebersichtsseite fuer abstimmreife Themen nicht finden.'. "\n\n";
 die();
}
$baseurl = url_to_absolute($lqfb, substr($listurl, 0, strpos($listurl, '?')));
$listurl = $baseurl . substr($listurl, strpos($listurl, '?'));
$issuelist = POSTURL($listurl, $loginurl);
$issuelist = parse_getvotableissues($issuelist);

print count($issuelist) . " abstimmreife Themen gefunden\n\n";

foreach(array_keys($issuelist) as $issueid) {
 $issue = $issuelist[$issueid];

 if ($issue['delegating'] == 1) {
  print "Ueberspringe Thema $issueid: Themendelegation gesetzt\n\n";
 } else {
  $baseurl = url_to_absolute($lqfb, substr($issue['url'], 0, strpos($issue['url'], '?')));
  $issueurl = $baseurl . substr($issue['url'], strpos($issue['url'], '?'));

  $issuevoteform = POSTURL($issueurl, $listurl);
  $voteform = parse_voteform($issuevoteform);

  if ($voteform['post']['scoring'] == '') {
   print 'Konnte Abstimmseite zu Thema nicht laden.'. "\n\n";
   file_put_contents('debug', $issuevoteform);
   print 'Debuginfo in Datei "debug"'. "\n";
   die();
  } else {
   print "Lehne Initiativen ab:\n" . join("\n", $voteform['issuenames']) . "\n\n";

   # Reject ALL the issues!
   $voteform['post']['scoring'] = preg_replace('/([0-9]+):[0-9]+/', '\1:-1', $voteform['post']['scoring']);
   if (substr($voteform['post']['scoring'], -1) != ';') $voteform['post']['scoring'] .= ';';
  
   $voteurl = url_to_absolute($issueurl, $voteform['action']);
   $vote = POSTURL($voteurl, $issueurl, $voteform['post']);
   # RÃ¼ckmeldung kÃ¶nnte noch auf Erfolg getestet werden
  }
 }
}

print "Abmelden...\n";

$logouturl = url_to_absolute($lqfb, $logout['action']);
$commencelogout = POSTURL($logouturl, $lasturl, $logout['post']); $lasturl = $logouturl;
$logout = parse_logoutform($commencelogout);
if ($logout['action'] != '' || $logout['method'] != '') {
 print 'Abmeldung fehlgeschlagen.'. "\n\n";
 file_put_contents('debug', $commencelogout);
 print 'Debuginfo in Datei "debug"'. "\n";
 die();
}

print "Erfolgreich abgemeldet.\n";
?>
