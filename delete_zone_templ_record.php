<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/delete_zone_templ_record"/>
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

$confirm = "-1";
if (isset($_GET['confirm']) && v_num($_GET['confirm'])) {
    $confirm = $_GET['confirm'];
}

if ($record_id == "-1" || $zone_templ_id == "-1") {
    error(ERR_INV_INPUT);
} else {
    $owner = get_zone_templ_is_owner($zone_templ_id, $_SESSION['userid']);
    if ($confirm == '1' && $owner) {
        if (delete_zone_templ_record($record_id)) {
            success(SUC_RECORD_DEL);
        }
    } else {
        $templ_details = get_zone_templ_details($zone_templ_id);
        $record_info = get_zone_templ_record_from_id($record_id);

        echo "     <h2>" . _('Delete record in zone') . " \"" . $templ_details['name'] . "\"</h2>\n";

        if (!(do_hook('verify_permission' , 'zone_master_add' )) || !$owner) {
            error(ERR_PERM_DEL_RECORD);
        } else {
            echo "     <table>\n";
            echo "      <tr>\n";
            echo "       <th>Name</th>\n";
            echo "       <th>Type</th>\n";
            echo "       <th>Content</th>\n";
            echo "       <th>Priority</th>\n";
            echo "       <th>TTL</th>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "       <td>" . $record_info['name'] . "</td>\n";
            echo "       <td>" . $record_info['type'] . "</td>\n";
            echo "       <td>" . $record_info['content'] . "</td>\n";
            echo "       <td>" . $record_info['prio'] . "</td>\n";
            echo "       <td>" . $record_info['ttl'] . "</td>\n";
            echo "      </tr>\n";
            echo "     </table>\n";
            echo "     <p>" . _('Are you sure?') . "</p>\n";
            echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='delete_zone_templ_record.php?id=" . $record_id . "&amp;zone_templ_id=" . $zone_templ_id . "&amp;confirm=1'\" value=\"" . _('Yes') . "\">\n";
            echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='index.php'\" value=\"" . _('No') . "\">\n";
        }
    }
}
include_once("inc/footer.inc.php");
