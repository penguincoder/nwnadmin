 <table id="menu" width="100%" cellpadding="0" cellspacing="0" border="0">
 <tr><td align="center"><?php echo Horde::img('banner.png', _("Banner"), '', $GLOBALS['registry']->getImageDir('penguincoder')) ?></td></tr>
 <tr><td><?php echo NWNAdmin::getMenu('string') ?></td></tr></table>
<br class="spacer"/>
<?php $GLOBALS['notification']->notify(array('listeners' => 'status')) ?>
