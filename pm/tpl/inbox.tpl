<br />

<div id="pm">
<form name="form" method="POST" action="{{php_self}}?action=delete">

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td width=100% colspan="6" class="contentHead"><img src="{{admin_url}}/plugins/pm/img/nav.gif" hspace="8" align="left" />{{ lang['pm:inbox'] }}</td>
	</tr>
			
	<tr>
		<td width=100% colspan="6">&nbsp;</td>
	</tr>
			
	<tr align="center">
		<td width="50%" colspan="2" class="contentHead"><a href="/plugin/pm/"><b>{{ lang['pm:inbox'] }}</b></a></td>
		<td width="50%" colspan="2" class="contentHead"><a href="/plugin/pm/?action=outbox">{{ lang['pm:outbox'] }}</a></td>
		<td width="50%" colspan="2" class="contentHead"><a href="{{php_self}}?action=set" align="right">{{ lang['pm:set'] }}</a></td>
	</tr>
			
	<tr>
		<td width=100% colspan="6">&nbsp;</td>
	</tr>
			
	<tr align="center">
		<td width="20%" class="contentHead">{{ lang['pm:date'] }}</td>
		<td width="35%" class="contentHead">{{ lang['pm:subject'] }}</td>
		<td width="25%" class="contentHead">{{ lang['pm:from'] }}</td>
		<td width="15%" class="contentHead">{{ lang['pm:state'] }}</td>
		<td width="5%" class="contentHead"><input class="check" type="checkbox" name="master_box" title="{{ lang['pm:checkall'] }}" onclick="javascript:check_uncheck_all(form)"></td>
	</tr>
			
	{% for entry in entries %}
		<tr align="center">
		<td class="contentEntry1">{{entry.pmdate|date('Y-m-d H:i')}}</td>
		<td class="contentEntry1"><a href="{{php_self}}?action=read&pmid={{entry.pmid}}&location=inbox">{{entry.subject}}</a></td>
		<td class="contentEntry1">{{entry.link}}</td>
		<td class="contentEntry1">{% if (entry.viewed == 1) %}<img src="/engine/plugins/pm/img/viewed.yes.gif" />{% else %}<img src="/engine/plugins/pm/img/viewed.no.gif" />{% endif %}</td>
		<td class="contentEntry1"><input name="selected_pm[]" value="{{entry.pmid}}" class="check" type="checkbox"/></td>
		</tr>
	{% endfor %}
			
	<tr>
		<td width=100% colspan="6"><div style="padding: 10px; text-align:center;">{{ pagination }}</div></td>
	</tr>
	
	<tr align="center">
		<td width="100%" colspan="6" class="contentEdit">
		<input class="button" type="submit" value="{{ lang['pm:delete'] }}">
</form>
		<form name="pm" method="POST" action="{{php_self}}?action=write"><input class="button" type="submit" value="{{ lang['pm:write'] }}"></form>
		</td>
	</tr>	
</table>
</div>