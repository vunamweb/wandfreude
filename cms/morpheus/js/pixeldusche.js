//****************************************************************************************************
//****************************************************************************************************
// THNXS TO BURKHARD KRAUPA - eine Tasse Kaffee trinkend ...
//****************************************************************************************************
//****************************************************************************************************
// pixel-dusche.de  - 2007
//****************************************************************************************************
//****************************************************************************************************

/**
@param String url
@param object param {key: value} query parameter
*/
function modifyURLQuery(url, param){
    var value = {};

    var query = String(url).split('?');

    if (query[1]) {
        var part = query[1].split('&');

        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }
    }

    value = $.extend(value, param);

    // Remove empty value
    for (i in value){
        if(!value[i]){
            delete value[i];
        }
    }

    // Return url with modified parameter
    if(value){
        return query[0] + '?' + $.param(value);
    } else {
        return query[0];
    }
}

function set_url(new_url) {
	if (history.pushState) {
		window.history.pushState("remove neu", "CMS Morpheus", new_url);
	} else {
		document.location.href = new_url;
	}

}

function getObjWidth (o) {
    return (o)?parseInt(o.offsetWidth):0;
}

function getObjHeight (o) {
    return (o)?parseInt(o.offsetHeight):0;
}

function addEvent (obj, evType, fn) {
    var o=(obj.id)?obj.id:obj.nodeName;
    if (obj.addEventListener) {
        obj.addEventListener(evType, fn, false);return true;
    } else if (obj.attachEvent) {
        var r = obj.attachEvent("on"+evType, fn);return r;
    } else return false;
}

function getEvTarget (e) {
    return (e.target)?e.target:e.srcElement;
}

var closedMenuHeight = 18;
var openItem = null;
var currentItem = null;
var active = null;
var speed = 2;
var itemHeight = 380;

function initMenu () {
    var menu=document.getElementById('menu'),menuHeight=getObjHeight(menu),arrItems=menu.getElementsByTagName('LI'),countItems=arrItems.length;
    //itemHeight=menuHeight-(countItems-1)*closedMenuHeight;

    for (var i=0; i<countItems; i++) {
        if (parseInt(i) === start) {
			openItem = arrItems[i];
			mh = document.getElementById('sw1').scrollHeight;
            arrItems[i].style.height=itemHeight+'px';
			 document.getElementById('sw'+i).style.background='url("images/table-header.gif")';
        } else {
            mH=getObjHeight(arrItems[i]);
            arrItems[i].style.height=closedMenuHeight+'px';
        }
        addEvent(arrItems[i],'click',showMenuPoint);
    }
}

function showMenuPoint (e) {
    var item =
getEvTarget(e);while(item.nodeName.toLowerCase()!='li')item=item.parentNode;
    if (item !== openItem && active==null) {
        currentItem = item;
        active = window.setInterval(doExpand,1);
    }
}

function doExpand () {
    currentItem.style.height = parseInt(currentItem.style.height) +
parseInt(speed) + 'px';
    openItem.style.height = parseInt(openItem.style.height) -
parseInt(speed) + 'px';

    if (parseInt(speed)>=50) {
        speed = 50;
    } else {
        speed++;
    }

    if (parseInt(currentItem.style.height)>=itemHeight) {
        currentItem.style.height = itemHeight+'px';
        openItem.style.height = closedMenuHeight+'px';
        cnameO=openItem.className;
        cnameC=currentItem.className;

 document.getElementById(cnameO).style.background='url("images/table-header-cl.gif")';


 document.getElementById(cnameC).style.background='url("images/table-header.gif")';

        openItem = currentItem;
        window.clearInterval(active);
        active = null;
        speed = 2;
    }
}

//****************************************************************************************************

