<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/delete_user"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

do_hook('verify_permission' , 'user_edit_own' ) ? $perm_edit_own = "1" : $perm_edit_own = "0";
do_hook('verify_permission' , 'user_edit_others' ) ? $perm_edit_others = "1" : $perm_edit_others = "0";
do_hook('verify_permission' , 'user_is_ueberuser' ) ? $perm_is_godlike = "1" : $perm_is_godlike = "0";

if (!(isset($_GET['id']) && v_num($_GET['id']))) {
    error(ERR_INV_INPUT);
    include_once("inc/footer.inc.php");
    exit;
} else {
    $uid = $_GET['id'];
}

if (isset($_POST['commit'])) {

    if (do_hook('is_valid_user' , $uid )) {
        $zones = array();
        if (isset($_POST['zone'])) {
            $zones = $_POST['zone'];
        }

        if (do_hook('delete_user' , $uid, $zones )) {
            success(SUC_USER_DEL);
        }
    } else {
        header("Location: users.php");
        exit;
    }
} else {

    if (($uid != $_SESSION['userid'] && $perm_edit_others == "0") || ($uid == $_SESSION['userid'] && $perm_is_godlike == "0")) {
        error(ERR_PERM_DEL_USER);
        include_once("inc/footer.inc.php");
        exit;
    } else {
        $fullname = do_hook('get_fullname_from_userid' , $uid );
        $zones = get_zones("own", $uid);

        echo "     <h2>" . _('Delete user') . " \"" . $fullname . "\"</h2>\n";
        echo "     <form method=\"post\" action=\"\">\n";
        echo "      <table>\n";

        if (count($zones) > 0) {

            $users = do_hook('show_users');

            echo "       <tr>\n";
            echo "        <td colspan=\"5\">\n";

            echo "         " . _('You are about to delete a user. This user is owner for a number of zones. Please decide what to do with these zones.') . "\n";
            echo "        </td>\n";
            echo "       </tr>\n";

            echo "       <tr>\n";
            echo "        <th>" . _('Zone') . "</th>\n";
            echo "        <th>" . _('Delete') . "</th>\n";
            echo "        <th>" . _('Leave') . "</th>\n";
            echo "        <th>" . _('Add new owner') . "</th>\n";
            echo "        <th>" . _('Owner to be added') . "</th>\n";
            echo "       </tr>\n";

            foreach ($zones as $zone) {
                echo "       <input type=\"hidden\" name=\"zone[" . $zone['id'] . "][zid]\" value=\"" . $zone['id'] . "\">\n";
                echo "       <tr>\n";
                echo "        <td>" . $zone['name'] . "</td>\n";
                echo "        <td><input type=\"radio\" name=\"zone[" . $zone['id'] . "][target]\" value=\"delete\"></td>\n";
                echo "        <td><input type=\"radio\" name=\"zone[" . $zone['id'] . "][target]\" value=\"leave\" CHECKED></td>\n";
                echo "        <td><input type=\"radio\" name=\"zone[" . $zone['id'] . "][target]\" value=\"new_owner\"></td>\n";
                echo "        <td>\n";
                echo "         <select name=\"zone[" . $zone['id'] . "][newowner]\">\n";

                foreach ($users as $user) {
                    echo "          <option value=\"" . $user["id"] . "\">" . $user["fullname"] . "</option>\n";
                }

                echo "         </select>\n";
                echo "        </td>\n";
                echo "       </tr>\n";
            }
        }
        echo "       <tr>\n";
        echo "        <td colspan=\"5\">\n";

        echo "         " . _('Really delete this user?') . "\n";
        echo "        </td>\n";
        echo "       </tr>\n";

        echo "      </table>\n";
        echo "     <input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _('Commit changes') . "\">\n";
        echo "     </form>\n";
    }
}

include_once("inc/footer.inc.php");
