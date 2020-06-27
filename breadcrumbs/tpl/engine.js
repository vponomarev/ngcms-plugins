//
// Basic JS functions for BixBite CMS core
$(function() {

    $('.scrollTop').on('click', function(){
        return $('html, body').animate({ scrollTop: 0 }, 888);
    });

    // for bootstrap element
    $('input[type=checkbox]').each(function() {
        if ( $(this).prop('checked') == true ) {
            $(this).parent().addClass('active');
        } else {
            $(this).parent().removeClass('active');
        }
    });

    // Select/unselect all
    $('table .select-all').on('click', function() {
        $(this).parents('table').find('input:checkbox:not([disabled])').prop('checked', $(this).prop('checked'));
    });

    // Process spoilers
    $('.sp-head').on('click', function() {
        if ($(this).hasClass("expanded")) {
            $(this).removeClass("expanded");
            $(this).next('.sp-body').slideUp("fast");
        } else {
            $(this).addClass("expanded");
            $(this).next('.sp-body').slideDown("fast");
        }

    });

    // Reload captcha
    $('#img_captcha').on('click', function() {
        reload_captcha();
    });

});

// Reload captcha
function reload_captcha() {
    $('#img_captcha').attr('src', $('#img_captcha').attr('src').replace(/(rand=)[0\.?\d*]+/, '$1' + Math.random()));
}

/* cookie style core */
function setCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function deleteCookie(name) {
    setCookie(name,"",-1);
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

// ngShowLoading
function ngShowLoading() {
    var setX = ( $(window).width() - $("#loading-layer").width() ) / 2;
    var setY = ( $(window).height() - $("#loading-layer").height() ) / 2;

    $("#loading-layer").css( {
        left : setX + "px",
        top : setY + "px",
        position : 'fixed',
        zIndex : '99'
    });

    $("#loading-layer").fadeIn(0);
}

// ngHideLoading
function ngHideLoading() {
    $("#loading-layer").fadeOut('slow');
}

// confirmIt
function confirmIt(url, text){
    if (confirm(text)) document.location=url;
    return false;
}

// Request JSON - new style ajax
$.reqJSON = function(url, method, params, callback, notPreload) {
    $.ajax({
        type: 'POST',
        url: url,
        cache: false,
        dataType: 'json',
        data: {
            json: 1,
            rndval: new Date().getTime(),
            methodName: method,
            reqReferer: window.location.href,
            params: JSON.stringify(params),
        },
        beforeSend: function(jqXHR) {
            if (!notPreload) {
                ngShowLoading();
            }
            jqXHR.overrideMimeType("application/json; charset=UTF-8");
            // Repeat send header ajax
            jqXHR.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        },
    })
    .done(function(data, textStatus, jqXHR) {
        if (typeof(data) == 'object') {
            // this schema {"status": 1, "errorCode": 0, "errorText": ""}
            // if data.length > 3 => callback this
            if (data.status || Object.keys(data).length > 3) {
                callback.call(null, data);
            } else {
                $.notify({message:data.errorText},{type: 'danger'});
                //$.notify({message:'Error ['+data.errorCode+']: '+data.errorText},{type: 'danger'});
            }
        } else {
            data = $.parseJSON(data);
            if (typeof(data) == 'object') {
                callback.call(null, data);
            } else {
                $.notify({message: '<i><b>Bad reply from server</b></i>'},{type: 'danger'});
            }
        }
    })
    .always(function(jqXHR, textStatus, errorThrown) {
        ngHideLoading();
    })
    .catch(function(jqXHR, textStatus, exception) {
        if (jqXHR.status === 0) {
            $.notify({message: 'Not connect.n Verify Network.'},{type: 'danger'});
        } else if (jqXHR.status == 404) {
            $.notify({message: 'Requested page not found. [404]'},{type: 'danger'});
        } else if (jqXHR.status == 500) {
            $.notify({message: 'Internal Server Error [500].'},{type: 'danger'});
        } else if (exception === 'parsererror') {
            $.notify({message: 'Requested JSON parse failed.'},{type: 'danger'});
        } else if (exception === 'timeout') {
            $.notify({message: 'Time out error.'},{type: 'danger'});
        } else if (exception === 'abort') {
            $.notify({message: 'Ajax request aborted.'},{type: 'danger'});
        } else {
            $.notify({message: 'Uncaught Error.n ' + jqXHR.status + ': ' + jqXHR.statusText},{type: 'danger'});
        }
    });
}

// insertext
function insertext(open, close, field) {
    msgfield = document.getElementById((field != '') ? field : 'content');

    // IE support
    if (document.selection && document.selection.createRange){
        msgfield.focus();
        sel = document.selection.createRange();
        sel.text = open + sel.text + close;
        msgfield.focus();
    }
    // Moz support
    else if (msgfield.selectionStart || msgfield.selectionStart == "0"){
        var startPos = msgfield.selectionStart;
        var endPos = msgfield.selectionEnd;

        msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
        msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
        msgfield.focus();
    }
    // Fallback support for other browsers
    else {
        msgfield.value += open + close;
        msgfield.focus();
    }
    $('html, body').animate({ scrollTop: $(msgfield).offset().top-200 }, 888);
    return;
}

// insertimage
function insertimage(open) {
    insertext(open, ' ');
}
/* Quote user */
var q_txt = '';

function copy_quote(q_name) {

    if (window.getSelection) {
        q_txt = window.getSelection();
    } else if (document.getSelection) {
        q_txt = document.getSelection();
    } else if (document.selection) {
        q_txt = document.selection.createRange().text;
    }

    if (q_txt == '') {
        q_txt = '[b]'+q_name+'[/b],';
    } else {
        q_txt = '[quote='+q_name+']'+q_txt+'[/quote]';
    }

}

function quote(q_name) {
    insertext(q_txt, ' ', 'content');
}

// emailCheck
function emailCheck(emailStr) {
    var emailPat = /^(.+)@(.+)$/,
        specialChars = "\\(\\)<>@,;:\\\\\\\"\\.\\[\\]",
        validChars = "\[^\\s" + specialChars + "\]",
        quotedUser = "(\"[^\"]*\")",
        ipDomainPat = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/,
        atom = validChars + '+',
        word = "(" + atom + "|" + quotedUser + ")",
        userPat = new RegExp("^" + word + "(\\." + word + ")*$"),
        domainPat = new RegExp("^" + atom + "(\\." + atom +")*$");

    var matchArray = emailStr.match(emailPat);
    if (matchArray == null) return false;

    var user = matchArray[1],
        domain = matchArray[2];
    if (user.match(userPat) == null) return false;

    var IPArray = domain.match(ipDomainPat);
    if (IPArray != null) {
        for (var i=1;i<=4;i++) {
            if (IPArray[i]>255) return false;
        }
        return true;
    }

    var domainArray = domain.match(domainPat);
    if (domainArray == null) return false;

    var atomPat = new RegExp(atom,"g"),
        domArr = domain.match(atomPat),
        len = domArr.length;
    if (domArr[domArr.length-1].length<2 || domArr[domArr.length-1].length>3) return false;

    if (len<2) return false;

    return true;

}

// formatSize
function formatSize($file_size){
    if ($file_size >= 1073741824) {
        $file_size = Math.round( $file_size / 1073741824 * 100 ) / 100 + " Gb";
    } else if ($file_size >= 1048576) {
        $file_size = Math.round( $file_size / 1048576 * 100 ) / 100 + " Mb";
    } else if ($file_size >= 1024) {
        $file_size = Math.round( $file_size / 1024 * 100 ) / 100 + " Kb";
    } else {
        $file_size = $file_size + " b";
    }
    return $file_size;
}

// calculateMaxLen
function calculateMaxLen(oId, tId, maxLen) {
    var delta = maxLen - oId.val().length;

    if (tId) {
        tId.html(delta);
        tId.css('color', ((delta > 0) ? 'black' : 'red'));
    }
}

// Simple timer
function timerShow(id) {
    var timer = 0,hour = 0,minute = 0,second = 0;
    window.setInterval(function(){
        ++timer;
        hour   = Math.floor(timer / 3600);
        minute = Math.floor((timer - hour * 3600) / 60);
        second = timer - hour * 3600 - minute * 60;
        if (hour < 10) hour = '0' + hour;
        if (minute < 10) minute = '0' + minute;
        if (second < 10) second = '0' + second;
        $('#'+id).html(hour + ':' + minute + ':' + second);
        }, 1000);
}

// printElem
function printElem(data) {
    
    var printing_css='<style>* {color:#888;} input{display:none;} a {text-decoration:none;}</style>';
    var html_to_print=printing_css + data;
    var iframe=$('<iframe id="print_frame">');
    $('body').append(iframe);
    var doc = $('#print_frame')[0].contentDocument || $('#print_frame')[0].contentWindow.document;
    var win = $('#print_frame')[0].contentWindow || $('#print_frame')[0];
    doc.getElementsByTagName('body')[0].innerHTML=html_to_print;
    win.print();
    $('iframe').remove();

    return true;
}

/* Main function to show Modal Bootsrtap */
function showModal(textOrID, header, footer, size) {
    var withID = document.getElementById(textOrID);
    if (withID && !header && !footer) { // Show modal with ID
        $(withID).modal('show');
        return;
    }
    var modalContent = '';
    if (header) {
        if (textOrID) {
            modalContent = '<div class="modal-header">\
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                    <span aria-hidden="true">&times;</span>\
                                </button>\
                                <h5 class="modal-title">' + header + '</h5>\
                            </div>';
        } else {
            modalContent = '<div class="modal-header">\
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                    <span aria-hidden="true">&times;</span>\
                                </button>\
                                <h5 class="modal-title">Info</h5>\
                            </div>';
        }
    } else {
        modalContent = '<div class="modal-header">\
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                <span aria-hidden="true">&times;</span>\
                            </button>\
                            <h5 class="modal-title">Error</h5>\
                        </div>';
    }
    if (textOrID)
        modalContent += '<div class="modal-body">' + textOrID + '</div>';
    else
        modalContent += '<div class="modal-body">Unable to load content . . .</div>';
    
    if (footer) {
        modalContent += '<div class="modal-footer">' + footer + '</div>';
    } else {
        modalContent += '<div class="modal-footer">\
                            <button type="button" class="btn btn-default btn-secondary" data-dismiss="modal">\
                            Close\
                            </button>\
                        </div>';
    }
    if (size == 'modal-lg')
        $('#modal-dialog .modal-dialog').addClass('modal-lg');
    else
        $('#modal-dialog .modal-dialog').removeClass('modal-lg');

    $('#modal-dialog .modal-content').html(modalContent); // #modal-dialog isset in html document'Рµ
    $('#modal-dialog').modal('show');

    return;
}

// validateFile
function validateFile(fileInput,multiple,fileMaxSize) {
    var htext = '';
    var hsize = '';
    var btnFileInput = $(fileInput).closest('.btn-fileinput');
    
    if (!fileInput.value) {
        btnFileInput.attr('style', '');
        btnFileInput.addClass('btn');
        btnFileInput.children('span').eq(0).html('<i class="fa fa-plus"></i> Add files ...');
        btnFileInput.children('span').attr('style', '');
        return false;
    }
    
    if (multiple) {
        for (var i=0;i<fileInput.files.length;i++) {
            if (fileMaxSize) {
                htext += '<tr><td style="overflow:hidden;text-overflow:ellipsis;max-width: 400px;">' + fileInput.files[i].name+'</td><td nowrap><div id="progressbar" class="progress-bar progress-bar-success" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div><b class="pull-right' + (fileInput.files[i].size>fileMaxSize?' text-danger':'') + '">'+formatSize(fileInput.files[i].size)+'</b></td></tr>';
            } else {
                htext += '<tr><td style="overflow:hidden;text-overflow:ellipsis;max-width: 400px;">' + fileInput.files[i].name+'</td><td nowrap><div id="progressbar" class="progress-bar progress-bar-success" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div><b class="pull-right">'+formatSize(fileInput.files[i].size)+'</td></tr>';
            }
            hsize = Number(fileInput.files[i].size) + Number(hsize);
        }
        
        btnFileInput.removeClass('btn');
        btnFileInput.children('span').eq(0).html('<table\
            class="table-condensed" style="width: 100%;">\
            ' + htext + '<tr><td colspan="2" class="text-right">' + formatSize(hsize) + '</td></tr></table>');
        btnFileInput.children('span').eq(0).css({'width': '100%', 'display': 'block'/*, 'white-space': 'nowrap'*/});
        btnFileInput.css({'width': '100%', 'display': 'block'});
    } else {
        for (var i=0;i< fileInput.files.length;i++) {
            htext += fileInput.files[i].name+' ('+formatSize(fileInput.files[i].size)+')<br />';
            hsize = Number(fileInput.files[i].size) + Number(hsize);
        }
        
        btnFileInput.children('span').eq(0).html(htext);
    }
    
    return true;
}


/* ************ */
/* DINAMIC TIME */
/* ************ */
UsAgentLang = (navigator.language || navigator.systemLanguage || navigator.userLanguage).substr(0, 2).toLowerCase();

Lang = {}
// Р’С‹Р±РёСЂР°РµРј РЅСѓР¶РЅСѓСЋ Р»РѕРєР°Р»РёР·Р°С†РёСЋ.
switch (UsAgentLang) {
    case 'ru' :
        Lang.Now = 'С‚РѕР»СЊРєРѕ С‡С‚Рѕ';
        Lang.Ago = 'РЅР°Р·Р°Рґ';
        Lang.After = 'С‡РµСЂРµР·';
        Lang.NameMonths = ['РЇРЅРІР°СЂСЏ', 'Р¤РµРІСЂР°Р»СЏ', 'РњР°СЂС‚Р°', 'РђРїСЂРµР»СЏ', 'РњР°СЏ', 'Р�СЋРЅСЏ', 'Р�СЋР»СЏ', 'РђРІРіСѓСЃС‚a', 'РЎРµРЅС‚СЏР±СЂСЏ', 'РћРєС‚СЏР±СЂСЏ', 'РќРѕСЏР±СЂСЏ', 'Р”РµРєР°Р±СЂСЏ'];
        Lang.NameMonthsMin = ['РЇРЅРІ', 'Р¤РµРІ', 'РњР°СЂ', 'РђРїСЂ', 'РњР°Р№', 'Р�СЋРЅ', 'Р�СЋР»', 'РђРІРі', 'РЎРµРЅ', 'РћРєС‚', 'РќРѕСЏ', 'Р”РµРє'];
        Lang.NameWeekdays = ['Р’РѕСЃРєСЂРµСЃРµРЅСЊРµ', 'РџРѕРЅРµРґРµР»СЊРЅРёРє', 'Р’С‚РѕСЂРЅРёРє', 'РЎСЂРµРґР°', 'Р§РµС‚РІРµСЂРі', 'РџСЏС‚РЅРёС†Р°', 'РЎСѓР±Р±РѕС‚Р°'];
        Lang.NameWeekdaysMin = ['Р’СЃ', 'РџРЅ', 'Р’С‚', 'РЎСЂ', 'Р§С‚', 'РџС‚', 'РЎР±'];
        Lang.DimensionTime = {
                'n' : ['РјРµСЃСЏС†РµРІ', 'РјРµСЃСЏС†', 'РјРµСЃСЏС†Р°', 'РјРµСЃСЏС†'],
                'j' : ['РґРЅРµР№', 'РґРµРЅСЊ', 'РґРЅСЏ'],
                'G' : ['С‡Р°СЃРѕРІ', 'С‡Р°СЃ', 'С‡Р°СЃР°'],
                'i' : ['РјРёРЅСѓС‚', 'РјРёРЅСѓС‚Сѓ', 'РјРёРЅСѓС‚С‹'],
                's' : ['СЃРµРєСѓРЅРґ', 'СЃРµРєСѓРЅРґСѓ', 'СЃРµРєСѓРЅРґС‹']
        }
        break;
    default:
        Lang.Now = 'now';
        Lang.Ago = 'ago';
        Lang.After = 'after';
        Lang.NameMonths = ['January', 'February', 'Marth', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        Lang.NameMonthsMin = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        Lang.NameWeekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        Lang.NameWeekdaysMin = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        Lang.DimensionTime = {
                'n' : ['months', 'month', 'months'],
                'j' : ['days', 'day', 'days'],
                'G' : ['hours', 'h', 'hours'],
                'i' : ['minutes', 'minute', 'minutes'],
                's' : ['seconds', 'second', 'seconds']
        }
        break;
}

// Р’С‹РІРѕРґРёС‚ СЌР»РµРјРµРЅС‚ РґР°С‚С‹ СЃ РЅСѓР¶РЅРѕР№ СЂР°Р·РјРµСЂРЅРѕСЃС‚СЊСЋ, Рё РІ РЅСѓР¶РЅРѕРј СЃРєР»РѕРЅРµРЅРёРё
function NiceDate(chislo, type) {
    var n;
    // РЈР·РЅР°РµРј РЅСѓР¶РЅРѕРµ СЃРєР»РѕРЅРµРЅРёРµ РґР»СЏ РІСЂРµРјРµРЅРЅРѕР№ РµРґРёРЅРёС†С‹
    if (chislo >= 5 && chislo <= 20)
        n = 0;
    else if (chislo == 1 || chislo % 10 == 1)
        n = 1;
    else if ((chislo <= 4 && chislo >= 1) || (chislo % 10 <= 4 && chislo % 10 >= 1))
        n = 2;
    else
        n = 0;
        

    return chislo + ' ' + Lang.DimensionTime[type][n];

}

// Р’С‹РІРѕРґРёС‚ РґРІСѓР·РЅР°С‡РЅРѕРµ С‡РёСЃР»Рѕ СЃ РІРµРґСѓС‰РёРј РЅСѓР»РµРј
function ZeroPlus(x) {
    if (x < 10)
        x = '0' + x;
    return x;
}
// РџРµСЂРµРІРѕРґРёС‚ РІ 12 С‡Р°СЃРѕРІРѕР№ С„РѕСЂРјР°С‚
function ToAM(x) {
    if (x > 12) 
        x -= 12;
    return x;
}

// РђРЅР°Р»РѕРі С„СѓРЅРєС†РёРё date() РёР· PHP
function ParseDateFormat(format, Time) {
    var DateInFormat = '';
    if (format.length === 0)
        return;
    for (var i = 0; i < format.length; i++) {
        switch (format[i]) {
            // Р§Р°СЃС‹
            // 12 С‡Р°СЃРѕРІРѕР№
            case 'g' : DateInFormat += ToAM(Time.getUTCHours()); break; // Р±РµР· РІРµРґСѓС‰РµРіРѕ РЅСѓР»СЏ
            case 'h' : DateInFormat += ZeroPlus(ToAM(Time.getUTCHours())); break; // C РІРµРґСѓС‰РёРј РЅСѓР»РµРј
            // 24 С‡Р°СЃРѕРІРѕР№
            case 'G' : DateInFormat += Time.getUTCHours(); break; // Р±РµР· РІРµРґСѓС‰РµРіРѕ РЅСѓР»СЏ
            case 'H' : DateInFormat += ZeroPlus(Time.getUTCHours()); break; // СЃ РІРµРґСѓС‰РёРј РЅСѓР»С‘Рј
            // Р“РѕРґС‹
            case 'Y' : DateInFormat += Time.getUTCFullYear(); break; // Р§РµС‚С‹СЂРµ С†РёС„СЂС‹
            case 'y' : DateInFormat += String(Time.getUTCFullYear()).substr(2); break; // Р”РІРµ С†РёС„СЂС‹
            // РњРµСЃСЏС†С‹
            case 'm' : DateInFormat += ZeroPlus(Time.getUTCMonth() + 1); break; //РџРѕСЂСЏРґРєРѕРІС‹Р№ РЅРѕРјРµСЂ РјРµСЃСЏС†Р° СЃ РІРµРґСѓС‰РёРј РЅСѓР»С‘Рј
            case 'n' : DateInFormat += Time.getUTCMonth() + 1; break; // РџРѕСЂСЏРґРєРѕРІС‹Р№ РЅРѕРјРµСЂ РјРµСЃСЏС†Р° Р±РµР· РІРµРґСѓС‰РµРіРѕ РЅСѓР»СЏ
            case 'F' : DateInFormat += Lang.NameMonths[Time.getUTCMonth()]; break; // РџРѕР»РЅРѕРµ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ РјРµСЃСЏС†Р°
            case 'M' : DateInFormat += Lang.NameMonthsMin[Time.getUTCMonth()]; break; // РЎРѕРєСЂР°С‰РµРЅРЅРѕРµ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ РјРµСЃСЏС†Р°
            // Р”РЅРё
            case 'd' : DateInFormat += ZeroPlus(Time.getUTCDate()); break;// Р”РµРЅСЊ РјРµСЃСЏС†Р°
            case 'j' : DateInFormat += Time.getUTCDate(); break; // Р”РµРЅСЊ РјРµСЃСЏС†Р° Р±РµР· РІ.РЅ.
            // Р”РЅРё РЅРµРґРµР»Рё
            case 'N' : DateInFormat += Time.getUTCDay() + 1; break; // РџРѕСЂСЏРґРєРѕРІС‹Р№ РЅРѕРјРµСЂ РґРЅСЏ РЅРµРґРµР»Рё
            case 'D' : DateInFormat += Lang.NameWeekdaysMin[Time.getUTCDay()]; break; // РўРµРєСЃС‚РѕРІРѕРµ, СЃРѕРєСЂР°С‰РµРЅРЅРѕРµ, РїСЂРµРґСЃС‚Р°РІР»РµРЅРёРµ РґРЅСЏ РЅРµРґРµР»Рё
            case 'L' : DateInFormat += Lang.NameWeekdays[Time.getUTCDay()]; break; // РџРѕР»РЅРѕРµ РЅР°РёРјРµРЅРѕРІР°РЅРёРµ РґРЅСЏ РЅРµРґРµР»Рё
            // РњРёРЅСѓС‚С‹
            case 'i' : DateInFormat += ZeroPlus(Time.getUTCMinutes()); break; // СЃ РІРµРґСѓС‰РёРј РЅСѓР»С‘Рј
            // РЎРµРєСѓРЅРґС‹
            case 's' : DateInFormat += ZeroPlus(Time.getUTCSeconds()); break; // СЃ РІРµРґСѓС‰РёРј РЅСѓР»С‘Рј
            
            default : DateInFormat += format[i]; break;
        }
    }
    
    return DateInFormat;
}

// Р’С‹РІРѕРґРёС‚ РѕС‚РЅРѕСЃРёС‚РµР»СЊРЅРѕРµ РІСЂРµРјСЏ. Рђ С‚Р°Рє Р¶Рµ РµСЃР»Рё check = true С‚Рѕ РїСЂРѕСЃС‚Рѕ РґРµР»Р°РµС‚ РїСЂРѕРІРµСЂРєСѓ, РѕС‚РЅРѕСЃРёС‚РµР»СЊРЅСѓСЋ Р»Рё РґР°С‚Сѓ РІС‹РІРѕРґРёС‚СЊ
function OffsetDate(Time, Now, check) {
    
    if (check) {
        if (((new Date(Now - Time)) < (new Date(1970, 1))) || Time > Now)
            return true;
        else
            return false;
    }

    if (Time > Now)
        var OffsetTime = new Date(Time - Now);
    else
        var OffsetTime = new Date(Now - Time);
    
    var s = OffsetTime.getUTCSeconds(), // РЎРµРєСѓРЅРґС‹
         i = OffsetTime.getUTCMinutes(), // РњРёРЅСѓС‚С‹
         G = OffsetTime.getUTCHours(), // Р§Р°СЃС‹
         j = OffsetTime.getUTCDate()-1, // Р”РЅРё
         n = OffsetTime.getUTCMonth(), // РњРµСЃСЏС†
         output = '';
    
    // Р•СЃР»Рё РІСЂРµРјСЏ РїРѕС€Р»Рѕ РЅР° РјРµСЃСЏС†С‹ С‚Рѕ РІС‹РІРѕРґРёРј С‚РѕР»СЊРєРѕ РјРµСЃСЏС†С‹ Рё РґРЅРё(РµСЃР»Рё РЅРµ РЅРѕР»СЊ)
    if (n) {
        output += NiceDate(n, 'n') + ' ';
        if (j) output += NiceDate(j, 'j') + ' ';
    // Р•СЃР»Рё РІСЂРµРјСЏ РїРѕС€Р»Рѕ РЅР° РґРЅРё С‚Рѕ РІС‹РІРѕРґРёРј С‚РѕР»СЊРєРѕ РґРЅРё
    } else if (j) {
        output += NiceDate(j, 'j') + ' ';
    // Р•СЃР»Рё РІСЂРµРјСЏ РїРѕС€Р»Рѕ РЅР° С‡Р°СЃС‹ С‚Рѕ РІС‹РІРѕРґРёРј С‚РѕР»СЊРєРѕ С‡Р°СЃС‹ Рё РјРёРЅСѓС‚С‹(РµСЃР»Рё РЅРµ РЅРѕР»СЊ)
    } else if (G) {
        output += NiceDate(G, 'G') + ' ';
    // Р•СЃР»Рё РІСЂРµРјСЏ РїРѕС€Р»Рѕ РЅР° РјРёРЅСѓС‚С‹ С‚Рѕ РІС‹РІРѕРґРёРј С‚РѕР»СЊРєРѕ РјРёРЅСѓС‚С‹ Рё СЃРµРєСѓРЅРґС‹(РµСЃР»Рё РЅРµ РЅРѕР»СЊ)
    } else if (i) {
        output += NiceDate(i, 'i') + ' ';
    // Р•СЃР»Рё РІСЂРµРјРµРЅРё РїСЂРѕС€Р»Рѕ РјРµРЅРµРµ РјРёРЅСѓС‚С‹ С‚Рѕ РІС‹РІРѕРґРёРј СЃРµРєСѓРЅРґС‹
    } else {
        output += Lang.Now;
        return output;
    }

    if (Time > Now)
        return Lang.After + '  ' + output;
    else
        return output + '  ' + Lang.Ago;

}

// Р’С‹РІРѕРґРёС‚ РґР°С‚Сѓ РІ РЅСѓР¶РЅРѕРј С„РѕСЂРјР°С‚Рµ
function FormatTime(el) {
    
    var format = el.data('type'),
        stime = Date.parse(el.attr('datetime')),
        Now = new Date(), // РћР±СЉРµРєС‚ С‚РµРєСѓС‰РµР№ РґР°С‚С‹
        Time = new Date(stime), // РћР±СЊРµРєС‚ СѓРєР°Р·Р°РЅРЅРѕРіРѕ РІСЂРµРјРµРЅРё
        f = OffsetDate(Time, Now, true); // РџСЂРѕРІРµСЂРєР° РЅР° С‚РёРї РІС‹РІРѕРґРёРјРѕРіРѕ РІСЂРµРјРµРЅРё(РѕС‚РЅРѕСЃРёС‚РµР»СЊРЅС‹Р№ РёР»Рё РґР°С‚Р°)
        
    // Р’С‹РІРѕРґРёРј РѕС‚РЅРѕСЃРёС‚РµР»СЊРЅРѕРµ РІСЂРµРјСЏ
    if (f)
        el.html(OffsetDate(Time, Now, false));
    else {
        // Р—РґРµСЃСЊ РїСЂРѕСЃС‚Рѕ РІС‹РІРѕРґРёРј РІ РЅСѓР¶РЅРѕРј С„РѕСЂРјР°С‚Рµ...
        // Р•СЃР»Рё СЌС‚Сѓ РґР°С‚Сѓ(РЅРµ РѕС‚РЅРѕСЃРёС‚РµР»СЊРЅСѓСЋ) РјС‹ СѓР¶Рµ РѕР±СЂР°Р±РѕС‚Р°Р»Рё, С‚Рѕ РЅРµ С‚СЂРѕРіР°РµРј РµС‘.
        if (!el.data('compiled')) {
            el.html(ParseDateFormat(format, Time));
            el.attr('data-compiled', 'true');
        }
    }
}

// Р�С‰РµРј РґР°С‚С‹ РЅР° СЃС‚СЂР°РЅРёС†Рµ Рё РёР·РјРµРЅСЏРµРј РёС… РїРѕРґ РєР»РёРµРЅС‚Р°
function UpdateTime() {
    var BlockTime = $('time');
    $.each(BlockTime, function () {
        if ($(this).attr('data-type'))
            FormatTime($(this));
    });
}

// РџРµСЂРІРѕРЅР°С‡Р°Р»СЊРЅР°СЏ РѕР±СЂР°Р±РѕС‚РєР° РІСЂРµРјРµРЅРё.
$(function() {UpdateTime();});
// Р”РёРЅР°РјРёС‡РµСЃРєРѕРµ РѕР±РЅРѕРІР»РµРЅРёРµ РґР°С‚.
setInterval(UpdateTime, 10000);

//РґРѕР±Р°РІР»РµРЅРёРµ СЂР°Р·РјРµСЂР° РЁСЂРёС„С‚Р°
function insertfortext(teg,val,field){
        try { var    msgfield = document.getElementById((field=='')?'content':field);
        } catch (err) {
            return false;
        }    
        var open ='['+teg+'="'+val+'"]';
        var close='[/'+teg+']';
    // IE support
    if (document.selection && document.selection.createRange){
        msgfield.focus();
        sel = document.selection.createRange();
        sel.text = open + sel.text + close;
        msgfield.focus();
    }
    // Moz support
    else if (msgfield.selectionStart || msgfield.selectionStart == "0"){
        var startPos = msgfield.selectionStart;
        var endPos = msgfield.selectionEnd;
        msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
        msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
        msgfield.focus();
    }
    // Fallback support for other browsers
    else {
        msgfield.value += open + close;
        msgfield.focus();
    }
    return true;
}
