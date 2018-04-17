<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/dnssec_add_key"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$zone_id = "-1";
if (isset($_GET['id']) && v_num($_GET['id'])) {
    $zone_id = $_GET['id'];
}

$user_is_zone_owner = do_hook('verify_user_is_owner_zoneid' , $zone_id );

if ($user_is_zone_owner == "0") {
    error(ERR_PERM_VIEW_ZONE);
    include_once("inc/footer.inc.php");
    exit();
}

if (zone_id_exists($zone_id) == "0") {
    error(ERR_ZONE_NOT_EXIST);
    include_once("inc/footer.inc.php");
    exit();
}

$key_type = "";
if (isset($_POST['key_type'])) {
    $key_type = $_POST['key_type'];

    if ($key_type != 'ksk' && $key_type != 'zsk') {
        error(ERR_INV_INPUT);
        include_once("inc/footer.inc.php");
        exit;
    }
}

$bits = "";
if (isset($_POST["bits"])) {
    $bits = $_POST["bits"];

    $valid_values = array('2048', '1024', '768', '384', '256');
    if (!in_array($bits, $valid_values)) {
        error(ERR_INV_INPUT);
        include_once("inc/footer.inc.php");
        exit;
    }
}

$algorithm = "";
if (isset($_POST["algorithm"])) {
    $algorithm = $_POST["algorithm"];

    $valid_algorithm = array('rsasha1', 'rsasha256', 'rsasha512', 'gost', 'ecdsa256', 'ecdsa384');
    if (!in_array($algorithm, $valid_algorithm)) {
        error(ERR_INV_INPUT);
        include_once("inc/footer.inc.php");
        exit;
    }
}

$domain_name = get_zone_name_from_id($zone_id);
if (isset($_POST["submit"])) {
    if (dnssec_add_zone_key($domain_name, $key_type, $bits, $algorithm)) {
        success(SUC_EXEC_PDNSSEC_ADD_ZONE_KEY);
    } else {
        error(ERR_EXEC_PDNSSEC_ADD_ZONE_KEY);
    }
}

echo "     <h2>" . _('Add key for zone '). $domain_name . "</h2>\n";

echo "     <form method=\"post\" action=\"dnssec_add_key.php?id=".$zone_id."\">\n";
echo "      <table>\n";
echo "       <tr>\n";
echo "        <td width=\"100\" class=\"n\">" . _('Key type') . "</td>\n";
echo "        <td class=\"n\">\n";
echo "         <select name=\"key_type\">\n";
echo "          <option value=\"\"></option>\n";
echo "          <option value=\"ksk\">KSK</option>\n";
echo "          <option value=\"zsk\">ZSK</option>\n";
echo "         </select>\n";
echo "        </td>\n";
echo "       </tr>\n";
echo "       <tr>\n";
echo "        <td class=\"n\">" . _('Bits in length') . "</td>\n";
echo "        <td class=\"n\">\n";
echo "         <select name=\"bits\">\n";
echo "          <option value=\"\"></option>\n";
echo "          <option value=\"2048\">2048</option>\n";
echo "          <option value=\"1024\">1024</option>\n";
echo "          <option value=\"768\">768</option>\n";
echo "          <option value=\"384\">384</option>\n";
echo "          <option value=\"256\">256</option>\n";
echo "         </select>\n";
echo "        </td>\n";
echo "       </tr>\n";
echo "       <tr>\n";
echo "        <td class=\"n\">" . _('Algorithm') . "</td>\n";
echo "        <td class=\"n\">\n";

echo "         <select name=\"algorithm\">\n";
echo "          <option value=\"\"></option>\n";
echo "          <option value=\"rsasha1\">".dnssec_shorthand_to_algorithm_name('rsasha1')."</option>\n";
echo "          <option value=\"rsasha256\">".dnssec_shorthand_to_algorithm_name('rsasha256')."</option>\n";
echo "          <option value=\"rsasha512\">".dnssec_shorthand_to_algorithm_name('rsasha512')."</option>\n";
echo "          <option value=\"gost\">".dnssec_shorthand_to_algorithm_name('gost')."</option>\n";
echo "          <option value=\"ecdsa256\">".dnssec_shorthand_to_algorithm_name('ecdsa256')."</option>\n";
echo "          <option value=\"ecdsa384\">".dnssec_shorthand_to_algorithm_name('ecdsa384')."</option>\n";
echo "         </select>\n";

echo "        </td>\n";
echo "       </tr>\n";
echo "       <tr>\n";
echo "        <td class=\"n\">&nbsp;</td>\n";
echo "        <td class=\"n\">\n";
echo "         <input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _('Add key') . "\">\n";
echo "        </td>\n";
echo "       </tr>\n";
echo "      </table>\n";
echo "     </form>\n";

include_once("inc/footer.inc.php");
