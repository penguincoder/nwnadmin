<?php
/**
 * NWNAdmin base inclusion file.
 *
 * This file brings in all of the dependencies that every NWNAdmin
 * script will need and sets up objects that all scripts use.
 */

// Check for a prior definition of HORDE_BASE (perhaps by an
// auto_prepend_file definition for site customization).
if (!defined('HORDE_BASE')) {
    @define('HORDE_BASE', dirname(__FILE__) . '/../..');
}

// Load the Horde Framework core, and set up inclusion paths.
require_once HORDE_BASE . '/lib/core.php';

// Registry.
$registry = &Registry::singleton();
if (is_a(($pushed = $registry->pushApp('nwnadmin', !defined('AUTH_HANDLER'))),
    'PEAR_Error')) {
    if ($pushed->getCode() == 'permission_denied') {
        Horde::authenticationFailureRedirect();
    }
    Horde::fatal($pushed, __FILE__, __LINE__, false);
}
$conf = &$GLOBALS['conf'];
@define('NWNADMIN_TEMPLATES', $registry->get('templates'));

// Find the base file path of NWNAdmin.
@define('NWNADMIN_BASE', dirname(__FILE__) . '/..');

// Notification system.
$notification = &Notification::singleton();
$notification->attach('status');

// NWNAdmin base libraries.
require_once NWNADMIN_BASE . '/lib/NWNAdmin.php';
require_once NWNADMIN_BASE . '/lib/NWNDriver.php';

// Start compression.
Horde::compressOutput();

// global driver
$GLOBALS['nwndriver'] = &NWNDriver::singleton();