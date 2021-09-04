
<!--[if lt IE 9]><script type="text/javascript" src="{tpl_url}/plugins/tags/skins/js/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="{tpl_url}/plugins/tags/skins/3d/js/tagcanvas.js"></script>
<script type="text/javascript">
 window.onload = function() {
	TagCanvas.textFont = 'Impact,"Arial Black",sans-serif';
	TagCanvas.textColour = '#00f';
	TagCanvas.outlineThickness = 5;
	TagCanvas.outlineOffset = 1;
	TagCanvas.outlineMethod = 'block';
	TagCanvas.maxSpeed = 0.06;
	TagCanvas.minBrightness = 0.1;
	TagCanvas.depth = 0.95;
	TagCanvas.pulsateTo = 0.2;
	TagCanvas.pulsateTime = 0.75;
	TagCanvas.decel = 0.9;
	TagCanvas.reverse = true;
	TagCanvas.hideTags = false;
	TagCanvas.shadowBlur = 2;
	TagCanvas.fadeIn = 800;
	try {
		TagCanvas.Start('myCanvas','weightTags', {textFont:null, textColour:null, weight: true});
	} catch(e) {
      // something went wrong, hide the canvas container
      document.getElementById('myCanvasContainer').style.display = 'none';
    }
  };
 </script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td><img border="0" src="{tpl_url}/images/2z_35.gif" width="7" height="36" /></td>
					<td style="background-image:url('{tpl_url}/images/2z_36.gif');" width="100%"><b>
							<font color="#FFFFFF">Облако тегов</font>
						</b></td>
					<td><img border="0" src="{tpl_url}/images/2z_38.gif" width="7" height="36" /></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td style="background-image:url('{tpl_url}/images/2z_56.gif');" width="7">&nbsp;</td>
					<td bgcolor="#FFFFFF" id="insertTagCloud">
						<canvas width="300" height="300" id="myCanvas">
							<p>Anything in here will be replaced on browsers that support the canvas element</p>
							<ul id="weightTags">
								{entries}
							</ul>
						</canvas>
					</td>
					<td style="background-image:url('{tpl_url}/images/2z_58.gif');" width="7">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td><img border="0" src="{tpl_url}/images/2z_60.gif" width="7" height="11" /></td>
					<td style="background-image:url('{tpl_url}/images/2z_61.gif');" width="100%"></td>
					<td><img border="0" src="{tpl_url}/images/2z_62.gif" width="7" height="11" /></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
