<?php
@define('NWNADMIN_BASE', dirname(__FILE__));

require_once NWNADMIN_BASE . '/lib/base.php';

// script global variables
global $nwndriver;
$admin = Auth::isAdmin('nwnadmin:admin');
$adminDelete = Auth::isAdmin('nwnadmin:admin', PERMS_DELETE);
$serverUp = $nwndriver->serverRunning();
if (!$serverUp) {
    $notification->push(
            _("The server is down; player info is unavailable. "));
}

// figure out what to do
$actionId = Util::getFormData('actionId');
$playerId = Util::getFormData('playerId');
$valid = false;
$unban = false;
switch($actionId) {
case 'unbanip':
case 'unbanname':
case 'unbankey':
    $unban = true;
case 'banip':
case 'banname':
case 'bankey':
case 'kick':
    $valid = true;
}
if ($valid && $admin && $serverUp) {
    $result = $nwndriver->sendCommand($actionId . ' ' . $playerId);
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem with the command: ") .
                $result->getMessage());
    } else {
        $notification->push(_("Success!"), 'horde.success');
    }
}

// get the players and bans
$players = array();
$bans = array();
if ($serverUp)  {
    $result = $nwndriver->sendCommand('status', true);
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem getting the player info: ")
                . $result->getMessage(), 'horde.error');
    } else {
        sleep(1);
        $playerResult = $nwndriver->getLogContent();
        $resultArray = explode("\n", $playerResult);
        foreach ($resultArray as $pr) {
            $prA = explode('|', $pr);
            if (count($prA) == 5 && trim($prA[0]) !== 'ID') {
                $players[] = array('id' => trim($prA[0]),
                        'name' => trim($prA[1]), 'ip' => trim($prA[2]),
                        'character' => trim($prA[3]), 'key' => trim($prA[4]));
            }
        }
        if (empty($players)) {
            $notification->push(_("There are no players connected."));
        }
    }

    $result = $nwndriver->sendCommand('listbans', true);
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("There was a problem getting the ban info: ")
                . $result->getMessage(), 'horde.error');
    } else {
        sleep(1);
        $banResult = $nwndriver->getLogContent();
        $resultArray = explode("\n", $banResult);
        $banType = '';
        foreach ($resultArray as $theBan) {
            $theBan = trim($theBan);
            if (strpos($theBan, 'Key')) {
                $banType = 'unbankey';
                continue;
            } elseif (strpos($theBan, 'Addresses')) {
                $banType = 'unbanip';
                continue;
            } elseif (strpos($theBan, 'Name')) {
                $banType = 'unbanname';
                continue;
            }
            if (!empty($banType) && !empty($theBan)) {
                $bans[$banType][] = $theBan;
            }
        }
        if (empty($bans)) {
            $notification->push(_("There are no bans in place."));
        }
    }
}

// page setup
$title = _("Player Information");
require_once NWNADMIN_TEMPLATES . '/common-header.inc';
require_once NWNADMIN_TEMPLATES . '/menu.inc';

// render the player info
$baseUrl = Horde::applicationUrl('player.php', true);
if (!empty($players)) {
    require NWNADMIN_TEMPLATES . '/player/pheader.inc';
    $style = 'item1';
    foreach ($players as $player) {
        if ($style == 'item1') {
            $style = 'item0';
        } else {
            $style = 'item1';
        }
        require NWNADMIN_TEMPLATES . '/player/player.inc';
    }
    require NWNADMIN_TEMPLATES . '/player/footer.inc';
}

// render ban info
if (!empty($bans)) {
    require NWNADMIN_TEMPLATES . '/player/bheader.inc';
    foreach ($bans as $unbantype => $b) {
        $style = 'item1';
        foreach ($b as $theBan) {
            if ($style == 'item1') {
                $style = 'item0';
            } else {
                $style = 'item1';
            }
            require NWNADMIN_TEMPLATES . '/player/ban.inc';
        }
    }
    require NWNADMIN_TEMPLATES . '/player/footer.inc';
}

// finish up the page
require_once $registry->get('templates', 'horde') . '/common-footer.inc';