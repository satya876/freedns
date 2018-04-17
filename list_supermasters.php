<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/list_supermaster"/>

</html>

<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

(do_hook('verify_permission', 'supermaster_view')) ? $perm_sm_view = "1" : $perm_sm_view = "0";
(do_hook('verify_permission', 'supermaster_edit')) ? $perm_sm_edit = "1" : $perm_sm_edit = "0";

$supermasters = get_supermasters();
$num_supermasters = ($supermasters == -1) ? 0 : count($supermasters);

echo "     <h2>" . _('List supermasters') . "</h2>\n";
echo "     <table>\n";
echo "      <tr>\n";
echo "       <th>&nbsp;</th>\n";
echo "       <th>" . _('IP address of supermaster') . "</th>\n";
echo "       <th>" . _('Hostname in NS record') . "</th>\n";
echo "       <th>" . _('Account') . "</th>\n";
echo "      </tr>\n";
if ($num_supermasters == "0") {
    echo "      <tr>\n";
    echo "       <td class=\"n\">&nbsp;</td>\n";
    echo "       <td class=\"n\" colspan=\"3\">\n";
    echo "        " . _('There are no zones to show in this listing.') . "\n";
    echo "       </td>\n";
    echo "      </tr>\n";
} else {
    foreach ($supermasters as $c) {
        echo "      <tr>\n";
        if ($perm_sm_edit == "1") {
            echo "        <td class=\"n\"><a href=\"delete_supermaster.php?master_ip=" . $c['master_ip'] . "&amp;ns_name=" . $c['ns_name'] . "\"><img src=\"images/delete.gif\" title=\"" . _('Delete supermaster') . ' ' . $c['master_ip'] . "\" alt=\"[  " . _('Delete supermaster') . " ]\"></a></td>\n";
        } else {
            echo "<td>&nbsp;</td>\n";
        }
        echo "       <td class=\"y\">" . $c['master_ip'] . "</td>\n";
        echo "       <td class=\"y\">" . $c['ns_name'] . "</td>\n";
        echo "       <td class=\"y\">" . $c['account'] . "</td>\n";
        echo "      </tr>\n";
    }
}
echo "     </table>\n";

include_once("inc/footer.inc.php");
