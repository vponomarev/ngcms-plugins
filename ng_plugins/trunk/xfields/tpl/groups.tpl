<style type="text/css">
.xListEntry TD {
	padding: 5px 0 1px 5px;
	background-color: #ffffff;
	border-bottom: 1px solid #f0f0f0;
	font: normal 11px verdana, tahoma, sans-serif;
	color: #555;
	text-align: left;
}
.contNav {
    padding: 10px 0 10px 10px;
    background: #eaf0f7 url({skins_url}/images/1px.png) repeat-x;
    color: #152F59; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size:13px;
    border-top: 1px solid #dfe5ec;
    border-bottom: 3px solid #dfe5ec; margin-top: 10px;
}
.btnMenu {
	font: 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal;
	float: left;
	color: #555;
}
.btnInactive {
	width: 170px;
	float: left;
	margin-top: 5px;
	margin-bottom: 5px;
	padding: 7px;
	padding-left: 35px;
	border: 1px solid #dbe4ed;
	cursor: pointer;
	background: #f6f8fb url("{skins_url}/images/no_plug.png") no-repeat;
	background-position: 8px center;
}
.btnInactive A { font: normal 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal; text-decoration: none; }

.btnActive {
	width: 170px;
	float: left;
	margin-top: 5px;
	margin-bottom: 5px;
	padding: 7px;
	padding-left: 35px;
	border: 1px solid #54a1c1;
	cursor: pointer;
	background: #FFFFFF url("{skins_url}/images/yes_plug.png") no-repeat;
	background-position: 8px center;
}



.btnActive A { font: normal 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal; text-decoration: none; }

.btnSeparator {float: left; width: 10px;}
.btnDelimiter {float: left; width: 50px;}


.fldHead {
width:100%; height: 200px; border: #BFBFBF 1px solid; overflow: auto;
}
.fldList TD {
border: #CFCFCF 1px solid;
}

.fldList TR:hover { background-color: #EAF0F7; border: #CFCFCF 1px solid;}


#loading-layer {
	display : none;
	width : 180px;
	height : 40px;
	background : #fff;
	text-align : center;
	border : 1px solid #eeeeef;
}

</style>
<script type="text/javascript" src="{admin_url}/includes/js/ajax.js"></script>
<script type="text/javascript" src="{admin_url}/includes/js/admin.js"></script>

<div id="loading-layer"><img src="{tpl_url}/images/loading.gif" alt="" /></div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="content">
<tbody>
<tr>
<td colspan="5" class="contentHead" width="100%"><img src="{skins_url}/images/nav.gif" hspace="8">{l_config_text}: xfields</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%">

<tbody><tr>
<td colspan="8" class="contNav" width="100%">
<div id="btnMenu">
<span class="{bclass.news}" onclick='document.location="?mod=extra-config&plugin=xfields&section=news";'>Новости: список полей</span><span class="btnSeparator">&nbsp;</span>
<span class="{bclass.grp.news}" onclick='document.location="?mod=extra-config&plugin=xfields&section=grp.news";'>Новости: группы</span><span class="btnDelimiter">&nbsp;</span>
<!--
<span class="{bclass.users}" onclick='document.location="?mod=extra-config&plugin=xfields&section=users";'>Пользователи: список полей</span><span class="btnSeparator">&nbsp;</span>
<span class="{bclass.grp.users}">Пользователи: группы</span>
-->
</div>
&nbsp;
</td>
</tr>
</tbody>
</table>



<table width="100%">
<tr align="left">
<td class="contentHead" width="200" colspan="2"><b>Группы</b></td>
<td class="contentHead" colspan="3"><b>Поля, находящиеся в группе (<span id="grpName">n/a</span>)</b></td>
</tr>
<tr>
<td width="200" colspan="2"><select size=15 style="width:100%; border: #BFBFBF 1px solid;" id="gList" name="gList" onclick="selectGroupList(0);" onkeyup="selectGroupList(0);"></select></td>
<td colspan="3">
<div class="fldHead">
<table width="100%" class="fldList" id="fList">
<tr>
 <td width="50">date</td>
 <td>Дата добавления новости</td>
 <td width="90" align="right" nowrap>(up) (down) (del)</td>
</tr>
<tr>
 <td width="50">&nbsp;date</td>
 <td>Дата добавления новости</td>
 <td width="90" align="right" nowrap>(up) (down) (del)</td>
</tr>
</table>
</div>
</td>
</tr>
<tr class="contRow1">
<td width="70" nowrap="nowrap">ID группы:</td>
<td><input id="edGrpId" style="width: 200px; height: 15px;"> <input type="button" id="btnDelGroup" class="button" value="Удалить"/></td>
<td width="90">Добавить поле:</td><td><select style="width: 200px;  height: 19px; border: #BFBFBF 1px solid;" id="selectFList"></select> <input type="button" id="btnAddField" class="button" value="Добавить"/></td>
</tr>
<tr class="contRow1">
<td width="70" nowrap="nowrap">Имя группы:</td>
<td><input id="edGrpName" style="width: 200px; height: 15px;"></td>
</tr>
</table>

<table width="100%">
<tr>&nbsp;</tr>
<tr align="center">
<td class="contentEdit" valign="top" width="100%">
<input type="button" id="btnModGroup" class="button" value="Добавить"/>
</td>
</tr>
</table>
{entries}

</form>


<script type="text/javascript" language="javascript">
<!--
// Connect to configuration data
var gConfig	= {json.groups.config};
var fConfig	= {json.fields.config};

var grpList	= document.getElementById('gList');
var fldList	= document.getElementById('fList');

var gListValue	= 0;
var gFldValue	= 0;

function selectGroupList(force) {
	if ((force == 1) || (grpList.value != gListValue)) {
		document.getElementById('grpName').innerHTML = (grpList.value != '')?grpList.value:'n/a';
		while (fldList.rows.length)
			fldList.deleteRow(0);

		grpList.parentNode.enabled = false;
		if (grpList.value != '') {
			var rowNo = 0;
			for (var i in gConfig[grpList.value]['entries']) {
			        var fldName = gConfig[grpList.value]['entries'][i];

			        var r = fldList.insertRow(-1);
			        r.tag = rowNo++;
			        //r.onclick = function() { alert(this.tag); }
			        var tl = document.createElement('td');
			        tl.innerHTML = fldName;
			        tl.width = 50;
			        r.appendChild(tl);

			        tl = document.createElement('td');
			        tl.innerHTML = fConfig[fldName]?fConfig[fldName]['title']:'n/a';
			        r.appendChild(tl);

			        tl = document.createElement('td');
			        tl.width = 70;
			        tl.style.align = 'right';
			        tl.innerHTML =	'<img src="/engine/skins/default/images/up.gif" onclick="fieldModifyRequest(this.parentNode.parentNode.tag, 1);"/> '+
			        				'<img src="/engine/skins/default/images/down.gif" onclick="fieldModifyRequest(this.parentNode.parentNode.tag, 2);"/> '+
			        				'<img src="/engine/skins/default/images/delete.gif" onclick="fieldModifyRequest(this.parentNode.parentNode.tag, 3);"/>';
			        r.appendChild(tl);
			}
		}

		grpList.parentNode.enabled = true;
		gListValue = grpList.value;

		document.getElementById('edGrpId').value = gListValue;
		document.getElementById('edGrpName').value = (gListValue != '')?gConfig[gListValue]['title']:'';
		if (gListValue == '') {
			document.getElementById('edGrpId').readOnly = false;
			document.getElementById('edGrpId').style.backgroundColor= '#FFFFFF';
			document.getElementById('btnModGroup').value = "Добавить";
		} else {
			document.getElementById('edGrpId').readOnly = true;
			document.getElementById('edGrpId').style.backgroundColor= '#EAF0F7';
			document.getElementById('btnModGroup').value = "Сохранить";
		}
	}

}

function fieldModifyRequest(id, action) {
	// Check if we're in EDIT  mode
	if (!document.getElementById('edGrpId').readOnly) {
		alert('Group is not selected');
		return;
	}

	var fn = gConfig[gListValue]['entries'][id];
	var fa = 'fld'+((action==1)?'Up':((action==2)?'Down':'Del'));
	//alert('FieldName ('+id+')['+gListValue+']: '+fn+ '; action: '+fa);
	//return;

	rpcRequest(
		'plugin.xfields.group.modify',
		{
			'action' : fa,
			'utoken' : 'UTOKEN',
			'id'	 : document.getElementById('edGrpId').value,
			'field'	 : fn,
		});

}

function drawGroupList(gID) {
	grpList.options.length = 0;

	for (var i in gConfig) {
		var o = document.createElement('option');
		o.value=i;
		o.text = i + ' :: '+gConfig[i]['title'];
		grpList.options[grpList.options.length] = o;
	}
	var o = document.createElement('option');
	o.value = '';
	o.text  = '** новая группа **';
	grpList.options[grpList.options.length] = o;
	grpList.value = gID;
}

function generateFieldList() {
	var items = document.getElementById('selectFList');
	items.options.length = 0;
	for (var i in fConfig) {
		var o = document.createElement('option');
		o.value = i;
		o.text = i + ' :: ' + fConfig[i]['title'];
	//	alert('ADD');
		items.options.add(o);
	}
}

function initEvents() {
	document.getElementById('btnAddField').onclick = function() {
		var value = document.getElementById('selectFList').value;
		if (gListValue != '') {
			// Check if field is already in list
			var dup = 0;
			for (var i in gConfig[gListValue]['entries']) {
				if (gConfig[gListValue]['entries'][i] == value)
					dup = 1;
			}

			if (dup) {
				alert('Duplicate entry');
			} else {
				gConfig[gListValue]['entries'][gConfig[gListValue]['entries'].length] = value;
				selectGroupList(1);
			}
		}
	}
	document.getElementById('btnModGroup').onclick = function() {
		rpcRequest(
			'plugin.xfields.group.modify',
			{
				'action' : 'grp'+(document.getElementById('edGrpId').readOnly?'Edit':'Add'),
				'utoken' : 'UTOKEN',
				'id'	 : document.getElementById('edGrpId').value,
				'name'	 : document.getElementById('edGrpName').value
			});
	}
	document.getElementById('btnDelGroup').onclick = function() {
		// Check if we're in EDIT  mode
		if (!document.getElementById('edGrpId').readOnly) {
			alert('Nothing to delete!');
			return;
		}

		rpcRequest(
			'plugin.xfields.group.modify',
			{
				'action' : 'grpDel',
				'utoken' : 'UTOKEN',
				'id'	 : document.getElementById('edGrpId').value
			});
	}

	document.getElementById('btnAddField').onclick = function() {
		// Check if we're in EDIT  mode
		if (!document.getElementById('edGrpId').readOnly) {
			alert('Group is not selected');
			return;
		}

		rpcRequest(
			'plugin.xfields.group.modify',
			{
				'action' : 'fldAdd',
				'utoken' : 'UTOKEN',
				'id'	 : document.getElementById('edGrpId').value,
				'field'	 : document.getElementById('selectFList').value,
			});
	}

}

function rpcRequest(method, params) {
 //var dOut = json_encode(dData);

 var linkTX = new sack();
 linkTX.requestFile = 'rpc.php';
 linkTX.setVar('json', '1');
 linkTX.setVar('methodName', method);
 linkTX.setVar('params', json_encode(params));
 linkTX.method='POST';
 linkTX.onComplete = function() {
	if (linkTX.responseStatus[0] == 200) {
		var resTX;
        try {
  	 		resTX = eval('('+linkTX.response+')');
  		} catch (err) { alert('{l_fmsg.save.json_parse_error} '+linkTX.response); }

  		// First - check error state
  		if (!resTX['status']) {
  			// ERROR. Display it
  			alert('Error ('+resTX['errorCode']+'): '+resTX['errorText']);
  		} else {
  			//alert('Request complete, answer: '+resTX['data']+'; '+typeof(resTX['config']));
  			if (typeof(resTX['config'])=='object') {
  				gConfig = resTX['config'];
  				drawGroupList(gListValue);
  				selectGroupList(1);
			}
  		}
  	} else {
  		alert('{l_fmsg.save.httperror} '+linkTX.responseStatus[0]);
	}
 }
 linkTX.onShow();
 linkTX.runAJAX();
}


initEvents();
drawGroupList(0);
grpList.selectedIndex = 0;
selectGroupList(0);
generateFieldList();
//rpcRequest('plugin.xfields.demo', { 'action' : 'add', 'name' : 'infomania'});
</script>