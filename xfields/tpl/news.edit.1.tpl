<tr>
	<th colspan="2">
		{{ lang['xfields_group_title'] }}
		<span id="xf_profile"></span>
	</th>
</tr>

{% for entry in entries %}
<tr id="xfl_{{ entry.id }}">
	<td valign="top">{{ entry.title }}{% if entry.flags.required %} <b>(*)</b>{% endif %}:</td>
	<td valign="top">{{ entry.input }}</td>
</tr>
{% endfor %}
