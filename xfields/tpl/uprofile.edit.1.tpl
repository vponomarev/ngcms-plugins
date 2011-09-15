<tr style="padding: 3px;"><td width="100%" class="contentEdit" colspan="2" align="center"><b>{{ lang['xfields_group_title'] }}</b></td></tr>
{% for entry in entries %}
<tr id="xfl_{{entry.id}}">
	<td valign="top" class="entry">{{entry.title}}{% if entry.flags.required %} <b>(*)</b>{% endif %}:</td>
	<td valign="top" class="entry">{{entry.input}}</td>
</tr>
{% endfor %}
