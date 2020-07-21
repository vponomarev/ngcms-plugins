{% include 'plugins/feedback/tpl/conf.navi.tpl' %}

<form action="?mod=extra-config&plugin=feedback&action=editrow&form_id={{ formID }}&name={{ fieldName }}" method="post" name="feedbackform">
	<input type="hidden" name="edit" value="{{ flags.addField ? '0' : '1' }}" />

	<div class="card">
		<h5 class="card-header font-weight-light">
			{{ flags.addField ? lang['feedback:adding_new_field'] : lang['feedback:editing_field'] }}
		</h5>

		<table class="table table-sm">
			<tbody>
				<tr>
					<td width="50%" colspan="2">{{ lang['feedback:field_id'] }}</td>
					<td width="50%">
						<div class="input-group">
							<input type="text" name="name" value="{{ field.name }}" class="form-control" {{ not(flags.addField) ? 'readonly' : ''}} />
							<div class="input-group-append">
								<a class="btn btn-outline-primary" data-toggle="popover" data-placement="left" data-trigger="focus" data-html="true" data-content="{{ lang['feedback:field_id#descr'] }}" tabindex="0">
									<i class="fa fa-question"></i>
								</a>
							</div>
						</div>
						{% if (not flags.addField) %}<small class="form-text text-danger">[{{ lang['feedback:no_edit_id'] }}]</small>{% endif %}
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">{{ lang['feedback:field_title'] }}</td>
					<td width="50%">
						<input type="text" name="title" value="{{ field.title }}" class="form-control" />
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">{{ lang['feedback:field_type'] }}</td>
					<td width="50%">
						<select name="type" size="5" class="custom-select" oninput="clx(this.value);">
							{{ field.type.options }}
						</select>
					</td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: TEXT -->
			<tbody id="type_text" class="bg-light">
				<tr>
					<td colspan="2">{{ lang['feedback:default_value'] }}</td>
					<td><input type="text" name="text_default" value="{{ field.text_default }}" class="form-control" />
				</tr>
			</tbody>

			<!-- FIELD TYPE: EMAIL -->
			<tbody id="type_email" class="bg-light">
				<tr>
					<td colspan="2">{{ lang['feedback:field_type_email_sendmail'] }}</td>
					<td>
						<select name="email_template" class="custom-select">
							{% for x, xv in field.email_template.options %}
								<option value="{{ x }}" {{ x == field.email_template.value ? 'selected' : '' }}>
									{% if (x == '') %}
										{{ lang['feedback:field_type_email_not_send'] }}
									{% else %}
										{{ xv }}
									{% endif %}
								</option>
							{% endfor %}
						</select>
					</td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: TEXTAREA -->
			<tbody id="type_textarea" class="bg-light">
				<tr>
					<td colspan="2">{{ lang['feedback:default_value'] }}</td>
					<td width="50%">
						<textarea name="textarea_default" rows="4" class="form-control">{{ field.textarea_default }}</textarea>
					</td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: SELECT -->
			<tbody id="type_select" class="bg-light">
				<tr>
					<td colspan="2">{{ lang['feedback:field_type_select_options'] }}</td>
					<td width="50%">
						<textarea name="select_options" rows="8" class="form-control">{{ field.select_options }}</textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2">{{ lang['feedback:default_value'] }}</td>
					<td width="50%">
						<input type="text" name="select_default" value="{{ field.select_default }}" class="form-control" />
					</td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: DATE -->
			<tbody id="type_date" class="bg-light">
				<tr>
					<td colspan="2">{{ lang['feedback:default_date_value'] }}</td>
					<td width="50%">
						<input type="text" name="date_default" value="{{ field.date_default }}" class="form-control" />
					</td>
				</tr>
			</tbody>
			<!-- FIELD TYPE: /CLOSED/ -->

			<tbody>
				<tr>
					<td width="50%" colspan="2">
						{{ lang['feedback:field_required'] }}
						<small class="form-text text-muted">{{ lang['feedback:field_required#descr'] }}</small>
					</td>
					<td width="50%">
						<select name="required" class="custom-select">
							{% for x in field.required.options %}
								<option value="{{ x }}" {% if (field.required.value == x) %}selected="selected"{% endif %}>{{ lang['feedback:field_required_' ~ x] }}</option>
							{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">
						{{ lang['feedback:field_autofill'] }}
						<small class="form-text text-muted">{{ lang['feedback:field_autofill#descr'] }}</small>
					</td>
					<td width="50%">
						<select name="auto" class="custom-select">
							{% for x in field.auto.options %}
								<option value="{{ x }}" {% if (field.auto.value == x) %}selected="selected"{% endif %}>{{ lang['feedback:field_autofill_' ~ x] }}</option>
							{% endfor %}
						</select>
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">
						{{ lang['feedback:field_block'] }}
						<small class="form-text text-muted">{{ lang['feedback:field_block#descr'] }}</small>
					</td>
					<td width="50%">
						<select name="block" class="custom-select">
							{% for x in field.block.options %}
								<option value="{{ x }}" {% if (field.block.value == x) %}selected="selected"{% endif %}>{{ lang['feedback:field_block_' ~ x] }}</option>
							{% endfor %}
						</select>
					</td>
				</tr>
			</tbody>
		</table>


		<div class="card-footer text-center">
			<button type="submit" class="btn btn-outline-success">{{ lang['feedback:button_save'] }}</button>
		</div>
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		$('[data-toggle="popover"]').popover();
		clx('{{ field.type.value ?: 'text' }}');
	});

	function clx(mode) {
		$('#type_text').toggle(mode == 'text');
		$('#type_email').toggle(mode == 'email');
		$('#type_textarea').toggle(mode == 'textarea');
		$('#type_select').toggle(mode == 'select');
		$('#type_date').toggle(mode == 'date');
	}
</script>
