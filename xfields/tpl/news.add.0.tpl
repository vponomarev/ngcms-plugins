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

{% if (flags.tdata) %}
<tr>
	<th colspan="2">Табличные данные</th>
</tr>
<tr>
	<td colspan="2">
		<table id="tdataTable" class="table table-sm table-bordered mb-0">
			<thead>
				<tr>
					<th>#</th>
					{% for entry in xtableHdr %}
					<th>{{ entry.title }}</th>
					{% endfor %}
					<th>Действие</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
			<tfoot>
				<tr>
					<td colspan="{{ (xtablecnt+2) }}" class="text-right">
						<button type="button" class="btn btn-sm btn-outline-success" onclick="tblLoadData(0);">Добавить строки...</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</td>
</tr>
{% endif %}
