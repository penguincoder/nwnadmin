<?php
@define('NWNADMIN_BASE', dirname(__FILE__));

require_once NWNADMIN_BASE . '/lib/base.php';

/**
 * Delete a file, or a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.2
 * @param       string   $dirname    Directory to delete
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recurse
        rmdirr("$dirname/$entry");
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
}

// script global variables
global $nwndriver;
$admin = Auth::isAdmin('nwnadmin:admin');
$adminDelete = Auth::isAdmin('nwnadmin:admin', PERMS_DELETE);
$saveDir = NWNAdmin::getSaveGamePath();
$serverUp = $nwndriver->serverRunning();
if ($admin && !$serverUp) {
    $notification->push(_("The server is down; save game loading is ".
            "unavailable."));
}

// figure out what to do
$actionId = Util::getFormData('actionId');
$saveName = Util::getFormData('saveName');
if (isset($actionId) && !isset($saveName)) {
   $notification->push(_("Invalid options! Try again..."), 'horde.warning');
} else {
    switch($actionId) {
    case 'delete':
        $result = false;
        $length = strlen($conf['server']['root']);
        if ($adminDelete &&
            substr($saveName, 0, $length) == $conf['server']['root']) {
            $saveName = str_replace('../', '', $saveName);
            $result = rmdirr(escapeshellcmd($saveName));
        }
        if (!$result) {
            $notification->push(_("Could not delete the save game."),
                    'horde.error');
        } else {
            $notification->push(_("Successfully deleted the saved game."),
                    'horde.success');
        }
        break;
    case 'load':
        $result = false;
        if ($admin) {
            $result = NWNAdmin::sendCommand('load ' .$saveName);
        }
        if (is_a($result, 'PEAR_Error')) {
            $notification->push(
                    _("There was a problem loading the game: ") .
                    $result->getMessage(), 'horde.error');
        } else {
            $notification->push(_("Save game loaded."),
                    'horde.sucess');
        }
        break;
    }
}

// get the listing of modules
$saveList = NWNAdmin::getSaveGameList($saveDir);
$saveDone = empty($saveList);
if ($saveDone) {
    $notification->push(_("No save games were found"), 'horde.warning');
}

// page setup
$title = _("Saved Games");
require_once NWNADMIN_TEMPLATES . '/common-header.inc';
require_once NWNADMIN_TEMPLATES . '/menu.inc';

// render the available modules
if (!$saveDone) {
    require NWNADMIN_TEMPLATES . '/savegame/header.inc';
    $style = 'item1';
    foreach ($saveList as $savegame) {
        $baseSave = basename($savegame);
        $args = split("/\s+/", $baseSave);
        $saveNumber = sprintf("%d", $args[0]);
        if ($style == 'item1') {
            $style = 'item0';
        } else {
            $style = 'item1';
        }
        include NWNADMIN_TEMPLATES . '/savegame/savegame.inc';
    }
    require NWNADMIN_TEMPLATES . '/savegame/footer.inc';
}

// finish up the page
require_once $registry->get('templates', 'horde') . '/common-footer.inc';
