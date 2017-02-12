{% include localPath(0) ~ "conf.navi.tpl" %}

<table width="100%">
	<tr align="left">
		<td class="contentHead"><b>Код</b></td>
		<td class="contentHead"><b>ID формы</b></td>
		<td class="contentHead"><b>Название формы</b></td>
		<td class="contentHead"><b>Привязка к новостям</b></td>
		<td class="contentHead"><b>Активна</b></td>
		<td class="contentHead">&nbsp;</td>
	</tr>
	{% for entry in entries %}
		<tr align="left" class="contRow1">
			<td width="30"><a href="{{ entry.linkEdit }}">{{ entry.id }}</a></td>
			<td style="padding:2px;"><a href="{{ entry.linkEdit }}">{{ entry.name }}</a></td>
			<td>{{ entry.title }}</td>
			<td>{{ lang['feedback:link_news.' ~ entry.link_news] }}</td>
			<td>{{ entry.flags.active ? lang['yesa'] : lang['noa'] }}</td>
			<td nowrap>{% if (entry.flags.active) %}
				<a onclick="alert('{{ lang['feedback:active_nodel'] }}');">{% else %}
					<a href="{{ entry.linkDel }}" onclick="return confirm('{{ lang['feedback:suretest'] }}');">{% endif %}
						<img src="{{ skins_url }}/images/delete.gif" alt="DEL" width="12" height="12"/></a></td>
		</tr>
	{% endfor %}
	<tr>
		<td></td>
		<td colspan="5" style="text-align: left; padding: 10px 10px 0 0;">
			<a href="?mod=extra-config&plugin=feedback&action=addform">Создать новую форму</a>
		</td>
	</tr>
</table>
</form>