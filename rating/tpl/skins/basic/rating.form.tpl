<script type="text/javascript">
	var ajax = new sack();
	function rating(rating, post_id) {
		ajax.onShow("");
		ajax.setVar("rating", rating);
		ajax.setVar("post_id", post_id);
		ajax.requestFile = '{ajax_url}';
		ajax.method = 'GET';
		ajax.element = 'ratingdiv_' + post_id;
		ajax.runAJAX();
	}
</script>

<div id="ratingdiv_{post_id}">
	<div class="rating" style="float:left;">
		<ul class="uRating">
			<li class="r{rating}">{rating}</li>
			<li><a href="#" title="1" class="r1u" onclick="rating('1', '{post_id}'); return false;">1</a></li>
			<li><a href="#" title="2" class="r2u" onclick="rating('2', '{post_id}'); return false;">2</a></li>
			<li><a href="#" title="3" class="r3u" onclick="rating('3', '{post_id}'); return false;">3</a></li>
			<li><a href="#" title="4" class="r4u" onclick="rating('4', '{post_id}'); return false;">4</a></li>
			<li><a href="#" title="5" class="r5u" onclick="rating('5', '{post_id}'); return false;">5</a></li>
		</ul>
	</div>
	<div class="rating" style="float:left; padding-top:2px;">&nbsp;({l_rating_votes} {votes})</div>
</div>