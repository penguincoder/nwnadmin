    <tr class="<?php echo $style ?>" onmouseover="className='selected';"
        onmouseout="className='<?php echo $style ?>';">
    <?php if($adminDelete): ?>
    <td align="center"><?php
        $url = Horde::applicationUrl('savegame.php', true);
        $url = Util::addParameter(Util::addParameter($url, 'actionId',
                'delete'), 'saveName', $savegame);
        echo Horde::link($url, _("Delete Save Game"), '', '', '',
                _("Delete Saved Game"));?><?php echo
        Horde::img('delete.png',  _("Delete Saved Game"), '',
                $registry->getImageDir('horde')); ?></a></td>
    <?php endif; ?>
    <td><?php
        if ($admin && $serverUp)
        {
            $url = Horde::applicationUrl('savegame.php', true);
            $url = Util::addParameter(Util::addParameter($url,
                    'actionId', 'load'), 'saveName', $saveNumber);
            echo Horde::link($url, _("Load Save Game"), '', '', '',
                    _("Load Save Game")) . $baseSave . '</a>';
        } else {
            echo $baseSave;
        }
    ?></td>
    </tr>