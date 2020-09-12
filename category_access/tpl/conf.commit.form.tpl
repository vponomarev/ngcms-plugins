<form method="post" id="commit_delete"
	action="admin.php?mod=extra-config&amp;plugin=category_access&amp;action=dell_category">
	<input type="hidden" name="category" value="{category}" />
	<input type="hidden" id="commit" name="commit" value="no" />
	<div class="col-sm-12 mt-2">
		<div class="card">
			<div class="card-header">{action}</div>
			<div class="card-body">
				<div align="center">
					<font color="red" size="+2">{commit}</font>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-12 text-center mt-2">
		<div class="card">
			<div class="card-body">
				<input type="submit" value="{l_category_access:button_cancel}" class="btn btn-outline-success" />&#160;
				<input type="submit"
					onclick="document.forms['commit_delete'].elements['commit'].value='yes'; return true;"
					value="{l_category_access:button_dell}" class="btn btn-outline-danger" />
			</div>
		</div>
	</div>
</form>
