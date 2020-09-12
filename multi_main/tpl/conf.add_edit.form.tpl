<form method="post"
	action="admin.php?mod=extra-config&amp;plugin=multi_main&amp;action=add_form[edit]&amp;cat={cat}[/edit]">
	<div class="col-sm-12 mt-2">
		<div class="card">
			<div class="card-header">
				{action}
			</div>
			<div class="card-body">
				<table class="table table-sm">
					<tr>
						<td width="50%" class="contentEntry1">{l_multi_main:label_cat}<br />
							<small>{l_multi_main:desc_cat}</small>
						</td>
						<td width="50%" class="contentEntry2">{cat_list}</td>
					</tr>
					<tr>
						<td width="50%" class="contentEntry1">{l_multi_main:label_tpl}<br />
							<small>{l_multi_main:desc_tpl}</small>
						</td>
						<td width="50%" class="contentEntry2"><input type="text" size="80" name="tpl" value="{tpl}" />
						</td>
					</tr>
				</table>
			</div>
			<div class="card-footer text-center">
				<input type="submit"
					value="[add]{l_multi_main:button_add_submit}[/add][edit]{l_multi_main:button_edit_submit}[/edit]"
					class="btn btn-outline-success" /></div>
		</div>
	</div>
</form>
