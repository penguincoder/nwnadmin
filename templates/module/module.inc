    <tr class="<?php echo $style ?>" onmouseover="className='selected-over';"
        onmouseout="className='<?php echo $style ?>';">
    <?php if($adminDelete): ?>
    <td align="center"><?php
        $url = Horde::applicationUrl('module.php', true);
        $url = Util::addParameter(Util::addParameter($url, 'actionId',
                'delete'), 'moduleName', $module);
        echo Horde::link($url, _("Delete Module"), '', '', '',
                _("Delete Module"));?><?php echo
        Horde::img('delete.png',  _("Delete Module"), '',
                $registry->getImageDir('horde')); ?></a></td>
    <?php endif; ?>
    <td><?php
        if ($admin && $serverUp && !$currentFlag)
        {
            $url = Horde::applicationUrl('module.php', true);
            $url = Util::addParameter(Util::addParameter($url,
                    'actionId', 'activate'), 'moduleName', $baseModule);
            echo Horde::link($url, _("Activate Module"), '', '', '',
                    _("Activate Module")) . $baseModule . '</a>';
        } else {
            echo $baseModule;
        }
    ?></td>
    </tr>
