<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">Category_access</h1>
		</div><!-- /.col -->
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
				<li class="breadcrumb-item"><a href="admin.php?mod=extras">{l_extras}</a></li>
				<li class="breadcrumb-item active" aria-current="page">Category_access - {action}</li>
			</ol>
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-6 text-center">
			<div class="btn-group" role="group" aria-label="Basic example">
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access'"
					value="{l_category_access:button_general}" class="btn btn-outline-primary" />
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=list_user'"
					value="{l_category_access:button_list_user}" class="btn btn-outline-primary" />
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=list_category'"
					value="{l_category_access:button_list_category}" class="btn btn-outline-primary" />
			</div>
		</div>
		<div class="col-sm-6 text-center">
			<div class="btn-group" role="group" aria-label="Basic example">
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=add_user'"
					value="{l_category_access:button_add_user}" class="btn btn-outline-primary" />
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=category_access&action=add_category'"
					value="{l_category_access:button_add_category}" class="btn btn-outline-primary" />
			</div>
		</div>
	</div>
		{entries}
	</div>
