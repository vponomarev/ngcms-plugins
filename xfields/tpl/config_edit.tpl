<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{ admin_url }}"><i class="fa fa-home"></i></a></li>
		<li class="breadcrumb-item"><a href="?mod=extras">{{ lang['extras'] }}</a></li>
		<li class="breadcrumb-item"><a href="?mod=extra-config&plugin=xfields&section={{ sectionID }}">{{ lang.xfconfig['config_text'] }} xfields</a></li>
		<li class="breadcrumb-item active" aria-current="page">{% if (not flags.editMode) %}{{ lang.xfconfig['title_add'] }}{% else %}{{ lang.xfconfig['title_edit'] }} ({{ id }}){% endif %}</li>
	</ol>
</nav>

<div id="edit_yakor"></div>

<form action="?mod=extra-config&plugin=xfields&action=doedit&section={{ sectionID }}" method="post" name="xfieldsform">
	<input type="hidden" name="mod" value="extra-config" />
	<input type="hidden" name="edit" value="{% if (flags.editMode) %}1{% else %}0{% endif %}" />

	<div class="card">
		<div class="card-header text-right">
			<div class="custom-control custom-switch py-2 mr-auto">
				<input id="disabled_field" type="checkbox" name="disabled" value="1" class="custom-control-input" {{ flags.disabled ? 'checked' : '' }} />
				<label for="disabled_field" class="custom-control-label text-danger">{{ lang.xfconfig['disabled'] }}</label>
			</div>
		</div>

		<table class="table table-sm">
			<tbody>
				<tr>
					<td width="50%" colspan="2">
						{{ lang.xfconfig['id'] }}
					</td>
					<td width="50%">
						<div class="input-group">
							<input type="text" name="id" value="{{ id }}" class="form-control" pattern="[a-z]{1}[a-z0-9]{2,}" {{ flags.editMode ? 'readonly' : '' }} required />
							<div class="input-group-append">
								<a class="btn btn-outline-primary" data-toggle="popover" data-placement="left" data-trigger="focus" data-html="true" data-content="{{ lang.xfconfig['id#descr'] }}" tabindex="0">
									<i class="fa fa-question"></i>
								</a>
							</div>
						</div>
						{% if (flags.editMode) %}<small class="form-text text-danger">[{{ lang.xfconfig['no_edit_id'] }}]</small>{% endif %}
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">{{ lang.xfconfig['title'] }}</td>
					<td width="50%">
						<input type="text" name="title" value="{{ title }}" class="form-control" required />
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">{{ lang.xfconfig['type'] }}</td>
					<td width="50%">
						<select id="xfSelectType" name="type" size="6" class="custom-select" oninput="clx(this.value);" required>
							{{ type_opts }}
						</select>
					</td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: TEXT -->
			<tbody id="type_text" class="bg-light">
				<tr>
					<td colspan="2">{{ lang.xfconfig['html_support'] }}</td>
					<td width="50%"><input type="checkbox" name="text_html_support" value="1" {{ html_support }} /></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['bb_support'] }}</td>
					<td width="50%"><input type="checkbox" name="text_bb_support" value="1" {{ bb_support }} /></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['default'] }}</td>
					<td width="50%"><input type="text" name="text_default" value="{{ defaults.text }}" class="form-control" /></td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: TEXTAREA -->
			<tbody id="type_textarea" class="bg-light">
				<tr>
					<td colspan="2">{{ lang.xfconfig['html_support'] }}</td>
					<td width="50%"><input type="checkbox" name="textarea_html_support" value="1" {{ html_support }} /></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['bb_support'] }}</td>
					<td width="50%"><input type="checkbox" name="textarea_bb_support" value="1" {{ bb_support }} /></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['noformat'] }}</td>
					<td width="50%"><input type="checkbox" name="textarea_noformat" value="1" {{ noformat }} /></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['default'] }}</td>
					<td width="50%"><textarea name="textarea_default" class="form-control" rows="4">{{ defaults.textarea }}</textarea></td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: SELECT -->
			<tbody id="type_select" class="bg-light">
				<tr>
					<td colspan="2">{{ lang.xfconfig['tselect_storekeys'] }}</td>
					<td width="50%"><select name="select_storekeys" class="custom-select">{{ storekeys_opts }}</select></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['tselect_options'] }}</td>
					<td width="50%">
						<table id="xfSelectTable" class="table table-sm">
							<thead>
								<tr>
									<th>Код</th>
									<th>Значение</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody id="xfSelectRows">
								{{ sOpts }}
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">
										<button id="xfBtnAdd" type="button" class="btn btn-sm btn-outline-success">+ Добавить строку</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['tselect_default'] }}</td>
					<td width="50%"><input type="text" name="select_default" value="{{ defaults.select }}" class="form-control" /></td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: MULTISELECT -->
			<tbody id="type_multiselect" class="bg-light">
				<tr>
					<td colspan="2">{{ lang.xfconfig['tselect_storekeys'] }}</td>
					<td width="50%"><select name="select_storekeys_multi" class="custom-select">{{ storekeys_opts }}</select></td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['tselect_options'] }}</td>
					<td width="50%">
						<table id="xfSelectTable_multi" class="table table-sm">
							<thead>
								<tr>
									<th>Код</th>
									<th>Значение</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody id="xfSelectRows_multi">
								{{ m_sOpts }}
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">
										<button id="xfBtnAdd_multi" type="button" class="btn btn-sm btn-outline-success">+ Добавить строку</button>
									</td>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">{{ lang.xfconfig['tselect_default'] }}</td>
					<td width="50%"><input type="text" name="select_default_multi" value="{{ defaults.select }}" class="form-control" /></td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: CHECKBOX -->
			<tbody id="type_checkbox" class="bg-light">
				<tr>
					<td colspan="2">{{ lang.xfconfig['default'] }}</td>
					<td width="50%"><input type="checkbox" name="checkbox_default" value="1" {{ defaults.checkbox }} /></td>
				</tr>
			</tbody>

			<!-- FIELD TYPE: IMAGES -->
			<tbody id="type_images" class="bg-light">
				<tr>
					<td colspan="2">Максимальное кол-во изображений для загрузки:</td>
					<td width="50%"><input type="number" name="images_maxCount" value="{{ images.maxCount }}" class="form-control" /></td>
				</tr>
				<tr>
					<td colspan="2">Добавлять штамп:</td>
					<td width="50%"><input type="checkbox" name="images_imgStamp" value="1" {{ images.imgStamp }} /></td>
				</tr>
				<tr>
					<td colspan="2">Добавлять тень:</td>
					<td width="50%"><input type="checkbox" name="images_imgShadow" value="1" {{ images.imgShadow }} /></td>
				</tr>
				<tr>
					<td colspan="2">Уменьшенная копия:</td>
					<td width="50%">
						<div class="form-check">
							<input id="images_imgThumb" type="checkbox" name="images_imgThumb" value="1" class="form-check-input" {{ images.imgThumb }} />
							{# <label for="images_imgThumb" class="form-check-label">Уменьшенная копия:</label> #}
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="pl-5"><label class="col-form-label">Размер (не более), пикселей</label></td>
					<td width="50%">
						<div class="input-group">
							<input type="number" name="images_thumbWidth" value="{{ images.thumbWidth }}" class="form-control" />
							<div class="input-group-prepend input-group-append">
								<label class="input-group-text">x</label>
							</div>
							<input type="number" name="images_thumbHeight" value="{{ images.thumbHeight }}" class="form-control" />
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="pl-5">Добавлять штамп:</td>
					<td width="50%">
						<input type="checkbox" name="images_thumbStamp" value="1" {{ images.thumbStamp }} />
					</td>
				</tr>
				<tr>
					<td colspan="2" class="pl-5">Добавлять тень:</td>
					<td width="50%">
						<input type="checkbox" name="images_thumbShadow" value="1" {{ images.thumbShadow }} />
					</td>
				</tr>
			</tbody>
			<!-- FIELD TYPE: /CLOSED/ -->

			<tbody>
				<tr>
					<td width="50%" colspan="2">Режим сохранения данных:</td>
					<td width="50%">
						<select id="storage" name="storage" class="custom-select" oninput="storageMode(this.value);">
							<option value="0" {{ not(storage) ? 'selected' : '' }}>Единое хранилище</option>
							<option value="1" {{ storage ? 'selected' : '' }}>Персональное поле в БД</option>
						</select>
					</td>
				</tr>
				<tr id="storageRow" class="{{ not(storage) ? 'd-none' : '' }}">
					<td width="50%" colspan="2">Тип поля в БД и его длина:</td>
					<td width="50%">
						<div class="input-group">
							<select id="db.type" name="db_type" class="custom-select">
								<option value="int" {{ db_type in ['', 'int'] ? 'selected' : '' }}>int - только цифры</option>
								<option value="decimal" {{ 'decimal' == db_type ? 'selected' : '' }}>decimal - число с фиксированной точкой</option>
								<option value="char" {{ 'char' == db_type ? 'selected' : '' }}>char - текст с ограничением длины (255)</option>
								<option value="text" {{ 'text' == db_type ? 'selected' : '' }}>text - текст с ограничением длины (65535)</option>
								<option value="datetime" {{ 'datetime' == db_type ? 'selected' : '' }}>datetime - дата-время</option>
							</select>
							<input id="db.len" type="text" name="db_len" value="{{ db_len }}" maxlength="5" class="form-control" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="50%" colspan="2">
						{{ lang.xfconfig['required'] }}
						<small class="form-text text-muted">{{ lang.xfconfig['required#descr'] }}</small>
					</td>
					<td width="50%">
						<select name="required" class="custom-select">{{ required_opts }}</select>
					</td>
				</tr>
				{% if (sectionID != 'tdata') %}
				<tr>
					<td width="50%" colspan="2">
						{{ lang.xfconfig['block_location'] }}
					</td>
					<td width="50%">
						<div class="input-group">
							<input type="number" name="area" value="{{ area }}" class="form-control" />
							<div class="input-group-append">
								<a class="btn btn-outline-primary" data-toggle="popover" data-placement="left" data-trigger="focus" data-html="true" data-content="{{ lang.xfconfig['block_location#descr'] }}" tabindex="0">
									<i class="fa fa-question"></i>
								</a>
							</div>
						</div>

					</td>
				</tr>
				{% endif %}
				{% if (sectionID == 'users') and (type != 'images') %}
				<tr>
					<td width="50%" colspan="2"></td>
					<td width="50%">
						<div class="custom-control custom-switch py-2 mr-auto">
							<input id="regpage" type="checkbox" name="regpage" value="1" class="custom-control-input" {{ flags.regpage ? 'checked' : '' }} />
							<label for="regpage" class="custom-control-label">{{ lang.xfconfig['regpage'] }}</label>
						</div>
					</td>
				</tr>
				{% endif %}
			</tbody>
		</table>


		<div class="card-footer text-center">
			<button id="xfBtnSubmit" type="submit" class="btn btn-outline-success">
				{% if (flags.editMode) %}{{ lang.xfconfig['edit'] }}{% else %}{{ lang.xfconfig['save'] }}{% endif %}
			</button>
		</div>
	</div>
</form>

<script type="text/javascript">
	$(document).ready(function() {
		$('[data-toggle="popover"]').popover();

		clx('{{ type }}');

		var soMaxNum = $('#xfSelectTable >tbody >tr').length + 1;

		$('#xfSelectTable a').click(function () {
			if ($('#xfSelectTable >tbody >tr').length > 1) {
				$(this).parent().parent().remove();
			} else {
				$(this).parent().parent().find("input").val('');
			}
		});

		$("#xfBtnSubmit").click(function () {
			// Check if type == 'select'
			if ($("#xfBtnType").val() == 'select') {
				// Prepare list of data

			}
		});

		// jQuery - INIT `select` configuration
		$("#xfBtnAdd").click(function () {
			var xl = $('#xfSelectTable tbody>tr:last').clone();
			xl.find("input").val('');
			xl.find("input").eq(0).attr("name", "so_data[" + soMaxNum + "][0]");
			xl.find("input").eq(1).attr("name", "so_data[" + soMaxNum + "][1]");
			soMaxNum++;

			xl.insertAfter('#xfSelectTable tbody>tr:last');
			$('#xfSelectTable a').click(function () {
				if ($('#xfSelectTable >tbody >tr').length > 1) {
					$(this).parent().parent().remove();
				} else {
					$(this).parent().parent().find("input").val('');
				}
			});
		});


		var soMaxNum_multi = $('#xfSelectTable_multi >tbody >tr').length + 1;

		$('#xfSelectTable_multi a').click(function () {
			if ($('#xfSelectTable_multi >tbody >tr').length > 1) {
				$(this).parent().parent().remove();
			} else {
				$(this).parent().parent().find("input").val('');
			}
		});

		$("#xfBtnAdd_multi").click(function () {
			var xl = $('#xfSelectTable_multi tbody>tr:last').clone();
			xl.find("input").val('');
			xl.find("input").eq(0).attr("name", "mso_data[" + soMaxNum_multi + "][0]");
			xl.find("input").eq(1).attr("name", "mso_data[" + soMaxNum_multi + "][1]");
			soMaxNum_multi++;

			xl.insertAfter('#xfSelectTable_multi tbody>tr:last');
			$('#xfSelectTable_multi a').click(function () {
				if ($('#xfSelectTable_multi >tbody >tr').length > 1) {
					$(this).parent().parent().remove();
				} else {
					$(this).parent().parent().find("input").val('');
				}
			});
		});
	});

	function clx(mode) {
		$('#type_text').toggle(mode == 'text');
		$('#type_textarea').toggle(mode == 'textarea');
		$('#type_select').toggle(mode == 'select');
		$('#type_multiselect').toggle(mode == 'multiselect');
		$('#type_checkbox').toggle(mode == 'checkbox');
		$('#type_images').toggle(mode == 'images');
	}

	function storageMode(mode) {
		$('#storageRow').toggleClass('d-none', 0 == mode);
	}
</script>
