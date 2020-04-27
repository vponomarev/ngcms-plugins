<br><br>
<div class="not_logged">
	<form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
		<input type=hidden name="LMI_PAYMENT_DESC" value="{descr}">
		<input type=hidden name="userid" value="{userid}">
		<input type=hidden name="login" value="{login}">
		<input type=hidden name="home" value="{home}">
		<u>Пополнение внутреннего счёта за счёт средств сервиса <b>WebMoney</b></u><br><br>
		<table>
			<tr>
				<td width=120>Внутренняя валюта:</td>
				<td><b>{syscurrency}</b></td>
			</tr>
			<tr>
				<td width=120>Сумма пополнения:</td>
				<td><input type="input" value="{sum}" name="LMI_PAYMENT_AMOUNT"> <select name="LMI_PAYEE_PURSE">{currency_list}</select>
				</td>
			</tr>
			<tr>
				<td colspan=2><input type=submit value="Перейти на страницу пополнения"></td>
			</tr>
		</table>
	</form>
</div>