<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/edit_zone_templ"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$zone_templ_id = "-1";
if (isset($_GET['id']) && v_num($_GET['id'])) {
    $zone_templ_id = $_GET['id'];
}

if ($zone_templ_id == "-1") {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
}

/*
  Check permissions
 */
$owner = get_zone_templ_is_owner($zone_templ_id, $_SESSION['userid']);

if (isset($_POST['commit']) && $owner) {
    success(SUC_ZONE_TEMPL_UPD);
    foreach ($_POST['record'] as $record) {
        edit_zone_templ_record($record);
    }
}

if (isset($_POST['edit']) && $owner) {
    edit_zone_templ($_POST, $zone_templ_id);
}

if (isset($_POST['save_as'])) {
    if (zone_templ_name_exists($_POST['templ_name'])) {
        error(ERR_ZONE_TEMPL_EXIST);
    } elseif ($_POST['templ_name'] == '') {
        error(ERR_ZONE_TEMPL_IS_EMPTY);
    } else {
        success(SUC_ZONE_TEMPL_ADD);
        $templ_details = get_zone_templ_details($zone_templ_id);
        add_zone_templ_save_as($_POST['templ_name'], $_POST['templ_descr'], $_SESSION['userid'], $_POST['record']);
    }
}

if (isset($_POST['update_zones'])) {
    $zones = get_list_zone_use_templ($zone_templ_id, $_SESSION['userid']);
    success(SUC_ZONES_UPD);
    foreach ($zones as $zone) {
        update_zone_records($zone['id'], $zone_templ_id);
    }
}

if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
    error(ERR_PERM_EDIT_ZONE_TEMPL);
} else {
    if (zone_templ_id_exists($zone_templ_id) == "0") {
        error(ERR_ZONE_TEMPL_NOT_EXIST);
    } else {
        $record_count = count_zone_templ_records($zone_templ_id);
        $templ_details = get_zone_templ_details($zone_templ_id);
        echo "   <h2>" . _('Edit zone template') . " \"" . $templ_details['name'] . "\"</h2>\n";

        echo "   <div class=\"showmax\">\n";
        show_pages($record_count, $iface_rowamount, $zone_templ_id);
        echo "   </div>\n";

        $records = get_zone_templ_records($zone_templ_id, ROWSTART, $iface_rowamount, RECORD_SORT_BY);
        if ($records == "-1") {
            echo " <p>" . _("This template zone does not have any records yet.") . "</p>\n";
        } else {
            echo "   <form method=\"post\" action=\"\">\n";
            echo "   <table>\n";
            echo "    <tr>\n";
            echo "     <th>&nbsp;</th>\n";
            echo "     <th><a href=\"edit_zone_templ.php?id=" . $zone_templ_id . "&amp;record_sort_by=name\">" . _('Name') . "</a></th>\n";
            echo "     <th><a href=\"edit_zone_templ.php?id=" . $zone_templ_id . "&amp;record_sort_by=type\">" . _('Type') . "</a></th>\n";
            echo "     <th><a href=\"edit_zone_templ.php?id=" . $zone_templ_id . "&amp;record_sort_by=content\">" . _('Content') . "</a></th>\n";
            echo "     <th><a href=\"edit_zone_templ.php?id=" . $zone_templ_id . "&amp;record_sort_by=prio\">" . _('Priority') . "</a></th>\n";
            echo "     <th><a href=\"edit_zone_templ.php?id=" . $zone_templ_id . "&amp;record_sort_by=ttl\">" . _('TTL') . "</a></th>\n";
            echo "    </tr>\n";
            foreach ($records as $r) {
                echo "    <tr>\n";
                echo "     <td class=\"n\">\n";
                echo "    <input type=\"hidden\" name=\"record[" . $r['id'] . "][rid]\" value=\"" . $r['id'] . "\">\n";
                echo "      <a href=\"edit_zone_templ_record.php?id=" . $r['id'] . "&amp;zone_templ_id=" . $zone_templ_id . "\">
						<img src=\"images/edit.gif\" alt=\"[ " . _('Edit record') . " ]\"></a>\n";
                echo "      <a href=\"delete_zone_templ_record.php?id=" . $r['id'] . "&amp;zone_templ_id=" . $zone_templ_id . "\">
						<img src=\"images/delete.gif\" ALT=\"[ " . _('Delete record') . " ]\" BORDER=\"0\"></a>\n";
                echo "     </td>\n";
                echo "      <td class=\"u\"><input class=\"wide\" name=\"record[" . $r['id'] . "][name]\" value=\"" . $r['name'] . "\"></td>\n";
                echo "      <td class=\"u\">\n";
                echo "       <select name=\"record[" . $r['id'] . "][type]\">\n";
                $found_selected_type = false;
                foreach (get_record_types() as $type_available) {
                    if ($type_available == $r['type']) {
                        $add = " SELECTED";
                        $found_selected_type = true;
                    } else {
                        $add = "";
                    }
                    echo "         <option" . $add . " value=\"" . $type_available . "\" >" . $type_available . "</option>\n";
                }
                if (!$found_selected_type) {
                    echo "         <option SELECTED value=\"" . htmlspecialchars($r['type']) . "\"><i>" . $r['type'] . "</i></option>\n";
                }
                echo "       </select>\n";
                echo "      </td>\n";
                echo "      <td class=\"u\"><input class=\"wide\" name=\"record[" . $r['id'] . "][content]\" value='" . $r['content'] . "'></td>\n";
                if ($r['type'] == "MX" || $r['type'] == "SRV") {
                    echo "      <td class=\"u\"><input name=\"record[" . $r['id'] . "][prio]\" value=\"" . $r['prio'] . "\"></td>\n";
                } else {
                    echo "      <td class=\"n\">&nbsp;</td>\n";
                }
                echo "      <td class=\"u\"><input name=\"record[" . $r['id'] . "][ttl]\" value=\"" . $r['ttl'] . "\"></td>\n";
                echo "     </tr>\n";
            }
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
            echo "     <tr>\n";
            echo "      <td colspan=\"6\"><br>Save as new template:</td>\n";
            echo "     </tr>\n";
            echo "      <tr>\n";
            echo "       <th>" . _('Template Name') . "</th>\n";
            echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_name\" value=\"\"></td>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "       <th>" . _('Template Description') . "</th>\n";
            echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_descr\" value=\"\"></td>\n";
            echo "      </tr>\n";
            echo "    </table>\n";
            echo "     <input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _('Commit changes') . "\">\n";
            echo "     <input type=\"reset\" class=\"button\" name=\"reset\" value=\"" . _('Reset changes') . "\">\n";
            echo "     <input type=\"submit\" class=\"button\" name=\"save_as\" value=\"" . _('Save as template') . "\">\n";
            echo "     <input type=\"submit\" class=\"button\" name=\"update_zones\" value=\"" . _('Update zones') . "\">\n";
            echo "    </form>";
        }

        echo "    <form method=\"post\" action=\"\">\n";
        echo "     <table>\n";
        echo "      <tr>\n";
        echo "       <th>" . _('Name') . "</th>\n";
        echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_name\" value=\"" . $templ_details['name'] . "\"></td>\n";
        echo "      </tr>\n";
        echo "      <tr>\n";
        echo "       <th>" . _('Description') . "</th>\n";
        echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_descr\" value=\"" . $templ_details['descr'] . "\"></td>\n";
        echo "      </tr>\n";
        echo "     </table>\n";
        echo "     <input type=\"submit\" class=\"button\" name=\"edit\" value=\"" . _('Commit changes') . "\">\n";
        echo "     </form>\n";
        echo "    <input type=\"button\" class=\"button\" OnClick=\"location.href='add_zone_templ_record.php?id=" . $zone_templ_id . "'\" value=\"" . _('Add record') . "\">&nbsp;&nbsp\n";
        echo "    <input type=\"button\" class=\"button\" OnClick=\"location.href='delete_zone_templ.php?id=" . $zone_templ_id . "'\" value=\"" . _('Delete zone template') . "\">\n";
    }
}

include_once("inc/footer.inc.php");
