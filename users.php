<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
	<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
	<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
	<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
    <meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
    <meta name="Author" content="Bluella"/>
    <meta name="copyright" content="Bluella"/>
	<link rel="canonical" href="https://dns.bluella.in/users"/>
</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once ("inc/toolkit.inc.php");
include_once ("inc/header.inc.php");

do_hook ( 'verify_permission', 'user_view_others' ) ? $perm_view_others = "1" : $perm_view_others = "0";
do_hook ( 'verify_permission', 'user_edit_own' ) ? $perm_edit_own = "1" : $perm_edit_own = "0";
do_hook ( 'verify_permission', 'user_edit_others' ) ? $perm_edit_others = "1" : $perm_edit_others = "0";
do_hook ( 'verify_permission', 'templ_perm_edit' ) ? $perm_templ_perm_edit = "1" : $perm_templ_perm_edit = "0";
do_hook ( 'verify_permission', 'user_is_ueberuser' ) ? $perm_is_godlike = "1" : $perm_is_godlike = "0";
do_hook ( 'verify_permission', 'user_add_new' ) ? $perm_add_new = "1" : $perm_add_new = "0";

// f (isset($_GET['action']) && $_GET['action'] === "switchuser" && $perm_is_godlike === "1"){
// $_SESSION["userlogin"] = $_GET['username'];
// echo '<meta http-equiv="refresh" content="1"/>';
//

unset ( $commit_button );

if (isset ( $_POST ['commit'] )) {
	foreach ( $_POST ['user'] as $user ) {
		do_hook('update_user_details' ,  $user );
	}
}

$users = do_hook ( 'get_user_detail_list', "" );
echo "    <h2>" . _ ( 'User administration' ) . "</h2>\n";
echo "    <form method=\"post\" action=\"\">\n";
echo "     <table>\n";
echo "      <tr>\n";
echo "       <th>&nbsp;</th>\n";
echo "       <th>" . _ ( 'Username' ) . "</th>\n";
echo "       <th>" . _ ( 'Fullname' ) . "</th>\n";
echo "       <th>" . _ ( 'Description' ) . "</th>\n";
echo "       <th>" . _ ( 'Email address' ) . "</th>\n";
echo "       <th>" . _ ( 'Template' ) . "</th>\n";
if ($ldap_use) {
	echo "       <th>" . _ ( 'LDAP' ) . "</th>\n";
}
echo "       <th>" . _ ( 'Enabled' ) . "</th>\n";
echo "      </tr>\n";

if (count ( $users ) > 0 ) {
	foreach ( $users as $user ) {
		if ($user ['active'] == "1") {
			$active = " checked";
		} else {
			$active = "";
		}
		if ($user ['use_ldap'] == "1") {
			$use_ldap = " checked";
		} else {
			$use_ldap = "";
		}
		if (($user ['uid'] == $_SESSION ["userid"] && $perm_edit_own == "1") || ($user ['uid'] != $_SESSION ["userid"] && $perm_edit_others == "1")) {
			$commit_button = "1";
			echo "      <tr>\n";
			echo "       <td>\n";
			echo "        <input type=\"hidden\" name=\"user[" . $user ['uid'] . "][uid]\" value=\"" . $user ['uid'] . "\">\n";
			echo "        <a href=\"edit_user.php?id=" . $user ['uid'] . "\"><img src=\"images/edit.gif\" alt=\"[ " . _ ( 'Edit user' ) . " ]\"></a>\n";
			
			// do not allow to delete him- or herself
			if ($user ['uid'] != $_SESSION ["userid"]) {
				echo "        <a href=\"delete_user.php?id=" . $user ['uid'] . "\"><img src=\"images/delete.gif\" alt=\"[ " . _ ( 'Delete user' ) . " ]\"></a>";
			}
			
			// if ($user['uid'] != $_SESSION["userid"] && $perm_is_godlike == "1") {
			// echo " <a href=\"users.php?action=switchuser&username=" . $user['username'] . "\"><img src=\"images/switch_user.png\" alt=\"[ " . _('Switch user') . " ]\"></a>\n";
			// }
			
			echo "       </td>\n";
			echo "       <td><input type=\"text\" name=\"user[" . $user ['uid'] . "][username]\" value=\"" . $user ['username'] . "\"></td>\n";
			echo "       <td><input type=\"text\" name=\"user[" . $user ['uid'] . "][fullname]\" value=\"" . $user ['fullname'] . "\"></td>\n";
			echo "       <td><input type=\"text\" name=\"user[" . $user ['uid'] . "][descr]\" value=\"" . $user ['descr'] . "\"></td>\n";
			echo "       <td><input type=\"text\" name=\"user[" . $user ['uid'] . "][email]\" value=\"" . $user ['email'] . "\"></td>\n";
			echo "       <td>\n";
			if ($perm_templ_perm_edit == "1") {
				echo "        <select name=\"user[" . $user ['uid'] . "][templ_id]\">\n";
				foreach ( do_hook('list_permission_templates') as $template ) {
					($template ['id'] == $user ['tpl_id']) ? $select = " SELECTED" : $select = "";
					echo "          <option value=\"" . $template ['id'] . "\"" . $select . ">" . $template ['name'] . "</option>\n";
				}
				echo "         </select>\n";
			} else {
				echo "         <input type=\"hidden\" name=\"user[" . $user ['uid'] . "][templ_id]\" value=\"" . $user ['tpl_id'] . "\">\n";
				echo "         " . $user ['tpl_name'] . "\n";
			}
			echo "       </td>\n";
			
			if ($ldap_use) {
				if (($perm_is_godlike == "1")) {
					echo "       <td><input type=\"checkbox\" name=\"user[" . $user ['uid'] . "][use_ldap]\"" . $use_ldap . "></td>\n";
				} else {
					if ($use_ldap == " checked") {
						echo "       <td>Yes</td>\n";
					} else {
						echo "       <td>No</td>\n";
					}
				}
			}
			
			if ($user ['uid'] != $_SESSION ["userid"]) {
				echo "       <td><input type=\"checkbox\" name=\"user[" . $user ['uid'] . "][active]\"" . $active . "></td>\n";
			} else {
				echo "       <td><input type=\"hidden\" name=\"user[" . $user ['uid'] . "][active]\" value=\"on\"></td>\n";
			}
			echo "      </tr>\n";
		} else {
			echo "      <tr>\n";
			echo "       <td>&nbsp;</td>\n";
			echo "       <td>" . $user ['username'] . "</td>\n";
			echo "       <td>" . $user ['fullname'] . "</td>\n";
			echo "       <td>" . $user ['descr'] . "</td>\n";
			echo "       <td>" . $user ['email'] . "</td>\n";
			echo "       <td>" . $user ['tpl_name'] . "</td>\n";
			if ($active == " checked") {
				echo "       <td>Yes</td>\n";
			} else {
				echo "       <td>No</td>\n";
			}
			if ($use_ldap == " checked") {
				echo "       <td>Yes</td>\n";
			} else {
				echo "       <td>No</td>\n";
			}
			echo "      </tr>\n";
		}
	}
}

echo "     </table>\n";
if (isset ( $commit_button ) && $commit_button) {
	echo "     <input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _ ( 'Commit changes' ) . "\">\n";
	echo "     <input type=\"reset\" class=\"button\" name=\"reset\" value=\"" . _ ( 'Reset changes' ) . "\">\n";
}
echo "    </form>\n";

if ($perm_templ_perm_edit == "1" || $perm_add_new == "1") {
	echo "    <ul>\n";
}

if ($perm_templ_perm_edit == "1") {
	echo "<li><a href=\"list_perm_templ.php\">" . _ ( 'Edit permission template' ) . "</a>.</li>\n";
}

if ($perm_add_new == "1") {
	echo "<li><a href=\"add_user.php\">" . _ ( 'Add user' ) . "</a>.</li>\n";
}

if ($perm_templ_perm_edit == "1" || $perm_add_new == "1") {
	echo "    </ul>\n";
}

include_once ("inc/footer.inc.php");
