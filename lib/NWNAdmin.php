<?php

class NWNAdmin
{

    function getMenu($type = 'object')
    {
        global $conf, $registry;

        require_once 'Horde/Menu.php';

        $menu = &new Menu();

        if (Auth::isAdmin('nwadmin:admin')) {
            $menu->add(Horde::applicationUrl('start.php'), _("Run"),
                    'reload.png', $registry->getImageDir('horde'));

            $menu->add(Horde::applicationUrl('player.php'), _("Players"),
                    'user.png', $registry->getImageDir('horde'));
        }

        $menu->add(Horde::applicationUrl('server.php'), _("Server Settings"),
                'map_eu.png', $registry->getImageDir('horde'));

        $menu->add(Horde::applicationUrl('module.php'), _("Modules"),
                'download.png', $registry->getImageDir('horde'));

        $menu->add(Horde::applicationUrl('savegame.php'), _("Saved Games"),
                'data.png', $registry->getImageDir('horde'));

        if ($type == 'object') {
            return $menu;
        }
        return $menu->render();
    }

    function getServerRoot()
    {
        global $conf;
        return (substr($conf['server']['root'], -1) == '/' ?
            $conf['server']['root'] : $conf['server']['root'] . '/' );
    }

    function getModulePath()
    {
        global $conf;
        return (substr($conf['server']['root'], -1) == '/' ?
            $conf['server']['root'] : $conf['server']['root'] . '/' ) .
            'modules/';
    }

    function getServerExecutable()
    {
        global $conf;
        return (substr($conf['server']['root'], -1) == '/' ?
            $conf['server']['root'] : $conf['server']['root'] . '/' ) .
            $conf['server']['binary'];
    }

    function getModuleList($moduleDir = null)
    {
        if (is_null($moduleDir)) {
            return array();
        }
        return glob(escapeshellcmd($moduleDir ) . '{*.mod,MOD}', GLOB_BRACE);
    }

    function getSaveGamePath()
    {
        global $conf;
        return (substr($conf['server']['root'], -1) == '/' ?
            $conf['server']['root'] : $conf['server']['root'] . '/') . 'saves/';
    }

    function getSaveGameList($saveDir = null)
    {
        if (is_null($saveDir)) {
            return array();
        }
        return glob(escapeshellcmd($saveDir) . '*', GLOB_ONLYDIR);
    }
}
