<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/edit_record"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

global $pdnssec_use;

if (do_hook('verify_permission', 'zone_content_view_others')) {
    $perm_view = "all";
} elseif (do_hook('verify_permission', 'zone_content_view_own')) {
    $perm_view = "own";
} else {
    $perm_view = "none";
}

if (do_hook('verify_permission', 'zone_content_edit_others')) {
    $perm_content_edit = "all";
} elseif (do_hook('verify_permission', 'zone_content_edit_own')) {
    $perm_content_edit = "own";
} elseif (do_hook('verify_permission', 'zone_content_edit_own_as_client')) {
    $perm_content_edit = "own_as_client";
} else {
    $perm_content_edit = "none";
}

if (do_hook('verify_permission', 'zone_meta_edit_others')) {
    $perm_meta_edit = "all";
} elseif (do_hook('verify_permission', 'zone_meta_edit_own')) {
    $perm_meta_edit = "own";
} else {
    $perm_meta_edit = "none";
}

$zid = get_zone_id_from_record_id($_GET['id']);

$user_is_zone_owner = do_hook('verify_user_is_owner_zoneid' , $zid );
$zone_type = get_domain_type($zid);
$zone_name = get_zone_name_from_id($zid);

if (isset($_POST["commit"])) {
    if ($zone_type == "SLAVE" || $perm_content_edit == "none" || ($perm_content_edit == "own" || $perm_content_edit == "own_as_client") && $user_is_zone_owner == "0") {
        error(ERR_PERM_EDIT_RECORD);
    } else {
        $old_record_info = get_record_from_id($_POST["rid"]);
        $ret_val = edit_record($_POST);
        if ($ret_val == "1") {
            if ($_POST['type'] != "SOA") {
                update_soa_serial($zid);
            }
            success(SUC_RECORD_UPD);
            $new_record_info = get_record_from_id($_POST["rid"]);
            log_info(sprintf('client_ip:%s user:%s operation:edit_record'
                             .' old_record_type:%s old_record:%s old_content:%s old_ttl:%s old_priority:%s'
                             .' record_type:%s record:%s content:%s ttl:%s priority:%s',
                              $_SERVER['REMOTE_ADDR'], $_SESSION["userlogin"],
                              $old_record_info['type'], $old_record_info['name'], $old_record_info['content'], $old_record_info['ttl'], $old_record_info['prio'],
                              $new_record_info['type'], $new_record_info['name'], $new_record_info['content'], $new_record_info['ttl'], $new_record_info['prio']));

            if ($pdnssec_use) {
                if (dnssec_rectify_zone($zid)) {
                    success(SUC_EXEC_PDNSSEC_RECTIFY_ZONE);
                }
            }
        }
    }
}

echo "    <h2>" . _('Edit record in zone') . " \"<a href=\"edit.php?id=" . $zid . "\">" . $zone_name . "</a>\"</h2>\n";

if ($perm_view == "none" || $perm_view == "own" && $user_is_zone_owner == "0") {
    error(ERR_PERM_VIEW_RECORD);
} else {
    $record = get_record_from_id($_GET["id"]);
    echo "     <form method=\"post\" action=\"edit_record.php?domain=" . $zid . "&amp;id=" . $_GET["id"] . "\">\n";
    echo "      <table>\n";
    echo "       <tr>\n";
    echo "        <th>" . _('Name') . "</th>\n";
    echo "        <th>&nbsp;</th>\n";
    echo "        <th>" . _('Type') . "</th>\n";
    echo "        <th>" . _('Content') . "</th>\n";
    echo "        <th>" . _('Priority') . "</th>\n";
    echo "        <th>" . _('TTL') . "</th>\n";
    echo "       </tr>\n";

    if ($zone_type == "SLAVE" || $perm_content_edit == "none" || ($perm_content_edit == "own" || $perm_content_edit == "own_as_client") && $user_is_zone_owner == "0") {
        echo "      <tr>\n";
        echo "       <td>" . $record["name"] . "</td>\n";
        echo "       <td>IN</td>\n";
        echo "       <td>" . htmlspecialchars($record["type"]) . "</td>\n";
        echo "       <td>" . htmlspecialchars($record['content']) . "</td>\n";
        echo "       <td>" . htmlspecialchars($record["prio"]) . "</td>\n";
        echo "       <td>" . htmlspecialchars($record["ttl"]) . "</td>\n";
        echo "      </tr>\n";
    } else {
        echo "      <tr>\n";
        echo "       <td><input type=\"hidden\" name=\"rid\" value=\"" . $_GET["id"] . "\">\n";
        echo "       <input type=\"hidden\" name=\"zid\" value=\"" . $zid . "\">\n";
        echo "       <input type=\"text\" name=\"name\" value=\"" . htmlspecialchars(trim(str_replace($zone_name, '', $record["name"]), '.')) . "\" class=\"input\">." . $zone_name . "</td>\n";
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
            echo "         <option" . $add . " value=\"" . htmlspecialchars($type_available) . "\" >" . $type_available . "</option>\n";
        }
        if (!$found_selected_type)
            echo "         <option SELECTED value=\"" . htmlspecialchars($record['type']) . "\"><i>" . $record['type'] . "</i></option>\n";
        echo "        </select>\n";
        echo "       </td>\n";
        echo "       <td><input type=\"text\" name=\"content\" value=\"" . htmlspecialchars($record['content']) . "\" class=\"input\"></td>\n";
        echo "       <td><input type=\"text\" name=\"prio\" value=\"" . htmlspecialchars($record["prio"]) . "\" class=\"sinput\"></td>\n";
        echo "       <td><input type=\"text\" name=\"ttl\" value=\"" . htmlspecialchars($record["ttl"]) . "\" class=\"sinput\"></td>\n";
        echo "      </tr>\n";
    }
    echo "      </table>\n";
    echo "       <input type=\"submit\" name=\"commit\" value=\"" . _('Commit changes') . "\" class=\"button\">&nbsp;&nbsp;\n";
    echo "       <input type=\"reset\" name=\"reset\" value=\"" . _('Reset changes') . "\" class=\"button\">&nbsp;&nbsp;\n";
    echo "     </form>\n";
}


include_once("inc/footer.inc.php");
