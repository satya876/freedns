<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/edit_comment"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

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

$zid = $_GET['domain'];

$user_is_zone_owner = do_hook('verify_user_is_owner_zoneid' , $zid );
$zone_type = get_domain_type($zid);
$zone_name = get_zone_name_from_id($zid);

if (isset($_POST["commit"])) {
    if ($zone_type == "SLAVE" || $perm_content_edit == "none" || ($perm_content_edit == "own" || $perm_content_edit == "own_as_client") && $user_is_zone_owner == "0") {
        error(ERR_PERM_EDIT_COMMENT);
    } else {
        edit_zone_comment($_GET['domain'], $_POST['comment']);
        success(SUC_COMMENT_UPD);
    }
}

echo "    <h2>" . _('Edit comment in zone') . " " . $zone_name . "</h2>\n";

if ($perm_view == "none" || $perm_view == "own" && $user_is_zone_owner == "0") {
    error(ERR_PERM_VIEW_COMMENT);
} else {
    $comment = get_zone_comment($zid);
    echo "     <form method=\"post\" action=\"edit_comment.php?domain=" . $zid . "\">\n";
    echo "      <table>\n";
    echo "      <tr>\n";
    echo "       <td colspan=\"6\">&nbsp;</td>\n";
    echo "      </tr>\n";
    echo "      <tr>\n";
    echo "       <td>&nbsp;</td><td colspan=\"5\">Comments:</td>\n";
    echo "      </tr>\n";

    if ($zone_type == "SLAVE" || $perm_content_edit == "none" || ($perm_content_edit == "own" || $perm_content_edit == "own_as_client") && $user_is_zone_owner == "0") {
        echo "    <tr>\n";
        echo "     <td class=\"n\">\n";
        echo "      &nbsp;\n";
        echo "     </td>\n";
        echo "     <td colspan=\"4\"><textarea rows=\"15\" name=\"comment\" disabled>" . $comment . "</textarea></td>\n";
        echo "     <td>&nbsp;</td>\n";
        echo "    </tr>\n";
    } else {
        echo "    <tr>\n";
        echo "     <td class=\"n\">\n";
        echo "      &nbsp;\n";
        echo "     </td>\n";
        echo "     <td colspan=\"4\"><textarea rows=\"15\" name=\"comment\">" . $comment . "</textarea></td>\n";
        echo "     <td>&nbsp;</td>\n";
        echo "    </tr>\n";
    }
    echo "      </table>\n";
    echo "      <p>\n";
    echo "       <input type=\"submit\" name=\"commit\" value=\"" . _('Commit changes') . "\" class=\"button\">&nbsp;&nbsp;\n";
    echo "       <input type=\"reset\" name=\"reset\" value=\"" . _('Reset changes') . "\" class=\"button\">&nbsp;&nbsp;\n";
    echo "      </p>\n";
    echo "     </form>\n";
}


include_once("inc/footer.inc.php");
