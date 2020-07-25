{% include 'plugins/xfields/tpl/navi.tpl' %}

{% if not(xfields|length) %}
<div class="alert alert-info">
	<h5>{{ lang['msgi_info'] }}</h5>
	<p>{{ lang.xfconfig['no_fields'] }}</p>
	<hr>
	{# Группирование полей используется только для новостей, поэтому не используем переменную `sectionID`, а напрямую указываем `section=news` #}
	<a href="?mod=extra-config&plugin=xfields&action=add&section=news" class="alert-link- btn btn-outline-success">{{ lang.xfconfig['add'] }}</a>
</div>
{% else %}

<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col">
				<h5 class="font-weight-light py-2 m-0">{{ section_name }}</h5>
			</div>
			<div class="col text-right">
				<button id="btn-create-group" type="button" data-toggle="modal" data-target="#groupEditorModal" data-backdrop="static" class="btn btn-outline-success">Создать группу</button>
			</div>
		</div>
	</div>

	<table id="groups-list" class="table table-sm mb-0">
		<thead>
			<tr>
				<th>Идентификатор группы</th>
				<th>Имя группы</th>
				<th>Поля, находящиеся в группе</th>
			</tr>
		</thead>
		<tbody>
			<tr><td colspan="3">Загрузка групп дополнительных полей ...</td></tr>
		</tbody>
	</table>

	<!-- Modal -->
	<div id="groupEditorModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 id="groupEditorModalLabel" class="modal-title">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="form-group-editor" action="" method="post">
					<div class="modal-body">
						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Идентификатор группы</label>
							<div class="col-sm-8">
								<div class="input-group">
									<input id="current-group-id" type="text" name="group-id" pattern="[a-zA-Z0-9_]{2,}" class="form-control" />
									<div class="input-group-append">
										<a class="btn btn-outline-primary" data-toggle="popover" data-placement="left" data-trigger="focus" data-html="true" data-content="Поле может содержать буквенно-цифровые символы, а также тире и подчеркивания и должно быть длиной от двух символов." tabindex="0">
											<i class="fa fa-question"></i>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-sm-4 col-form-label">Имя группы</label>
							<div class="col-sm-8">
								<input id="current-group-title" name="group-title" class="form-control" />
							</div>
						</div>

						<div id="current-group-fields">
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Добавить поле в группу</label>
								<div class="col-sm-8">
									<div class="input-group mb-3">
										<select id="xfields-list" class="custom-select">
											{% for field, data in xfields %}
											<option value="{{ field }}">{{ field }} :: {{ data.title }}</option>
											{% endfor %}
										</select>
										<div class="input-group-append">
											<button data-field-modify="fldAdd" type="button" class="btn btn-outline-success">Добавить</button>
										</div>
									</div>
								</div>
							</div>

							<table class="table table-sm mb-0">
								<thead>
									<tr>
										<th colspan="3" class="text-center">Поля, находящиеся в группе</th>
									</tr>
									<tr>
										<th>Идентификатор</th>
										<th>Название</th>
										<th>Действия</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<div class="modal-footer">
						<button id="btn-delete-group" type="button" class="btn btn-outline-danger mr-auto">Удалить</button>
						<button type="submit" class="btn btn-outline-success">Добавить</button>
						<button type="button" class="btn btn-outline-dark" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<template id="group-template">
		<tr>
			<td>
				<button type="button" data-group-id="" data-toggle="modal" data-target="#groupEditorModal" data-backdrop="static" class="btn btn-link">id</button>
			</td>
			<td>title</td>
			<td nowrap>['fields']</td>
		</tr>
	</template>

	<template id="field-template">
		<tr>
			<td>id</td>
			<td>title</td>
			<td class="text-right" nowrap>
				<div class="btn-group btn-group-sm" role="group">
					<button type="button" data-field-modify="fldUp" data-field-id="" class="btn btn-outline-primary"><i class="fa fa-arrow-up"></i></button>
					<button type="button" data-field-modify="fldDown" data-field-id="" class="btn btn-outline-primary"><i class="fa fa-arrow-down"></i></button>
				</div>
				<div class="btn-group btn-group-sm" role="group">
					<button type="button" data-field-modify="fldDel" data-field-id="" class="btn btn-outline-danger"><i class="fa fa-trash"></i></button>
				</div>
			</td>
		</tr>
	</template>

	<script type="text/javascript">
		const GROUPS = {{ groups | json_encode(constant('JSON_PRETTY_PRINT') b-or constant('JSON_UNESCAPED_UNICODE')) }};
		const XFIELDS = {{ xfields | json_encode(constant('JSON_PRETTY_PRINT') b-or constant('JSON_UNESCAPED_UNICODE')) }};
	</script>

	<script type="text/javascript">
		var current_group = getEmptyGroup();

		$(document).ready(function() {
			$('[data-toggle="popover"]').popover();

			// Заполняем список имеющихся групп доп.полей.
			fillGroupList();

			// Отслеживаем изменения при вводе в форму.
			$('#form-group-editor').on('input', function(event) {
				const target = event.target;

				if ('group-id' === target.name) {
					current_group.id = target.value;

					// Маркер режима редактирования.
					const isEditMode = GROUPS.hasOwnProperty(current_group.id);

					$(this).find('[type="submit"]')
						.attr('disabled', isEditMode)
						.text(isEditMode ? 'Сохранить' : 'Добавить');
				}

				'group-title' === target.name && (current_group.title = target.value);
			});

			// Добавить/Изменить/Удалить поле в группе.
			$('#current-group-fields').on('click', 'button[data-field-modify]', function(event) {
				if (!current_group.id) {
					alert('Не указан Идентификатор группы.');
				} else if (!current_group.title) {
					alert('Не указано Имя группы.');
				} else {
					const action = $(this).attr('data-field-modify');
					const field = 'fldAdd' === action ?
						$('#xfields-list').val() :
						$(this).attr('data-field-id');

					rpcRequest('plugin.xfields.group.modify', {
							'action': action,
							'utoken': 'UTOKEN',
							'id': current_group.id,
							'field': field,
						})
						.then(() => {
							current_group = {
								id: current_group.id,
								...GROUPS[current_group.id]
							};

							fillTableBodyForCurrentGroup(current_group.entries);
						});
				}
			});

			// Создаем/Сохраняем группу дополнительных полей.
			$('#form-group-editor').on('submit', function(event) {
				event.preventDefault();

				if (!current_group.id) {
					alert('Не указан Идентификатор группы.');
				} else if (!current_group.title) {
					alert('Не указано Имя группы.');
				} else {
					// Маркер режима редактирования.
					const isEditMode = GROUPS.hasOwnProperty(current_group.id);

					// Сохраняем Идентификатор текущей группы.
					const current_group_id = current_group.id;

					rpcRequest('plugin.xfields.group.modify', {
							// Если текущая группа существует, значит обновление.
							'action': 'grp' + (isEditMode ? 'Edit' : 'Add'),
							'utoken': 'UTOKEN',
							'id': current_group_id,
							'name': current_group.title
						})
						.then(() => {
							current_group = GROUPS[current_group_id];
							current_group.id = current_group_id;

							$('#groupEditorModal').modal('hide');
						});
				}
			});

			// Удалить группу.
			$('#btn-delete-group').on('click', function(event) {
				if (!current_group.id) {
					alert('Не указан Идентификатор группы.');
				} else {
					const result = confirm('Хотите удалить группу доп. полей ['+current_group.id+']?');

					result && rpcRequest('plugin.xfields.group.modify', {
							'action': 'grpDel',
							'utoken': 'UTOKEN',
							'id': current_group.id,
						})
						.then(() => {
							current_group = getEmptyGroup();
							$('#groupEditorModal').modal('hide');
						});
				}
			});

			$('#groupEditorModal').on('show.bs.modal', function(event) {
				const button = $(event.relatedTarget);
				const group_id = button.data('group-id');

				current_group = GROUPS[group_id] || getEmptyGroup();

				if (undefined === group_id) {
					$('#groupEditorModalLabel').text('Добавление группы доп. полей');
					$('#btn-delete-group').hide();
					$('#current-group-id').val('').removeAttr('readonly');
					$('#current-group-title').val('');
					$('#current-group-fields').hide();
					$(this).find('[type="submit"]').text('Добавить');
				} else {
					current_group.id = group_id;

					$('#groupEditorModalLabel').text('Изменение группы доп. полей');
					$('#btn-delete-group').show();
					$('#current-group-id').val(group_id).attr('readonly', true);
					$('#current-group-title').val(current_group.title);
					$('#current-group-fields').show();
					$(this).find('[type="submit"]').text('Сохранить');
				}

				fillTableBodyForCurrentGroup(current_group.entries)
			});
		});

		/**
		 * Получить пустую группу.
		 * @return {object}
		 */
		function getEmptyGroup() {
			return {
				id: 0,
				title: '',
				entries: []
			};
		}

		function fillGroupList() {
			const tbody_groups = document.querySelector('#groups-list tbody');

			tbody_groups.innerHTML = !Object.keys(GROUPS).length ? '<tr><td colspan="3">Вы еще не создали группы дополнительных полей.</td></tr>' : '';

			// Проверяем поддерживает ли браузер тег <template>
			// проверив наличие аттрибута content у элемента template
			if ('content' in document.createElement('template')) {
				const template = document.querySelector('#group-template');
				const cells = template.content.querySelectorAll('td');

				for (const group in GROUPS) {
					if (GROUPS.hasOwnProperty(group)) {
						// создаём новую строку
						const button = template.content.querySelector('button');

						button.dataset.groupId = button.textContent = group;
						cells[1].textContent = GROUPS[group].title;
						cells[2].textContent = JSON.stringify(GROUPS[group].entries);

						// клонируем новую строку и вставляем её в таблицу
						const clone = document.importNode(template.content, true);

						tbody_groups.appendChild(clone);
					}
				}
			} else {
				// необходимо найти другой способ добавить строку в таблицу т.к.
				// тег <template> не поддерживатся браузером
			}
		}

		/**
		 * Заполнить тело таблицы дополнительными полями группы.
		 * @param  {array} xfields
		 */
		function fillTableBodyForCurrentGroup(xfields) {
			const tbody_fields = document.querySelector('#current-group-fields tbody');

			tbody_fields.innerHTML = !xfields.length ? '<tr><td colspan="3">Нет полей в группе.</td></tr>' : '';

			// Проверяем поддерживает ли браузер тег <template>
			// проверив наличие аттрибута content у элемента template
			if ('content' in document.createElement('template')) {
				const template = document.querySelector('#field-template');
				const cells = template.content.querySelectorAll('td');
				const buttons = template.content.querySelectorAll('button');

				xfields.map(function(field) {
					cells[0].textContent = field;
					cells[1].textContent = XFIELDS[field].title;
					[...buttons].forEach(button => button.dataset.fieldId = field)

					// клонируем новую строку и вставляем её в таблицу
					const clone = document.importNode(template.content, true);

					tbody_fields.appendChild(clone);
				});
			} else {
				// необходимо найти другой способ добавить строку в таблицу т.к.
				// тег <template> не поддерживатся браузером
			}
		}

		/**
		 * Создаем дополнительную обертку для перерисовки списка групп.
		 * @param  {string} method
		 * @param  {object} params
		 * @return {promise}
		 */
		function rpcRequest(method, params) {
			return post(method, params)
				.then((response) => {
					// При успешном выполнении запроса, всегда должен приходить новый `config`.
					for (const group in GROUPS) {
						if (GROUPS.hasOwnProperty(group)) {
							delete GROUPS[group];
						}
					}

					for (const group in response.config) {
						GROUPS[group] = JSON.parse(JSON.stringify(response.config[group]));
					}

					fillGroupList();
				});
		}
	</script>
</div>
{% endif %}
