<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
	<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
	<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
	<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
    <meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
    <meta name="Author" content="Bluella"/>
    <meta name="copyright" content="Bluella"/>
	<link rel="canonical" href="https://dns.bluella.in/list_zones"/>
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
    $perm_edit = "all";
} elseif (do_hook('verify_permission', 'zone_content_edit_own')) {
    $perm_edit = "own";
} else {
    $perm_edit = "none";
}

$count_zones_all = zone_count_ng("all");
$count_zones_all_letterstart = zone_count_ng($perm_view, LETTERSTART);
$count_zones_view = zone_count_ng($perm_view);
$count_zones_edit = zone_count_ng($perm_edit);

# OUCH: Temporary workaround for nasty sorting issue.
# The problem is that sorting order is saved as a session variable
# and it's used in two different screens - zone list and search results.
# Both have different queries for getting data, but same order field
# that causes failure.

$zone_sort_by = ZONE_SORT_BY;
if (!in_array(ZONE_SORT_BY, array('name', 'type', 'count_records', 'owner'))) {
    $zone_sort_by = 'name';
}

echo "    <h2>" . _('List zones') . "</h2>\n";

if ($perm_view == "none") {
    echo "     <p>" . _('You do not have the permission to see any zones.') . "</p>\n";
} elseif (($count_zones_view > $iface_rowamount && $count_zones_all_letterstart == "0") || $count_zones_view == 0) {
    if ($count_zones_view > $iface_rowamount) {
        echo "<div class=\"showmax\">";
        show_letters(LETTERSTART, $_SESSION["userid"]);
        echo "</div>";
    }
    echo "     <p>" . _('There are no zones to show in this listing.') . "</p>\n";
} else {
    if (LETTERSTART != 'all') {
        echo "     <div class=\"showmax\">\n";
        show_pages($count_zones_all_letterstart, $iface_rowamount);
        echo "     </div>\n";
    }

    if ($count_zones_view > $iface_rowamount) {
        echo "<div class=\"showmax\">";
        show_letters(LETTERSTART, $_SESSION["userid"]);
        echo "</div>";
    }
    echo "     <form method=\"post\" action=\"delete_domains.php\">\n";
    echo "     <table>\n";
    echo "      <tr>\n";
    echo "       <th>&nbsp;</th>\n";
    echo "       <th>&nbsp;</th>\n";
    echo "       <th><a href=\"list_zones.php?zone_sort_by=name\">" . _('Name') . "</a></th>\n";
    echo "       <th><a href=\"list_zones.php?zone_sort_by=type\">" . _('Type') . "</a></th>\n";
    echo "       <th><a href=\"list_zones.php?zone_sort_by=count_records\">" . _('Records') . "</a></th>\n";
    echo "       <th><a href=\"list_zones.php?zone_sort_by=owner\">" . _('Owner') . "</a></th>\n";

    if ($iface_zonelist_serial) {
        echo "       <th>" . _('Serial') . "</th>\n";
    }

    if ($pdnssec_use) {
        echo "       <th>" . _('DNSSEC') . "</th>\n";
    }

    echo "      </tr>\n";

    if ($count_zones_view <= $iface_rowamount) {
        $zones = get_zones($perm_view, $_SESSION['userid'], "all", ROWSTART, $iface_rowamount, $zone_sort_by);
    } elseif (LETTERSTART == 'all') {
        $zones = get_zones($perm_view, $_SESSION['userid'], "all", ROWSTART, 'all', $zone_sort_by);
    } else {
        $zones = get_zones($perm_view, $_SESSION['userid'], LETTERSTART, ROWSTART, $iface_rowamount, $zone_sort_by);
        $count_zones_shown = ($zones == -1) ? 0 : count($zones);
    }
    foreach ($zones as $zone) {
        if ($zone['count_records'] == NULL) {
            $zone['count_records'] = 0;
        }

        if ($iface_zonelist_serial)
            $serial = get_serial_by_zid($zone['id']);

        if ($perm_edit != "all" || $perm_edit != "none") {
            $user_is_zone_owner = do_hook('verify_user_is_owner_zoneid', $zone["id"]);
        }
        echo "         <tr>\n";
        echo "          <td class=\"checkbox\">\n";
        if ($count_zones_edit > 0 && ($perm_edit == "all" || ( $perm_edit == "own" && $user_is_zone_owner == "1"))) {
            echo "       <input type=\"checkbox\" name=\"zone_id[]\" value=\"" . $zone['id'] . "\">";
        }
        echo "          </td>\n";
        echo "          <td class=\"actions\">\n";
        echo "           <a href=\"edit.php?name=" . $zone['name'] . "&id=" . $zone['id'] . "\"><img src=\"images/edit.gif\" title=\"" . _('View zone') . " " . $zone['name'] . "\" alt=\"[ " . _('View zone') . " " . $zone['name'] . " ]\"></a>\n";
        if ($perm_edit == "all" || ( $perm_edit == "own" && $user_is_zone_owner == "1")) {
            echo "           <a href=\"delete_domain.php?name=" . $zone['name'] . "&id=" . $zone["id"] . "\"><img src=\"images/delete.gif\" title=\"" . _('Delete zone') . " " . $zone['name'] . "\" alt=\"[ " . _('Delete zone') . " " . $zone['name'] . " ]\"></a>\n";
        }
        echo "          </td>\n";
        echo "          <td class=\"name\">" . $zone["name"] . "</td>\n";
        echo "          <td class=\"type\">" . strtolower($zone["type"]) . "</td>\n";
        echo "          <td class=\"count\">" . $zone["count_records"] . "</td>\n";
        echo "          <td class=\"owner\">" . $zone["owner"] . "</td>\n";
        if ($iface_zonelist_serial) {
            if ($serial != "") {
                echo "          <td class=\"y\">" . $serial . "</td>\n";
            } else {
                echo "          <td class=\"n\">&nbsp;</td>\n";
            }
        }
        if ($pdnssec_use) {
            echo "          <td class=\"dnssec\"><input type=\"checkbox\" onclick=\"return false\" " . ($zone["secured"] ? 'checked' : '') . "></td>\n";
        }
        echo "           </tr>\n";
    }
    echo "          </table>\n";
    echo "      <input type=\"submit\" name=\"commit\" value=\"" . _('Delete zone(s)') . "\" class=\"button\">\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
