<form name="stopserver" action="<?php echo Horde::selfUrl() ?>"
method="POST"><table width="100%" cellpadding="0" cellspacing="0">
<tr class="header"><td class="header">Stop Server</td></tr>
<tr class="control"><td class="control"><input type="hidden" name="actionId"
value="stop" /><input type="submit" name="submitbutton" value="Stop"
class="button" /></td></tr>
</table></form>

<br />

<form name="killserver" action="<?php echo Horde::selfUrl() ?>"
method="POST"><table width="100%" cellpadding="0" cellspacing="0">
<tr class="header"><td class="header">Kill Server</td></tr>
<tr class="control"><td class="control"><input type="hidden" name="actionId"
value="kill" /><input type="submit" name="submitbutton" value="Kill"
class="button" /></td></tr>
</table></form>

<br />

<form name="rawcommand" action="<?php echo Horde::selfUrl() ?>"
method="POST"><table width="100%" cellpadding="0" cellspacing="0">
<tr class="header"><td class="header" colspan="2">Raw Command</td></tr>
<tr class="item0"><td class="item0" align="right">String</td><td
class="item0"><input type="text" name="command" size="30" class="input"
/></td></tr><tr class="control"><td class="control" colspan="2"><input
type="hidden" name="actionId" value="raw" /><input type="submit"
name="submitbutton" value="Send" class="button" /></td></tr>
</table></form>
