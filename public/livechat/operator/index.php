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

// prevent direct php access
define('LS_ADMIN_PREVENT_ACCESS', 1);

if (!file_exists('config.php')) {
    die('[index.php] config.php not found');
}
require_once 'config.php';

$page = ($temppa ? filter_var($temppa, FILTER_SANITIZE_STRING) : '');
$page1 = ($temppa1 ? filter_var($temppa1, FILTER_SANITIZE_STRING) : '');
$page2 = ($temppa2 ? filter_var($temppa2, FILTER_SANITIZE_STRING) : '');
$page3 = ($temppa3 ? filter_var($temppa3, FILTER_SANITIZE_STRING) : '');
$page4 = ($temppa4 ? filter_var($temppa4, FILTER_SANITIZE_STRING) : '');
$page5 = ($temppa5 ? filter_var($temppa5, FILTER_SANITIZE_STRING) : '');
$page6 = ($temppa5 ? filter_var($temppa6, FILTER_SANITIZE_STRING) : '');

$LS_SPECIALACCESS = false;
$LS_UNDELETABLE = false;

// Only the SuperAdmin in the config file see everything
if ($lsuser->lsSuperadminaccess(LS_USERID_RHINO)) {
    $LS_SPECIALACCESS = true;
    $LS_UNDELETABLE = true;
    define('LS_SUPERADMINACCESS', true);
} else {
    define('LS_SUPERADMINACCESS', false);
}

// Get the redirect into a sessions for better login handler
if ($page && $page != '404')
    $_SESSION['LSRedirect'] = $_SERVER['REQUEST_URI'];

// Define for template the real request
$realrequest = substr($getURL->lsRealrequest(), 1);
define('LS_PARSE_REQUEST', $realrequest);

// We need the template folder, title, author and lang as template variable
$LS_CONTACT_FORM = LS_CONTACTFORM;
$LS_REGISTER_FORM = LS_REGISTER;
define('LS_PAGINATE_ADMIN', 1);

// Get the language for the operator
if (LS_USERID_RHINO)
    $USER_LANGUAGE = $lsuser->getVar("language");

if ($USER_LANGUAGE && file_exists(APP_PATH . 'operator/lang/' . $USER_LANGUAGE . '.ini')) {
    $tl = parse_ini_file(APP_PATH . 'operator/lang/' . $USER_LANGUAGE . '.ini', true);
    $_SESSION['lc_ulang'] = $BT_LANGUAGE;
} elseif (!$USER_LANGUAGE && file_exists(APP_PATH . 'operator/lang/' . LS_LANG . '.ini')) {
    $tl = parse_ini_file(APP_PATH . 'operator/lang/' . LS_LANG . '.ini', true);
} else {
    trigger_error('Translation file not found');
}

// First check if the user is logged in
if (LS_USERID_RHINO) {

    define('LS_ADMINACCESS', true);
    $_SESSION['ls_superoperator'] = LS_SUPERADMINACCESS;
    $_SESSION['ls_opid'] = LS_USERID_RHINO;

// Get the name from the user for the welcome message
    $LS_WELCOME_NAME = $lsuser->getVar("name");
// Get the department(s)
    $LS_USR_DEPARTMENTS = $lsuser->getVar("departments");
    $_SESSION['usr_department'] = $LS_USR_DEPARTMENTS;

    if ($LS_USR_DEPARTMENTS == 0) {
        $LS_USR_DEPARTMENTS = $tl['general']['g105'];
    } else {

        if (is_numeric($LS_USR_DEPARTMENTS)) {
            $depsql = 'id = ' . $LS_USR_DEPARTMENTS;
        } else {
            $depsql = 'id IN(' . $LS_USR_DEPARTMENTS . ')';
        }

        $depresult = $lsdb->query('SELECT title FROM ' . DB_PREFIX . 'departments WHERE ' . $depsql . ' AND active = 1 ORDER BY dorder ASC');
        while ($deprow = $depresult->fetch_assoc()) {
            $deplist[] = $deprow['title'];
        }

        if (!empty($deplist)) {
            $LS_USR_DEPARTMENTS = join(", ", $deplist);
        } else {
            $LS_USR_DEPARTMENTS = $tl['general']['g105'];
        }
    }
} else {
    define('LS_ADMINACCESS', false);
}

// Now get the forgot password link into the right shape
$P_FORGOT_PASS_ADMIN = LS_rewrite::lsParseurl($tl['login']['l12'], '', '', '', '');

// When there is a post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

// Delete the conversation if whish so
    if (isset($_POST['delete_conv'])) {

        // check to see if conversation is to be stored
        $result = $lsdb->query('SELECT id, name, email FROM ' . DB_PREFIX . 'sessions WHERE id = "' . $_POST['id'] . '"');
        $row = $result->fetch_assoc();

        $lsdb->query('UPDATE ' . DB_PREFIX . 'sessions SET status = 0, ended = "' . time() . '", hide = 1  WHERE id = "' . $row['id'] . '"');

        $lsdb->query('INSERT INTO ' . DB_PREFIX . 'transcript SET 
	name = "' . $lsuser->getVar("name") . '",
	message = "' . smartsql($tl['general']['g63']) . '",
	user = "' . $lsuser->getVar("username") . '",
	convid = "' . $row['id'] . '",
	time = NOW(),
	class = "notice"');

        ls_redirect(BASE_URL);
    }

// transfer customer
    if (isset($_POST['transfer_customer']) && is_numeric($_POST['userid']) && is_numeric($_POST['cid'])) {

        // check to see if conversation is to be stored
        $result = $lsdb->query('SELECT name FROM ' . DB_PREFIX . 'user WHERE id = "' . $_POST['userid'] . '"');
        $row = $result->fetch_assoc();

        $msg = $row['name'] . ':#:' . strip_tags($_POST['transfermsg']);

        $result = $lsdb->query('UPDATE ' . DB_PREFIX . 'sessions SET transferid = "' . smartsql($_POST['operator']) . '", transfermsg = "' . smartsql($msg) . '"  WHERE id = "' . $_POST['cid'] . '"');

        if ($result) {

            ls_redirect(BASE_URL);
        } else {

            $operatori = $tl['general']['g116'];
        }
    }
}

$checkp = 0;

if (!isset($_SERVER['HTTP_REFERER'])) {
    $_SERVER['HTTP_REFERER'] = '';
}

// home
if ($page == '') {
    #show login page only if the admin is not logged in
    #else show homepage
    if (!LS_USERID_RHINO) {
        require_once 'login.php';
    } else {
        $LS_PROVED = 1;
        $LS_PAGE_ACTIVE = 1;
        $html_title = $tl['menu']['m'];

        $cacheverfile = APP_PATH . LS_CACHE_DIRECTORY . '/version.php';
        $admincacheexist = false;
        if (file_exists($cacheverfile)) {
            include_once $cacheverfile;
            $admincacheexist = true;
            $admin_cache_expire = LS_ADMIN_CACHE_CREATED + 86400;
            if ($admin_cache_expire < time()) {
                if (file_exists($cacheverfile)) {
                    unlink($cacheverfile);
                    $admincacheexist = false;
                }
            }
        }if (!$admincacheexist && function_exists('simplexml_load_file')) {
            $versioncont = "<?php\n";
            $versioncont.="define('LS_ADMIN_CACHE_CREATED','" . time() . "');\n";
            $urlxml = parse_url(BASE_URL_ORIG);
            $jakXML = @simplexml_load_file('http://vc.livesupportrhino.com/rhinosocket.xml?url=' . $urlxml["host"] . '&on=' . LS_O_NUMBER);
            if ($jakXML) {
                $versioncont.="define('LS_VC_STATUS',1);\n";
                foreach ($jakXML->children()as $child) {
                    $versioncont.="define('LS_VC_" . strtoupper($child->getName()) . "','" . $child . "');\n";
                }
            } else {
                $jakXML = jak_load_xml_from_url('http://vc.livesupportrhino.com/rhinosocket.xml?url=' . $urlxml["host"] . '&on=' . LS_O_NUMBER);
                if ($jakcurlxml) {
                    $versioncont.="define('LS_VC_STATUS',1);\n";
                    foreach ($jakXML->children()as $child) {
                        $versioncont.="define('LS_VC_" . strtoupper($child->getName()) . "','" . $child . "');\n";
                    }
                } else {
                    $versioncont.="define('LS_VC_STATUS',0);\n";
                }
            }$versioncont.="?>";
            LS_base::lsWriteinCache($cacheverfile, $versioncont, '');
            if (file_exists($cacheverfile))
                include_once $cacheverfile;
        }

        $template = 'index.php';
    }
    $checkp = 1;
}
if ($page == 'logout') {
    $checkp = 1;
    if (LS_USERID_RHINO) {
        $lsuserlogin->lsLogout(LS_USERID_RHINO);
        $LS_PROVED = 1;
        $html_title = $tl['logout']['l'];
        $template = 'success.php';
    } else {
        ls_redirect(BASE_URL);
    }
}
if ($page == 'forgot') {
    if (LS_USERID_RHINO || !is_numeric($page1) || !$lsuserlogin->lsForgotactive($page1))
        ls_redirect(BASE_URL);
    require_once 'forgot.php';
    $html_title = $tl['general']['g94'];
    $LS_PROVED = 0;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'success') {
    if (!LS_USERID_RHINO) {
        ls_redirect(BASE_URL);
    }
    $template = 'success.php';
    $LS_PROVED = 1;
    $checkp = 1;
}
if ($page == 'error') {
    if (!LS_USERID_RHINO) {
        ls_redirect(BASE_URL);
    }
    $template = 'error.php';
    $LS_PROVED = 1;
    $checkp = 1;
}
if ($page == '404') {
    if (!LS_USERID_RHINO) {
        ls_redirect(BASE_URL);
    }
    // Go to the 404 Page
    $LS_PROVED = 1;
    $html_title = '404 / ' . LS_TITLE;
    $template = '404.php';
    $checkp = 1;
}
if ($page == 'files') {
    require_once 'files.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'response') {
    require_once 'response.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'departments') {
    require_once 'departments.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'uonline') {
    require_once 'uonline.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'leads') {
    require_once 'leads.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'chat') {
    require_once 'chat.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'chats') {
    require_once 'chats.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'notes') {
    require_once 'notes.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'emails') {
    require_once 'emails.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'proactive') {
    require_once 'proactive.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'settings') {
    require_once 'setting.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'style') {
    require_once 'style.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'users') {
    require_once 'user.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'statistics') {
    require_once 'statistics.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'logs') {
    require_once 'logs.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'maintenance') {
    require_once 'maintenance.php';
    $LS_PROVED = 1;
    $LS_PAGE_ACTIVE = 1;
    $checkp = 1;
}
if ($page == 'checkstatus') {
    unset($_SESSION['LSRedirect']);
    if (LS_USERID_RHINO && LS_USERID_RHINO == $_GET['uid']) {
        echo json_encode(array('status' => 1));
    } else {
        echo json_encode(array('status' => 0));
    }
    exit;
}

// if page not found
if ($checkp == 0) {
    ls_redirect(BASE_URL . 'index.php?p=404');
}

if (isset($template) && $template != '') {
    include_once APP_PATH . 'operator/template/' . $template;
}

// Finally close all db connections
$lsdb->ls_close();
?>