<input type="hidden" id="xftable" name="xftable" value=""/>

<script type="text/javascript">
	// XFields configuration profile mapping
	var xfGroupConfig = {{ xfGC }};
	var xfCategories = {{ xfCat }};
	var xfList = {{ xfList }};

	var tblConfig = {{ xtableConf }};
	var tblData = {{ xtableVal }};


	function tblLoadData(initMode) {
		// Load body collection
		var trows = $("#tdataTable >tbody");

		var irows;
		if (initMode) {
			irows = tblData;
		} else {
			// Scan default values
			var irow = {'#id': '*'};
			for (var cfgRow in tblConfig) {
				irow[cfgRow] = tblConfig[cfgRow]['default'];
			}
			irows = [irow];
		}

		for (var dataRow in irows) {
			//alert('dataRow = '+dataRow);
			// Create new row
			var trow = $("<tr>").appendTo(trows);

			// Mark number
			$("<td>").html(irows[dataRow]['#id']).appendTo(trow);

			// Create elements
			for (var cfgRow in tblConfig) {
				// ** TEXT ELEMENT **
				if (tblConfig[cfgRow]['type'] == 'text') {
					var t = $("<td>").appendTo(trow);
					$("<input>").val(irows[dataRow][cfgRow]).appendTo(t);
				}

				// ** SELECT ELEMENT **
				if (tblConfig[cfgRow]['type'] == 'select') {
					var t = $("<td>").appendTo(trow);
					var s = $("<select>").appendTo(t);

					for (var opt in tblConfig[cfgRow]['options']) {
						$("<option>").val((tblConfig[cfgRow]['storekeys']) ? opt : tblConfig[cfgRow]['options'][opt]).html(tblConfig[cfgRow]['options'][opt]).appendTo(s);
					}
					s.val(irows[dataRow][cfgRow]);
				}
			}
			var t = $("<td>").appendTo(trow);
			$("<a>")
				.html(
					$("<img>")
						.attr("src", "{{ skins_url }}/images/delete.gif")
				)
				.attr("href", "#")
				.bind("click", function () {
					$(this).parent().parent().remove();
					return false;
				})
				.appendTo(t);
		}
	}

	function tblSaveData() {
		// Load body collection
		var trows = $("#tdataTable >tbody tr");

		// Fill original field numbers
		var num = 1;
		var fmatrix = [];

		for (var tc in tblConfig) {
			fmatrix[num++] = tc;
		}

		var tblRecs = [];
		for (var i = 0; i < trows.length; i++) {
			var trow = trows[i];
			var tblRec = {'#id': trow.childNodes[0].innerHTML};

			for (var x = 0; x < trow.childNodes.length; x++) {
				var cnode = trow.childNodes[x];
				if ((x > 0) && (x < (trow.childNodes.length - 1))) {
					tblRec[fmatrix[x]] = cnode.childNodes[0].value;
					if ((cnode.childNodes[0].value == '') && (tblConfig[fmatrix[x]]['required'])) {
						alert('Не заполнено обязательное поле!');
						return false;
					}

				}
			}
			//tblRec['#id'] = trow.childNodes[0].innerHTML;
			tblRecs.push(tblRec);
		}
		document.getElementById('xftable').value = json_encode(tblRecs);
		//alert(json_encode(tblRecs));

	}

	tblLoadData(1);

	/**
	 * Обновить видимость доп. полей плагина `xfields`.
	 * Ввиду множества нативных функций, здесь не применялся jQuery.
	 * @param  {string|int}  category_id  Идентификатор категории.
	 */
	function xf_update_visibility(category_id) {
	    // Работаем только с числовыми значениями.
	    category_id = parseInt(category_id, 10);

	    // Регулярное выражение для идентификаторов DOM-элеметов, по которому будем их искать.
	    const finderRegex = /xfl_(.*)/;

	    // Если не передан идентификатор категории,
	    // либо не существует группы доп. полей,
	    // либо для указанной категории не задана группа доп. полей,
	    // то отображаем по умолчанию весь список.
	    const isDefaultVisible = !category_id || !xfCategories[category_id] || !xfGroupConfig[xfCategories[category_id]];

	    // Эталонный список по которому будет производиться сортировка отображаемых доп. полей.
	    let compareList = xfList;

	    if (isDefaultVisible) {
	        // Убираем название группы доп. полей.
	        document.querySelector('#xf_profile').textContent = '';
	    } else {
	        // Выбираем группу доп. полей для текущей категории.
	        const group = xfGroupConfig[xfCategories[category_id]];

	        // Изменяем подпись, отображающую название группы доп. полей.
	        document.querySelector("#xf_profile").textContent = '[ ' + xfCategories[category_id] + ' :: ' + group['title'] + ' ]';

	        // Меняем эталонный список.
	        compareList = group['entries']
	    }

	    // Ищем все DOM-элементы с доп. полями.
	    const [...fields] = document.querySelectorAll('[id*=xfl_]');

	    // Сортируем DOM-элементы в необходимом порядке.
	    fields.sort(function(a, b) {
	        return compareList.indexOf(finderRegex.exec(a.id).pop()) - compareList.indexOf(finderRegex.exec(b.id).pop());
	    });

	    // Перебираем DOM-элементы.
	    fields.map(function(element, index) {
	        element.parentNode.appendChild(element);

	        element.classList.toggle('d-none', !compareList.includes(finderRegex.exec(element.id).pop()));
	    });
	}

	// Manage fields after document is loaded
	$(document).ready(function () {
		// Catch change of #catmenu selector
		$("#catmenu").change(function () {
			xf_update_visibility(this.value);
		})
		.trigger('change');
	});

	$("#postForm").submit(function () {
		return tblSaveData();
	});
</script>
