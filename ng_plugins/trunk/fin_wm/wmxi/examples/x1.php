<?php
	require_once("_header.php");

	# ��������� � ��������� ������ �����
	if (count($_POST) > 0) {

		$response = $wmxi->X1(
			intval($_POST["orderid"]),    # ����� ����� � ������� ����� ��������; ����� ����� ����� ��� �����
			$_POST["customerwmid"],       # WMId ����������
			$_POST["storepurse"],         # ����� ��������, �� ������� ���������� �������� ����
			floatval($_POST["amount"]),   # ����� � ��������� ������ ��� ���������� ��������
			trim($_POST["desc"]),         # ������������ ������ �� 0 �� 255 ��������; ������� � ������ ��� ����� �� �����������
			trim($_POST["address"]),      # ������������ ������ �� 0 �� 255 ��������; ������� � ������ ��� ����� �� �����������
			intval($_POST["period"]),     # ����� ����� �� 0 �� 255; ���� 0 - ��������� ������ ��� ������ ����� �� ���������
			intval($_POST["expiration"])  # ����� ����� �� 0 �� 255; ���� 0 - ���� ������ �� ���������
		);

		# ��������������� ����� ������� � ���������. ������� ���������:
		# - XML-����� �������
		# - ���������, ������������ �� �����. �� ��������� ������������ UTF-8
		$structure = $parser->Parse($response, DOC_ENCODING);

		# ����������� ������� ��������� � ����� ������� ��� �������.
		# �� ������������� ��������� ����� �������������� � � �����������, ���� �� ��������
		# ��������� ���������� ����� (��������, ������ ����������)
		# ���� ���������� � ���������� XML-����� ������ ���, �� ������ �������� �����
		# ���������� � false - � ����� ������ ��������� ������ ����� ����������
		$transformed = $parser->Reindex($structure, true);

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X1</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	��������� �������� ����������:
	<a href="http://webmoney.ru/rus/developers/interfaces/xml/issueinvoice/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/issueinvoice/index.shtml</a>
	<br />

	<form action="" method="post">

		<label>����� ����� � ������� ����� ��������; ����� ����� ����� ��� �����:</label>
		<input type="text" name="orderid" value="1" />
		<br/>

		<label>WMId ����������:</label>
		<input type="text" name="customerwmid" value="" />
		<br/>

		<label>����� ��������, �� ������� ���������� �������� ����:</label>
		<input type="text" name="storepurse" value="" />
		<br/>

		<label>����� �����, ������������ ��� ������ ����������:</label>
		<input type="text" name="amount" value="0.01" />
		<br/>

		<label>�������� ������ ��� ������, �� ������� ������������ ����:</label>
		<input type="text" name="desc" value="������������ X1 wmxi" />
		<br/>

		<label>����� �������� ������:</label>
		<input type="text" name="address" value="��� ����� �� ��� � �� �����" />
		<br/>

		<label>����������� ���������� ���� ��������� ������ � ���� ��� ������ �����:</label>
		<input type="text" name="period" value="1" />
		<br/>

		<label>����������� ���������� ���� ������ ����� � ����:</label>
		<input type="text" name="expiration" value="1" />
		<br/>

		<input type="submit" value="�������� ����" />
		<br/>

	</form>

	<pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre>
	<pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre>
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<pre><!-- ������ � ���������� �������� ������������� ������� ����� ��������� ������ � ������� -->
		����� �����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["orderid"], ENT_QUOTES); ?></b>
		����������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["customerwmid"], ENT_QUOTES); ?></b>
		������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["storepurse"], ENT_QUOTES); ?></b>
		�����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["amount"], ENT_QUOTES); ?></b>
		��������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["desc"], ENT_QUOTES); ?></b>
		�����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["address"], ENT_QUOTES); ?></b>
		���� ���������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["period"], ENT_QUOTES); ?></b>
		���� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["expiration"], ENT_QUOTES); ?></b>
		���������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["state"], ENT_QUOTES); ?></b>
		������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["datecrt"], ENT_QUOTES); ?></b>
		������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["invoice"]["dateupd"], ENT_QUOTES); ?></b>
		��� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		�������� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
