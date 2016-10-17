function trim(str) {
    return str.replace(/^\s+|\s+$/g,"");
}

function DeleteConfirm(name, pk, csrftoken) {
    if (confirm('Delete ' + name + '?')) {
        event.preventDefault();
        transfer(document.URL,
          {'action_delete': 1, 'pk': pk});
    }
}

// for FK only
	
function ClearField(id) {
	$('#' + id).val('');
	$('#' + id + '_label').val('');
}

function ChooseSpecialty(id) {
    openWindow('specialty.php', {'choose': 'yes', 'id': id});
//    window.open("specialty.php?choose=yes&id=" + id, "_blank",
//      "height=600, width=800, top=100, left=100, tab=no, " +
//      "location=no, menubar=no, status=no, toolbar=no", false);
}

function MadeChoice(id, result, label) { // executes in popup
    window.opener.HaveChoice(id, result, label);
    window.close();
}

function HaveChoice(id, result, label) { // executes in main window
    $('#' + id).val(result);
    $('#' + id + '_label').val(label);
}

function PasswordDidChange(id, username) {
    $('#password-strength').
      html(passwordStrength($('#' + id).val(), username));
}

// Password strength meter
// This jQuery plugin is written by firas kassem [2007.04.05]
// Firas Kassem  phiras.wordpress.com || phiras@gmail.com
// for more information:
// http://phiras.wordpress.com/2007/04/08/password-strength-
// meter-a-jquery-plugin/
var shortPass = 'Too short'
var badPass = 'Weak' // MJR changed from 'bad'
var goodPass = 'Good'
var strongPass = 'Strong'

function passwordStrength(password, username)
{
    score = 0

    //password < 10  -- MJR changed from 4 to 10
    if (password.length < 10) {
        return shortPass
    }

    //password == username
    if (password.toLowerCase() == username.toLowerCase())
        return badPass

    //password length
    score += password.length * 4
    score += (checkRepetition(1, password).length - password.length) * 1
    score += (checkRepetition(2, password).length - password.length) * 1
    score += (checkRepetition(3, password).length - password.length) * 1
    score += (checkRepetition(4, password).length - password.length) * 1

    //password has 3 numbers
    if (password.match(/(.*[0-9].*[0-9].*[0-9])/))
        score += 5

    //password has 2 symbols
    if (password.match(
      /(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/))
        score += 5

    //password has Upper and Lower chars
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
        score += 10

    //password has number and chars
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))
        score += 15
    //
    //password has number and symbol
    if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([0-9])/))
        score += 15

    //password has char and symbol
    if (password.match(/([!,@,#,$,%,^,&,*,?,_,~])/) && password.match(/([a-zA-Z])/))
        score += 15

    //password is just a nubers or chars
    if (password.match(/^\w+$/) || password.match(/^\d+$/))
        score -= 10

    //verifing 0 < score < 100
    if (score < 0)
        score = 0
    if (score > 100)
        score = 100

    if (score < 34)
        return badPass
    if (score < 68)
        return goodPass
    return strongPass
}


// checkRepetition(1,'aaaaaaabcbc')   = 'abcbc'
// checkRepetition(2,'aaaaaaabcbc')   = 'aabc'
// checkRepetition(2,'aaaaaaabcdbcd') = 'aabcd'

function checkRepetition(pLen,str) {
    res = ""
    for ( i=0; i<str.length ; i++ ) {
        repeated=true
        for (j=0;j < pLen && (j+i+pLen) < str.length;j++)
            repeated=repeated && (str.charAt(j+i)==str.charAt(j+i+pLen))
        if (j<pLen) repeated=false
        if (repeated) {
            i+=pLen-1
            repeated=false
        }
        else {
            res+=str.charAt(i)
        }
    }
    return res
}

// Based on contribution from Rakesh Pai on StackOverflow
// (http://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit/3259946#3259946)
function transfer(url, params) {
    var form = document.createElement("form");
    form.setAttribute("method", 'post');
    form.setAttribute("action", url);
    for(var key in params) {
        if (params.hasOwnProperty(key)) {
            appendHiddenField(form, key, params[key]);
         }
    }
    appendHiddenField(form, 'csrftoken', csrftoken);
    $(document).ready(function () {
        document.body.appendChild(form);
        form.submit();
    });
}

var openWindowNumber = 0;

// Based on http://stackoverflow.com/questions/3951768/window-open-and-pass-parameters-by-post-method-problem
function openWindow(url, params) {
    openWindowNumber++;
    var target = 'EPMADD-' + openWindowNumber;
    var form = document.createElement("form");
    form.setAttribute("method", 'post');
    form.setAttribute("action", url);
    form.setAttribute("target", target);
    for(var key in params)
        if (params.hasOwnProperty(key))
            appendHiddenField(form, key, params[key]);
    appendHiddenField(form, 'csrftoken', csrftoken);
    window.open('', target,
      "height=600, width=800, top=100, left=100, tab=no, " +
      "location=no, menubar=no, status=no, toolbar=no", false);
    form.submit();
}

function appendHiddenField(form, key, val) {
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", key);
    hiddenField.setAttribute("value", val);
    form.appendChild(hiddenField);
}

function browser_signature(url, params) {
    var div = document.createElement('div');
    div.setAttribute('id', 'inch');
    div.setAttribute('style',
      'width:1in;height:1in;position:absolute');
    var t = document.createTextNode(' '); // needed?
    div.appendChild(t);
    document.body.appendChild(div);
/*
            <div id="inch" style="width:1in;height:1in;position:absolute">&nbsp;</div>
*/
    var x = navigator.userAgent + '-';
    x += document.getElementById("inch").offsetWidth + '-' +
      document.getElementById("inch").offsetWidth;
    if (typeof(screen.width) == "number")
        x += '-' + screen.width;
    if (typeof(screen.height) == "number")
        x += '-' + screen.height;
    if (typeof(screen.availWidth) == "number")
        x += '-' + screen.availWidth;
    if (typeof(screen.availHeight) == "number")
        x += '-' + screen.availHeight;
    if (typeof(screen.pixelDepth) == "number")
        x += '-' + screen.pixelDepth;
    if (typeof(screen.colorDepth) == "number")
        x += '-' + screen.colorDepth;
    params['browser'] = x;
    transfer(url, params);
}

function getCookie(name) {
    var start = document.cookie.indexOf(name + "=");
    var len = start + name.length + 1;
    if ((!start) && (name != document.cookie.substring(0, name.length))) {
        return null;
    }
    if (start == -1)
        return null;
    var end = document.cookie.indexOf(';', len);
    if (end == -1)
        end = document.cookie.length;
    return unescape(document.cookie.substring(len, end));
}

// expires is in days from today
// secure is boolean
function setCookie(name, value, expires, path, domain, secure) {
    var today = new Date();
    today.setTime(today.getTime());
    if (expires)
        expires = expires * 1000 * 60 * 60 * 24;
    var date = new Date(today.getTime() + (expires));
    document.cookie = name + '=' + escape(value) +
      ((expires) ? ';expires=' + date.toGMTString() : '') +
      ((path) ? ';path=' + path : '') +
      ((domain) ? ';domain=' + domain : '') +
      ((secure) ? ';secure' : '');
}

function deleteCookie(name, path, domain) {
    if (getCookie(name))
        document.cookie = name + '=' +
                ((path) ? ';path=' + path : '') +
                ((domain) ? ';domain=' + domain : '') +
                ';expires=Thu, 01-Jan-1970 00:00:01 GMT';
}
