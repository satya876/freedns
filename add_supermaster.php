<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/add_supermaster"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$master_ip = "";
if (isset($_POST["master_ip"])) {
    $master_ip = $_POST["master_ip"];
}

$ns_name = "";
if (isset($_POST["ns_name"])) {
    $ns_name = $_POST["ns_name"];
}

$account = "";
if (isset($_POST["account"])) {
    $account = $_POST["account"];
}

(do_hook('verify_permission' , 'supermaster_add' )) ? $supermasters_add = "1" : $supermasters_add = "0";
(do_hook('verify_permission' , 'user_view_others' )) ? $perm_view_others = "1" : $perm_view_others = "0";

$error = 0;
if (isset($_POST["submit"])) {
    if (add_supermaster($master_ip, $ns_name, $account)) {
        success(SUC_SM_ADD);
    } else {
        $error = "1";
    }
}

echo "     <h2>" . _('Add supermaster') . "</h2>\n";

if ($supermasters_add != "1") {
    echo "     <p>" . _("You do not have the permission to add a new supermaster.") . "</p>\n";
} else {
    echo "     <form method=\"post\" action=\"add_supermaster.php\">\n";
    echo "      <table>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('IP address of supermaster') . "</td>\n";
    echo "        <td class=\"n\">\n";
    if ($error) {
        echo "         <input type=\"text\" class=\"input\" name=\"master_ip\" value=\"" . $master_ip . "\">\n";
    } else {
        echo "         <input type=\"text\" class=\"input\" name=\"master_ip\" value=\"\">\n";
    }
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Hostname in NS record') . "</td>\n";
    echo "        <td class=\"n\">\n";
    if ($error) {
        echo "         <input type=\"text\" class=\"input\" name=\"ns_name\" value=\"" . $ns_name . "\">\n";
    } else {
        echo "         <input type=\"text\" class=\"input\" name=\"ns_name\" value=\"\">\n";
    }
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Account') . "</td>\n";
    echo "        <td class=\"n\">\n";

    echo "         <select name=\"account\">\n";
    /*
      Display list of users to assign slave zone to if the
      editing user has the permissions to, otherise just
      display the adding users name
     */
    $users = do_hook('show_users');
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['userid']) {
            echo "          <option value=\"" . $user['username'] . "\" selected>" . $user['fullname'] . "</option>\n";
        } elseif ($perm_view_others == "1") {
            echo "          <option value=\"" . $user['username'] . "\">" . $user['fullname'] . "</option>\n";
        }
    }
    echo "         </select>\n";

    echo "        </td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">&nbsp;</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _('Add supermaster') . "\">\n";
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "      </table>\n";
    echo "     </form>\n";
}
include_once("inc/footer.inc.php");
