    <tr class="<?php echo $style ?>" onmouseover="className='selected';"
        onmouseout="className='<?php echo $style ?>';">
    <?php if($adminDelete): ?>
    <td align="center"><?php
        $url = Util::addParameter(Util::addParameter($baseUrl, 'actionId',
                'kick'), 'playerId', $player['id']);
        echo Horde::link($url, _("Kick Player"), '', '', '',
                _("Kick Player"));?><?php echo
        Horde::img('logout.png',  _("Kick Player"), '',
                $registry->getImageDir('horde')); ?></a></td>
    <?php endif; ?>
    <td align="center"><?php echo $player['character']?></td>
    <?php if ($admin): ?>
    <td align="center"><?php echo Horde::link(Util::addParameter(
            Util::addParameter($baseUrl, 'actionId', 'banname'), 'playerId',
            $player['name']), _("Ban Name"), '', '', '', _("Ban Name")) .
            $player['name']; ?></a></td>
    <td align="center"><?php echo Horde::link(Util::addParameter(
            Util::addParameter($baseUrl, 'actionId', 'banip'), 'playerId',
            $player['ip']), _("Ban IP"), '', '', '', _("Ban IP")) .
            $player['ip']; ?></a></td>
    <td align="center"><?php echo Horde::link(Util::addParameter(
            Util::addParameter($baseUrl, 'actionId', 'bankey'), 'playerId',
            $player['key']), _("Ban Key"), '', '', '', _("Ban Key")) .
            $player['key']; ?></a></td>
    <? else: ?>
    <td align="center"><?php echo $player['name']?></td>
    <? endif ?>
    </tr>