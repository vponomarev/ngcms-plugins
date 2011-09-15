<tr><td width="100%" class="contentHead" colspan="3"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt="" />{{ lang['xfields_group_title'] }} <span id="xf_profile"></span></td></tr>
<tr><td colspan="3">
<table width="100%">
{% for entry in entries %}
	<tr id="xfl_{{entry.id}}">
		<td valign="top" width="200">{{entry.title}}{% if entry.flags.required %} <b>(*)</b>{% endif %}:</td>
		<td valign="top">{{entry.input}}</td>
	</tr>
{% endfor %}
</table>
</td></tr>