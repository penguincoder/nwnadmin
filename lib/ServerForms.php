<?php

// Required Horde libraries
require_once 'Horde/Variables.php';
require_once 'Horde/Form.php';
require_once 'Horde/Form/Renderer.php';

class ServerSettings extends Horde_Form
{
    function ServerSettings(&$vars)
    {
        parent::Horde_Form($vars);

        $this->addHidden('', 'actionId', 'text', true);
        $vars->set('actionId', 'serversettings');
        $this->addVariable(_("Server Name"), 'servername', 'text', true);
        $this->addVariable(_("Server Port"), 'port', 'int', false);
        $this->addVariable(_("Player Password"), 'playerpass', 'text', false);
        $this->addVariable(_("DM Password"), 'dmpass', 'text', false);
        $this->addVariable(_("Maximum Clients"), 'maxclients', 'int', true);
        $this->addVariable(_("Minimum Player Level"), 'minlevel', 'int',
                true);
        $this->addVariable(_("Maximum Player Level"), 'maxlevel', 'int',
                true);
        $this->addVariable(_("Pause And Play"), 'pauseandplay', 'enum',
                true, false, null, array(array('0' => 'DM Only',
                '1' => 'DM and Players')));
        $this->addVariable(_("Player Vs Player"), 'pvp', 'enum', true,
                false, null, array(array('0' => 'None', '1' => 'Party Only',
                '2' => 'Free For All')));
        $this->addVariable(_("Server Vault"), 'servervault', 'enum',
                true, false, null, array(array('0' => 'Local Characters Only',
                '1' => 'Server Characters Only')));
        $this->addVariable(_("Enforce Legal Characters"), 'elc', 'enum',
                true, false, null, array(array(
                '0' => 'Enforce Legal Characters', '1' => 'Any Characters')));
        $this->addVariable(_("Item Level Restrictions"), 'ilr', 'enum',
                true, false, null, array(array(
                '0' => 'Enforce Item Restrictions', '1' => 'Any Items')));
        $this->addVariable(_("Game Type"), 'gametype', 'enum', true,
                false, null, array(array('0' => 'Action', '1' => 'Story',
                '2' => 'Story Lite', '3' => 'Role Play', '4' => 'Team',
                '5' => 'Melee', '6' => 'Arena', '7' => 'Aocial',
                '8' => 'Alternative', '9' => 'PW Action', '10' => 'PW Story',
                '11' => 'Solo', '12' => 'Tech Support')));
        $this->addVariable(_("Parties"), 'oneparty', 'enum', true, false,
                null, array(array('0' => 'One Party',
                '1' => 'Multiple Parties')));
        $this->addVariable(_("Difficulty"), 'difficulty', 'enum', true,
                false, null, array(array('1' => 'Easy', '2' => 'Normal',
                '3' => 'D&D Hardcore', '4' => 'Insane')));
        $this->addVariable(_("Auto Save Interval"), 'autosaveinterval',
                'int', true, false, _("In minutes, use 0 to disable"));
        $this->addVariable(_("Persistence"), 'reloadwhenempty', 'enum',
                true, false, null, array(array('0' => 'Persistent Module',
                '1' => 'Reload When Empty')));
        $this->addVariable(_("Server Visibility"), 'publicserver', 'enum',
                true, false, null, array(array('0' => 'Private',
                '1' => 'Public')));

        $this->setTitle(_("Configure Server Settings"));
        $this->setButtons(_("Update Config"));
    }
}