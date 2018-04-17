<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
	<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
	<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
	<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
    <meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
    <meta name="Author" content="Bluella"/>
    <meta name="copyright" content="Bluella"/>
	<link rel="canonical" href="https://dns.bluella.in/index"/>
</html>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

echo "     <h3>" . _('Welcome') . " " . $_SESSION["name"] . "</h3>\n";

do_hook('verify_permission', 'search') ? $perm_search = "1" : $perm_search = "0";
do_hook('verify_permission', 'zone_content_view_own') ? $perm_view_zone_own = "1" : $perm_view_zone_own = "0";
do_hook('verify_permission', 'zone_content_view_others') ? $perm_view_zone_other = "1" : $perm_view_zone_other = "0";
do_hook('verify_permission', 'supermaster_view') ? $perm_supermaster_view = "1" : $perm_supermaster_view = "0";
do_hook('verify_permission', 'zone_master_add') ? $perm_zone_master_add = "1" : $perm_zone_master_add = "0";
do_hook('verify_permission', 'zone_slave_add') ? $perm_zone_slave_add = "1" : $perm_zone_slave_add = "0";
do_hook('verify_permission', 'supermaster_add') ? $perm_supermaster_add = "1" : $perm_supermaster_add = "0";

echo "    <ul>\n";
echo "    <li><a href=\"index.php\">" . _('Home') . "</a></li>\n";
if ($perm_search == "1") {
    echo "    <li><a href=\"search.php\">" . _('Search') . "</a></li>\n";
}
if ($perm_view_zone_own == "1" || $perm_view_zone_other == "1") {
    echo "    <li><a href=\"list_zones.php\">" . _('List zones') . "</a></li>\n";
}
if ($perm_zone_master_add) {
    echo "    <li><a href=\"list_zone_templ.php\">" . _('List zone templates') . "</a></li>\n";
}
if ($perm_supermaster_view) {
    echo "    <li><a href=\"list_supermasters.php\">" . _('List supermasters') . "</a></li>\n";
}
if ($perm_zone_master_add) {
    echo "    <li><a href=\"add_zone_master.php\">" . _('Add master zone') . "</a></li>\n";
}
if ($perm_zone_slave_add) {
    echo "    <li><a href=\"add_zone_slave.php\">" . _('Add slave zone') . "</a></li>\n";
}
if ($perm_supermaster_add) {
    echo "    <li><a href=\"add_supermaster.php\">" . _('Add supermaster') . "</a></li>\n";
}
if ($_SESSION["auth_used"] != "ldap") {
    echo "    <li><a href=\"change_password.php\">" . _('Change password') . "</a></li>\n";
}
echo "    <li><a href=\"users.php\">" . _('User administration') . "</a></li>\n";
echo "    <li><a href=\"index.php?logout\">" . _('Logout') . "</a></li>\n";
echo "   </ul>\n";

include_once("inc/footer.inc.php");
