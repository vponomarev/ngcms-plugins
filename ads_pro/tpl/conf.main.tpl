<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark" style="padding: 20px 0 0 0;"><a href="?mod=extra-config&plugin=ads_pro">ads_pro</a> &#8594; {action}</h1>
		</div><!-- /.col -->
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
				<li class="breadcrumb-item"><a href="admin.php?mod=extras">{l_extras}</a></li>
				<li class="breadcrumb-item active" aria-current="page">ads_pro - {action}</li>
			</ol>
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.container-fluid -->
<div style="text-align : left;">
	<ul class="nav nav-tabs nav-fill mb-3 d-md-flex d-block" role="tablist">
		<li class="nav-item"><a	onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=ads_pro'"
				class="nav-link active" data-toggle="tab">{l_ads_pro:button_general}</a></li>
		<li class="nav-item"><a
				onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=ads_pro&action=list'"
				class="nav-link" data-toggle="tab">{l_ads_pro:button_list}</a></li>
		<li class="nav-item"><a
				onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=extra-config&plugin=ads_pro&action=add'"
				class="nav-link" data-toggle="tab">{l_ads_pro:button_add}</a></li>

	</ul>

	<div id="userTabs" class="tab-content">
		<!-- ########################## DB TAB ########################## -->
		<div id="userTabs-db" class="tab-pane show active">
			{entries}
		</div>
	</div>
</div>
