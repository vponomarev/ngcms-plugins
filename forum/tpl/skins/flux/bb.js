// Startup variables
var imageTag = false;
var theSelection = false;

// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla') != -1) && (clientPC.indexOf('spoofer') == -1)
&& (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera') == -1)
&& (clientPC.indexOf('webtv') == -1) && (clientPC.indexOf('hotjava') == -1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win") != -1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac") != -1);

// Helpline messages
h_help = "Подсказка: Можно быстро применить стили к выделенному тексту"
b_help = "Жирный текст: [b]текст[/b]";
i_help = "Наклонный текст: [i]текст[/i]";
u_help = "Подчёркнутый текст: [u]текст[/u]";
ss_help = "Зачеркнутый текст: [s]текст[/s]";
q_help = "Цитата: [quote]текст[/quote]";
p_help = "Код PHP: [code=php]код[/code=php]";
m_help = "Вставить картинку: [img]http://image_url[/img]";
w_help = "Вставить ссылку: [url]http://url[/url] или [url=http://url]текст ссылки[/url]";
a_help = "Закрыть все открытые теги bbCode";
s_help = "Цвет шрифта: [color=red]текст[/color]";
si_help = "Размер текста: [size=15]текст[/size]";

// Define the bbCode tags
bbcode = new Array();
bbtags = new Array('[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[quote]', '[/quote]', '[code=php]', '[/code=php]', '[img]', '[/img]', '[url]', '[/url]');
imageTag = false;


//create smiles buttons. id - id of container for smiles
function getSmiles(id) {
	//array with  smiles
	var advsmiles = new Array('wall', 'baks', 'bis', 'girl', 'gordo', 'gy', 'girlgy', 'haha', 'helpme', 'hm', 'hnyk', 'idea', 'hrap', 'ispug', 'jahu', 'girlhnyk', 'mat', 'mda', 'mdya', 'or', 'pardon', 'plak', 'plaksa', 'plaksa2', 'rzhu', 'sad', 'sarkastik', 'sorri', 'stranno', 'tanz', 'umora', 'ura', 'vopros', 'wink', 'wutka', 'ww', 'yeh', 'zharko', 'zlaya', 'zloy');
	var container = document.getElementById(id);
	if (container !== 'undefined') {
		for (i = 0; i < advsmiles.length; i++) {
			container.innerHTML = container.innerHTML + '<div><a href="javascript://" onClick="emoticon(\':' + advsmiles[i] + ':\');"><img src="/sys/img/smiles/'
				+ advsmiles[i] + '.gif" /></a></div>';
		}
	}
	return;
}


// Shows the help messages in the helpline window
function helpline(help) {
	document.getElementById("sendForm").helpbox.value = eval(help + "_help");
}


// Replacement for arrayname.length property
function getarraysize(thearray) {
	for (i = 0; i < thearray.length; i++) {
		if ((thearray[i] == "undefined") || (thearray[i] == "") || (thearray[i] == null))
			return i;
	}
	return thearray.length;
}

// Replacement for arrayname.push(value) not implemented in IE until version 5.5
// Appends element to the array
function arraypush(thearray, value) {
	thearray[getarraysize(thearray)] = value;
}

// Replacement for arrayname.pop() not implemented in IE until version 5.5
// Removes and returns the last element of an array
function arraypop(thearray) {
	thearraysize = getarraysize(thearray);
	retval = thearray[thearraysize - 1];
	delete thearray[thearraysize - 1];
	return retval;
}


function checkForm() {

	formErrors = false;

	if (document.getElementById("sendForm").message.value.length < 2) {
		formErrors = "Вы должны ввести текст сообщения";
	}

	if (formErrors) {
		alert(formErrors);
		return false;
	} else {
		bbstyle(-1);
		//formObj.preview.disabled = true;
		//formObj.submit.disabled = true;
		return true;
	}
}

function emoticon(text) {
	var txtarea = document.getElementById("sendForm").message;
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value += text;
		txtarea.focus();
	}
}

function bbfontstyle(bbopen, bbclose) {
	var txtarea = document.getElementById("sendForm").message;

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += bbopen + bbclose;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = bbopen + theSelection + bbclose;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0)) {
		mozWrap(txtarea, bbopen, bbclose);
		return;
	}
	else {
		txtarea.value += bbopen + bbclose;
		txtarea.focus();
	}
	storeCaret(txtarea);
}


function bbstyle(bbnumber) {
	var txtarea = document.getElementById("sendForm").message;

	txtarea.focus();
	donotinsert = false;
	theSelection = false;
	bblast = 0;

	if (bbnumber == -1) { // Close all open tags & default button names
		while (bbcode[0]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			buttext = eval('document.getElementById("sendForm").addbbcode' + butnumber + '.value');
			eval('document.getElementById("sendForm").addbbcode' + butnumber + '.value ="' + buttext.substr(0, (buttext.length - 1)) + '"');
		}
		imageTag = false; // All tags are closed including image tags :D
		txtarea.focus();
		return;
	}

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text; // Get text selection
		if (theSelection) {
			// Add tags around selection
			document.selection.createRange().text = bbtags[bbnumber] + theSelection + bbtags[bbnumber + 1];
			txtarea.focus();
			theSelection = '';
			return;
		}
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0)) {
		mozWrap(txtarea, bbtags[bbnumber], bbtags[bbnumber + 1]);
		return;
	}

	// Find last occurance of an open tag the same as the one just clicked
	for (i = 0; i < bbcode.length; i++) {
		if (bbcode[i] == bbnumber + 1) {
			bblast = i;
			donotinsert = true;
		}
	}

	if (donotinsert) {		// Close all open tags up to the one just clicked & default button names
		while (bbcode[bblast]) {
			butnumber = arraypop(bbcode) - 1;
			txtarea.value += bbtags[butnumber + 1];
			buttext = eval('document.getElementById("sendForm").addbbcode' + butnumber + '.value');
			eval('document.getElementById("sendForm").addbbcode' + butnumber + '.value ="' + buttext.substr(0, (buttext.length - 1)) + '"');
			imageTag = false;
		}
		txtarea.focus();
		return;
	} else { // Open tags

		if (imageTag && (bbnumber != 16)) {		// Close image tag before adding another
			txtarea.value += bbtags[17];
			lastValue = arraypop(bbcode) - 1;	// Remove the close image tag from the list
			document.getElementById("sendForm").addbbcode16.value = "Img";	// Return button back to normal state
			imageTag = false;
		}

		// Open tag
		txtarea.value += bbtags[bbnumber];
		if ((bbnumber == 16) && (imageTag == false)) imageTag = 1; // Check to stop additional tags after an unclosed image tag
		arraypush(bbcode, bbnumber + 1);
		eval('document.getElementById("sendForm").addbbcode' + bbnumber + '.value += "*"');
		txtarea.focus();
		return;
	}
	storeCaret(txtarea);
}

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, open, close) {
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2)
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0, selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + open + s2 + close + s3;
	return;
}

// Insert at Claret position.
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

/* paste smile */
/*
 function smile(img) {
 var txtarea = document.getElementById("sendForm").message;
 txtarea.focus();

 txtarea.i
 }
 */


var selection = false; // Selection data


function emoticon_wospaces(text) {
	var txtarea = document.getElementById("sendForm").message;
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value += text;
		txtarea.focus();
	}
}

// Catching selection
function catchSelection() {
	if (window.getSelection) {
		selection = window.getSelection().toString();
	}
	else if (document.getSelection) {
		selection = document.getSelection();
	}
	else if (document.selection) {
		selection = document.selection.createRange().text;
	}
}

// Putting username to the post box
function putName(name) {
	emoticon_wospaces('[b]' + name + '[/b]\n');
	document.getElementById("sendForm").message.focus();
	return;
}

// Putting selection to the post box
function quoteSelection(name) {
	if (selection) {
		emoticon_wospaces('[quote="' + name + '"]' + selection + '[/quote]\n');
		selection = '';
		document.getElementById("sendForm").message.focus();
		return;
	}
	else {
		alert(l_no_text_selected);
		return;
	}
}

/* add file field */
function addFileField(elementId) {
	var container = document.getElementById(elementId);
	var fields = container.getElementsByClassName('attachField');
	var cntFields = fields.length + 1;
	if (cntFields <= 5) {
		if (cntFields < 1) {
			cntFields = 1;
		}
		var new_div = document.createElement('div');
		new_div.innerHTML = '&nbsp;[' + cntFields + ']&nbsp;';
		new_div.innerHTML += '<input type="file" id="attach' + cntFields + '" name="attach' + cntFields + '" class="attachField" onChange="getFile(' + cntFields + ')" /><span id="attachMeta' + cntFields + '"></span>';
		container.appendChild(new_div);
	}
}

/* get and identific file */
function getFile(n) {
	var t = document.getElementById('attach' + n);
	if (t.value) {
		ext = new Array('png', 'jpg', 'gif', 'jpeg');
		var img = t.value.replace(/\\/g, '/');
		var pic = img.toLowerCase();
		var ok = 0;
		for (i = 0; i < ext.length; i++) {
			m = pic.indexOf('.' + ext[i]);
			if (m != -1) {
				ok = 1;
				break;
			}
		}
		if (ok == 1) {
			var code = '{IMAGE' + n + '}';
			document.getElementById('attachMeta' + n).innerHTML = '&nbsp;<input type="text" readonly value="' + code + '" title="Вставьте этот код в любое место сообщения" size="' + (code.length) + '" style="font-family:monospace;color:#ff8e00;" />';
		} else {
			document.getElementById('attach' + n).innerHTML = '';
		}
	} else {
		document.getElementById('attach' + n).innerHTML = '';
	}
} 
