    <tr class="<?php echo $style ?>" onmouseover="className='selected';"
        onmouseout="className='<?php echo $style ?>';">
    <?php if($adminDelete): ?>
    <td align="center"><?php
        $url = Util::addParameter(Util::addParameter($baseUrl, 'actionId',
                $unbantype), 'playerId', $theBan);
        echo Horde::link($url, _("Remove Ban"), '', '', '',
                _("Remove Ban"));?><?php echo
        Horde::img('delete.png',  _("Remove Ban"), '',
                $registry->getImageDir('horde')); ?></a></td>
    <?php endif; ?>
    <td align="left"><?php echo $theBan; ?></td>
    </tr>