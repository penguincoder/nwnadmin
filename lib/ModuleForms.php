<?php

// Required Horde libraries
require_once 'Horde/Variables.php';
require_once 'Horde/Form.php';
require_once 'Horde/Form/Renderer.php';

class NewNWNModule extends Horde_Form
{
    function NewNWNModule(&$vars)
    {
        parent::Horde_Form($vars);

        $this->addVariable(_("New Module"), 'module', 'file', true);

        $this->setTitle(_("Upload New Module"));
        $this->setButtons(_("Upload"));

        $this->useToken(true);
    }
}