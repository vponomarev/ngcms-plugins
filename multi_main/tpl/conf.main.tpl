<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">multi_main</h1>
		</div><!-- /.col -->
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
				<li class="breadcrumb-item"><a href="admin.php?mod=extras">{l_extras}</a></li>
				<li class="breadcrumb-item active" aria-current="page">multi_main - {action}</li>
			</ol>
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-12 text-center">
			<div class="btn-group" role="group" aria-label="Basic example">
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=multi_main'"
					value="{l_multi_main:button_general}" class="btn btn-outline-primary" />
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=multi_main&action=list_menu'"
					value="{l_multi_main:button_list}" class="btn btn-outline-primary" />
				<input type="button"
					onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=multi_main&action=add_form'"
					value="{l_multi_main:button_add_group}" class="btn btn-outline-primary" />
			</div>
		</div>
	</div>
	{entries}
</div>
