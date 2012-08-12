<br />

<div id="pm">
<form name="form" method="POST" action="{php_self}?action=delete">

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width=100% colspan="4" class="contentHead"><img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8" align=left>{l_pm:outbox}</td>
	</tr>
	
	<tr>
		<td width=100% colspan="4">&nbsp;</td>
	</tr>

	<tr align="center">
		<td width="50%" colspan="2" class="contentHead"><a href="/plugin/pm/">{l_pm:inbox}</a></td>
		<td width="50%" colspan="2" class="contentHead"><a href="/plugin/pm/?action=outbox"><b>{l_pm:outbox}</b></a></td>
	</tr>
	
	<tr>
		<td width=100% colspan="4">&nbsp;</td>
	</tr>
	
	<tr align="center">
		<td width="25%" class="contentHead">{l_pm:date}</td>
		<td width="40%" class="contentHead">{l_pm:subject}</td>
		<td width="30%" class="contentHead">{l_pm:too}</td>
		<td width="5%" class="contentHead"><input class="check" type="checkbox" name="master_box" title="{l_pm:checkall}" onclick="javascript:check_uncheck_all(form)"></td>
	</tr>
	
	{entries}

	<tr>
		<td width=100% colspan="4">&nbsp;</td>
	</tr>
	
	<tr align="center">
		<td width="100%" colspan="4" class="contentEdit">
		<input class="button" type="submit" value="{l_pm:delete}">
</form>
		<form name="pm" method="POST" action="{php_self}?action=write"><input class="button" type="submit" value="{l_pm:write}"></form>
		</td>
	</tr>
</table>
</div>