<script language="javascript">
	countryData = {areas};
</script>


Для пополнения Вашего счёта вам необходимы выполнить следующие шаги:<br/>
1. Отправить SMS собщение:<br/>
<br/>
<div class="not_logged" style="margin:0px; padding: 5px;">
	<table>
		<tr>
			<td>Ваша страна:</td>
			<td><select onkeyup="updateCountry();" onchange="updateCountry();" id="countrySelect">
					<option value="">-- загрузка --
				</select></td>
		</tr>
		<tr>
			<td>Ваш сотовый оператор:</td>
			<td><select onkeyup="updateOperator();" onchange="updateOperator();" id="operatorSelect">
					<option value="-">-- загрузка --
				</select></td>
		</tr>
		<tr>
			<td>Короткие номера:</td>
			<td>
				<table id="snList" border=1>
					<thead>
					<tr>
						<td>Короткий номер</td>
						<td>Вы платите</td>
						<td>Счет пополняется на</td>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td colspan=3>-- нет информации --</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
	<br/>
	Вам необходимо отправить на один из указанных коротких SMS номеров следующий текст:<br/>
	<b>+AMP</b><br/><br/>
</div>
<script language="javascript">
	// Загрузка данных по странам
	{
		var c = document.getElementById('countrySelect');
		c.options.length = 0;
		for (var country in countryData) {
			var opt = document.createElement("OPTION");
			opt.value = country;
			opt.text = country;
			c.options.add(opt);
		}
		updateCountry();
		updateOperator();
	}


	function updateCountry() {
		var c = document.getElementById('countrySelect');
		var o = document.getElementById('operatorSelect');
		o.options.length = 0;

		for (var operator in countryData[c.value]) {
			var opt = document.createElement("OPTION");
			o.options.add(opt);
			opt.value = operator;
			opt.text = operator;
		}
	}

	function updateOperator() {
		var c = document.getElementById('countrySelect');
		var o = document.getElementById('operatorSelect');
		var lst = countryData[c.value][o.value];
		var tbl = document.getElementById('snList');
		while (tbl.rows.length > 1) tbl.deleteRow(1);
		for (var sn in lst) {
			var nr = tbl.insertRow(tbl.rows.length);
			nr.insertCell(-1).innerHTML = sn;
			nr.insertCell(-1).innerHTML = lst[sn][0];
			nr.insertCell(-1).innerHTML = lst[sn][1];
		}


	}
</script>
<br/><br/>
2. В ответном сообщении (приходит обычно в течении 2-5 минут после отправки SMS) Вы получите
секретный код при активации которого на Ваш счёт будет начислена сумма из колонки "Счет
пополняется на", при этом с Вашего счёта будет списана сумма из колонки "Вы платите".

<br/><br/>
<div class="not_logged">
	<form method="GET" action="/plugin/finance/">
		<input type=hidden name="mode" value="pay_accept"/>
		<input type=hidden name="acceptor" value="sms"/>
		<input type=hidden name="service_id" value="{serviceID}"/>
		<input type=hidden name="back" value="{back}"/>
		<u>Активация секретного кода</u><br/><br/>
		<table>
			<tr>
				<td width=55>Код:</td>
				<td><input type=input name="passCode"/></td>
			</tr>
			<tr>
				<td colspan=2><input type=submit value="Активировать секретный код"/></td>
			</tr>
		</table>
	</form>
</div>