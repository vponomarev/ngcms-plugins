<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">clear_config</h1>
		</div><!-- /.col -->
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
				<li class="breadcrumb-item"><a href="admin.php?mod=extras">{l_extras}</a></li>
				<li class="breadcrumb-item active" aria-current="page">clear_config</li>
			</ol>
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<div class="col-sm-12 mt-2">
	<div class="card">
		<div class="card-body">
			<font color="red"><b>ЭТО ВАЖНО!!!</b><br />Обязательно сделайте резервную копию папки
				<b>.../engine/conf/</b></font>
		</div>
	</div>
</div>
<div class="col-sm-12 mt-2">
	<div class="card">
		<div class="card-body">
			<table class="table table-sm table-bordered table-hover ">
				<thead>
					<tr>
						<td>Код плагина</td>
						<td>Удалить конфигурацию</td>
					</tr>
				</thead>
				{entries}
			</table>
		</div>
	</div>
</div>
