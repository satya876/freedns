<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/bulk_registration"/>

</html>

<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require_once("inc/toolkit.inc.php");
include_once("inc/header.inc.php");

$owner = "-1";
if ((isset($_POST['owner'])) && (v_num($_POST['owner']))) {
    $owner = $_POST['owner'];
}

$dom_type = "NATIVE";
if (isset($_POST["dom_type"]) && (in_array($_POST['dom_type'], $server_types))) {
    $dom_type = $_POST["dom_type"];
}

if (isset($_POST['domains'])) {
    $domains = explode("\r\n", $_POST['domains']);
    foreach ($domains as $key => $domain) {
        $domain = trim($domain);
        if ($domain == '') {
            unset($domains[$key]);
        } else {
            $domains[$key] = $domain;
        }
    }
} else {
    $domains = array();
}

if (isset($_POST['zone_template'])) {
    $zone_template = $_POST['zone_template'];
} else {
    $zone_template = "none";
}

/*
  Check user permissions
 */
(do_hook('verify_permission' , 'zone_master_add' )) ? $zone_master_add = "1" : $zone_master_add = "0";
(do_hook('verify_permission' , 'user_view_others' )) ? $perm_view_others = "1" : $perm_view_others = "0";

if (isset($_POST['submit']) && $zone_master_add == "1") {
    $error = false;
    foreach ($domains as $domain) {
        if (!is_valid_hostname_fqdn($domain, 0)) {
            error($domain . ' failed - ' . ERR_DNS_HOSTNAME);
        } elseif (domain_exists($domain)) {
            error($domain . " failed - " . ERR_DOMAIN_EXISTS);
            $error = true;
        } elseif (add_domain($domain, $owner, $dom_type, '', $zone_template)) {
            success("<a href=\"edit.php?id=" . get_zone_id_from_name($domain) . "\">" . $domain . " - " . SUC_ZONE_ADD . '</a>');
        }
    }

    if (false === $error) {
        unset($domains, $owner, $dom_type, $zone_template);
    }
}

if ($zone_master_add != "1") {
    error(ERR_PERM_ADD_ZONE_MASTER);
} else {
    echo "     <h2>" . _('Bulk registration') . "</h2>\n";

    $available_zone_types = array("MASTER", "NATIVE");
    $users = do_hook('show_users');
    $zone_templates = get_list_zone_templ($_SESSION['userid']);

    echo "     <form method=\"post\" action=\"bulk_registration.php\">\n";
    echo "      <table>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\" width=\"100\">" . _('Owner') . ":</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <select name=\"owner\">\n";
    /*
      Display list of users to assign zone to if creating
      user has the proper permission to do so.
     */
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['userid']) {
            echo "          <option value=\"" . $user['id'] . "\" selected>" . $user['fullname'] . "</option>\n";
        } elseif ($perm_view_others == "1") {
            echo "          <option value=\"" . $user['id'] . "\">" . $user['fullname'] . "</option>\n";
        }
    }
    echo "         </select>\n";
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Type') . ":</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <select name=\"dom_type\">\n";
    foreach ($available_zone_types as $type) {
        echo "          <option value=\"" . $type . "\">" . strtolower($type) . "</option>\n";
    }
    echo "         </select>\n";
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Template') . ":</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <select name=\"zone_template\">\n";
    echo "          <option value=\"none\">none</option>\n";
    foreach ($zone_templates as $zone_template) {
        echo "          <option value=\"" . $zone_template['id'] . "\">" . $zone_template['name'] . "</option>\n";
    }
    echo "         </select>\n";
    echo "        </td>\n";
    echo "       </tr>\n";

    echo "       <tr>\n";
    echo "        <td class=\"n\">" . _('Zones') . ":</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <ul id=\"domain_names\" style=\"list-style-type:none; padding:0 \">\n";
    echo "		<li>" . _('Type one domain per line') . ":</li>\n";
    echo "          <li><textarea class=\"input\" name=\"domains\" rows=\"10\" cols=\"30\" style=\"width: 500px;\">";
    if (isset($error) && isset($_POST['domains'])) {
        echo $_POST['domains'];
    }
    echo "</textarea></li>\n";
    echo "         </ol>\n";
    echo "        </td>\n";
    echo "       </tr>\n";

    echo "       <tr>\n";
    echo "        <td class=\"n\">&nbsp;</td>\n";
    echo "        <td class=\"n\">\n";
    echo "         <input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _('Add zones') . "\">\n";
    echo "        </td>\n";
    echo "       </tr>\n";
    echo "      </table>\n";
    echo "     </form>\n";
}

include_once("inc/footer.inc.php");
