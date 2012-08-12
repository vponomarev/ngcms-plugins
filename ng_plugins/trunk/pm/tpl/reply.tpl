<br />

<div id="pm">
<form method=post name=form action="{php_self}?action=send">
<input type="hidden" name="title" value="{title}">
<input type="hidden" name="sendto" value="{sendto}">

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr align="center">
		<td width="50%" colspan="0" class="contentHead"><a href="/plugin/pm/">{l_pm:inbox}</a></td>
		<td width="50%" colspan="0" class="contentHead"><a href="/plugin/pm/?action=outbox">{l_pm:outbox}</a></td>
	</tr>
</table>

<br />

<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
	<tr>
		<td width="50%" style="padding-right:10px;" valign="top">
		<table border="0" width="100%" cellspacing="0" cellpadding="0" align="left">
		<tr>
			<td width=100% class="contentHead"><img src="{admin_url}/plugins/pm/img/nav.gif" hspace="8">{l_pm:textmessage}</td>
		</tr>
		
		<tr>
		<td width=100% class="contentEntry1"><br />{quicktags}<br /><br />{smilies}<br /><textarea name="content" id="content" rows="10" cols="60" tabindex="1" maxlength="3000" /></textarea>
		<br /><br /><input name="saveoutbox" class="check" type="checkbox"/> {l_pm:saveoutbox}<br /><br /></td>
		</tr>
		</table>
		</td>
	</tr>

	<tr align="center">
		<td width="100%" colspan="2" class="contentEdit" valign="top">
		<input class="button" type="submit" value="{l_pm:send}" accesskey="s" />
		</td>
	</tr>
</table>
</form>
</div>