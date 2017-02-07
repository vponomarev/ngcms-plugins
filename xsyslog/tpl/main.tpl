<script type="text/javascript" src="{{ scriptLibrary }}/ajax.js"></script>
<script type="text/javascript" src="{{ scriptLibrary }}/admin.js"></script>
<script type="text/javascript" src="{{ scriptLibrary }}/libsuggest.js"></script>

<script language="javascript" type="text/javascript">
	<!--

	function addEvent(elem, type, handler) {
		if (elem.addEventListener) {
			elem.addEventListener(type, handler, false)
		} else {
			elem.attachEvent("on" + type, handler)
		}
	}

	// DateEdit filter
	function filter_attach_DateEdit(id) {
		var field = document.getElementById(id);
		if (!field)
			return false;

		if (field.value == '')
			field.value = 'DD.MM.YYYY';

		field.onfocus = function (event) {
			var ev = event ? event : window.event;
			var elem = ev.target ? ev.target : ev.srcElement;

			if (elem.value == 'DD.MM.YYYY')
				elem.value = '';

			return true;
		}


		field.onkeypress = function (event) {
			var ev = event ? event : window.event;
			var keyCode = ev.keyCode ? ev.keyCode : ev.charCode;
			var elem = ev.target ? ev.target : ev.srcElement;
			var elv = elem.value;

			isMozilla = false;
			isIE = false;
			isOpera = false;
			if (navigator.appName == 'Netscape') {
				isMozilla = true;
			}
			else if (navigator.appName == 'Microsoft Internet Explorer') {
				isIE = true;
			}
			else if (navigator.appName == 'Opera') {
				isOpera = true;
			}
			else { /* alert('Unknown navigator: `'+navigator.appName+'`'); */
			}

			//document.getElementById('debugWin').innerHTML = 'keyPress('+ev.keyCode+':'+ev.charCode+')['+(ev.shiftKey?'S':'.')+(ev.ctrlKey?'C':'.')+(ev.altKey?'A':'.')+']<br/>' + document.getElementById('debugWin').innerHTML;

			// FF - onKeyPress captures functional keys. Skip anything with charCode = 0
			if (isMozilla && !ev.charCode)
				return true;

			// Opera - dumb browser, don't let us to determine some keys
			if (isOpera) {
				var ek = '';
				//for (i in event) { ek = ek + '['+i+']: '+event[i]+'<br/>\n'; }
				//alert(ek);
				if (ev.keyCode < 32) return true;
				if (!ev.shiftKey && ((ev.keyCode >= 33) && (ev.keyCode <= 47))) return true;
				if (!ev.keyCode) return true;
				if (!ev.which) return true;
			}


			// Don't block CTRL / ALT keys
			if (ev.altKey || ev.ctrlKey || !keyCode)
				return true;

			// Allow to input only digits [0..9] and dot [.]
			if (((keyCode >= 48) && (keyCode <= 57)) || (keyCode == 46))
				return true;

			return false;
		}

		return true;
	}

	-->
</script>


<div style="text-align : left;">
	<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="100%" colspan="2" class="contentHead">
				<img src="{{ skins_url }}/images/nav.gif" hspace="8" alt=""/>Настройка плагина: Журнал действий
				пользователей
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>

	<!-- Hidden SUGGEST div -->
	<div id="suggestWindow" class="suggestWindow">
		<table id="suggestBlock" cellspacing="0" cellpadding="0" width="100%"></table>
		<a href="#" align="right" id="suggestClose">close</a>
	</div>

	<form action="{{ php_self }}?mod=extra-config&plugin=xsyslog" method="post" name="options_bar">
		<table width="1000" border="0" cellspacing="0" cellpadding="0" class="editfilter">
			<tr>
				<!--Block 1-->
				<td rowspan="2">
					<table border="0" cellspacing="0" cellpadding="0" class="filterblock">
						<tr>
							<td valign="top">
								<label>Дата</label>
								с:&nbsp;
								<input type="text" id="dr1" name="dr1" value="{{ fDateStart }}" class="bfdate"/>&nbsp;&nbsp;
								по&nbsp;&nbsp;
								<input type="text" id="dr2" name="dr2" value="{{ fDateEnd }}" class="bfdate"/>
							</td>
						</tr>
						<tr>
							<td>
								<label>Пользователь</label>
								<input name="an" id="an" class="bfauthor" type="text" value="{{ an }}" autocomplete="off"/>
								<span id="suggestLoader" style="width: 20px; visibility: hidden;"><img src="{{ skins_url }}/images/loading.gif"/></span>
							</td>
						</tr>
					</table>

				</td><!--/Block 1-->

				<!--Block 2-->
				<td valign="top">
					<table border="0" cellspacing="0" cellpadding="0" class="filterblock2">
						<tr>
							<td colspan="2">
								<label class="left">Plugin</label>&nbsp;&nbsp;
								{{ catPlugins }}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<label class="left">Item</label>&nbsp;&nbsp;
								{{ catItems }}
							</td>
						</tr>

					</table>

				</td>
				<!--/Block 2-->

				<!--Block 3-->
				<td rowspan="2">
					<table border="0" cellspacing="0" cellpadding="0" class="filterblock">
						<tr>
							<td>
								<label>Статус</label>
								<select name="status" class="bfstatus">
									<option value="null" {% if fstatus  == 'null' %}selected{% endif %}>- Все -</option>
									<option value="0" {% if fstatus == '0' %}selected{% endif %}>0</option>
									<option value="1" {% if fstatus == '1' %}selected{% endif %}>1</option>
								</select>
							</td>
							<td>
								<label>На странице</label>
								<input name="rpp" value="{{ rpp }}" type="text" size="3"/>
							</td>
						</tr>
						<tr>
							<!--  <td colspan="2">
							  <label>Очистить данные</label>
							  <input type="button" name="clearbtn" value="Очистить" class="filterbutton"  />
							  </td> -->
						</tr>
					</table>

				</td>

			</tr>
			<tr>
				<td><input type="submit" value="Показать" class="filterbutton"/></td>
			</tr>
		</table>
	</form>
	<!-- Конец блока фильтрации -->

	<br/>

	{{ entries }}

</div>


<script language="javascript" type="text/javascript">
	// Init jQueryUI datepicker
	$("#dr1").datepicker({currentText: "", dateFormat: "dd.mm.yy"});
	$("#dr2").datepicker({currentText: "", dateFormat: "dd.mm.yy"});


	<
	!--
// INIT NEW SUGGEST LIBRARY [ call only after full document load ]
		function systemInit() {
			var aSuggest = new ngSuggest('an',
				{
					'localPrefix': '',
					'reqMethodName': 'core.users.search',
					'lId': 'suggestLoader',
					'hlr': 'true',
					'iMinLen': 1,
					'stCols': 2,
					'stColsClass': ['cleft', 'cright'],
					'stColsHLR': [true, false],
				}
			);

		}

	// Init system [ IE / Other browsers should be inited in different ways ]
	if (document.body.attachEvent) {
		// IE
		document.body.onload = systemInit;
	} else {
		// Others
		systemInit();
	}

	filter_attach_DateEdit('dr1');
	filter_attach_DateEdit('dr2');
	-- >
</script>