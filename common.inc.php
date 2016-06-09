<?php

function validV4($ipv4addr) {
  if(filter_var($ipv4addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {    
    return true;
  } else {
    return false;
  }
}
function validV6($ipv6addr) {
  if(filter_var($ipv6addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    return true;
  } else {
    return false;
  }
}
function info_by_reverse($dnslookup) {
  $result = dns_get_record($dnslookup, DNS_TXT);
  if($result[0]['txt']) {
    // 14061 | 2604:a880:1::/48 | US | arin | 2013-04-11
    $txt = $result[0]['txt'];
    list($asn, $prefix, $bgpcountry, $region, $somedate) = explode(" | ", $txt);
    echo "prefix $prefix advertised by $asn ($asname)\n";
  }
}
function asn_by_reverse($dnslookup) {
  $result = dns_get_record($dnslookup, DNS_TXT);
  if($result[0]['txt']) {
    // 14061 | 2604:a880:1::/48 | US | arin | 2013-04-11
    $txt = $result[0]['txt'];
    list($asn, $prefix, $bgpcountry, $region, $somedate) = explode(" | ", $txt);
    echo "$asn\n";
  }
}
function prefix_by_reverse($dnslookup) {
  $result = dns_get_record($dnslookup, DNS_TXT);
  if($result[0]['txt']) {
    // 14061 | 2604:a880:1::/48 | US | arin | 2013-04-11
    $txt = $result[0]['txt'];
    list($asn, $prefix, $bgpcountry, $region, $somedate) = explode(" | ", $txt);
    echo "$prefix\n";
  }
}
function asnname($asnumber) {
  //AS23028.asn.cymru.com
  $dnslookup = 'AS' . (int)$asnumber . '.asn.cymru.com.';
  $result = dns_get_record($dnslookup, DNS_TXT);
  if($result[0]['txt']) {
    // 23028 | US | arin | 2002-01-04 | TEAMCYMRU - SAUNET
    $txt = $result[0]['txt'];
    list($asn, $bgpcountry, $region, $somedate, $asndesc) = explode(" | ", $txt);
    list($null1, $asndesc2) = explode(" - ", $asndesc);
//    list($asndesc3, $null2) = explode(", ", $asndesc2);
    return($asndesc2);
  } else {
    return "";
  }
}
?>