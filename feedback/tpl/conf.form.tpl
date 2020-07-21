{% include 'plugins/feedback/tpl/conf.navi.tpl' %}

<ul class="nav nav-pills mb-3 d-md-flex d-block">
	<li class="nav-item">
		<a id="config-tab" href="#config-pane" data-toggle="tab" class="nav-link active">{{ lang['feedback:tab_config'] }}</a>
	</li>
	<li class="nav-item">
		<a id="emails-tab" href="#emails-pane" data-toggle="tab" class="nav-link">{{ lang['feedback:tab_emails'] }}</a>
	</li>
	<li class="nav-item">
		<a id="fields-tab" href="#fields-pane" data-toggle="tab" class="nav-link">{{ lang['feedback:tab_fields'] }}</a>
	</li>
</ul>

<div class="tab-content">
	<div id="config-pane" class="tab-pane fade show active">

		<form id="feedback_config" method="post" action="">
			<input type="hidden" name="mod" value="extra-config" />
			<input type="hidden" name="plugin" value="feedback" />
			<input type="hidden" name="action" value="saveform" />

			<div class="card">
				<div class="card-header">
					<div class="custom-control custom-switch py-2 mr-auto">
						<input id="disabled_form" type="checkbox" name="active" value="1" class="custom-control-input" {{ flags.active ? 'checked' : '' }} />
						<label for="disabled_form" class="custom-control-label">{{ lang['feedback:form_is_active'] }}</label>
					</div>
				</div>

				<table class="table table-sm">
					<tbody>
						<tr>
							<td width="50%">{{ lang['feedback:form_id'] }}</td>
							<td width="50%">
								<input type="number" name="id" value="{{ id }}" class="form-control" readonly required />
							</td>
						</tr>
						<tr>
							<td width="50%">{{ lang['feedback:form_url'] }}</td>
							<td width="50%">
								<div class="input-group">
									<input type="text" value="{{ url }}" class="form-control" readonly />
									{% if flags.active %}
									<div class="input-group-append">
										<span class="input-group-text">
											<a href="{{ url }}" target="_blank"><i class="fa fa-external-link"></i></a>
										</span>
									</div>
									{% endif %}
								</div>
							</td>
						</tr>
						<tr>
							<td width="50%">{{ lang['feedback:form_name'] }}</td>
							<td width="50%">
								<input type="text" name="name" value="{{ name }}" class="form-control" required />
							</td>
						</tr>
						<tr>
							<td width="50%">{{ lang['feedback:form_title'] }}</td>
							<td width="50%">
								<input type="text" name="title" value="{{ title }}" class="form-control" required />
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:form_description'] }}
								<small class="form-text text-muted">{{ lang['feedback:form_description#descr'] }}</small>
							</td>
							<td width="50%">
								<textarea name="description" rows="3" class="form-control">{{ description }}</textarea>
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:check_input_fields'] }}
								<small class="form-text text-muted">{{ lang['feedback:check_input_fields#descr'] }}</small>
							</td>
							<td width="50%">
								<input type="checkbox" name="jcheck" value="1" {{ flags.jcheck ? 'checked' : '' }} />
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:use_captcha'] }}
								<small class="form-text text-muted">{{ lang['feedback:use_captcha#descr'] }}</small>
							</td>
							<td width="50%">
								<input type="checkbox" name="captcha" value="1" {{ flags.captcha ? 'checked' : '' }} />
							</td>
						</tr>
						<tr>
							<td width="50%">{{ lang['feedback:link_to_news'] }}</td>
							<td width="50%">
								<select name="link_news" class="custom-select">
									{% for x in link_news.options %}
										<option value="{{ x }}" {{ x == link_news.value ? 'selected' : '' }}>{{ lang['feedback:link_to_news_' ~ x] }}</option>
									{% endfor %}
								</select>
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:used_template'] }}
								<small class="form-text text-muted">{{ lang['feedback:used_template#descr'] }}</small>
							</td>
							<td width="50%">
								<select name="template" class="custom-select">
									{{ template_options }}
								</select>
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:template_on_site'] }} {% if (not tfiles.site.isFound) %}<span class="text-info">[{{ lang['feedback:by_default'] }}]</span>{% endif %}
							</td>
							<td width="50%">
								<input type="text" value="{{ tfiles.site.file }}" class="form-control" readonly />
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:template_in_mail'] }} {% if (not tfiles.mail.isFound) %}<span class="text-info">[{{ lang['feedback:by_default'] }}]</span>{% endif %}
							</td>
							<td width="50%">
								<input type="text" value="{{ tfiles.mail.file }}" class="form-control" readonly />
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:use_html_format'] }}
								<small class="form-text text-muted">{{ lang['feedback:use_html_format#descr'] }}</small>
							</td>
							<td width="50%">
								<select name="html" class="custom-select">
									<option value="" {{ not(flags.html) ? 'selected' : '' }}>{{ lang.noa }}</option>
									<option value="1" {{ flags.html ? 'selected' : '' }}>{{ lang.yesa }}</option>
								</select>
							</td>
						</tr>
						<tr>
							<td width="50%">
								{{ lang['feedback:custom_email_subject'] }}
								<small class="form-text text-muted">{{ lang['feedback:custom_email_subject#descr'] }}</small>
							</td>
							<td width="50%">
								<div class="input-group">
									<div class="input-group-prepend">
										<div class="input-group-text">
											<input type="checkbox" name="isSubj" value="1" {{ flags.subj ? 'checked' : '' }}
												oninput="document.querySelector('#subj').toggleAttribute('readonly', !this.checked);"
											 />
										</div>
									</div>
									<input id="subj" type="text" name="subj" value="{{ subj ?: lang['feedback:mail.subj'] }}" {{ not(flags.subj) ? 'readonly' : '' }} class="form-control" />
								</div>
							</td>
						</tr>
						<!-- <tr>
							<td width="50%">
								Исправление <b>UTF-8</b> кодировки
								<small class="form-text text-muted">Преобразовывать данные из формы в кодировку Win-1251, если они пришли в UTF-8</small>
							</td>
							<td width="50%">
								<input type="hidden" name="utf8" value="1" {{ flags.utf8 ? 'checked="checked"' : '' }} />
							</td>
						</tr> -->
					</tbody>
				</table>

				<div class="card-footer text-center">
					<button type="submit" form="feedback_config" class="btn btn-outline-success">{{ lang['feedback:button_save'] }}</button>
				</div>

			</div>
		</form>
	</div>

	<div id="emails-pane" class="tab-pane fade">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col text-right">
						<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#legendModal">
							<i class="fa fa-question"></i>
						</button>
					</div>
				</div>
			</div>

			<table class="table table-sm mb-0">
				<thead>
					<tr>
						<th width="20%">{{ lang['feedback:emails_group_id'] }}</th>
						<th>{{ lang['feedback:emails_group_name'] }}</th>
						<th>{{ lang['feedback:emails_group_list'] }}</th>
					</tr>
				</thead>
				<tbody>
				{% for egroup in egroups %}
					<tr>
						<td><input type="number" name="elist[{{ loop.index }}][0]" value="{{ egroup.num }}" class="form-control" disabled /></td>
						<td><input type="text" name="elist[{{ loop.index }}][1]" value="{{ egroup.name }}" class="form-control" /></td>
						<td><input type="text" name="elist[{{ loop.index }}][2]" value="{{ egroup.value }}" class="form-control" /></td>
					</tr>
				{% endfor %}
				</tbody>
			</table>

			<div class="card-footer text-center">
				<button type="submit" form="feedback_config" class="btn btn-outline-success">{{ lang['feedback:button_save'] }}</button>
			</div>
		</div>
	</div>

	<div id="fields-pane" class="tab-pane fade">
		{% if not(entries|length) %}
			<div class="alert alert-info">
				<h5>{{ lang['msgi_info'] }}</h5>
				<p>{{ lang['feedback:no_fields'] }}</p>
				<hr>
				<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}" class="btn btn-outline-success">{{ lang['feedback:add_new_field'] }}</a>
			</div>
		{%else%}
			<div class="card">
				<div class="card-header text-md-right">
					<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}" class="btn btn-outline-success">{{ lang['feedback:add_new_field'] }}</a>
				</div>

				<table class="table table-sm mb-0">
					<thead>
						<tr>
							<th>{{ lang['feedback:field_id'] }}</th>
							<th>{{ lang['feedback:field_title'] }}</th>
							<th>{{ lang['feedback:field_type'] }}</th>
							<th>{{ lang['feedback:field_autofill'] }}</th>
							<th>{{ lang['feedback:field_block'] }}</th>
							<th>{{ lang['feedback:actions'] }}</th>
						</tr>
					</thead>
					<tbody>
						{% for entry in entries %}
							<tr>
								<td>
									<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}&row={{ entry.name }}">{{ entry.name }}</a>
								</td>
								<td>{{ entry.title }}</td>
								<td>{{ lang['feedback:field_type_' ~ entry.type] }}</td>
								<td>{{ lang['feedback:field_autofill_' ~ entry.auto] }}</td>
								<td>{{ lang['feedback:field_block_' ~ entry.block] }}</td>
								<td class="text-right" nowrap>
									<div class="btn-group btn-group-sm" role="group">
										<a href="?mod=extra-config&plugin=feedback&action=update&subaction=up&id={{ formID }}&name={{ entry.name }}" class="btn btn-outline-primary">
											<i class="fa fa-arrow-up"></i>
										</a>
										<a href="?mod=extra-config&plugin=feedback&action=update&subaction=down&id={{ formID }}&name={{ entry.name }}" class="btn btn-outline-primary">
											<i class="fa fa-arrow-down"></i>
										</a>
									</div>

									<div class="btn-group btn-group-sm" role="group">
										<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}&row={{ entry.name }}" class="btn btn-outline-primary">
											<i class="fa fa-pencil"></i>
										</a>
									</div>

									<div class="btn-group btn-group-sm" role="group">
										<a href="?mod=extra-config&plugin=feedback&action=update&subaction=del&id={{ formID }}&name={{ entry.name }}" onclick="return confirm('{{ lang['feedback:msg_you_sure'] }}');" class="btn btn-outline-danger">
											<i class="fa fa-trash"></i>
										</a>
									</div>
								</td>
							</tr>
						{% endfor %}
					</tbody>
				</table>
			</div>
		{% endif %}
	</div>
</div>

<div id="legendModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="legendModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">{{ lang['msgi_info'] }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>{{ lang['feedback:msg_legend_emails_group_info'] }}</p>
				<div class="alert alert-warning">
					{{ lang['feedback:msg_legend_emails_group_single_group'] }}
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
