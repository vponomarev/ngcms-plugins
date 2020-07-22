<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ admin_url }}"><i class="fa fa-home"></i></a></li>
		<li class="breadcrumb-item"><a href="?mod=extras">{{ lang['extras'] }}</a></li>
		<li class="breadcrumb-item {{ not(flags.haveForm) ? 'active' : '' }}"><a href="?mod=extra-config&plugin=feedback">feedback</a></li>
		{% if (flags.haveForm) %}
			<li class="breadcrumb-item {{ not(flags.haveField) and not(flags.addField) ? 'active' : '' }}">
				{{ lang['feedback:form'] }} [<a href="?mod=extra-config&plugin=feedback&action=form&id={{ formID }}">{{ formName }}</a>]
			</li>
			{% if (flags.haveField) %}
				<li class="breadcrumb-item active">
					{{ lang['feedback:field'] }} [<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}&row={{ fieldName }}">{{ fieldName }}</a>]
				</li>
			{% endif %}
			{% if (flags.addField) %}
				<li class="breadcrumb-item active">{{ lang['feedback:adding_new_field'] }}</li>
			{% endif %}
		{% else %}
			<li class="breadcrumb-item active">{{ lang['feedback:forms_list'] }}</li>
		{% endif %}
	</ol>
</nav>
