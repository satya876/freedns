<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/delete_perm_templ"/>

<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$perm_templ = "-1";
if (isset($_GET['id']) && (v_num($_GET['id']))) {
    $perm_templ = $_GET['id'];
}

$confirm = "-1";
if ((isset($_GET['confirm'])) && v_num($_GET['confirm'])) {
    $confirm = $_GET['confirm'];
}

if ($perm_templ == "-1") {
    error(ERR_INV_INPUT);
} else {
    if (!(do_hook('verify_permission' , 'user_edit_templ_perm' ))) {
        error(ERR_PERM_DEL_PERM_TEMPL);
    } else {
        $templ_details = do_hook('get_permission_template_details' , $perm_templ );
        echo "     <h2>" . _('Delete permission template') . " \"" . $templ_details['name'] . "\"</h2>\n";

        if (isset($_GET['confirm']) && $_GET["confirm"] == '1') {
            if (delete_perm_templ($perm_templ)) {
                success(SUC_PERM_TEMPL_DEL);
            }
        } else {
            echo "     <p>" . _('Are you sure?') . "</p>\n";
            echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='delete_perm_templ.php?id=" . $perm_templ . "&amp;confirm=1'\" value=\"" . _('Yes') . "\">\n";
            echo "     <input type=\"button\" class=\"button\" OnClick=\"location.href='index.php'\" value=\"" . _('No') . "\">\n";
        }
    }
}

include_once("inc/footer.inc.php");

