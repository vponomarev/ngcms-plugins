<?php
require_once("_header.php");
# Получение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X1(
		intval($_POST["orderid"]),    # номер счета в системе учета магазина; любое целое число без знака
		$_POST["customerwmid"],       # WMId покупателя
		$_POST["storepurse"],         # номер кошелька, на который необходимо оплатить счет
		floatval($_POST["amount"]),   # число с плавающей точкой без незначащих символов
		trim($_POST["desc"]),         # произвольная строка от 0 до 255 символов; пробелы в начале или конце не допускаются
		trim($_POST["address"]),      # произвольная строка от 0 до 255 символов; пробелы в начале или конце не допускаются
		intval($_POST["period"]),     # целое число от 0 до 255; если 0 - протекция сделки при оплате счета не разрешена
		intval($_POST["expiration"])  # целое число от 0 до 255; если 0 - срок оплаты не определен
	);
	# Преобразовываем ответ сервера в структуру. Входные параметры:
	# - XML-ответ сервера
	# - кодировка, используемая на сайте. По умолчанию используется UTF-8
	$structure = $parser->Parse($response, DOC_ENCODING);
	# преобразуем индексы структуры к более удобным для доступа.
	# Не рекомендуется проводить такое преобразование с с результатом, если он содержит
	# множество однотипных строк (например, список транзакций)
	# если надобности в аттрибутах XML-тегов ответа нет, то второй параметр можно
	# установить в false - в таком случае структура выйдет более компактной
	$transformed = $parser->Reindex($structure, true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X1</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
Детальное описание параметров:
<a href="http://webmoney.ru/rus/developers/interfaces/xml/issueinvoice/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/issueinvoice/index.shtml</a>
<br/>

<form action="" method="post">

	<label>номер счета в системе учета магазина; любое целое число без знака:</label>
	<input type="text" name="orderid" value="1"/>
	<br/>

	<label>WMId покупателя:</label>
	<input type="text" name="customerwmid" value=""/>
	<br/>

	<label>номер кошелька, на который необходимо оплатить счет:</label>
	<input type="text" name="storepurse" value=""/>
	<br/>

	<label>сумма счета, выставленная для оплаты покупателю:</label>
	<input type="text" name="amount" value="0.01"/>
	<br/>

	<label>описание товара или услуги, на который выписывается счет:</label>
	<input type="text" name="desc" value="тестирование X1 wmxi"/>
	<br/>

	<label>адрес доставки товара:</label>
	<input type="text" name="address" value="мой адрес не дом и не улица"/>
	<br/>

	<label>максимально допустимый срок протекции сделки в днях при оплате счета:</label>
	<input type="text" name="period" value="1"/>
	<br/>

	<label>максимально допустимый срок оплаты счета в днях:</label>
	<input type="text" name="expiration" value="1"/>
	<br/>

	<input type="submit" value="выписать счёт"/>
	<br/>

</form>

<pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre>
<pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre>
<!--pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre-->

<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		Номер счёта: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["orderid"], ENT_QUOTES); ?></b>
		Покупатель: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["customerwmid"], ENT_QUOTES); ?></b>
		Кошелёк: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["storepurse"], ENT_QUOTES); ?></b>
		Сумма: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["amount"], ENT_QUOTES); ?></b>
		Описание: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["desc"], ENT_QUOTES); ?></b>
		Адрес: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["address"], ENT_QUOTES); ?></b>
		Срок протекции: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["period"], ENT_QUOTES); ?></b>
		Срок оплаты: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["expiration"], ENT_QUOTES); ?></b>
		Состояние: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["state"], ENT_QUOTES); ?></b>
		Создан: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["datecrt"], ENT_QUOTES); ?></b>
		Изменён: <b><?= htmlspecialchars(@$transformed["w3s.response"]["invoice"]["dateupd"], ENT_QUOTES); ?></b>
		Код ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		Описание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
