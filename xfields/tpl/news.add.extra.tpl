{% if (flags.tdata) %}
<tr><td width="100%" class="contentHead" colspan="2"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt="" />Табличные данные</td></tr>
<tr><td>
<table width="100%" id="tdataTable">
<thead>
<tr>
<td>#</td>
{% for entry in xtableHdr %}
<td>{{ entry.title }}</td>
{% endfor %}
<td>Действие</td>
</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr><td colspan="{{ (xtablecnt+2) }}"><input type="button" value="добавить строки.." onclick="tblLoadData(0);"/></td></tr>
</tfoot>
</table>
</td></tr>
{% endif %}
<tr><td width="100%" class="contentHead" colspan="2"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt="" />{{ lang['xfields_group_title'] }} <span id="xf_profile"></span></td></tr>
<tr><td>
<table width="100%">
{% for entry in entries %}
	<tr id="xfl_{{entry.id}}">
		<td valign="top" width="200">{{entry.title}}{% if entry.flags.required %} <b>(*)</b>{% endif %}:</td>
		<td valign="top">{{entry.input}}</td>
	</tr>
{% endfor %}
</table>
</td></tr>