<?php
@define('NWNADMIN_BASE', dirname(__FILE__));

require_once NWNADMIN_BASE . '/lib/base.php';

// script global variables
global $nwndriver;
$params = &$nwndriver->getParams();
$configured = false;
$rawResult = false;
$wait = false;

// no regular users allowed on this page
if (!Auth::isAdmin('nwnadmin:admin')) {
    header('Location: ./server.php');
}

// hack the page if the server is not configured
if (!$params || is_a($params, 'PEAR_Error') ||
    !isset($params['servername']) || $params['servername'] == '') {
    $notification->push(_("The server is not configured!"), 'horde.warning');
} else {
    $configured = true;
}

$actionId = Util::getFormData('actionId');
switch ($actionId) {
case 'stop':
    $wait = true;
    $result = $nwndriver->stopServer();
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem stopping the server: ") .
                $result->getMessage(), 'horde.error');
    } else {
        $notification->push(_("The server was stopped."), 'horde.success');
        sleep(2);
    }
    break;
case 'start':
    $wait = true;
    $result = $nwndriver->startServer();
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem starting the server: ") .
                $result->getMessage(), 'horde.error');
    } else {
        $notification->push(_("The server was started."), 'horde.success');
    }
    break;
case 'kill':
    $wait = true;
    $result = $nwndriver->killServer();
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem killing the server: ") .
                $result->getMessage(), 'horde.error');
    } else {
        $notification->push(_("The server was killed."), 'horde.warning');
    }
case 'raw':
    $wait = true;
    $result = $nwndriver->sendCommand(Util::getFormData('command'), true);
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem sending the command: ") .
                $result->getMessage(), 'horde.error');
    } else {
        $rawResult = $result;
        $notification->push(_("The command was accepted."), 'horde.success');
    }
}

// select form to display
if ($wait) { sleep(2); }
if ($nwndriver->serverRunning()) {
    $title = _("Stop/Restart/Kill Server");
    $form = 'kill.inc';
} else {
    $title = _("Run Server");
    $form = 'start.inc';
}

// start the page
require_once NWNADMIN_TEMPLATES . '/common-header.inc';
require_once NWNADMIN_TEMPLATES . '/menu.inc';

// this is cheap, but the redirect/notification combination doesn't work right
if ($configured) {
    // output the form
    include NWNADMIN_TEMPLATES . '/start/' . $form;
}

if ($rawResult) {
    printf("<br /><div class='header'>Command Result</div><br />" .
            "<div class='fixed'>%s</div>", nl2br($nwndriver->getLogContent()));
}

// finish up the page
require_once $registry->get('templates', 'horde') . '/common-footer.inc';
