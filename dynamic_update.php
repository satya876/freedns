<html xmlns="http://www.w3.org/1999/xhtml" itemscope="" itemtype="http://schema.org/WebPage">

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge; chrome=1; text/html"/>
<link href="https://dns.bluella.in/favicon.png" rel="shortcut icon" type="image/x-icon"/>
<link rel="shortcut icon" type="image/x-icon" href="https://dns.bluella.in/favicon.png" />
<meta name="description" content="Bluella is the fastest growing company providing business &#38;#38; IT Consulting Services with Offshore development center (ODC). Bluella operates powerful and secure cloud computing technology which protect against DDoS attacks"/>
<meta name="keywords" content="freedns, free dns, software company, IT Services, business consulting, DNS Hosting Provider, free dns service, managed dns services, anycast network, global presence, integrations, Analytics, Mobility, Cloud Computing, CDN, ODC, bluella"/>
<meta name="Author" content="Bluella"/>
<meta name="copyright" content="Bluella"/>
<link rel="canonical" href="https://dns.bluella.in/dynamic_update"/>
<?php

/*  Bluella, DNS a friendly web-based admin tool for DNS Management.
 *  See <https://www.bluella.in> for more details.
 */
 
require('inc/config.inc.php');
require('inc/database.inc.php');
require('inc/record.inc.php');

$db = dbConnect();

/** Make sql query safe
 *
 * @param mixed $value Unsafe Value
 *
 * @return mixed $value Safe Value
 */
function safe($value)
{
    global $db, $db_type;

    if ($db_type == 'mysql' || $db_type == 'sqlite') {
        $value = $db->quote($value, 'text');
        $value = substr($value, 1, -1); // remove quotes
    } elseif ($db_type == 'pgsql') {
        $value = pg_escape_string($value);
    } else {
        return status_exit('baddbtype');
    }

    return $value;
}

/** Get exit status message
 *
 * Print verbose status message for request
 *
 * @param string $status Short status message
 *
 * @return boolean false
 */
function status_exit($status)
{
    $verbose_codes = array(
        'badagent' => 'Your user agent is not valid.',
        'badauth' => 'No username available.',
        'badauth2' => 'Invalid username or password.  Authentication failed.',
        'notfqdn' => 'The hostname you specified was not valid.',
        'dnserr' => 'A DNS error has occurred on our end.  We apologize for any inconvenience.',
        '!yours' => 'The specified hostname does not belong to you.',
        'nohost' => 'The specified hostname does not exist.',
        'good' => 'Your hostname has been updated.',
        '911' => 'A critical error has occurred on our end.  We apologize for any inconvenience.',
        'nochg' => 'This update was identical to your last update, so no changes were made to your hostname configuration.',
        'baddbtype' => 'Unsupported database type',
    );

    if (isset($_REQUEST['verbose'])) {
        $pieces = preg_split('/\s/', $status);
        $status = $verbose_codes[$pieces[0]];
    }
    echo "$status\n";
    return false;
}

/** Check whether the given address is an IP address
 *
 * @param string $ip Given IP address
 *
 * @return string A if IPv4, AAAA if IPv6 or 0 if invalid
 */
function valid_ip_address($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $value = 'A';
    } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $value = 'AAAA';
    } else {
        $value = 0;
    }
    return $value;
}

if (!(isset($_SERVER)) && !$_SERVER['HTTP_USER_AGENT']) {
    return status_exit('badagent');
}

// Grab username & password based on HTTP auth, alternatively the query string
if (isset($_SERVER['PHP_AUTH_USER'])) {
    $auth_username = $_SERVER['PHP_AUTH_USER'];
} elseif (isset($_REQUEST['username'])) {
    $auth_username = $_REQUEST['username'];
}
if (isset($_SERVER['PHP_AUTH_PW'])) {
    $auth_password = $_SERVER['PHP_AUTH_PW'];
} elseif (isset($_REQUEST['password'])) {
    $auth_password = $_REQUEST['password'];
}

// If we still don't have a username, throw up
if (!isset($auth_username)) {
    header('WWW-Authenticate: Basic realm="DNS Update"');
    header('HTTP/1.0 401 Unauthorized');
    return status_exit('badauth');
}

$username = safe($auth_username);
// FIXME: supports only md5 hashes
$password = md5(safe($auth_password));
$hostname = safe($_REQUEST['hostname']);

// Grab IP to use
$given_ip = "";
$given_ip6 = "";
if (!empty($_REQUEST['myip'])) {
    $given_ip = $_REQUEST['myip'];
} elseif (!empty($_REQUEST['ip'])) {
    $given_ip = $_REQUEST['ip'];
}
if (!empty($_REQUEST['myip6'])) {
    $given_ip6 = $_REQUEST['myip6'];
} elseif (!empty($_REQUEST['ip6'])) {
    $given_ip6 = $_REQUEST['ip6'];
}

if (valid_ip_address($given_ip) === 'AAAA') {
    $given_ip6 = $given_ip;
}
// Look for tag to grab the IP we coming from
if (($given_ip6 == "whatismyip") && (valid_ip_address($_SERVER['REMOTE_ADDR']) === 'AAAA')) {
    $given_ip6 = $_SERVER['REMOTE_ADDR'];
}
if (($given_ip == "whatismyip") && (valid_ip_address($_SERVER['REMOTE_ADDR']) === 'A')) {
    $given_ip = $_SERVER['REMOTE_ADDR'];
} elseif (($given_ip == "whatismyip") && (valid_ip_address($_SERVER['REMOTE_ADDR']) === 'AAAA') && (!(valid_ip_address($given_ip6) === 'AAAA'))) {
    $given_ip6 = $_SERVER['REMOTE_ADDR'];
}

// Finally get safe version of the IP
$ip = safe($given_ip);
$ip6 = safe($given_ip6);
// Check its ok...
if ((!valid_ip_address($ip)) && (!valid_ip_address($ip6))) {
    return status_exit('dnserr');
}

if (!strlen($hostname)) {
    return status_exit('notfqdn');
}

$user_query = "
    SELECT
        users.id
    FROM
        users, perm_templ, perm_templ_items, perm_items
    WHERE
        users.username = '$username'
        AND users.password = '$password'
        AND users.active = 1
        AND perm_templ.id = users.perm_templ
        AND perm_templ_items.templ_id = perm_templ.id
        AND perm_items.id = perm_templ_items.perm_id
        AND (
                perm_items.name = 'zone_content_edit_own'
                OR perm_items.name = 'zone_content_edit_others'
        )
";
$user = $db->queryRow($user_query);
if (!$user) {
    return status_exit('badauth2');
}

$zones_query = "SELECT domain_id FROM zones WHERE owner='{$user["id"]}'";
$zones_result = $db->query($zones_query);
$was_updated = false;
$no_update_necessary = false;

while ($zone = $zones_result->fetchRow()) {
    $zone_updated = false;
    $name_query = "SELECT name, type, content FROM records WHERE domain_id='{$zone["domain_id"]}' and type = 'A' OR type = 'AAAA' ";
    $result = $db->query($name_query);

    while ($record = $result->fetchRow()) {
        if ($hostname == $record['name']) {
            if (($record['type'] == 'A') && (valid_ip_address($ip) === 'A')) {
                if ($ip == $record['content']) {
                    $no_update_necessary = true;
                } else {
                    $update_query = "UPDATE records SET content ='{$ip}' where name='{$record["name"]}' and type='A'";
                    $update_result = $db->query($update_query);
                    $zone_updated = true;
                    $was_updated = true;
                }
            } elseif (($record['type'] == 'AAAA') && (valid_ip_address($ip6) === 'AAAA')) {
                if ($ip6 == $record['content']) {
                    $no_update_necessary = true;
                } else {
                    $update_query = "UPDATE records SET content ='{$ip6}' where name='{$record["name"]}' and type='AAAA'";
                    $update_result = $db->query($update_query);
                    $zone_updated = true;
                    $was_updated = true;
                }
            }
        }
    }
    if ($zone_updated) {
        update_soa_serial($zone['domain_id']);
    }
}

return (($was_updated || $no_update_necessary) ? status_exit('good') : status_exit('!yours'));
