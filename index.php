<?php
/**
 * See the enclosed file COPYING for license information (GPL).  If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 */

define('NWNADMIN_BASE', dirname(__FILE__));
$nwnadmin_configured = (@is_readable(NWNADMIN_BASE . '/config/conf.php'));

if (!$nwnadmin_configured) {
    require NWNADMIN_BASE . '/../lib/Test.php';
    Horde_Test::configFilesMissing('NWNAdmin', NWNADMIN_BASE,
        array('conf.php'));
} else {
    header('Location: ./start.php');
}