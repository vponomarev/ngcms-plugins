<br />

<div id="pm">
<form method="POST" action="{php_self}?action=delete&pmid={pmid}&location={location}">
<input type="hidden" name="title" value="{subject}">
<input type="hidden" name="from" value="{from}">

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr align="center">
		<td width="50%" colspan="0" class="contentHead"><a href="/plugin/pm/">{l_pm:inbox}</a></td>
		<td width="50%" colspan="0" class="contentHead"><a href="/plugin/pm/?action=outbox">{l_pm:outbox}</a></td>
	</tr>
</table>

<br />

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	
	<tr>
		<td width="100%" class="contentHead"><img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8">{subject}</td>
	</tr>

	<tr>
		<td width="100%"><blockquote>{content}</blockquote></td>
	</tr>
	
	<tr>
		<td>&nbsp;</td>
	</tr>
	
	<tr align="center">
		<td width="100%" class="contentEdit">
		<input class="button" type="submit" value="{l_pm:delete_one}"></form>
		[if-inbox]<form name="pm" method="POST" action="{php_self}?action=reply&pmid={pmid}"><input class="button" type="submit" value="{l_pm:reply}"></form>[/if-inbox]</td>
	</tr>		
</table>
</div>