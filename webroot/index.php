<?php

/*
  ********************************************************
  * IP to ASN, Prefix API
  * (c) 2016 Theodore Baschak
  ********************************************************
*/

require("../common.inc.php");

if(isset($_POST['text']))
  $ip = $_POST['text'];
else
  $ip = $_GET['ip'] ? $_GET['ip'] : $_SERVER['REMOTE_ADDR'];

$action = $_GET['l'] ? $_GET['l'] : 'info';

if(validV4($ip)) {
  // if valid and v4, check v4 origins
  $parts = explode('.',$ip); 
  $dnslookup = implode('.', array_reverse($parts)) . '.origin.asn.cymru.com.';
} elseif(validV6($ip)) {
  // if valid and v6, check v6 origins
  $addr = inet_pton($ip);
  $unpack = unpack('H*hex', $addr);
  $hex = $unpack['hex'];
  $dnslookup = implode('.', array_reverse(str_split($hex))) . '.origin6.asn.cymru.com.';
} else {
  // invalid IP address format
  echo 'Bad Input';
  exit;
}

switch($action) {
  case 'info':
    info_by_reverse($dnslookup);
    break;
  case 'asn':
    asn_by_reverse($dnslookup);
    break;
  case 'prefix':
    prefix_by_reverse($dnslookup);
    break;
}  

?>