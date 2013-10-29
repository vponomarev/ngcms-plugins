<!-- List of news start here -->
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr  class="contHead" align="left">
<td width="5%">ID</td>
<td width="15%">Data</td>
<td width="5%">IP</td>
<td width="10%">Plugin</td>
<td width="10%">Item</td>
<td width="5%">DS</td>
<td width="15%">Action</td>
<!-- <td width="15%">Alist</td> -->
<td width="10%">User</td>
<td width="5%">Status</td>
<td width="20%">Text</td>
</tr>
{% for entry in entries %}
<tr align="left">
<td width="5%" class="contentEntry1">{{ entry.id }}</td>
<td width="15%" class="contentEntry1">{{ entry.date }}</td>
<td width="5%" class="contentEntry1">{{ entry.ip }}</td>
<td width="10%" class="contentEntry1">{{ entry.plugin }}</td>
<td width="10%" class="contentEntry1">{{ entry.item }}</td>
<td width="5%" class="contentEntry1">{{ entry.ds }}</td>
<td width="15%" class="contentEntry1">{{ entry.action }}</td>
<!--<td width="15%">{{ entry.alist }}</td>  -->
<td width="10%" class="contentEntry1"><a href="admin.php?mod=users&action=editForm&id={{ entry.userid }}"  />{{ entry.username }}</a></td>
<td width="5%" class="contentEntry1">{{ entry.status }}</td>
<td width="20%" class="contentEntry1">{{ entry.stext }}</td>
</tr>
{% else %}
<tr align="left">
<td calspan="10" class="contentEntry1">По вашему запросу ничего не найдено.</td>
</tr>
{% endfor %}
<tr>
<td width="100%" colspan="10">&nbsp;</td>
</tr>

<tr>
<td align="center" colspan="10" class="contentHead">{{ pagesss }}</td>
</tr>
</table>