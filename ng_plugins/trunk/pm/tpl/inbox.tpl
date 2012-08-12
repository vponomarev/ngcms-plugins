<br />

<div id="pm">
<form name="form" method="POST" action="{php_self}?action=delete">

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width=100% colspan="5" class="contentHead"><img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8" align=left>{l_pm:inbox}</td>
	</tr>
			
	<tr>
		<td width=100% colspan="5">&nbsp;</td>
	</tr>
			
	<tr align="center">
		<td width="50%" colspan="2" class="contentHead"><a href="/plugin/pm/"><b>{l_pm:inbox}</b></a></td>
		<td width="50%" colspan="3" class="contentHead"><a href="/plugin/pm/?action=outbox">{l_pm:outbox}</a></td>
	</tr>
			
	<tr>
		<td width=100% colspan="5">&nbsp;</td>
	</tr>
			
	<tr align="center">
		<td width="20%" class="contentHead">{l_pm:date}</td>
		<td width="35%" class="contentHead">{l_pm:subject}</td>
		<td width="25%" class="contentHead">{l_pm:from}</td>
		<td width="15%" class="contentHead">{l_pm:state}</td>
		<td width="5%" class="contentHead"><input class="check" type="checkbox" name="master_box" title="{l_pm:checkall}" onclick="javascript:check_uncheck_all(form)"></td>
	</tr>
			
	{entries}
			
	<tr>
		<td width=100% colspan="5">&nbsp;</td>
	</tr>
			
	<tr align="center">
		<td width="100%" colspan="5" class="contentEdit">
		<input class="button" type="submit" value="{l_pm:delete}">
</form>
		<form name="pm" method="POST" action="{php_self}?action=write"><input class="button" type="submit" value="{l_pm:write}"></form>
		</td>
	</tr>
</table>
</div>