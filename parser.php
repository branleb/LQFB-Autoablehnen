<?php

##############
##
##  LQFB-Autoablehnen
##
##  Screenscraper/Parser
##
##############

define('PARSE_ArgumentPattern', '/\s([a-zA-Z0-9]+)=("([^"]*)"|\'([^\']*)\'|([^ ]*))/');

function parse_formtag($formtag) {
 preg_match_all(PARSE_ArgumentPattern, $formtag, $arguments, PREG_SET_ORDER);
 $method = ''; $action = '';
 foreach($arguments as $arg) {
  switch (strtolower($arg[1])) {
   case 'action': $action = $arg[3]; break;
   case 'method': $method = $arg[3]; break;
  }
 }
 return array('action' => $action, 'method' => $method);
}

function parse_loginpage($data) {
 $loginpagepattern = '/<form(( [^>]*)? class="login"[^>]*)>(.*?)<\/form>/';
 preg_match($loginpagepattern, $data, $matches);
 $formtag = $matches[1];
 $formcontent = $matches[3];

 $result = parse_formtag($formtag);

 $inputpattern = '/<input [^>]*\/>/';
 preg_match_all($inputpattern, $formcontent, $inputs, PREG_SET_ORDER);

 foreach($inputs as $input) {
  preg_match_all(PARSE_ArgumentPattern, $input[0], $arguments, PREG_SET_ORDER);
  $inputtag = array();
  foreach($arguments as $argument) {
   $inputtag[strtolower($argument[1])] = $argument[3];
  }

  switch ($inputtag['type']) {
   case 'hidden':
    $result['post'][$inputtag['name']] = $inputtag['value'];
    break;
   case 'password':
    $result['password'] = $inputtag['name'];
    break;
   case 'text':
    if ($inputtag['id'] == 'username_field') {
     $result['username'] = $inputtag['name'];
    } else {
     print "Loginpageparser: Unknown input field:\n";
     print_r($inputtag);
    }
    break;
   case 'submit':
    break;
   default:
    print "Loginpageparser: Unknown input field:\n";
    print_r($inputtag);
    break;
  }
 }

 return $result;
}

function parse_logoutform($data) {
 $logoutpattern = '/<form(( [^>]*)? action="[^"]*logout"[^>]*)>(.*?)<\/form>/';
 preg_match($logoutpattern, $data, $matches);
 $formtag = array_key_exists(1, $matches) ? $matches[1] : null;
 $formcontent = array_key_exists(3, $matches) ? $matches[3] : null;

 $result = parse_formtag($formtag);

 $inputpattern = '/<input [^>]*\/>/';
 preg_match_all($inputpattern, $formcontent, $inputs, PREG_SET_ORDER);

 foreach($inputs as $input) {
  preg_match_all(PARSE_ArgumentPattern, $input[0], $arguments, PREG_SET_ORDER);
  $inputtag = array();
  foreach($arguments as $argument) {
   $inputtag[strtolower($argument[1])] = $argument[3];
  }

  switch ($inputtag['type']) {
   case 'hidden':
    $result['post'][$inputtag['name']] = $inputtag['value'];
    break;
   case 'submit':
    break;
   default:
    print "Logoutformparser: Unknown input field:\n";
    print_r($inputtag);
    break;
  }
 }

 return $result;
}

function parse_getlistingsurl($data) {
 $listingspattern = '/<a( [^>]*?)? href="([^"?]*\?[^"]*tab=open[^"]*)"[^>]*>/';
 preg_match($listingspattern, $data, $matches);

 $url = $matches[2];
 if ($url == '') return;
 $url = str_replace('&amp;','&',$url);

 $replacements = array( 'events' => 'global',
			'filter' => 'frozen',
			'filter_voting' => 'not_voted',
			'filter_interest' => 'unit');
 $patternpre = '/((\?|&)';
 $patternpost = ')=[a-zA-Z0-9+_]*/';
 $replacement = '\1=';

 foreach ($replacements as $name => $value) {
  $url = preg_replace($patternpre.$name.$patternpost, $replacement.$value, $url, 1, $count);
  if ($count == 0) $url .= '&'. $name .'='. $value;
 }

 return $url;
}

function parse_getvotableissues($data) {
 $votablepattern = '/<a( [^>]*?)? href="([^"]*?vote[^"]*?issue_id=([0-9]+)[^"]*)"[^>]*>/';
 preg_match_all($votablepattern, $data, $issues, PREG_SET_ORDER);

 $result = array();
 foreach ($issues as $issue) {
  $result[$issue[3]]['url'] = $issue[2];

  # Delegation für Thema gesetzt?
  $delegationlinkpattern = '/<a( [^>]*?)? href="[^"]*?delegation[^"]*?issue_id='.$issue[3].'[^0-9"]?[^"]*">([^<]*)</';
  preg_match($delegationlinkpattern, $data, $delegationlink);
  $delegationexists = (strpos($delegationlink[2], 'ndern') === false) ? 0 : 1;

  # Dummerweise hält LQFB auch eine Aufhebung der Delegation für ein einzelnes Thema
  # für eine Delegation, daher prüfen ob *an* jemand delegiert wird:
  $delegationinfoboxpattern = '/<a( [^>]*?)? href="[^"]*?delegation[^"]*?issue_id='.$issue[3].'[^0-9"]?[^"]*"[^>]*>([^<]*)<img/';
  $delegationnotempty = preg_match($delegationinfoboxpattern, $data, $delegationbox);

  $result[$issue[3]]['delegating'] = $delegationexists & $delegationnotempty;
 }
 return $result;
}

function parse_voteform($data) {
 $voteformpattern = '/<form(( [^>]*)? action="[^"]*?vote[^"]*?update[^"]*"[^>]*)>(.*?)<\/form>/';
 preg_match_all($voteformpattern, $data, $matches, PREG_SET_ORDER);

 foreach($matches as $match) {
  $formtag = $match[1];
  $formcontent = $match[3];

  $result = parse_formtag($formtag);
  if ($result['action'] == '' || $result['method'] == '') continue;

  $inputpattern = '/<input [^>]*\/>/';
  preg_match_all($inputpattern, $formcontent, $inputs, PREG_SET_ORDER);

  foreach($inputs as $input) {
   preg_match_all(PARSE_ArgumentPattern, $input[0], $arguments, PREG_SET_ORDER);
   $inputtag = array();
   foreach($arguments as $argument) {
    $inputtag[strtolower($argument[1])] = $argument[3];
   }

   switch ($inputtag['type']) {
    case 'hidden':
     $result['post'][$inputtag['name']] = $inputtag['value'];
     break;
    case 'image':
     break;
    case 'submit':
     break;
    default:
     print "Voteformparser: Unknown input field:\n";
     print_r($inputtag);
     break;
   }
  }

  if ($result['post']['discard'] == 'true') continue;
  if ($result['post']['scoring'] == '') continue;

  $issuenamepattern = '/<div>(i[0-9]+: [^<]*)</';
  preg_match_all($issuenamepattern, $formcontent, $issues);
  foreach($issues[1] as $issuename)
   $result['issuenames'][] = utf8_decode($issuename);

  return $result;
 }

 return array();
}

?>
