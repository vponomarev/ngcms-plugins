<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ admin_url }}"><i class="fa fa-home"></i></a></li>
		<li class="breadcrumb-item"><a href="?mod=extras">{{ lang['extras'] }}</a></li>
		<li class="breadcrumb-item"><a href="?mod=extra-config&plugin=xfields">xfields</a></li>
		<li class="breadcrumb-item active" aria-current="page">
			{{ lang.xfconfig['editfield'] }} (<a href="?mod=extra-config&plugin=xfields&action=edit&section={{ sectionID }}&field={{ id }}">{{ id }}</a>)
		</li>
	</ol>
</nav>

<div class="alert alert-success">
	{{ lang.xfconfig['savedone'] }}
</div>
