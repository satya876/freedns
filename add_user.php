<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/add_user"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

if (!do_hook('verify_permission' , 'user_add_new' )) {
    error(ERR_PERM_ADD_USER);
} else {
    if (isset($_POST["commit"])) {
        if (do_hook('add_new_user' , $_POST )) {
            success(SUC_USER_ADD);
        }
    }

    echo "     <h2>" . _('Add user') . "</h2>\n";
    echo "     <form method=\"post\" action=\"add_user.php\">\n";
    echo "      <table>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Username') . "</td>\n";
    echo "        <td class=\"n\"><input type=\"text\" class=\"input\" name=\"username\" value=\"\"></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Fullname') . "</td>\n";
    echo "        <td class=\"n\"><input type=\"text\" class=\"input\" name=\"fullname\" value=\"\"></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Password') . "</td>\n";
    echo "        <td class=\"n\"><input type=\"password\" class=\"input\" name=\"password\"></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Email address') . "</td>\n";
    echo "        <td class=\"n\"><input type=\"text\" class=\"input\" name=\"email\" value=\"\"></td>\n";
    echo "       </tr>\n";
    if (do_hook('verify_permission' , 'user_edit_templ_perm' )) {
        echo "       <tr>\n";
        echo "        <td class=\"n\">" . _('Permission template') . "</td>\n";
        echo "        <td class=\"n\">\n";
        echo "         <select name=\"perm_templ\">\n";
        foreach (do_hook('list_permission_templates' ) as $template) {
            echo "          <option value=\"" . $template['id'] . "\">" . $template['name'] . "</option>\n";
        }
        echo "         </select>\n";
        echo "       </td>\n";
        echo "       </tr>\n";
    }
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Description') . "</td>\n";
    echo "        <td class=\"n\"><textarea rows=\"4\" cols=\"30\" class=\"inputarea\" name=\"descr\"></textarea></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Enabled') . "</td>\n";
    echo "        <td class=\"n\"><input type=\"checkbox\" class=\"input\" name=\"active\" value=\"1\" CHECKED></td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">&nbsp;</td>\n";
    echo "        <td class=\"n\"><input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _('Commit changes') . "\"></td>\n";
    echo "      </table>\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
