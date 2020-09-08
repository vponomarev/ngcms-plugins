<form method="post" class="form-group"
	action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=add_user[edit]&amp;user={user}[/edit]">
	<div class="row mt-2">
		<div class="col-sm-12 mt-2">
			<div class="card">
				<div class="card-header">{action}</div>
				<div class="card-body">
					<table class="table table-hover">
						<td>{l_category_access:label_user_name}<br />
							<small>{l_category_access:desc_user}</small>
						</td>
						<td>{user_list}</td>
						</tr>
						<tr>
							<td>{l_category_access:label_category}<br />
								<small>{l_category_access:desc_category}</small>
							</td>
							<td>{category_list}</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="col-sm-12 text-center mt-2">
			<div class="card">
				<div class="card-body">
					<input type="submit"
						value="[add]{l_category_access:button_add_user}[/add][edit]{l_category_access:button_edit_user}[/edit]"
						class="btn btn-outline-success" />
				</div>
			</div>
		</div>
	</div>
</form>
