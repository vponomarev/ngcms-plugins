<div id='idx{{ cat_id }}' class='blocktable'>
	<h2><span>{{ cat_name }}<br />{{ cat_desc }}</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
				<thead>
					<tr>
						<th class="tcl" scope="col">�����</th>
						<th class="tc2" scope="col">����</th>
						<th class="tc3" scope="col">���������</th>
						<th class="tcr" scope="col">��������� ���������</th>
					</tr>
				</thead>
				<tbody>
				{% if (entries.true) %}
					{{ entries.print }}
					{% else %}
					<tr>
						<td class="tcl">
							<div class="intd">
								��� ������
							</div>
						</td>
						<td class='tc2'>0</td>
						<td class='tc3'>0</td>
						<td class='tcr'>��� ���������</td> 
					</tr>
				{% endif %}
				</tbody>
			</table>
		</div>
	</div>
</div>