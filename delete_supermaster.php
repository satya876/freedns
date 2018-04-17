<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/delete_supermaster"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$master_ip = "-1";
if (isset($_GET['master_ip']) && (is_valid_ipv4($_GET['master_ip']) || is_valid_ipv6($_GET['master_ip']))) {
    $master_ip = $_GET['master_ip'];
}

$ns_name = "-1";
if (isset($_GET['ns_name']) && (is_valid_hostname_fqdn($_GET['ns_name'], 0))) {
    $ns_name = $_GET['ns_name'];
}

$confirm = "-1";
if ((isset($_GET['confirm'])) && (v_num($_GET['confirm']))) {
    $confirm = $_GET['confirm'];
}

if ($master_ip == "-1" || $ns_name == "-1") {
    error(ERR_INV_INPUT);
} else {
    (do_hook('verify_permission' , 'supermaster_edit' )) ? $perm_sm_edit = "1" : $perm_sm_edit = "0";
    if ($perm_sm_edit == "0") {
        error(ERR_PERM_DEL_SM);
    } else {
        $info = get_supermaster_info_from_ip($master_ip);

        echo "     <h2>" . _('Delete supermaster') . " \"" . $master_ip . "\"</h2>\n";

        if (isset($_GET['confirm']) && $_GET["confirm"] == '1') {
            if (!supermaster_ip_name_exists($master_ip, $ns_name)) {
                header("Location: list_supermasters.php");
                exit;
            }

            if (delete_supermaster($master_ip, $ns_name)) {
                success(SUC_SM_DEL);
            }
        } else {
            echo "     <p>\n";
            echo "      " . _('Hostname in NS record') . ": " . $info['ns_name'] . "<br>\n";
            echo "      " . _('Account') . ": " . $info['account'] . "\n";
            echo "     </p>\n";
            echo "     <p>" . _('Are you sure?') . "</p>\n";
            echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='delete_supermaster.php?master_ip=" . $master_ip . "&amp;ns_name=" . $info['ns_name'] . "&amp;confirm=1'\" value=\"" . _('Yes') . "\">\n";
            echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='index.php'\" value=\"" . _('No') . "\">\n";
        }
    }
}

include_once("inc/footer.inc.php");
