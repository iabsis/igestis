messageObj = new DHTMLSuite.modalMessage();	// We only create one object of this class
messageObj.setWaitMessage('Loading message - please wait....');
messageObj.setShadowOffset(5);	// Large shadow

DHTMLSuite.commonObj.setCssCacheStatus(false);


function popup (L,H,F,TL,SR,ST) {
	var vpos=(screen.height-H)/2;
	var hpos=(screen.width-L)/2;
	window.open(F,'','top='+vpos+',left='+hpos+',width='+L+',height='+H+',toolbar='+TL+', scrollbars='+SR+',status='+ST+'');
}


function show_hide_block(block)
{// Function that show or hide a block
	var b = document.getElementById(block);
	if(b)
	{
		if(b.style.display == "block" || b.style.display == "" || b.style.display == "inline")
		{
			b.style.display = "none";
		}
		else
		{
			b.style.display = "block"
		}
	}
}

function show_progress_message(message)
{
	var field = document.getElementById('fullscreen_conteneur');
	if(field) 
	{
		field.style.display = "block";
		var largeur = document.body.scrollWidth;
		var hauteur = document.body.scrollHeight;
		if (document.body.offsetHeight)
		{
			if (document.body.offsetHeight > hauteur) hauteur = document.body.offsetHeight;
		}
		
		field.style.height = "2000px";
		
		document.getElementById('full_screen_message').innerHTML = message;
	}	
}

function hide_progress_message()
{
	var field = document.getElementById('fullscreen_conteneur');
	if(field) 
	{
		field.style.display = "none";
		document.getElementById('full_screen_message').innerHTML = "";
	}
}

function get_checked_value(radioObj) 
{
	var radioLength = radioObj.length;
	if(radioLength == undefined)
	{
		if(radioObj.checked) return radioObj.value;
		else return "";
	}
	
	
	for(var i = 0; i < radioLength; i++) 
	{
		if(radioObj[i].checked) 
		{
			return radioObj[i].value;
		}
	}
	return "";
}


function is_email(string)
{
	var email_reg_expr = /^[a-zA-Z0-9\-\.\_]+@{1}[a-zA-Z0-9\-\.\_]+\.{1}[a-zA-Z0-9]{2,4}$/;
	return email_reg_expr.test(string);
	return false;
}

function is_numeric(string)
{
	var numeric_reg_expr = /^[0-9]+$/;
	return email_reg_expr.test(string);
	return false;
}

// calcule le dcalage  gauche

function calculateOffsetLeft(r){
  return calculateOffset(r,"offsetLeft")
}



// calcule le dcalage vertical
function calculateOffsetTop(r){
  return calculateOffset(r,"offsetTop")
}

function calculateOffset(r,attr){
  var kb=0;
  while(r){
    kb+=r[attr];
    r=r.offsetParent
  }
  return kb
}

function replace_string(expr,a,b) {
	var i=0
	while (i!=-1) {
		i=expr.indexOf(a,i);
		if (i>=0) {
			expr=expr.substring(0,i)+b+expr.substring(i+a.length);
			i+=b.length;
		}
	}
	return expr
}

function vide_date(champ)
{
	if(champ.value == "dd/mm/yyyy") champ.value = "";
}

function populate_date(champ)
{
	if(champ.value == "") 
	{
		champ.value = "dd/mm/yyyy";
	}
	else
	{
		var tmp = champ.value.split("/");
		if(tmp[0].length == 1) tmp[0] = "0" + tmp[0];
		if(tmp[1].length == 1) tmp[1] = "0" + tmp[1];
		if(tmp[2].length == 2) 
		{
			if(tmp[2] < 20) tmp[2] = "20" + tmp[2];
			else tmp[2] = "19" + tmp[2];
		}
		champ.value = tmp[0] + "/" + tmp[1] + "/" + tmp[2];
	}
}

function populate_field(champ, init, type)
{
	if(type == "populate")
	{
		if(champ.value == "") champ.value = init;
	}
	else
	{
		if(champ.value == init) champ.value = "";
	}
}

function date_add_slash(event, champ)
{
	var nb = 0;
	var i = 0;
	
	if(event.keyCode)
	{
		if(event.keyCode != 8)
		{
			if ((champ.value.length == 2 || champ.value.length == 5 || champ.value.charAt(champ.value.length-3) == "/") && champ.value.charAt(champ.value.length-1) != "/") 
			{
				// On compte le nombre de "/" dj prsent dans le champs
				nb = 0;
				for(i = 0; i < champ.value.length; i++) if(champ.value.charAt(i) == "/") nb++;
				// On ajoute le "/" si il n'y en a pas encore 2
				if (nb < 2) champ.value = champ.value + "/";
			}
			champ.value = champ.value.replace(/\/\//, "/");
		}
	}
}


function empty_on_focus(field, default_value)
{
	if(field.value == default_value)
	{
		field.value = "";
	}
	
	field.onblur = function() {
			if(this.value == "") this.value = default_value;
		}
}

function sprintf( ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Ash Searle (http://hexmen.com/blog/)
    // + namespaced by: Michael White (http://getsprink.com)
    // +    tweaked by: Jack
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: sprintf("%01.2f", 123.1);
    // *     returns 1: 123.10
 
    var regex = /%%|%(\d+\$)?([-+#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
    var a = arguments, i = 0, format = a[i++];
 
    // pad()
    var pad = function(str, len, chr, leftJustify) {
        var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
        return leftJustify ? str + padding : padding + str;
    };
 
    // justify()
    var justify = function(value, prefix, leftJustify, minWidth, zeroPad) {
        var diff = minWidth - value.length;
        if (diff > 0) {
            if (leftJustify || !zeroPad) {
                value = pad(value, minWidth, ' ', leftJustify);
            } else {
                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
            }
        }
        return value;
    };
 
    // formatBaseX()
    var formatBaseX = function(value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
    };
 
    // formatString()
    var formatString = function(value, leftJustify, minWidth, precision, zeroPad) {
        if (precision != null) {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad);
    };
 
    // finalFormat()
    var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type) {
        if (substring == '%%') return '%';
 
        // parse flags
        var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false;
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++) switch (flags.charAt(j)) {
            case ' ':positivePrefix = ' ';break;
            case '+':positivePrefix = '+';break;
            case '-':leftJustify = true;break;
            case '0':zeroPad = true;break;
            case '#':prefixBaseX = true;break;
        }
 
        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth) {
            minWidth = 0;
        } else if (minWidth == '*') {
            minWidth = +a[i++];
        } else if (minWidth.charAt(0) == '*') {
            minWidth = +a[minWidth.slice(1, -1)];
        } else {
            minWidth = +minWidth;
        }
 
        // Note: undocumented perl feature:
        if (minWidth < 0) {
            minWidth = -minWidth;
            leftJustify = true;
        }
 
        if (!isFinite(minWidth)) {
            throw new Error('sprintf: (minimum-)width must be finite');
        }
 
        if (!precision) {
            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : void(0);
        } else if (precision == '*') {
            precision = +a[i++];
        } else if (precision.charAt(0) == '*') {
            precision = +a[precision.slice(1, -1)];
        } else {
            precision = +precision;
        }
 
        // grab value using valueIndex if required?
        var value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];
 
        switch (type) {
            case 's':return formatString(String(value), leftJustify, minWidth, precision, zeroPad);
            case 'c':return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
            case 'b':return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'o':return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'x':return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'X':return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
            case 'u':return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'i':
            case 'd': {
                        var number = parseInt(+value);
                        var prefix = number < 0 ? '-' : positivePrefix;
                        value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                        return justify(value, prefix, leftJustify, minWidth, zeroPad);
                    }
            case 'e':
            case 'E':
            case 'f':
            case 'F':
            case 'g':
            case 'G':
                        {
                        var number = +value;
                        var prefix = number < 0 ? '-' : positivePrefix;
                        var method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                        var textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                        value = prefix + Math.abs(number)[method](precision);
                        return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
                    }
            default:return substring;
        }
    };
 
    return format.replace(regex, doFormat);
}


function applet_show_loading(id) {
	var loading_message = document.getElementById('wainting_please');
	var applet_content = document.getElementById(id);
	if(loading_message && applet_content) {
		applet_content.innerHTML = loading_message.innerHTML;
	}
} 

function applet_close(applet_id) {
	if(confirm("Voulez-vous vraiment supprimer cet applet de votre page d'accueil ?")) {
		window.location.href = 'updatedb.php?section=applets&action=del&applet_id=' + applet_id;
	}
}

function show_warning_message(message, type) {
	/*var dialog = document.getElementById('warning_message');
	
	if(type == "warning") dialog.style.background = "red";
	if(dialog) {
		dialog.innerHTML = message;
		dialog.style.display = "block" ;
	}*/
    
    var dialog = $('<div class="alert alert-error">' +
        '<a class="close" data-dismiss="alert">×</a>' +
        message +
        '</div>').hide();

    dialog.find("img").each(function() {
       $(this).remove(); 
    });
    
    $("#wrap").prepend(dialog);
    dialog.fadeIn();
    
}

function igestis_show_tooltip(tooltip_id) {
    var tooltip = document.getElementById(tooltip_id);
    var tooltip_icon = document.getElementById(tooltip_id + "_icon");
    if(tooltip && tooltip.style.display != "block") {
        tooltip.style.left = (calculateOffsetLeft(tooltip_icon) + 30) + "px";
        tooltip.style.top = calculateOffsetTop(tooltip_icon) + "px";
        tooltip.style.display = "block";
    }
}

function igestis_hide_tooltip(tooltip_id) {
    var tooltip = document.getElementById(tooltip_id);
    if(tooltip && tooltip.style.display != "none") tooltip.style.display = "none";
}

function no_accent(my_string) {
var new_string = String (my_string);
new_string = new_string.replace(/(&#x40|&#064;|@|&commat;|&#x41|&#065;|A|&#x61|&#097;|&#xC0|&#192;|À|&Agrave;|&#xC1|&#193;|Á|&Aacute;|&#xC2|&#194;|Â|&Acirc;|&#xC3|&#195;|Ã|&Atilde;|&#xC4|&#196;|Ä|&Auml;|&#xC5|&#197;|Å|&Aring;|&#xE0|&#224;|à|&agrave;|&#xE1|&#225;|á|&aacute;|&#xE2|&#226;|â|&acirc;|&#xE3|&#227;|ã|&atilde;|&#xE4|&#228;|ä|&auml;|&#xE5|&#229;|å|&aring;)/gi,'a');
new_string = new_string.replace(/(&#xC7|&#199;|Ç|&Ccedil;|&#xE7|&#231;|ç|&ccedil;)/gi,'c');
new_string = new_string.replace(/(&#xD0|&#208;|Ð|&ETH;)/gi,'d');
new_string = new_string.replace(/(&#x45;|&#069;|E|&#x65;|&#101;|&#xC8;|&#200;|È|&Egrave;|&#xC9;|&#201;|É|&Eacute;|&#xCA;|&#202;|Ê|&Ecirc;|&#xCB;|&#203;|Ë|&Euml;|&#xE8;|&#232;|è|&egrave;|&#xE9;|&#233;|é|&eacute;|&#xEA;|&#234;|ê|&ecirc;|&#xEB;|&#235;|ë|&euml;)/gi,'e');
new_string = new_string.replace(/(&#x49|&#073;|I|&#x69|&#105;|&#xCC|&#204;|Ì|&Igrave;|&#xCD|&#205;|Í|&Iacute;|&#xCE|&#206;|Î|&Icirc;|&#xCF|&#207;|Ï|&Iuml;|&#xEC|&#236;|ì|&igrave;|&#xED|&#237;|í|&iacute;|&#xEE|&#238;|î|&icirc;|&#xEF|&#239;|ï|&iuml;)/gi,'i');
new_string = new_string.replace(/(&#x4E|&#078;|N|&#x6E|&#110;|&#xD1|&#209;|Ñ|&Ntilde;|&#xF1|&#241;|ñ|&ntilde;)/gi,'n');
new_string = new_string.replace(/(&#x4F|&#079;|O|&#x6F|&#111;|&#xD2|&#210;|Ò|&Ograve;|&#xD3|&#211;|Ó|&Oacute;|&#xD4|&#212;|Ô|&Ocirc;|&#xD5|&#213;|Õ|&Otilde;|&#xD6|&#214;|Ö|&Ouml;|&#xF2|&#242;|ò|&ograve;|&#xF3|&#243;|ó|&oacute;|&#xF4|&#244;|ô|&ocirc;|&#xF5|&#245;|õ|&otilde;|&#xF6|&#246;|ö|&ouml;|&#xF8|&#248;|ø|&oslash;)/gi,'o');
new_string = new_string.replace(/(&#x55|&#085;|U|&#x75|&#117;|&#xD9|&#217;|Ù|&Ugrave;|&#xDA|&#218;|Ú|&Uacute;|&#xDB|&#219;|Û|&Ucirc;|&#xDC|&#220;|Ü|&Uuml;|&#xF9|&#249;|ù|&ugrave;|&#xFA|&#250;|ú|&uacute;|&#xFB|&#251;|û|&ucirc;|&#xFC|&#252;|ü|&uuml;)/gi,'u');
new_string = new_string.replace(/(&#x59|&#089;|Y|&#x79|&#121;|&#xDD|&#221;|Ý|&Yacute;|&#xFD|&#253;|ý|&yacute;|&#xFF|&#255;|ÿ|&yuml;)/gi,'y');
new_string = new_string.replace(/(&#xC6|&#198;|Æ|&AElig;|&#xE6|&#230;|æ|&aelig;)/gi,'ae');
new_string = new_string.replace(/(&#x8C|&#140;|Œ|&OElig;|&#x9C|&#156;|œ|&oelig;)/gi,'oe');
new_string = new_string.toLowerCase();
return new_string;
}

function set_login_from_string(string) {
    var regexp_login = /^[a-z0-9A-Z\-]$/;
    var new_string = String (string);
    var created_string = String("");
    new_string = no_accent(new_string);

    for(i = 0; i < new_string.length; i++) {
        if(regexp_login.test(new_string.substr(i, 1))) {
            created_string += new_string.substr(i, 1);
        }
    }
    new_string = created_string;    
    new_string = new_string.toLowerCase();

    return new_string.substr(0,14);
}

/************************************************************************************************************
(C) www.dhtmlgoodies.com, October 2005

This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.

Updated:
        March, 11th, 2006 - Fixed positioning of tooltip when displayed near the right edge of the browser.
        April, 6th 2006, Using iframe in IE in order to make the tooltip cover select boxes.

Terms of use:
You are free to use this script as long as the copyright message is kept intact. However, you may not
redistribute, sell or repost it without our permission.

Thank you!

www.dhtmlgoodies.com
Alf Magne Kalleland

************************************************************************************************************/
var dhtmlgoodies_tooltip = false;
var dhtmlgoodies_tooltipShadow = false;
var dhtmlgoodies_shadowSize = 4;
var dhtmlgoodies_tooltipMaxWidth = 200;
var dhtmlgoodies_tooltipMinWidth = 100;
var dhtmlgoodies_iframe = false;
var tooltip_is_msie = (navigator.userAgent.indexOf('MSIE')>=0 && navigator.userAgent.indexOf('opera')==-1 && document.all)?true:false;
function showTooltip(e,tooltipTxt)
{

        var bodyWidth = Math.max(document.body.clientWidth,document.documentElement.clientWidth) - 20;

        if(!dhtmlgoodies_tooltip){
                dhtmlgoodies_tooltip = document.createElement('DIV');
                dhtmlgoodies_tooltip.id = 'dhtmlgoodies_tooltip';
                dhtmlgoodies_tooltipShadow = document.createElement('DIV');
                dhtmlgoodies_tooltipShadow.id = 'dhtmlgoodies_tooltipShadow';

                document.body.appendChild(dhtmlgoodies_tooltip);
                document.body.appendChild(dhtmlgoodies_tooltipShadow);

                if(tooltip_is_msie){
                        dhtmlgoodies_iframe = document.createElement('IFRAME');
                        dhtmlgoodies_iframe.frameborder='5';
                        dhtmlgoodies_iframe.style.backgroundColor='#FFFFFF';
                        dhtmlgoodies_iframe.src = '#';
                        dhtmlgoodies_iframe.style.zIndex = 100;
                        dhtmlgoodies_iframe.style.position = 'absolute';
                        document.body.appendChild(dhtmlgoodies_iframe);
                }

        }

        dhtmlgoodies_tooltip.style.display='block';
        dhtmlgoodies_tooltipShadow.style.display='block';
        if(tooltip_is_msie)dhtmlgoodies_iframe.style.display='block';

        var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
        if(navigator.userAgent.toLowerCase().indexOf('safari')>=0)st=0;
        var leftPos = e.clientX + 10;

        dhtmlgoodies_tooltip.style.width = null;	// Reset style width if it's set
        dhtmlgoodies_tooltip.innerHTML = tooltipTxt;
        dhtmlgoodies_tooltip.style.left = leftPos + 'px';
        dhtmlgoodies_tooltip.style.top = e.clientY + 10 + st + 'px';


        dhtmlgoodies_tooltipShadow.style.left =  leftPos + dhtmlgoodies_shadowSize + 'px';
        dhtmlgoodies_tooltipShadow.style.top = e.clientY + 10 + st + dhtmlgoodies_shadowSize + 'px';

        if(dhtmlgoodies_tooltip.offsetWidth>dhtmlgoodies_tooltipMaxWidth){	/* Exceeding max width of tooltip ? */
                dhtmlgoodies_tooltip.style.width = dhtmlgoodies_tooltipMaxWidth + 'px';
        }

        var tooltipWidth = dhtmlgoodies_tooltip.offsetWidth;
        if(tooltipWidth<dhtmlgoodies_tooltipMinWidth)tooltipWidth = dhtmlgoodies_tooltipMinWidth;


        dhtmlgoodies_tooltip.style.width = tooltipWidth + 'px';
        dhtmlgoodies_tooltipShadow.style.width = dhtmlgoodies_tooltip.offsetWidth + 'px';
        dhtmlgoodies_tooltipShadow.style.height = dhtmlgoodies_tooltip.offsetHeight + 'px';

        if((leftPos + tooltipWidth)>bodyWidth){
                dhtmlgoodies_tooltip.style.left = (dhtmlgoodies_tooltipShadow.style.left.replace('px','') - ((leftPos + tooltipWidth)-bodyWidth)) + 'px';
                dhtmlgoodies_tooltipShadow.style.left = (dhtmlgoodies_tooltipShadow.style.left.replace('px','') - ((leftPos + tooltipWidth)-bodyWidth) + dhtmlgoodies_shadowSize) + 'px';
        }

        if(tooltip_is_msie){
                dhtmlgoodies_iframe.style.left = dhtmlgoodies_tooltip.style.left;
                dhtmlgoodies_iframe.style.top = dhtmlgoodies_tooltip.style.top;
                dhtmlgoodies_iframe.style.width = dhtmlgoodies_tooltip.offsetWidth + 'px';
                dhtmlgoodies_iframe.style.height = dhtmlgoodies_tooltip.offsetHeight + 'px';

        }

}

function hideTooltip()
{
        dhtmlgoodies_tooltip.style.display='none';
        dhtmlgoodies_tooltipShadow.style.display='none';
        if(tooltip_is_msie)dhtmlgoodies_iframe.style.display='none';
}

/******************* Gestion des tableaux ajax *************************/
function empty_node(Node) {
    while(Node.hasChildNodes() == true) {
        var childList = Node.childNodes;

        if(childList.item(0).hasChildNodes() == true) empty_node(childList.item(0));
        else childList.item(0).parentNode.removeChild(childList.item(0));

    }
}

function fill_node(Node, content)  {
    Node.innerHTML = content;
}

/************ Affichage des wizzs *************/
var fadeout_wizz_timer = null;
function show_wizz(message, type, template_target, time) {
    var conteneur = document.getElementById('wizz_content');
    if(conteneur) {
        switch(type) {
            case "WIZZ_SUCCESS" :
                conteneur.innerHTML = "<img src=\"" + template_target + "/images/wizz_success_icone.gif\" alt=\"\" /><span>" + message + "</span>";
                conteneur.style.display = "block";
                conteneur.className = "wizz_success";
                fadeout_wizz_timer = setTimeout("fadeout_wizz()", time * 1000);
                break;
            case "WIZZ_ERROR" :
            default :
                conteneur.innerHTML = "<img src=\"" + template_target + "/images/wizz_error_icone.gif\" alt=\"\" /><span>" + message + "</span>";
                conteneur.style.display = "block";
                conteneur.className = "wizz_error";
                fadeout_wizz_timer = setTimeout("fadeout_wizz()", time * 1000);
                break;
        }
    }
    
}

var wizz_opacity = 100;
function fadeout_wizz() {
    wizz_opacity -= 5;
    var conteneur = document.getElementById('wizz_content');
    if(conteneur) {
        if(wizz_opacity <= 0) {
            clearTimeout(fadeout_wizz_timer);
            fadeout_wizz_timer = null;
            conteneur.className = "";
            conteneur.style.display = "none";
            wizz_opacity = 100;
            changeOpac(wizz_opacity, "wizz_content");
        }
        else {
            fadeout_wizz_timer = setTimeout("fadeout_wizz()", 100);
            changeOpac(wizz_opacity, "wizz_content");
        }
    }   
}

function changeOpac(opacity, id) {
    var object = document.getElementById(id).style;
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
}

$(function() {
    
   // Script requis pour changer la présentation des anciens modules pour les faire ressembler à la nouvelle version de igestis
   $(".normal_table").addClass("old-igestis-table table table-bordered table-condensed table-striped").removeClass("normal_table");
   $("#main-title").html($("#help_panel h1").html());
   $("#main-title-help").html($("#help_panel h2").html());
   
   // Gestion de la pagniation
   var paginationContent = $("#pagination-old-modules").remove();
   if(paginationContent.html()) {
       $(".old-igestis-table").after(paginationContent.html()).before("<hr />" + paginationContent.html());
       $(".paging_bootstrap").addClass("pull-right");
   }
   
   $(".main-content-top-actions").find("li a").each(function() {
       button = $(this).addClass("btn btn-small btn-success");
       $(this).remove();
       $(".main-content-top-actions").append(button).append("&nbsp;");
   });
   
   // Mange the search field
   var regexp = /'(.*)'/;
   var test = regexp.exec($(".search-field").attr("onfocus"));
   if(test != null) {
       var term  = test[1];   
       
       $(".search-field").before($("<label>" + term + "</label>"));
       $(".search-field").attr("placeholder", "Termes de la recherche");
       $(".search-field").removeAttr("onfocus");
       if($(".search-field").val() == term) {
           $(".search-field").val('');
       }
   }
   
   // Manage the search button
   $("form").addClass("allow-to-quit");
   $("ul.search-form form").append("<li><button type=\"submit\" class=\"btn btn-primary btn-small pull-right\">" + translations.search + "</button></li>");
   // Delete the previous button
   $("ul.search-form form").find("input:image, input:submit").remove().parents('li').remove();
   
   // Relace old border div by the new twitter style;
   $(".upperleft").addClass("well").removeClass("upperleft");
   

   // Replace button on the popup forms
   $("input:image").each(function() {
       var srcImage = /images\/[A-Z\/]*(.*)$/;
       found =  srcImage.exec($(this).attr("src"));
       if(found != null)  {
           switch(found[1]) {
               case "bt_valider.gif" :
                   $(this)
                    .after("<button type=\"submit\" class=\"btn btn-primary btn-small\">" + translations.save + "</button>");
                   $(this).remove();
                   break;
                case "add.png" :
                    //$(this)                    
                    //.after("<button type=\"submit\" class=\"btn btn-success btn-small\"><i class=\"icon-plus icon-white\"></i>" + translations.add + "</button>");
                   //$(this).remove();
                    break;
                default:
                    break;
           }
       }
   });
   
   // Hack for the iprojectis new/edit project special page format
   $(".main-content-top-actions #view_files").removeAttr("style");
   $(".main-content-top-actions form").removeAttr("style");
   $(".main_content_right").find("br").each(function() {
      if($(this).parent().hasClass("main_content_right")) {
          $(this).remove();
      }
   });
   
   var menuLi = $("#sidebarSpan3").find("ul > li");
   if(menuLi.length >= 3) {
       $("#pageContentSpan9").removeClass("span12").addClass("span9");
       $("#sidebarSpan3").addClass("well sidebar-nav").show();
   }
   else {
      $("#sidebarSpan3").remove();
   }
   
   
   
});