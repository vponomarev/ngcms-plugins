<tr>
 <td width="100%" class="contentHead" colspan="2"><img src="{admin_url}/skins/default/images/nav.gif" hspace="8" alt="" />���� �������</td>
</tr>
<tr>
 <td width="100%" class="contentEntry1">
  <table>
   <tr>
    <td>������ �����:<br/><small>����������� ����� �������</small></td>
    <td><input style="width: 300px;" name="tags" id="pTags"/></td>
   </tr>
  </table>
 </td>
</tr>
<script language="javascript" type="text/javascript">
// INIT NEW SUGGEST LIBRARY [ call only after full document load ]
var aSuggest = new ngSuggest('pTags',
								{
									'localPrefix'	: '{localPrefix}',
									'reqMethodName'	: 'plugin.tags.suggest',
									'lId'		: 'suggestLoader',
									'hlr'		: 'true',
									'iMinLen'	: 1,
									'stCols'	: 2,
									'stColsClass': [ 'cleft', 'cright' ],
									'stColsHLR'	: [ true, false ],
									'listDelimiter' : ',',
								}
							);

</script>
