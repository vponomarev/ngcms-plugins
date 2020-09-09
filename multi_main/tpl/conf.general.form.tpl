<form method="post" action="admin.php?mod=extra-config&amp;plugin=multi_main&amp;action=general_submit">
	<div class="col-sm-12 mt-2">
		<div class="card">
			<div class="card-header">{l_multi_main:legend_general}</div>
			<div class="card-body">
				<table class="table table-sm">
					<tr>
						<td>{l_multi_main:label_main}<br />
							<small>{l_multi_main:desc_main}</small>
						</td>
						<td><input type="text" size="80" name="main" value="{main}" />
						</td>
					</tr>
					<tr>
						<td>{l_multi_main:label_guest}<br />
							<small>{l_multi_main:desc_guest}</small>
						</td>
						<td><input type="text" size="80" name="guest" value="{guest}" /></td>
					</tr>
					<tr>
						<td>{l_multi_main:label_coment}<br />
							<small>{l_multi_main:desc_coment}</small>
						</td>
						<td><input type="text" size="80" name="coment" value="{coment}" /></td>
					</tr>
					<tr>
						<td>{l_multi_main:label_journ}<br />
							<small>{l_multi_main:desc_journ}</small>
						</td>
						<td><input type="text" size="80" name="journ" value="{journ}" /></td>
					</tr>
					<tr>
						<td>{l_multi_main:label_moder}<br />
							<small>{l_multi_main:desc_moder}</small>
						</td>
						<td><input type="text" size="80" name="moder" value="{moder}" /></td>
					</tr>
					<tr>
						<td>{l_multi_main:label_admin}<br />
							<small>{l_multi_main:desc_admin}</small>
						</td>
						<td><input type="text" size="80" name="admin" value="{admin}" /></td>
					</tr>
				</table>
			</div>
			<div class="card-footer text-center"><input type="submit" value="{l_multi_main:button_save}"
					class="btn btn-outline-success" /></div>
		</div>
	</div>
</form>
