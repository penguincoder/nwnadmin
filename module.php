<?php
@define('NWNADMIN_BASE', dirname(__FILE__));

require_once NWNADMIN_BASE . '/lib/base.php';
require_once NWNADMIN_BASE . '/lib/ModuleForms.php';

// script global variables
global $nwndriver;
$admin = Auth::isAdmin('nwnadmin:admin');
$adminDelete = Auth::isAdmin('nwnadmin:admin', PERMS_DELETE);
$moduleDir = NWNAdmin::getModulePath();
$moduleDirWritable = is_writable($moduleDir);
$serverUp = $nwndriver->serverRunning();
if ($admin && !$serverUp) {
    $notification->push(
            _("The server is down; module loading is unavailable."));
}

// figure out what to do
$actionId = Util::getFormData('actionId');
$moduleName = Util::getFormData('moduleName');
if (isset($actionId) && !isset($moduleName)) {
    $notification->push(_("Invalid options! Try again..."), 'horde.warning');
} else {
    switch($actionId) {
    case 'delete':
        if ($adminDelete &&
            strpos($conf['server']['root'], $moduleName) == 0) {
            $moduleName = str_replace('../', '', $moduleName);
            $result = unlink(escapeshellcmd($moduleName));
            if (!$result) {
                $notification->push(_("Could not delete the module."),
                        'horde.error');
            } else {
                $notification->push(_("Successfully deleted the module."),
                        'horde.success');
            }
        }
        break;
    case 'activate':
        if ($admin) {
            $result = $nwndriver->useModule($moduleName);
            if (is_a($result, 'PEAR_Error')) {
                $notification->push(
                        _("Module load failure: ") .
                        $result->getMessage(), 'horde.error');
            } else {
                $notification->push(_("Module loaded."), 'horde.success');
            }
        }
        break;
    }
}

// form configuration
$vars = &Variables::getDefaultVariables();
$renderer = &new Horde_Form_Renderer();
$form = &Horde_Form::singleton('NewNWNModule', $vars);
$valid = $form->validate($vars);
if ($valid) {
    // get the information about the file
    $form->getInfo($vars, $info);
    if (!empty($info['module']['file'])) {
        // as long as it was uploaded and there is information
        if (!is_a(Browser::wasFileUploaded('module'), 'PEAR_Error') &&
            filesize($info['module']['file'])) {
            // check the file for validity
            $extension = substr($info['module']['name'], -3);
            if ($extension == "mod" || $extension == "MOD") {
                $moduleOutputName = escapeshellcmd($moduleDir .
                    $info['module']['name']);
                // do not overwrite files
                if (file_exists($moduleOutputName)) {
                    $notification->push(_("Cannot overwrite existing module!"),
                            'horde.error');
                } else {
                    // get the file data
                    $fp = fopen($info['module']['file'], 'r');
                    $data = fread($fp, filesize($info['module']['file']));
                    fclose($fp);

                    // write the data to the output dir
                    $fp = @fopen($moduleOutputName, 'wb');
                    @fwrite($fp, $data);
                    @fclose($fp);

                    $notification->push(_("Successfully wrote the module: ") .
                            $info['module']['name'], 'horde.success');
                }

            } else {
                $notification->push(_("The uploaded file does not appear ".
                        "to be a valid NeverWinter module."), 'horde.error');
            }
        } else {
            // report the error
            if (!empty($info['module']['error'])) {
                $notification->push(sprintf(_("There was a problem " .
                        "uploading the module: %s"),
                        $info['module']['error']), 'horde.error');
            } elseif (!filesize($info['module']['file'])) {
                $notification->push(_("The uploaded file appears to " .
                        "be empty. It may not exist on your computer."),
                        'horde.error');
            } else {
                $notification->push(_("General failure, please debug!"),
                        'horde.error');
            }
        }
    }
}

// get the listing of modules
$moduleList = NWNAdmin::getModuleList($moduleDir);
$moduleDone = empty($moduleList);
if ($moduleDone) {
    $notification->push(_("No modules were found!"), 'horde.warning');
}
$currentModule = $nwndriver->getModule();

// page setup
$title = _("Modules");
require_once NWNADMIN_TEMPLATES . '/common-header.inc';
require_once NWNADMIN_TEMPLATES . '/menu.inc';

// render the available modules
if (!$moduleDone) {
    require NWNADMIN_TEMPLATES . '/module/header.inc';
    $style = 'item1';
    foreach ($moduleList as $module) {
        $currentFlag = false;
        $baseModule = substr(basename($module), 0, -4);
        if ($style == 'item1') {
            $style = 'item0';
        } else {
            $style = 'item1';
        }
        if ($currentModule == $baseModule && $serverUp) {
            $style = 'selectedRow'; $currentFlag = true;
        }
        include NWNADMIN_TEMPLATES . '/module/module.inc';
    }
    require NWNADMIN_TEMPLATES . '/module/footer.inc';
}

// render the upload form
if (isset($form) && $admin && $moduleDirWritable) {
    $form->renderActive($renderer, $vars, 'module.php', 'post');
}

// finish up the page
require_once $registry->get('templates', 'horde') . '/common-footer.inc';
