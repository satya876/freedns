<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/dnssec_delete_key"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

global $pdnssec_use;

$zone_id = "-1";
if (isset($_GET['id']) && v_num($_GET['id'])) {
    $zone_id = $_GET['id'];
}

$key_id = "-1";
if (isset($_GET['key_id']) && v_num($_GET['key_id'])) {
    $key_id = (int) $_GET['key_id'];
}

$confirm = "-1";
if (isset($_GET['confirm']) && v_num($_GET['confirm'])) {
    $confirm = $_GET['confirm'];
}

$user_is_zone_owner = do_hook('verify_user_is_owner_zoneid' , $zone_id );

if ($zone_id == "-1") {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
}

$domain_name = get_zone_name_from_id($zone_id);

if ($key_id == "-1") {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
}

if (!dnssec_zone_key_exists($domain_name, $key_id)) {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;    
}

echo "     <h2>" . _('Delete zone key') . "</h2>\n";

if ($confirm == '1') {
    if (dnssec_remove_zone_key($domain_name, $key_id)) {
        success(SUC_EXEC_PDNSSEC_REMOVE_ZONE_KEY);
    }
} else {
    if ($user_is_zone_owner == "1") {
        $key_info = dnssec_get_zone_key($domain_name, $key_id);
        echo "      " . _('Domain') . ": " . $domain_name . "<br>\n";
        echo "      " . _('Id') . ": " . $key_info[0] . "<br>\n";
        echo "      " . _('Type') . ": " . $key_info[1] . "<br>\n";
        echo "      " . _('Tag') . ": " . $key_info[2] . "<br>\n";
        echo "      " . _('Algorithm') . ": " . dnssec_algorithm_to_name($key_info[3]) . "<br>\n";
        echo "      " . _('Bits') . ": " . $key_info[4] . "<br>\n";
        echo "      " . _('Active') . ": " . ($key_info[5] ? _('Yes') : _('No')) . "\n";
        echo "     <p>" . _('Are you sure?') . "</p>\n";
        echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='dnssec_delete_key.php?id=" . $zone_id . "&amp;key_id=$key_id&amp;confirm=1'\" value=\"" . _('Yes') . "\">\n";
        echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='index.php'\" value=\"" . _('No') . "\">\n";
    } else {
        error(ERR_PDNSSEC_DEL_ZONE_KEY);
    }
}

include_once("inc/footer.inc.php");
