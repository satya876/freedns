<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/edit_users"/>

</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$edit_id = "-1";
if (isset($_GET['id']) && v_num($_GET['id'])) {
    $edit_id = $_GET['id'];
}

do_hook('verify_permission' , 'user_edit_own' ) ? $perm_edit_own = "1" : $perm_edit_own = "0";
do_hook('verify_permission' , 'user_edit_others' ) ? $perm_edit_others = "1" : $perm_edit_others = "0";

if ($edit_id == "-1") {
    error(ERR_INV_INPUT);
} elseif (($edit_id == $_SESSION["userid"] && $perm_edit_own == "1") || ($edit_id != $_SESSION["userid"] && $perm_edit_others == "1" )) {

    if (isset($_POST["commit"])) {

        $i_username = "-1";
        $i_fullname = "-1";
        $i_email = "-1";
        $i_description = "-1";
        $i_password = "-1";
        $i_perm_templ = "0";
        $i_active = "0";

        if (isset($_POST['username'])) {
            $i_username = $_POST['username'];
        }

        if (isset($_POST['fullname'])) {
            $i_fullname = $_POST['fullname'];
        }

        if (isset($_POST['email'])) {
            $i_email = $_POST['email'];
        }

        if (isset($_POST['description'])) {
            $i_description = $_POST['description'];
        }

        if (isset($_POST['password'])) {
            $i_password = $_POST['password'];
        }

        if (isset($_POST['perm_templ']) && v_num($_POST['perm_templ'])) {
            $i_perm_templ = $_POST['perm_templ'];
        }

        if (isset($_POST['active']) && v_num($_POST['active'])) {
            $i_active = $_POST['active'];
        }

        if ($i_username == "-1" || $i_fullname == "-1" || $i_email < "1" || $i_description == "-1" || $i_password == "-1") {
            error(ERR_INV_INPUT);
        } else {
            if ($i_username != "" && $i_perm_templ > "0" && $i_fullname) {
                if (!isset($i_active)) {
                    $active = 0;
                } else {
                    $active = 1;
                }
                if (do_hook('edit_user' , $edit_id, $i_username, $i_fullname, $i_email, $i_perm_templ, $i_description, $active, $i_password )) {
                    success(SUC_USER_UPD);
                }
            }
        }
    }

    $users = do_hook('get_user_detail_list' , $edit_id );

    foreach ($users as $user) {

        (($user['active']) == "1") ? $check = " CHECKED" : $check = "";

        echo "     <h2>" . _('Edit user') . " \"" . $user['fullname'] . "\"</h2>\n";
        echo "     <form method=\"post\" action=\"\">\n";
        echo "      <input type=\"hidden\" name=\"number\" value=\"" . $edit_id . "\">\n";
        echo "      <table>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Username') . "</td>\n";
        echo "        <td class=\"n\"><input type=\"text\" class=\"input\" name=\"username\" value=\"" . $user['username'] . "\"></td>\n";
        echo "       </tr>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Fullname') . "</td>\n";
        echo "        <td class=\"n\"><input type=\"text\" class=\"input\" name=\"fullname\" value=\"" . $user['fullname'] . "\"></td>\n";
        echo "       </tr>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Password') . "</td>\n";
        echo "        <td class=\"n\"><input type=\"password\" class=\"input\" name=\"password\"></td>\n";
        echo "       </tr>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Email address') . "</td>\n";
        echo "        <td class=\"n\"><input type=\"text\" class=\"input\" name=\"email\" value=\"" . $user['email'] . "\"></td>\n";
        echo "       </tr>\n";
        if (do_hook('verify_permission' , 'user_edit_templ_perm' )) {
            echo "       <tr>\n";
            echo "        <td class=\"n\">" . _('Permission template') . "</td>\n";
            echo "        <td class=\"n\">\n";
            echo "         <select name=\"perm_templ\">\n";
            foreach (do_hook('list_permission_templates' ) as $template) {
                ($template['id'] == $user['tpl_id']) ? $select = " SELECTED" : $select = "";
                echo "          <option value=\"" . $template['id'] . "\"" . $select . ">" . $template['name'] . "</option>\n";
            }
            echo "         </select>\n";
            echo "       </td>\n";
        }
        echo "       </tr>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Description') . "</td>\n";
        echo "        <td class=\"n\"><textarea rows=\"4\" cols=\"30\" class=\"inputarea\" name=\"description\">" . $user['descr'] . "</textarea></td>\n";
        echo "       </tr>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Enabled') . "</td>\n";
        echo "        <td class=\"n\"><input type=\"checkbox\" class=\"input\" name=\"active\" value=\"1\"" . $check . "></td>\n";
        echo "       </tr>\n";
        echo "       <tr>\n";
        echo "        <td class=\"n\">&nbsp;</td>\n";
        echo "        <td class=\"n\"><input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _('Commit changes') . "\">\n";
        echo "        <input type=\"reset\" class=\"button\" name=\"reset\" value=\"" . _('Reset changes') . "\"></td>\n";
        echo "      </table>\n";
        echo "     </form>\n";

        echo "     <p>\n";
        printf(_('This user has been assigned the permission template "%s".'), $user['tpl_name']);
        if ($user['tpl_descr'] != "") {
            echo " " . _('The description for this template is') . ": \"" . $user['tpl_descr'] . "\".";
        }
        echo " " . _('Based on this template, this user has the following permissions') . ":";
        echo "     </p>\n";
        echo "     <ul>\n";
        foreach (do_hook('get_permissions_by_template_id' , $user['tpl_id'] ) as $item) {
            echo "      <li>" . _($item['descr']) . " (" . $item['name'] . ")</li>\n";
        }
        echo "     </ul>\n";
    }
} else {
    error(ERR_PERM_EDIT_USER);
}

include_once("inc/footer.inc.php");
