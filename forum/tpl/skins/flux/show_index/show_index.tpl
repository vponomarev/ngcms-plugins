<div id='idx{{ cat_id }}' class='blocktable'>
	<h2><span>{{ cat_name }}<br/>{{ cat_desc }}</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
				<thead>
				<tr>
					<th class="tcl" scope="col">Форум</th>
					<th class="tc2" scope="col">Темы</th>
					<th class="tc3" scope="col">Сообщений</th>
					<th class="tcr" scope="col">Последнее сообщение</th>
				</tr>
				</thead>
				<tbody>
				{% if (entries) %}
					{{ entries }}
				{% else %}
					<tr>
						<td class="tcl">
							<div class="intd">
								Нет форума
							</div>
						</td>
						<td class='tc2'>0</td>
						<td class='tc3'>0</td>
						<td class='tcr'>Нет сообщений</td>
					</tr>
				{% endif %}
				</tbody>
			</table>
		</div>
	</div>
</div>