<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/add_zone_templ"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

if (!do_hook('verify_permission' , 'zone_master_add' )) {
    error(ERR_PERM_ADD_ZONE_TEMPL);
} else {

    if (isset($_POST['commit'])) {
        if (add_zone_templ($_POST, $_SESSION['userid'])) {
            success(SUC_ZONE_TEMPL_ADD);
        } // TODO: otherwise repopulate values to form
    }

    /*
      Display new zone template form
     */

    $username = do_hook('get_fullname_from_userid' , $_SESSION['userid'] );
    echo "    <h2>" . _('Add zone template for') . " " . $username . "</h2>\n";
    echo "    <form method=\"post\" action=\"add_zone_templ.php\">\n";
    echo "     <table>\n";
    echo "      <tr>\n";
    echo "       <th>" . _('Name') . "</th>\n";
    echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_name\" value=\"\"></td>\n";
    echo "      </tr>\n";
    echo "      <tr>\n";
    echo "       <th>" . _('Description') . "</th>\n";
    echo "       <td><input class=\"wide\" type=\"text\" name=\"templ_descr\" value=\"\"></td>\n";
    echo "      </tr>\n";
    echo "     </table>\n";
    echo "     <input type=\"submit\" class=\"button\" name=\"commit\" value=\"" . _('Add zone template') . "\">\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
