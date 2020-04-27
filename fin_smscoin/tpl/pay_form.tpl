<br><br>
<div class="not_logged">
	<form method="POST" action="{form_url}">
		<u>Пополнение внутреннего счёта путём отправки SMS сообщений с мобильного телефона.<br><br>
			<table>
				<tr>
					<td width=120>Внутренняя валюта:</td>
					<td><b>{syscurrency}</b></td>
				</tr>
				<tr>
					<td width=120>Ваша страна:</td>
					<td><select name="s_country" id="s_country" onchange="updateOplist(this.value)"></select></td>
				</tr>
				<tr>
					<td width=120>Ваш оператор:</td>
					<td>
						<select name="s_provider" id="s_provider" onchange="updatePrlist(document.getElementById('s_country').value, this.value)"></select>
					</td>
				</tr>
				<tr>
					<td width=120>Ваш платеж:</td>
					<td><select name="s_payment" id="s_payment" size="6" style="width: 300px; height: 150px;"></select>
					</td>
				</tr>
				<tr>
					<td colspan=2><input type=submit value="Перейти на страницу пополнения"></td>
				</tr>
			</table>
	</form>
	<script language="javascript">
		function updateOplist(country, manual) {
			if (!initComplete && !manual) {
				return;
			}

			var initSave = initComplete;
			initComplete = 0;
			var sP = document.getElementById('s_provider');
			sP.options.length = 0;
			for (var provider in priceList[country]['providers']) {
				var pRec = priceList[country]['providers'][provider];
				var sO = document.createElement('OPTION');
				sO.value = provider;
				sO.innerHTML = (provider == '') ? '-- все операторы --' : pRec['name'];
				sP.options.add(sO);
			}

			updatePrlist(country, sP.value, 1);
			initComplete = initSave;
		}

		function updatePrlist(country, provider, manual) {
			if (!initComplete && !manual) {
				return;
			}
			var sP = document.getElementById('s_payment');
			sP.options.length = 0;

			var pList = priceList[country]['providers'][provider]['recs'];
			for (var payment in pList) {
				var sO = document.createElement('OPTION');
				sO.innerHTML = '$' + pList[payment]['usd'] + ' (' + pList[payment]['price'] + ' ' + pList[payment]['currency'] + ') => ' + pList[payment]['profit'] + ' ' + systemCurrency;
				sO.value = pList[payment]['index'];
				sP.options.add(sO);
			}
		}

		var currentCountry = '';
		var currentProvider = '';
		var initComplete = 0;
		var systemCurrency = '{syscurrency}';
		var priceList = {pricelist};

		// Populate country list
		var sC = document.getElementById('s_country');
		for (var country in priceList) {
			var sO = document.createElement('OPTION');
			sO.innerHTML = priceList[country]['countryName'];
			sO.value = country;
			sC.options.add(sO);
		}

		// Populate providers list
		updateOplist(sC.value, 1);
		initComplete = 1;
	</script>
</div>