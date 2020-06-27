
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

<canvas width="300" height="300" id="myCanvas">
  <p>Anything in here will be replaced on browsers that support the canvas element</p>
  <ul id="weightTags">
   {entries}
  </ul>
 </canvas>
 