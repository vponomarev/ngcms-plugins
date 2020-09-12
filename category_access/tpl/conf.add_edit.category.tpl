<form method="post" action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=add_category">
	<div class="col-sm-12 mt-2">
		<div class="card">
			<div class="card-header">{action}</div>
			<div class="card-body">
				<table class="table table-sm table-bordered table-hover ">
					<thead>
						<tr>
							<td>{l_category_access:label_category}</td>
							<td>{l_category_access:label_add}</td>
						</tr>
						{entries}
				</table>
			</div>
		</div>
	</div>
	<div class="col-sm-12 text-center mt-2">
		<div class="card">
			<div class="card-body">
				<input type="submit" value="{l_category_access:button_add_category}" class="btn btn-outline-success" />
			</div>
		</div>
	</div>
</form>
