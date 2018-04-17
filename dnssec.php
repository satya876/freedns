<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/dnssec"/>
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

if ($zone_id == "-1") {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
}

/*
  Check permissions
 */
$user_is_zone_owner = do_hook('verify_user_is_owner_zoneid' , $zone_id );
if ($perm_meta_edit == "all" || ( $perm_meta_edit == "own" && $user_is_zone_owner == "1")) {
    $meta_edit = "1";
} else {
    $meta_edit = "0";
}

(do_hook('verify_permission' , 'user_view_others' )) ? $perm_view_others = "1" : $perm_view_others = "0";

if ($perm_view == "none" || $perm_view == "own" && $user_is_zone_owner == "0") {
    error(ERR_PERM_VIEW_ZONE);
    include_once("inc/footer.inc.php");
    exit();
}

if (zone_id_exists($zone_id) == "0") {
    error(ERR_ZONE_NOT_EXIST);
    include_once("inc/footer.inc.php");
    exit();
}

$domain_type = get_domain_type($zone_id);
$domain_name = get_zone_name_from_id($zone_id);
$record_count = count_zone_records($zone_id);
$zone_templates = get_list_zone_templ($_SESSION['userid']);
$zone_template_id = get_zone_template($zone_id);

echo "   <h2>" . _('DNSSEC keys for zone') . " \"" . get_zone_name_from_id($zone_id) . "\"</h2>\n";

echo "     <table>\n";
echo "      <tr>\n";
echo "       <th>&nbsp;</th>\n";
echo "       <th>" . _('ID') . "</th>\n";
echo "       <th>" . _('Type') . "</th>\n";
echo "       <th>" . _('Tag') . "</th>\n";
echo "       <th>" . _('Algorithm') . "</th>\n";
echo "       <th>" . _('Bits') . "</th>\n";
echo "       <th>" . _('Active') . "</th>\n";
echo "      </tr>\n";

$keys = dnssec_get_keys($domain_name);

foreach ($keys as $item) {
    echo "<tr>\n";
    echo "<td width=\"60\" class=\"actions\">&nbsp;\n";
    echo "<a href=\"dnssec_edit_key.php?id=" . $zone_id . "&key_id=" . $item[0] . "\"><img src=\"images/edit.gif\" title=\"" . _('Edit zone key') . " " . $item[0] . "\" alt=\"[ " . _('Edit zone key') . " " . $domain_name . " ]\"></a>\n";
    echo "<a href=\"dnssec_delete_key.php?id=" . $zone_id . "&key_id=" . $item[0] . "\"><img src=\"images/delete.gif\" title=\"" . _('Delete zone key') . " " . $item[0] . "\" alt=\"[ " . _('Delete zone key') . " " . $domain_name . " ]\"></a>\n";
    echo "</td>";
    echo "<td class=\"cell\">".$item[0]."</td>\n";
    echo "<td class=\"cell\">".$item[1]."</td>\n";
    echo "<td class=\"cell\">".$item[2]."</td>\n";
    echo "<td class=\"cell\">".dnssec_algorithm_to_name($item[3])."</td>\n";
    echo "<td class=\"cell\">".$item[4]."</td>\n";
    echo "<td class=\"cell\">".($item[5] ? _('Yes') : _('No'))."</td>\n";
    echo "</tr>\n";
}

echo "     </table>\n";
echo "      <input type=\"button\" class=\"button\" onclick=\"location.href = 'dnssec_add_key.php?id=".$zone_id."';\" value=\"" . _('Add new key') . "\">\n";
echo "      <input type=\"button\" class=\"button\" onclick=\"location.href = 'dnssec_ds_dnskey.php?id=".$zone_id."';\" value=\"" . _('Show DS and DNSKEY') . "\">\n";

include_once("inc/footer.inc.php");
