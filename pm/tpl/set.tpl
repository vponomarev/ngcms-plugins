<br />

<div id="pm">
<form method="POST" action="{{php_self}}?action=set">
<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width=100% colspan="2" class="contentHead"><img src="{{admin_url}}/plugins/pm/img/nav.gif" hspace="8" align="left" />{{ lang['pm:set'] }}</td>
	</tr>
			
	<tr>
		<td width=100% colspan="2">&nbsp;</td>
	</tr>
	
	<tr align="center">
		<td width="50%" class="contentHead"><a href="/plugin/pm/">{{ lang['pm:inbox'] }}</a></td>
		<td width="50%" class="contentHead"><a href="/plugin/pm/?action=outbox">{{ lang['pm:outbox'] }}</a></td>
	</tr>
	
	<tr>
		<td width=100% colspan="2">&nbsp;</td>
	</tr>
	
	<tr>
		<td width=100% colspan="2"><input class="check" type="checkbox" name="email" id="email" {{checked}} /> {{ lang['pm:email_set'] }}</td>
	</tr>
	<input type="hidden" name="check">
	<tr>
		<td width=100% colspan="2">&nbsp;</td>
	</tr>
	
	<tr align="center">
		<td width="100%" colspan="2" class="contentEdit">
		<input type="submit" class="button">
		</td>
	</tr>	
</form>
</table>
</div>