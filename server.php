<?php
@define('NWNADMIN_BASE', dirname(__FILE__));

require_once NWNADMIN_BASE . '/lib/base.php';
require_once NWNADMIN_BASE . '/lib/ServerForms.php';

global $nwndriver;

$vars = &Variables::getDefaultVariables();
$actionId = Util::getFormData('actionId');
if (is_null($actionId)) {
    $settings = &$nwndriver->getParams();
    if (is_null($settings)) {
        $notification->push(_("There was a problem fetching the settings!"),
                'horde.error');
    } elseif (!is_a($settings, 'PEAR_Error')) {
        foreach ($settings as $key => $val) {
            $vars->set($key, $val);
        }
    } else {
        $notification->push(_("There was a problem fetching the settings: ") .
                $settings->getMessage(), 'horde.error');
    }
}
$renderer = &new Horde_Form_Renderer();

$form = &Horde_Form::singleton('ServerSettings', $vars);
$valid = $form->validate($vars);
if ($valid) {
    $newSettings = array(
        'servername' => Util::getFormData('servername'),
        'port' => Util::getFormData('port'),
        'playerpass' => Util::getFormData('playerpass'),
        'dmpass' => Util::getFormData('dmpass'),
        'maxclients' => Util::getFormData('maxclients'),
        'minlevel' => Util::getFormData('minlevel'),
        'maxlevel' => Util::getFormData('maxlevel'),
        'pauseandplay' => Util::getFormData('pauseandplay'),
        'pvp' => Util::getFormData('pvp'),
        'servervault' => Util::getFormData('servervault'),
        'elc' => Util::getFormData('elc'),
        'ilr' => Util::getFormData('ilr'),
        'gametype' => Util::getFormData('gametype'),
        'onetype' => Util::getFormData('oneparty'),
        'difficulty' => Util::getFormData('difficulty'),
        'autosaveinterval' => Util::getFormData('autosaveinterval'),
        'reloadwhenempty' => Util::getFormData('reloadwhenempty'),
        'publicserver' => Util::getFormData('publicserver'),
    );
    $result = $nwndriver->setData($newSettings);
    if (is_a($result, 'PEAR_Error')) {
        $notification->push(_("Error while saving parameters: ") .
                $result->getMessage(), 'horde.error');
    } else {
        $notification->push(_("Successfully saved the settings."),
                'horde.success');
    }
    if ($nwndriver->serverRunning()) {
        $result = null;
        foreach ($newSettings as $key => $val) {
            $result = $nwndriver->sendCommand($key . ' ' . $val);
            if (is_a($result, 'PEAR_Error')) {
                $notification->push(
                        _("There was a problem loading the settings: ") .
                        $result->getMessage(), 'horde.error');
                break;
            }
        }
        if (!is_a($result, 'PEAR_Error')) {
            $notification->push(_("Server settings loaded successfully. " .
                    "Some settings require a server restart to take effect!"),
                    'horde.success');
        }
    }
}

$title = _("Configure Server");

require_once NWNADMIN_TEMPLATES . '/common-header.inc';
require_once NWNADMIN_TEMPLATES . '/menu.inc';

if (isset($form)) {
    if (Auth::isAdmin('nwnadmin:admin')) {
        $form->renderActive($renderer, $vars, 'server.php', 'post');
    } else {
        $form->renderInactive($renderer, $vars, 'server.php', 'post');
    }
}

require_once $registry->get('templates', 'horde') . '/common-footer.inc';
