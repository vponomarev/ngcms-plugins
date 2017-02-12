<?php
require_once("_header.php");
# Получение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X2(
		intval($_POST["tranid"]),    # номер перевода в системе учета отправителя; любое целое число без знака, должно быть уникальным
		$_POST["pursesrc"],          # номер кошелька с которого выполняется перевод (отправитель)
		$_POST["pursedest"],         # номер кошелька, но который выполняется перевод (получатель)
		floatval($_POST["amount"]),  # число с плавающей точкой без незначащих символов
		intval($_POST["period"]),    # целое от 0 до 255 символов; 0 - без протекции
		trim($_POST["pcode"]),       # произвольная строка от 0 до 255 символов; пробелы в начале или конце не допускаются
		trim($_POST["desc"]),        # произвольная строка от 0 до 255 символов; пробелы в начале или конце не допускаются
		intval($_POST["wminvid"])    # целое число > 0; если 0 - перевод не по счету
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
	<title>X2</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
Детальное описание параметров:
<a href="http://webmoney.ru/rus/developers/interfaces/xml/purse2purse/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/purse2purse/index.shtml</a>
<br/>

<form action="" method="post">

	<label>номер перевода:</label>
	<input type="text" name="tranid" value="2"/>
	<br/>

	<label>номер кошелька с которого выполняется перевод (отправитель):</label>
	<input type="text" name="pursesrc" value=""/>
	<br/>

	<label>номер кошелька, но который выполняется перевод (получатель):</label>
	<input type="text" name="pursedest" value=""/>
	<br/>

	<label>переводимая сумма:</label>
	<input type="text" name="amount" value="0.01"/>
	<br/>

	<label>срок протекции сделки в днях:</label>
	<input type="text" name="period" value="0"/>
	<br/>

	<label>код протекции сделки:</label>
	<input type="text" name="pcode" value=""/>
	<br/>

	<label>описание оплачиваемого товара или услуги:</label>
	<input type="text" name="desc" value="тестирование X2 wmxi"/>
	<br/>

	<label>номер счета (в системе WebMoney), по которому выполняется перевод:</label>
	<input type="text" name="wminvid" value="0"/>
	<br/>

	<input type="submit" value="отправить перевод"/>
	<br/>

</form>

<pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre>
<!--pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre-->

<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		Номер перевода: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["tranid"], ENT_QUOTES); ?></b>
		Отправитель: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["pursesrc"], ENT_QUOTES); ?></b>
		Получатель: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["pursedest"], ENT_QUOTES); ?></b>
		Сумма: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["amount"], ENT_QUOTES); ?></b>
		Коммиссия: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["comiss"], ENT_QUOTES); ?></b>
		Тип перевода: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["opertype"], ENT_QUOTES); ?></b>
		Срок протекции: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["period"], ENT_QUOTES); ?></b>
		Номер счёта: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["wminvid"], ENT_QUOTES); ?></b>
		Описание: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["desc"], ENT_QUOTES); ?></b>
		Создан: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["datecrt"], ENT_QUOTES); ?></b>
		Изменён: <b><?= htmlspecialchars(@$transformed["w3s.response"]["operation"]["dateupd"], ENT_QUOTES); ?></b>
		Код ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		Описание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
