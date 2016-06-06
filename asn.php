<?php

/*
  ********************************************************
  * IP to ASN, Prefix API
  * (c) 2016 Theodore Baschak
  ********************************************************
*/

require("./common.inc.php");

//$ip = $_GET["ip"];
if($argc > 1) {
  $ip = $argv[1];
} else {
  echo "Usage:\n <scriptname> <ip address>\n";
  exit;
}

if(validV4($ip)) {
  // if valid and v4, check v4 origins
  $parts = explode('.',$ip); 
  $dnslookup = implode('.', array_reverse($parts)) . '.origin.asn.cymru.com.';
  asn_by_reverse($dnslookup)
}
elseif(validV6($ip)) {
  // if valid and v6, check v6 origins
  $addr = inet_pton($ip);
  $unpack = unpack('H*hex', $addr);
  $hex = $unpack['hex'];
  $dnslookup = implode('.', array_reverse(str_split($hex))) . '.origin6.asn.cymru.com.';
  asn_by_reverse($dnslookup)
} else {
  // invalid
  echo "Bad Input";
  exit;
}

?>