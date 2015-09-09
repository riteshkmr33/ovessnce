<?php

/* ======================================================================*\
  || #################################################################### ||
  || # Rhino Socket 2.0                                                 # ||
  || # ---------------------------------------------------------------- # ||
  || # Copyright 2014 Rhino All Rights Reserved.                        # ||
  || # This file may not be redistributed in whole or significant part. # ||
  || #   ---------------- Rhino IS NOT FREE SOFTWARE ----------------   # ||
  || #                  http://www.livesupportrhino.com                 # ||
  || #################################################################### ||
  \*====================================================================== */

// Redirect to something...
function ls_redirect($url, $code = 302)
{
    header('Location: ' . $url, true, $code);
    exit;
}

// Get a secure mysql input
function smartsql($value)
{
    global $lsdb;

    if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
    }
    if (!is_int($value)) {
        $value = $lsdb->real_escape_string($value);
    }
    return $value;
}

// Check if userid can have access to the pages.
function ls_get_access($page, $array, $superoperator)
{
    $roles = explode(',', $array);
    if ((is_array($roles) && in_array($page, $roles)) || $superoperator) {
        return true;
    }
}

// Get the setting variable as well the default variable as array
function ls_get_setting($group)
{
    global $lsdb;
    $result = $lsdb->query('SELECT varname, value, defaultvalue FROM ' . DB_PREFIX . 'setting WHERE groupname = "' . smartsql($group) . '"');
    while ($row = $result->fetch_assoc()) {
        $setting[] = array('varname' => $row['varname'], 'value' => $row['value'], 'defaultvalue' => $row['defaultvalue']);
    }
    return $setting;
}

// Get the data only per ID (e.g. edit single user, edit category)
function ls_get_data($id, $table)
{

    global $lsdb;
    $result = $lsdb->query('SELECT * FROM ' . $table . ' WHERE id = "' . smartsql($id) . '"');
    while ($row = $result->fetch_assoc()) {
        // collect each record into $lsdata
        $lsdata = $row;
    }
    return $lsdata;
}

// Check if row exist
function ls_row_exist($id, $table)
{
    global $lsdb;
    $result = $lsdb->query('SELECT id FROM ' . $table . ' WHERE id = "' . smartsql($id) . '" LIMIT 1');
    if ($lsdb->affected_rows > 0) {
        return true;
    }
}

// Verify paramaters
function verifyparam($name, $regexp, $default = null)
{
    if (isset($_GET[$name])) {
        $val = $_GET[$name];
        if (preg_match($regexp, $val))
            return $val;
    } else if (isset($_POST[$name])) {
        $val = $_POST[$name];
        if (preg_match($regexp, $val))
            return $val;
    } else {
        if (isset($default))
            return $default;
    }
    echo "<html><head></head><body>Wrong parameter used or absent: " . $name . "</body></html>";
    exit;
}

// Verfiy if there is a online operator
function online_operators($dp, $did = 0, $oid = 0)
{
    $timeout = time() - 300;
    $timerunout = 1;
    $department = 0;
    $departmentall = array();
    $departments = array();
    $departmentp = array();

    global $lsdb;

    // Update database first to see who is online!
    $lsdb->query('UPDATE ' . DB_PREFIX . 'user SET available = 0 WHERE lastactivity < ' . $timeout . '');

    // Set to zero
    $sql_where = '';
    $sql_where1 = '';

    if (LS_TW_SID && LS_TW_TOKEN)
        $sql_where = ' (phonenumber != "" AND FIND_IN_SET("' . date("D") . '", tw_days) AND (TIME(NOW()) >= tw_time_from AND TIME(NOW()) <= tw_time_to))';

    if ($did > 0) {
        $sql_where1 = ($sql_where ? ' OR' : '') . ' available = 1 AND (departments = 0 OR FIND_IN_SET(' . $did . ', departments))';
    } else {
        $sql_where1 = ($sql_where ? ' OR' : '') . ' available = 1';
    }

    if ($oid > 0) {
        $sql_where1 = ($sql_where ? ' OR' : '') . ' id = "' . $oid . '" AND available = 1';
        $sql_where2 = ' AND id = "' . $oid . '"';
    }

    $result = $lsdb->query('SELECT id, departments FROM ' . DB_PREFIX . 'user WHERE (access = 1 AND' . $sql_where . $sql_where1 . ') OR (access = 1 AND emailnot = 1 AND FIND_IN_SET("' . date("D") . '", tw_days) AND (TIME(NOW()) >= tw_time_from AND TIME(NOW()) <= tw_time_to)' . $sql_where2 . ')');

    if ($lsdb->affected_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            if ($row["departments"] == 0) {
                $departmentall = $dp;
            }

            if (is_numeric($row["departments"])) {

                if (isset($dp) && is_array($dp))
                    foreach ($dp as $z) {

                        if ($z["id"] == $row["departments"]) {

                            $departments[] = $z;
                        }
                    }
            }

            if ($row["departments"] != 0 && !is_numeric($row["departments"])) {

                if (isset($dp) && is_array($dp))
                    foreach ($dp as $z) {

                        if (in_array($z["id"], explode(',', $row["departments"]))) {

                            $departmentp[] = $z;
                        }
                    }
            }
        }
    } else {
        $timerunout = 0;
    }

    if ($timerunout) {
        $department = array_merge($departmentall, $departments, $departmentp);

        if (is_array($department))
            $department = array_map("unserialize", array_unique(array_map("serialize", $department)));

        return $department;
    } else {
        return false;
    }
}

// Check if the lang folder for buttons exist
function folder_lang_button($lang)
{
    return file_exists('./img/buttons/' . $lang . '/');
}

// Get the real IP Address
function get_ip_address()
{
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

// Replace urls
function replace_urls($string)
{
    $string = preg_replace('/(https?|ftp)([\:\/\/])([^\\s]+)/', '<a href="$1$2$3" target="_blank">$1$2$3</a>', $string);
    return $string;
}

// only full words
function ls_cut_text($jakvar, $jakvar1, $jakvar2)
{
    if (empty($jakvar1)) {
        $jakvar1 = 160;
    }
    $crepl = array('<?', '<?php', '"', "'", "?>");
    $cfin = array('', '', '', '', '');
    $jakvar = str_replace($crepl, $cfin, $jakvar);
    $jakvar = trim($jakvar);
    $jakvar = strip_tags($jakvar);
    $txtl = strlen($jakvar);
    if ($txtl > $jakvar1) {
        for ($i = 1; $jakvar[$jakvar1 - $i] != " "; $i++) {
            if ($i == $jakvar1) {
                return substr($jakvar, 0, $jakvar1) . $jakvar2;
            }
        }
        $jakdata = substr($jakvar, 0, $jakvar1 - $i + 1) . $jakvar2;
    } else {
        $jakdata = $jakvar;
    }
    return $jakdata;
}

// Is search bot
function is_bot()
{
    $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
        "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
        "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
        "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
        "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
        "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
        "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler", "TweetmemeBot",
        "Butterfly", "Twitturls", "Me.dium", "Twiceler");

    foreach ($botlist as $bot) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
            return true; // Is a bot
    }

    return false; // Not a bot
}

// Detect Mobile Browser in a simple way to display videos in html5 or video/template not available message
function jak_find_browser($useragent, $wap)
{

    $ifmobile = preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile|o2|opera m(ob|in)i|palm( os)?|p(ixi|re)\/|plucker|pocket|psp|smartphone|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce; (iemobile|ppc)|xiino/i', $useragent);

    $ifmobileM = preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

    if ($ifmobile || $ifmobileM || isset($wap)) {
        return true;
    } else {
        return false;
    }
}

// Load the version from jakcms
function jak_load_xml_from_curl($url)
{

    if (function_exists('curl_version')) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    } else {
        return false;
    }
}

function selfURL()
{

    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['PHP_SELF'];

    $referrer = filter_var($referrer, FILTER_VALIDATE_URL);

    return $referrer;
}

// Get Browser and System
function getBrowser($agent)
{
    $u_agent = $agent;
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'Linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'Mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'Windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern
    );
}

// Some clean ups for bad configurations server side
function undoRegisterGlobals()
{
    if (ini_get("register_globals")) {
        $array = array("_REQUEST", "_SESSION", "_SERVER", "_ENV", "_FILES");
        foreach ($array as $value) {
            foreach ((array) $GLOBALS[$value] as $key => $var) {
                if (isset($GLOBALS[$key]) and $var === $GLOBALS[$key])
                    unset($GLOBALS[$key]);
            }
        }
    }
}

function undoMagicQuotes($value)
{

    if (!is_array($value))
        return stripslashes($value);
    else
        array_map("undoMagicQuotes", $value);
    return $value;
}

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = true, $atts = array())
{
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
    if ($img) {
        $url = '<img src="' . $url . '"';
        foreach ($atts as $key => $val)
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

// Verfiy if there is a online operator
function online_practitioners()
{
    $timeout = time() - 300;
    $timerunout = 1;

    global $lsdb;

    // Update database first to see who is online!
    $lsdb->query('UPDATE ' . DB_PREFIX . 'user SET available = 0 WHERE lastactivity < ' . $timeout . '');

    $result = $lsdb->query('SELECT id, name FROM ' . DB_PREFIX . 'user WHERE access = 1 AND available = 1');

    if ($lsdb->affected_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[$row['id']] = $row['name'];
        }
    } else {
        $timerunout = 0;
    }

    if ($timerunout) {
        return $users;
    } else {
        return false;
    }
}

?>