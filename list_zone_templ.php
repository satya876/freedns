<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/list_zone_templ"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

do_hook('verify_permission', 'zone_master_add') ? $perm_zone_master_add = "1" : $perm_zone_master_add = "0";

$zone_templates = get_list_zone_templ($_SESSION['userid']);
$username = do_hook('get_fullname_from_userid', $_SESSION['userid']);

if ($perm_zone_master_add == "0") {
    error(ERR_PERM_EDIT_ZONE_TEMPL);
} else {
    echo "    <h2>" . _('Zone templates for') . " " . $username . "</h2>\n";
    echo "     <table>\n";
    echo "      <tr>\n";
    echo "       <th>&nbsp;</th>\n";
    echo "       <th>" . _('Name') . "</th>\n";
    echo "       <th>" . _('Description') . "</th>\n";
    echo "      </tr>\n";

    foreach ($zone_templates as $template) {

        echo "      <tr>\n";
        if ($perm_zone_master_add == "1") {
            echo "       <td>\n";
            echo "        <a href=\"edit_zone_templ.php?id=" . $template["id"] . "\"><img src=\"images/edit.gif\" alt=\"[ " . _('Edit template') . " ]\"></a>\n";
            echo "        <a href=\"delete_zone_templ.php?id=" . $template["id"] . "\"><img src=\"images/delete.gif\" alt=\"[ " . _('Delete template') . " ]\"></a>\n";
            echo "       </td>\n";
        } else {
            echo "       <td>&nbsp;</td>\n";
        }
        echo "       <td class=\"y\">" . $template['name'] . "</td>\n";
        echo "       <td class=\"y\">" . $template['descr'] . "</td>\n";
        echo "      </tr>\n";
    }

    echo "     </table>\n";
    echo "     <ul>\n";
    echo "      <li><a href=\"add_zone_templ.php\">" . _('Add zone template') . "</a>.</li>\n";
    echo "     </ul>\n";
}

include_once("inc/footer.inc.php");
