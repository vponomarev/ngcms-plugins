<?php
	require_once("_header.php");

	# Получение и обработка данных формы
	if (count($_POST) > 0) {

		$response = $wmxi->X4(
			$_POST["purse"],               # номер кошелька для оплаты на который которого выписывался счет
			intval($_POST["wminvid"]),     # целое число > 0
			intval($_POST["orderid"]),     # номер счета в системе учета магазина; любое целое число без знака
			trim($_POST["datestart"]),     # ГГГГММДД ЧЧ:ММ:СС
			trim($_POST["datefinish"])     # ГГГГММДД ЧЧ:ММ:СС
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
		$transformed = $parser->Reindex($structure, false);

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X4</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	Детальное описание параметров:
	<a href="http://webmoney.ru/rus/developers/interfaces/xml/invhistory/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/invhistory/index.shtml</a>
	<br />

	<form action="" method="post">

		<label>номер кошелька для оплаты на который которого выписывался счет :</label>
		<input type="text" name="purse" value="" />
		<br/>

		<label>номер счета (в системе WebMoney):</label>
		<input type="text" name="wminvid" value="0" />
		<br/>

		<label>номер счета:</label>
		<input type="text" name="orderid" value="0" />
		<br/>

		<label>минимальное время и дата создания счета:</label>
		<input type="text" name="datestart" value="20070418 18:00:00" />
		<br/>

		<label>максимальное время и дата создания счета:</label>
		<input type="text" name="datefinish" value="20070420 18:00:00" />
		<br/>

		<input type="submit" value="получить список" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		<?
			$items = @$structure["0"]["node"]["1"]["node"];
			$items = is_array($items) ? $items : array();
			foreach($items as $k => $v) {
				$vv = $parser->Reindex($v["node"], true);
		?>

			<b>№ <?=$k;?></b>
			Кошелёк магазина: <b><?=htmlspecialchars(@$vv["storepurse"], ENT_QUOTES); ?></b>
			Корреспондент счёта: <b><?=htmlspecialchars(@$vv["customerwmid"], ENT_QUOTES); ?></b>
			Кошелёк корреспондента: <b><?=htmlspecialchars(@$vv["customerpurse"], ENT_QUOTES); ?></b>
			Сумма: <b><?=htmlspecialchars(@$vv["amount"], ENT_QUOTES); ?></b>
			Адрес: <b><?=htmlspecialchars(@$vv["address"], ENT_QUOTES); ?></b>
			Описание: <b><?=htmlspecialchars(@$vv["desc"], ENT_QUOTES); ?></b>
			Статус: <b><?=htmlspecialchars(@$vv["state"], ENT_QUOTES); ?></b>
			Создан: <b><?=htmlspecialchars(@$vv["datecrt"], ENT_QUOTES); ?></b>
			Изменён: <b><?=htmlspecialchars(@$vv["dateupd"], ENT_QUOTES); ?></b>
		<? } ?>

		Код ошибки: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		Описание ошибки: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
