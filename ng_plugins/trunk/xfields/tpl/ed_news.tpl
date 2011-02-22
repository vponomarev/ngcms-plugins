{% if (flags.tdata) %}
<tr><td width="100%" class="contentHead" colspan="2"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt="" />��������� ������ <span id="xf_profile"></span></td></tr>
<tr><td>
<table width="100%" id="tdataTable">
<thead>
<tr>
<td>#</td>
{% for entry in xtableHdr %}
<td>{{ entry.title }}</td>
{% endfor %}
<td>��������</td>
</tr>
</thead>
<tbody></tbody>
<tfoot>
<tr><td colspan="{{ (xtablecnt+2) }}"><input type="button" value="�������� ������.." onclick="tblLoadData(0);"/></td></tr>
</tfoot>
</table>
</td></tr>
{% endif %}
<tr><td width="100%" class="contentHead" colspan="2"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt="" />{{ lang['xfields_group_title'] }} <span id="xf_profile"></span></td></tr>
<tr><td>
<table width="100%">
{% for entry in entries %}
	<tr id="xfl_{{entry.id}}">
		<td valign="top" width="200">{{entry.title}}{% if entry.flags.required %} <b>(*)</b>{% endif %}:</td>
		<td valign="top">{{entry.input}}</td>
	</tr>
{% endfor %}
</table>
</td></tr>

<script type="text/javascript" language="javascript">
<!--
// XFields configuration profile mapping
var xfGroupConfig	= {{ xfGC }};
var xfCategories	= {{ xfCat }};
var xfList		= {{ xfList }};

var tblConfig		= {{ xtableConf }};
var tblData		= {{ xtableVal }};


function tblLoadData(initMode) {
	// Load body collection
	var trows = $("#tdataTable >tbody");

	var irows;
	if (initMode) {
		irows = tblData;
	} else {
		// Scan default values
		var irow = { '#id':'*' };
		for (var cfgRow in tblConfig) {
			irow[cfgRow] = tblConfig[cfgRow]['default'];
		}
		irows = [ irow ];
	}

	for (var dataRow in irows) {
		//alert('dataRow = '+dataRow);
		// Create new row
		var trow = $("<tr>").appendTo(trows);

		// Mark number
		$("<td>").html(irows[dataRow]['#id']).appendTo(trow);

		// Create elements
		for (var cfgRow in tblConfig) {
			// ** TEXT ELEMENT **
			if (tblConfig[cfgRow]['type'] == 'text') {
				var t = $("<td>").appendTo(trow);
				$("<input>").val(irows[dataRow][cfgRow]).appendTo(t);
			}

			// ** SELECT ELEMENT **
			if (tblConfig[cfgRow]['type'] == 'select') {
				var t = $("<td>").appendTo(trow);
				var s = $("<select>").appendTo(t);

				for (var opt in tblConfig[cfgRow]['options']) {
					$("<option>").val((tblConfig[cfgRow]['storekeys'])?opt:tblConfig[cfgRow]['options'][opt]).html(tblConfig[cfgRow]['options'][opt]).appendTo(s);
				}
				s.val(irows[dataRow][cfgRow]);
			}
		}
		var t = $("<td>").appendTo(trow);
		$("<a>")
			.html(
				$("<img>")
				.attr("src", "{{ skins_url }}/images/delete.gif")
			)
			.attr("href", "#")
			.bind("click", function() { $(this).parent().parent().remove(); return false; })
			.appendTo(t);
	}
}

function tblSaveData() {
	// Load body collection
	var trows = $("#tdataTable >tbody tr");

	// Fill original field numbers
	var num = 1;
	var fmatrix = [];

	for (var tc in tblConfig) {
		fmatrix[num++] = tc;
	}

	var tblRecs = [];
	for (var i = 0; i < trows.length; i++) {
		var trow = trows[i];
		var tblRec = { '#id' : trow.childNodes[0].innerHTML} ;

		for (var x=0; x < trow.childNodes.length; x++) {
			var cnode = trow.childNodes[x];
			if ((x > 0)&&(x < (trow.childNodes.length-1))) {
				tblRec[fmatrix[x]] = cnode.childNodes[0].value;
				if ((cnode.childNodes[0].value == '') && (tblConfig[fmatrix[x]]['required'])) {
					alert('�� ��������� ������������ ����!');
					return false;
				}

			}
		}
		//tblRec['#id'] = trow.childNodes[0].innerHTML;
		tblRecs.push(tblRec);
	}
	document.getElementById('xftable').value = json_encode(tblRecs);
	//alert(json_encode(tblRecs));

}

tblLoadData(1);


// Update visibility of XFields fields
function xf_update_visibility(cid) {
	// Show only fields for this category profile
	if ((xfCategories[cid] != '') && (xfGroupConfig[xfCategories[cid]])) {
		var xfGrp = xfGroupConfig[xfCategories[cid]];
		$("#xf_profile").text("[ "+xfCategories[cid]+" :: "+xfGroupConfig[xfCategories[cid]]['title']+" ]");
	} else {
		$("#xf_profile").text("");
	}


	//alert('XF update fieldList :: cat: '+cid+'; profile: '+xfCategories[cid]+'; list: '+xfGroupConfig[xfCategories[cid]]['entries']);
	for (var xfid in xfList) {
		var xf = xfList[xfid];
		//alert('check field: '+xf);

		// Show only fields for this category profile
		if ((xfCategories[cid] != '') && (xfGroupConfig[xfCategories[cid]])) {
			if (in_array(xf, xfGroupConfig[xfCategories[cid]]['entries'])) {
				//alert('< in_array');
				$("#xfl_"+xf).show();
			} else {
				$("#xfl_"+xf).hide();
			}
		} else {
			$("#xfl_"+xf).show();
		}
	}
}

// Manage fields after document is loaded
$(document).ready(function() {
	// Get current category
	var currentCategory = $("#catmenu").val();

	// decide groupName
	xf_update_visibility(currentCategory);

	// Catch change of #catmenu selector
	$("#catmenu").change(function(){
		//alert('Value changed: '+this.value);
		xf_update_visibility(this.value);
	});
});

$("#postForm").submit(function() { return tblSaveData(); });

-->
</script>
<input type="hidden" id="xftable" name="xftable" value=""/>
