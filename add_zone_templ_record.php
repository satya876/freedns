<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/add_zone_templ_record"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

/*
  Check and make sure all post values have made it through
  if not set them.
 */
$zone_templ_id = "-1";
if ((isset($_GET['id'])) && (v_num($_GET['id']))) {
    $zone_templ_id = $_GET['id'];
}

$ttl = $dns_ttl;
if ((isset($_POST['ttl'])) && (v_num($_POST['ttl']))) {
    $ttl = $_POST['ttl'];
}

$prio = "";
if ((isset($_POST['prio'])) && (v_num($_POST['prio']))) {
    $prio = $_POST['prio'];
}

if (isset($_POST['name'])) {
    $name = $_POST['name'];
} else {
    $name = "";
}

if (isset($_POST['type'])) {
    $type = $_POST['type'];
} else {
    $type = "";
}

if (isset($_POST['content'])) {
    $content = $_POST['content'];
} else {
    $content = "";
}

if ($zone_templ_id == "-1") {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
}

$templ_details = get_zone_templ_details($zone_templ_id);
$owner = get_zone_templ_is_owner($zone_templ_id, $_SESSION['userid']);

/*
  If the form as been submitted
  process it!
 */
if (isset($_POST["commit"])) {
    if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
        error(ERR_PERM_ADD_RECORD);
    } else {
        if (add_zone_templ_record($zone_templ_id, $name, $type, $content, $ttl, $prio)) {
            success(_('The record was successfully added.'));
            $name = $type = $content = $ttl = $prio = "";
        }
    }
}

/*
  Display form to add a record
 */
echo "    <h2>" . _('Add record to zone template') . " \"" . $templ_details['name'] . "\"</h2>\n";

if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
    error(ERR_PERM_ADD_RECORD);
} else {
    echo "     <form method=\"post\">\n";
    echo "      <input type=\"hidden\" name=\"domain\" value=\"" . $zone_templ_id . "\">\n";
    echo "      <table border=\"0\" cellspacing=\"4\">\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Name') . "</td>\n";
    echo "        <td class=\"n\">&nbsp;</td>\n";
    echo "        <td class=\"n\">" . _('Type') . "</td>\n";
    echo "        <td class=\"n\">" . _('Content') . "</td>\n";
    echo "        <td class=\"n\">" . _('Priority') . "</td>\n";
    echo "        <td class=\"n\">" . _('TTL') . "</td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\"><input type=\"text\" name=\"name\" class=\"input\" value=\"" . $name . "\"></td>\n";
    echo "        <td class=\"n\">IN</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <select name=\"type\">\n";
    $found_selected_type = !(isset($type) && $type);
    foreach (get_record_types() as $record_type) {
        if (isset($type) && $type) {
            if ($type == $record_type) {
                $add = " SELECTED";
                $found_selected_type = true;
            } else {
                $add = "";
            }
        } else {
            // TODO: from where comes $zone_name value and why this check exists here?
            if (isset($zone_name) && preg_match('/i(p6|n-addr).arpa/i', $zone_name) && strtoupper($record_type) == 'PTR') {
                $add = " SELECTED";
            } elseif (strtoupper($record_type) == 'A') {
                $add = " SELECTED";
            } else {
                $add = "";
            }
        }
        echo "          <option" . $add . " value=\"" . $record_type . "\">" . $record_type . "</option>\n";
    }
    if (!$found_selected_type)
        echo "          <option SELECTED value=\"" . htmlspecialchars($type) . "\"><i>" . htmlspecialchars($type) . "</i></option>\n";
    echo "         </select>\n";
    echo "        </td>\n";
    echo "        <td class=\"n\"><input type=\"text\" name=\"content\" class=\"input\" value=\"" . $content . "\"></td>\n";
    echo "        <td class=\"n\"><input type=\"text\" name=\"prio\" class=\"sinput\" value=\"" . $prio . "\"></td>\n";
    echo "        <td class=\"n\"><input type=\"text\" name=\"ttl\" class=\"sinput\" value=\"" . $ttl . "\"</td>\n";
    echo "       </tr>\n";
    echo "     <tr>\n";
    echo "      <td colspan=\"6\"><br><b>Hint:</b></td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td colspan=\"6\">" . _('The following placeholders can be used in template records') . "</td>\n";
    echo "     </tr>\n";
    echo "     <tr>\n";
    echo "      <td colspan=\"6\"><br>&nbsp;&nbsp;&nbsp;&nbsp; * [ZONE] - " . _('substituted with current zone name') . "<br>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp; * [SERIAL] - " . _('substituted with current date and 2 numbers') . " (YYYYMMDD + 00)</td>\n";
    echo "     </tr>\n";
    echo "      </table>\n";
    echo "      <br>\n";
    echo "      <input type=\"submit\" name=\"commit\" value=\"" . _('Add record') . "\" class=\"button\">\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
