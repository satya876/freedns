<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/edit_perm_templ"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$id = "-1";
if ((isset($_GET['id'])) || (v_num($_GET['id']))) {
    $id = $_GET['id'];
}

if ($id == "-1") {
    error(ERR_INV_INPUT);
} elseif (!do_hook('verify_permission' , 'templ_perm_edit' )) {
    error(ERR_PERM_EDIT_PERM_TEMPL);
} else {
    $id = $_GET['id'];

    if (isset($_POST['commit'])) {
        do_hook('update_perm_templ_details' , $_POST );
        success(SUC_RECORD_UPD);
    }

    $templ = do_hook('get_permission_template_details' , $id );
    $perms_templ = do_hook('get_permissions_by_template_id' , $id );
    $perms_avail = do_hook('get_permissions_by_template_id' );

    echo "    <h2>" . _('Edit permission template') . "</h2>\n";
    echo "    <form method=\"post\" action=\"\">\n";
    echo "    <input type=\"hidden\" name=\"templ_id\" value=\"" . $id . "\">\n";

    echo "     <table>\n";
    echo "      <tr>\n";
    echo "       <th>" . _('Name') . "</th>\n";
    echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_name\" value=\"" . $templ['name'] . "\"></td>\n";
    echo "      </tr>\n";
    echo "      <tr>\n";
    echo "       <th>" . _('Description') . "</th>\n";
    echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_descr\" value=\"" . $templ['descr'] . "\"></td>\n";
    echo "      </tr>\n";
    echo "     </table>\n";

    echo "     <table>\n";
    echo "      <tr>\n";
    echo "       <th>&nbsp;</th>\n";
    echo "       <th>" . _('Name') . "</th>\n";
    echo "       <th>" . _('Description') . "</th>\n";
    echo "      </tr>\n";

    foreach ($perms_avail as $perm_a) {

        echo "      <tr>\n";

        $has_perm = "";
        foreach ($perms_templ as $perm_t) {
            if (in_array($perm_a['id'], $perm_t)) {
                $has_perm = "checked";
            }
        }

        echo "       <td><input type=\"checkbox\" name=\"perm_id[]\" value=\"" . $perm_a['id'] . "\" " . $has_perm . "></td>\n";
        echo "       <td>" . $perm_a['name'] . "</td>\n";
        echo "       <td>" . _($perm_a['descr']) . "</td>\n";
        echo "      </tr>\n";
    }
    echo "     </table>\n";
    echo "     <input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _('Commit changes') . "\">\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
