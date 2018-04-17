<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/change_password"/>

<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");

if (isset($_POST['submit']) && $_POST['submit']) {
    do_hook('change_user_pass' , $_POST );
}

include_once("inc/header.inc.php");

echo "    <h2>" . _('Change password') . "</h2>\n";
echo "    <form method=\"post\" action=\"change_password.php\">\n";
echo "     <table border=\"0\" cellspacing=\"4\">\n";
echo "      <tr>\n";
echo "       <td class=\"n\">" . _('Current password') . ":</td>\n";
echo "       <td class=\"n\"><input type=\"password\" class=\"input\" name=\"currentpass\" value=\"\"></td>\n";
echo "      </tr>\n";
echo "      <tr>\n";
echo "       <td class=\"n\">" . _('New password') . ":</td>\n";
echo "       <td class=\"n\"><input type=\"password\" class=\"input\" name=\"newpass\" value=\"\"></td>\n";
echo "      </tr>\n";
echo "      <tr>\n";
echo "       <td class=\"n\">" . _('New password') . ":</td>\n";
echo "       <td class=\"n\"><input type=\"password\" class=\"input\" name=\"newpass2\" value=\"\"></td>\n";
echo "      </tr>\n";
echo "      <tr>\n";
echo "       <td class=\"n\">&nbsp;</td>\n";
echo "       <td class=\"n\">\n";
echo "        <input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _('Change password') . "\">\n";
echo "       </td>\n";
echo "      </tr>\n";
echo "     </table>\n";
echo "    </form>\n";

include_once("inc/footer.inc.php");
