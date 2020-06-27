	{% if (entriesImg) %}
	<link rel="stylesheet" href="{{tpl_url}}/css/picture-slides.css" type="text/css">
	<script type="text/javascript" src="{{tpl_url}}/js/PictureSlides-jquery-2.0.js"></script>
	<script type="text/javascript">
		jQuery.PictureSlides.set({
			// Switches to decide what features to use
			useFadingIn : true,
			useFadingOut : true,
			useFadeWhenNotSlideshow : true,
			useFadeForSlideshow : true,
			useDimBackgroundForSlideshow : true,
			loopSlideshow : false,
			usePreloading : true,
			useAltAsTooltip : true,
			useTextAsTooltip : false,
			
			// Fading settings
			fadeTime : 500, // Milliseconds	
			timeForSlideInSlideshow : 2000, // Milliseconds

			// At page load
			startIndex : 1,	
			startSlideShowFromBeginning : true,
			startSlideshowAtLoad : false,
			dimBackgroundAtLoad : false,

			// Large images to use and thumbnail settings
			images : [
				{% for entry in entriesImg %}
				{
					image : "{{entry.home}}/uploads/zboard/thumb/{{entry.filepath}}", 
					alt : "{{entry.filepath}}",
					text : "{{entry.filepath}}",
					url : "{{entry.home}}/uploads/zboard/{{entry.filepath}}"
				}{% if not (loop.last) %},{% endif %}
				{% endfor %}
			],
			thumbnailActivationEvent : "click",

			// Classes of HTML elements to use
			mainImageClass : "picture-slides-image", // Mandatory
			mainImageFailedToLoadClass : "picture-slides-image-load-fail",
			imageLinkClass : "picture-slides-image-link",
			fadeContainerClass : "picture-slides-fade-container",
			imageTextContainerClass : "picture-slides-image-text",
			previousLinkClass : "picture-slides-previous-image",
			nextLinkClass : "picture-slides-next-image",
			imageCounterClass : "picture-slides-image-counter",
			startSlideShowClass : "picture-slides-start-slideshow",
			stopSlideShowClass : "picture-slides-stop-slideshow",
			thumbnailContainerClass: "picture-slides-thumbnails",
			dimBackgroundOverlayClass : "picture-slides-dim-overlay"
		});
	</script>
	{% endif %}

					<div class="post clearfix">
						<div class="entry">
							<h2>{{announce_name}}</h2>
							<div class="tag">
								<a class="tag-{{cid}}" href="{{catlink}}">{{cat_name}}</a>
							</div>
							<div class="desc">{{date|date("m-d-Y H:i")}} / {{announce_author}}</div>
							<div class="view">
								<a title="{{views}}">{{views}}</a>
							</div>
						</div>
					</div>
					
	{% if (entriesImg) %}				
	<div id="container">

		<div class="picture-slides-container">
			<div class="picture-slides-fade-container">
				<a class="picture-slides-image-link">
					<span class="picture-slides-image-load-fail">The image failed to load image.</span>
					<img class="picture-slides-image" src="{{tpl_url}}/img/noimage_b.png" width="202px" />
				</a>
			</div>
			
			<ul class="picture-slides-thumbnails">
			{% for entry in entriesImg %}
            <li>
  	    	    <a href="{{entry.home}}/uploads/zboard/{{entry.filepath}}"><img src="{{entry.home}}/uploads/zboard/thumb/{{entry.filepath}}" /></a>
  	    	</li>
			{% endfor %}
			</ul>
		</div>
	</div>
	
	<div class="picture-slides-dim-overlay"></div>
	{% endif %}				
					
<div class="text">
<p>{{announce_description}}</p>
<p>{{announce_contacts}}</p>
</div>