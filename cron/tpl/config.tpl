<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr>
<td colspan="5" class="contentHead" width="100%"><img src="{{ skins_url }}/images/nav.gif" hspace="8"><a href="?mod=extras" title="”правление плагинами">”правление плагинами</a> &#8594; <a href="?mod=extra-config&plugin=cron">cron</a></td>
</tr>
</tbody>
</table>

<form action="?mod=extra-config&plugin=cron" method="post" name="commitForm" id="commitForm">
<input type="hidden" name="mod" value="extra-config">
<input type="hidden" name="action" value="commit">
<input type="hidden" name="token" value="{{ token }}">

<table width="100%">
<tr><td style="background-color: #EEEEEE; padding-bottom: 5px;" colspan="2">{{ lang.cron['title'] }}</td></tr>
<tr><td valign="top" width="60%"><br/><br/>{{ lang.cron['legend'] }}</td>
<td valign="top">
<table width="550">
<tr align="left">
	<td class="contentHead"><b>Plugin</b></td>
	<td class="contentHead"><b>Handler</b></td>
	<td class="contentHead"><b>Min</b></td>
	<td class="contentHead"><b>Hour</b></td>
	<td class="contentHead"><b>Day</b></td>
	<td class="contentHead"><b>Month</b></td>
	<td class="contentHead"><b>D.O.W.</b></td>
	<td class="contentHead">&nbsp;</td>
</tr>

{% for entry in entries %}
<tr align="left">
	<td><input name="data[{{ entry.id }}][plugin]" style="width: 85px;" value="{{ entry.plugin }}"/></b></td>
	<td><input name="data[{{ entry.id }}][handler]" style="width: 85px;" value="{{ entry.handler }}"/></td>
	<td><input name="data[{{ entry.id }}][min]" size="2" value="{{ entry.min }}"/></td>
	<td><input name="data[{{ entry.id }}][hour]" size="2" value="{{ entry.hour }}"/></td>
	<td><input name="data[{{ entry.id }}][day]" size="2" value="{{ entry.day }}"/></td>
	<td><input name="data[{{ entry.id }}][month]" size="2" value="{{ entry.month }}"/></b></td>
	<td><input name="data[{{ entry.id }}][dow]" size="2" value="{{ entry.dow }}"/></b></td>
	<td><!-- <b>”дал.</b> --></td>
</tr>
{% endfor %}
</table>
</td></tr></table>

<table width="100%">
<tr>&nbsp;</tr>
<tr align="center">
<td class="contentEdit" valign="top" width="100%">
<input value="{{ lang['commit_change'] }}" class="button" type="submit" onclick='document.location="?mod=extra-config&plugin=xfields&action=add&section={{ sectionID }}";'>
</td>
</tr>
</table>

</form>