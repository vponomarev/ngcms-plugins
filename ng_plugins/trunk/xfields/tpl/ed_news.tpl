<tr><td width="100%" class="contentHead" colspan="2"><img src="/engine/skins/default/images/nav.gif" hspace="8" alt="" />{l_xfields_group_title} <span id="xf_profile"></span></td></tr>
<tr><td>
<table>
{entries}
</table>
</td></tr>

<script type="text/javascript" language="javascript">
<!--
// XFields configuration profile mapping
var xfGroupConfig = {xfGC};
var xfCategories  = {xfCat};
var xfList        = {xfList};

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

-->
</script>