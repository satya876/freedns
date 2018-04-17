<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/delete_domains"/>

<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

if (do_hook('verify_permission' , 'zone_content_edit_others' )) {
    $perm_edit = "all";
} elseif (do_hook('verify_permission' , 'zone_content_edit_own' )) {
    $perm_edit = "own";
} else {
    $perm_edit = "none";
}

$confirm = "-1";
if (isset($_POST['confirm'])) {
    $confirm = "1";
}

$zones = $_POST['zone_id'];
if (!$zones) {
    header("Location: list_zones.php");
    exit;
}

echo "     <h2>" . _('Delete zones') . "</h2>\n";

if ($confirm == '1') {
    //Fetch information about zones before deleting them
    $deleted_zones = array();
    foreach ($zones as $zone) {
        $zone_info = get_zone_info_from_id($zone);
        array_push($deleted_zones,$zone_info);
    }
    $delete_domains = delete_domains($zones);
    if ($delete_domains) {
        count($deleted_zones) == 1 ? success(SUC_ZONE_DEL) : success(SUC_ZONES_DEL);
        //Zones successfully deleted so generate log messages from information retrieved earlier
        foreach ($deleted_zones as $zone_info) {
            log_info(sprintf('client_ip:%s user:%s operation:delete_zone zone:%s zone_type:%s',
                              $_SERVER['REMOTE_ADDR'], $_SESSION["userlogin"],
                              $zone_info['name'], $zone_info['type']));
        }
    }
} else {
    echo "     <form method=\"post\" action=\"delete_domains.php\">\n";
    foreach ($zones as $zone) {
        $zone_owners = do_hook('get_fullnames_owners_from_domainid' , $zone );
        $user_is_zone_owner = do_hook('verify_user_is_owner_zoneid' , $zone );
        $zone_info = get_zone_info_from_id($zone);
        if ($perm_edit == "all" || ( $perm_edit == "own" && $user_is_zone_owner == "1")) {
            echo "      <input type=\"hidden\" name=\"zone_id[]\" value=\"" . $zone . "\">\n";
            echo "      " . _('Name') . ": " . $zone_info['name'] . "<br>\n";
            echo "      " . _('Owner') . ": " . $zone_owners . "<br>\n";
            echo "      " . _('Type') . ": " . $zone_info['type'] . "\n";
            if ($zone_info['type'] == "SLAVE") {
                $slave_master = get_domain_slave_master($zone);
                if (supermaster_exists($slave_master)) {
                    echo "        <p>         \n";
                    printf(_('You are about to delete a slave zone of which the master nameserver, %s, is a supermaster. Deleting the zone now, will result in temporary removal only. Whenever the supermaster sends a notification for this zone, it will be added again!'), $slave_master);
                    echo "        </p>\n";
                }
            }
            echo "     <br><br>\n";
        } else {
            error(ERR_PERM_DEL_ZONE);
        }
    }
    echo "                     <p>" . _('Are you sure?') . "</p>\n";
    echo "                     <input type=\"submit\" name=\"confirm\" value=\"" . _('Yes') . "\" class=\"button\">\n";
    echo "                     <input type=\"button\" class=\"button\" OnClick=\"location.href='list_zones.php'\" value=\"" . _('No') . "\">\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
