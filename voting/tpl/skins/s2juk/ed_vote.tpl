<script type="text/javascript">
 var vajax = new sack();
 function make_vote(mode){
  var form = document.getElementById('voteForm');
  var choice = -1;
  
  // Return true (to run normal mode) if AJAX failed
  if (vajax.failed)
  	return true;

  for (i=0;i<form.elements.length;i++) {
  	var elem = form.elements[i];
  	if (elem.type == 'radio') {
  		if (elem.checked == true) {
  			choice = elem.value;
  		}
  	}
  }	

  var voteid = form.voteid.value;
  if (mode && (choice < 0)) {
  	alert('Сначала необходимо выбрать вариант!');
  	return false;
  }	

  
  if (mode) { 
  vajax.requestFile = "{home}?action=plugin&plugin=voting&style=ajax&voted="+voteid+"&list=0&mode=vote&choice="+choice;
  } else {
  vajax.requestFile = "{home}?action=plugin&plugin=voting&style=ajax&voted="+voteid+"&list=0&mode=show";
  }
  vajax.method = 'GET';
  vajax.element = 'voting_ng';
  vajax.runAJAX();
  return false;
 }
</script>


<div id="voting_ng">
{votename}<br/><br/>
<form action="{home}?action=plugin&plugin=voting" method="post" id="voteForm">
<input type=hidden name="mode" value="vote" />
<input type=hidden name="voteid" value="{voteid}" />
<input type=hidden name="referer" value="{REFERER}" />
{votelines}<br/>
<input type="submit"  value="Голосувати!" onclick="return make_vote(1);" class="search4" />
</form>
</div>