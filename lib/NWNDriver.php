<?php

require_once 'Horde/DataTree.php';

class NWNSettings extends DataTreeObject
{

    var $_datatree;

    function NWNSettings($name, $properties = null)
    {
        parent::DataTreeObject($name);

        // dto settings
        $this->data['type'] = 'nwnsettings';
        $this->data['name'] = isset($properties['name']) ? $properties['name'] :
            '';

        // datatree initialization for persistent storage
        global $conf;
        $driver = $conf['datatree']['driver'];
        $params = Horde::getDriverConfig('datatree', $driver);
        $params = array_merge($params, array('group' => 'nwnadmin.settings'));
        $this->_datatree = &DataTree::singleton($driver, $params);

        // create the neccessary datatree node if it does not exist
        if (!$this->_datatree->exists($this)) {
            $this->_datatree->add($this);
        }
    }

    /**
     * Set the attributes of the object.
     *
     * @param array    The attributes to set.
     * @param boolean  Determines whether the backend should
     *                 be updated or not
     */
    function setData($settings)
    {
        if (!is_array($settings)) {
            return PEAR::raiseError("Settings are not an array!");
        }
        parent::setData($settings);
        return $this->_datatree->updateData($this);
    }

    function &getSettings()
    {
        $rawsettings =
                $this->_datatree->getAttributes($this->_datatree->getId($this));
        $result = array();
        foreach ($rawsettings as $setting) {
            $result[$setting['name']] = $setting['value'];
        }
        return $result;
    }

    /**
     * Map this object's attributes from the data array into a format
     * that we can store in the attributes storage backend.
     *
     * @return array  The attributes array.
     */
    function _toAttributes()
    {
        foreach ($this->data as $key => $value) {
            $attributes[] = array('name' => $key,
                                 'key' => '',
                                 'value' => $value);
        }
        return $attributes;
    }

    /**
     * Take in a list of attributes from the backend and map it to our
     * internal data array.
     *
     * @param array $attributes  The list of attributes from the
     *                           backend (attribute name, key,
     *                           and value).
     */
    function _fromAttributes($attributes)
    {
        foreach ($attributes as $attribute) {
            $this->data[$attribute['name']] = $attribute['value'];
        }
    }

    function &factory($name, $properties = null)
    {
        return $ret = &new NWNSettings($name, $properties);
    }

    function &singleton($name, $properties = null)
    {
        static $instance;

        if (!isset($instance))
        {
            $instance = &NWNSettings::factory($name, $properties);
        }

        return $instance;
    }
}

class NWNDriver
{
    var $_settingsbackend;

    function NWNDriver()
    {
        $this->_settingsbackend = &NWNSettings::singleton('nwnadmin.settings',
            array('name' => 'nwnadmin.settings'));
    }

    function setData($settings)
    {
        return $this->_settingsbackend->setData($settings);
    }

    function &getParams()
    {
        return $this->_settingsbackend->getSettings();
    }

    function &factory()
    {
        return $ret = &new NWNDriver();
    }

    function &singleton()
    {
        static $instance;

        if (!isset($instance))
        {
            $instance = &NWNDriver::factory();
        }

        return $instance;
    }

    function serverRunning()
    {
        global $conf;
        static $command;
        if (is_null($command) || empty($command)) {
            $command = $conf['system']['ps'] . ' aux| ' .
                    $conf['system']['grep'] . ' ' .
                    $conf['server']['binary'] . ' | ' .
                    $conf['system']['grep'] . ' -v ' .
                    basename($conf['system']['grep']);
        }
        $result = shell_exec($command);
        if (!empty($result)) {
            $sets = preg_split('/\s+/', $result);
            $result = $sets[1];
        }
        return $result;
    }

    function startServer()
    {
        if ($this->serverRunning()) {
            return PEAR::raiseError("Server is up!");
        }
        global $conf;
        chdir(NWNAdmin::getServerRoot());
        $settings = &$this->_settingsbackend->getSettings();
        $settingString = '';
        foreach ($settings as $key => $val) {
            if (!empty($val)) {
                if (is_int($val)) {
                    $settingString .= sprintf(" -%s %s ", $key, $val);
                } else {
                    $settingString .= sprintf(" -%s '%s' ", $key,
                            escapeshellcmd($val));
                }
            }
        }
        //echo '<pre>' . NWNAdmin::getServerExecutable() .$settingString . ' > ' .
        //        $this->_getLog() . ' 2>&1 < ' . $this->_checkFifo() . ' &</pre>';
        shell_exec(NWNAdmin::getServerExecutable() .$settingString . ' > ' .
                $this->_getLog() . ' 2>&1 < ' . $this->_checkFifo() . ' &');

        return true;
    }

    function stopServer()
    {
        global $conf;
        shell_exec($conf['system']['echo'] . ' quit > ' . $this->_checkFifo());
        return true;
    }

    function killServer()
    {
        if (!($pid = $this->serverRunning())) {
            return PEAR::raiseError(_("Server is down!"));
        }
        return !posix_kill($pid, 9);
    }

    function sendCommand($command, $flush = false)
    {
        if (!$this->serverRunning()) {
            return PEAR::raiseError(_("Server is down!"));
        }
        global $conf;
        if ($flush) {
            shell_exec(implode(' ', array($conf['system']['echo'], '>',
                    $this->_getLog())));
        }
        shell_exec(implode(' ', array($conf['system']['echo'],
                escapeshellcmd($command), '>', $this->_checkFifo())));
        return true;
    }

    function getLogContent()
    {
        $fp = fopen($this->_getLog(), 'r');
        $data = fread($fp, filesize($this->_getLog()));
        fclose($fp);
        return $data;
    }

    function useModule($moduleName)
    {
        $settings = &$this->_settingsbackend->getSettings();
        $settings['module'] = $moduleName;
        if ($this->serverRunning()) {
            $this->sendCommand('module ' . $moduleName);
        }
        return $this->_settingsbackend->setData($settings);
    }

    function getModule()
    {
        $settings = &$this->_settingsbackend->getSettings();
        if (!isset($settings['module'])) {
            $settings['module'] = '';
        }
        return $settings['module'];
    }

    function _getLog()
    {
        static $logfile;
        if (is_null($logfile)) {
            $logfile = Horde::getTempDir() . '/nwnadmin.log';
        }
        return $logfile;
    }

    function _checkFifo()
    {
        static $fifoName;
        if (is_null($fifoName) || empty($fifoName)) {
            $fifoName = Horde::getTempDir() . '/nwnadmin-input';
        }
        if (!file_exists($fifoName) &&
            !posix_mkfifo($fifoName, 0640)) {
            $fifoName = '';
        }
        return $fifoName;
    }

}
