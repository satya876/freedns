<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/edit_zone_templ_record"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$record_id = "-1";
if (isset($_GET['id']) && v_num($_GET['id'])) {
    $record_id = $_GET['id'];
}

$zone_templ_id = "-1";
if (isset($_GET['zone_templ_id']) && v_num($_GET['zone_templ_id'])) {
    $zone_templ_id = $_GET['zone_templ_id'];
}

$owner = get_zone_templ_is_owner($zone_templ_id, $_SESSION['userid']);

if (isset($_POST["commit"])) {
    if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
        error(ERR_PERM_EDIT_RECORD);
    } else {
        $ret_val = edit_zone_templ_record($_POST);
        if ($ret_val == "1") {
            success(SUC_RECORD_UPD);
        } else {
            echo "     <div class=\"error\">" . $ret_val . "</div>\n";
        }
    }
}

$templ_details = get_zone_templ_details($zone_templ_id);
echo "    <h2>" . _('Edit record in zone template') . " \"" . $templ_details['name'] . "\"</h2>\n";

if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
    error(ERR_PERM_VIEW_RECORD);
} else {
    $record = get_zone_templ_record_from_id($record_id);
    echo "     <form method=\"post\" action=\"edit_zone_templ_record.php?zone_templ_id=" . $zone_templ_id . "&id=" . $record_id . "\">\n";
    echo "      <table>\n";
    echo "       <tr>\n";
    echo "        <th>" . _('Name') . "</td>\n";
    echo "        <th>&nbsp;</td>\n";
    echo "        <th>" . _('Type') . "</td>\n";
    echo "        <th>" . _('Content') . "</td>\n";
    echo "        <th>" . _('Priority') . "</td>\n";
    echo "        <th>" . _('TTL') . "</td>\n";
    echo "       </tr>\n";
    echo "      <input type=\"hidden\" name=\"rid\" value=\"" . $record_id . "\">\n";
    echo "      <input type=\"hidden\" name=\"zid\" value=\"" . $zone_templ_id . "\">\n";
    echo "      <tr>\n";
    echo "       <td><input type=\"text\" name=\"name\" value=\"" . htmlspecialchars($record["name"]) . "\" class=\"input\"></td>\n";
    echo "       <td>IN</td>\n";
    echo "       <td>\n";
    echo "        <select name=\"type\">\n";
    $found_selected_type = false;
    foreach (get_record_types() as $type_available) {
        if ($type_available == $record["type"]) {
            $add = " SELECTED";
            $found_selected_type = true;
        } else {
            $add = "";
        }
        echo "         <option" . $add . " value=\"" . $type_available . "\" >" . $type_available . "</option>\n";
    }
    if (!$found_selected_type)
        echo "         <option SELECTED value=\"" . htmlspecialchars($record['type']) . "\"><i>" . $record['type'] . "</i></option>\n";
    echo "        </select>\n";
    echo "       </td>\n";
    echo "       <td><input type=\"text\" name=\"content\" value=\"" . htmlspecialchars($record['content']) . "\" class=\"input\"></td>\n";
    echo "       <td><input type=\"text\" name=\"prio\" value=\"" . htmlspecialchars($record["prio"]) . "\" class=\"sinput\"></td>\n";
    echo "       <td><input type=\"text\" name=\"ttl\" value=\"" . htmlspecialchars($record["ttl"]) . "\" class=\"sinput\"></td>\n";
    echo "      </tr>\n";
    echo "      </table>\n";
    echo "      <p>\n";
    echo "       <input type=\"submit\" name=\"commit\" value=\"" . _('Commit changes') . "\" class=\"button\">&nbsp;&nbsp;\n";
    echo "       <input type=\"reset\" name=\"reset\" value=\"" . _('Reset changes') . "\" class=\"button\">&nbsp;&nbsp;\n";
    echo "      </p>\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
