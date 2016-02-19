(function() {
    moment.locale('es');
    if (Cookies.get('user') !== null && Cookies.get('pass') !== null) {
        $('#login-username').val(Cookies.get('user'));
        $('#login-password').val(Cookies.get('pass'));
    }
    $("#btn-login").click(function() {
        checkLoginAndPassword();
    });
    $("#btnEnviar").click(function() {
        sendMessage();
    });
    $('#btn-signup').click(function() {
        if (!$('signUpPasswd').val() && !$('#signUpPasswd').val() && $('#signUpPasswd').val() == $('#signUprPasswd').val()) {
        }
    });
    $("#novaEnquesta").click(function() {
        $("#chat").addClass("hidden");
        $("#enquesta").removeClass("hidden");
    });
    $("#tancaEnquesta").click(function() {
        $("#enquesta").addClass("hidden");
        $("#chat").removeClass("hidden");
    });
    /*
     * [Chat] =>
     * Enviar missatge amb pitjan ENTER.
     */
    $("#messageToSend").keypress(function(e) {
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
            $('#btnEnviar').click();
            return false;
        } else {
            return true;
        }
    });
    /*
     * [Registrar-se] =>
     * Contrasenya i repetir contrasenya, comprova que son iguals.
     */
    $('#signUprPasswd').change(function() {
        if ($('#signUpPasswd').val() !== $('#signUprPasswd').val()) {
            $('#signupalert').show()
        } else {
            $('#signupalert').hide()
        }
    });
})();
function getMessages() {
    setInterval(function() {
        requestAJAX("agafarMissatge.php", null, "agafarMissatges");
        changeDateTimes();
    }, 2000);
}
/**
 * asdasdasda
 * @return {clean}
 */
function updateCookie() {
    var user     = $('#login-username').val();
    var pass     = $('#login-password').val();
    var checkBox = $('#login-remember').is(':checked');
    if (Cookies.get('user') !== user && Cookies.get('pass') !== pass && checkBox) {
        Cookies.set('user', user);
        Cookies.set('pass', pass);
    }
}
function checkLoginAndPassword() {
    var user = $('#login-username').val();
    var pass = $('#login-password').val();
    if (user.trim() !== '' && pass.trim() !== '') {
        requestAJAX("validacio.php", {
            user: user,
            password: pass
        }, "checkLoginAndPassword");
    } else {
        //usuari o contrasenya buida
    }
}
function requestAJAX(url, parametres, option) {
    $.getJSON(url, parametres, function(data) {
        if (option == "checkLoginAndPassword") { //checkLoginAndPassword
            var respone = JSON.parse(data);
            if (respone[0].process == "success") {
                //success
                $('#loginbox').hide();
                $('#signupbox').hide();
                $('#chatbox').show();
                $('#userNameTitle').text($('#login-username').val());
                updateCookie();
                getMessages();
            } else {
                $('#login-alert').show();
                $('#login-alert').text("Usuari i/o contrasenya incorrecte!");
            }
        } else if (option == "sendMessage") { //sendMessage
        } else if (option = "agafarMissatges") { //agafarMissatges
            cleanUserOnline();
            $.each($(data), function(i, item) {
                if (item.MISSATGES.length > 0) {
                    $.each(item.MISSATGES, function(j, jtem) {
                        addMessage(jtem.MISSATGE, jtem.NOM, jtem.DATA);
                        scrollToBottom();
                    });
                }
                $.each(item.USUARIS, function(j, jtem) {
                    addUserOnline(jtem.ONLINE_USER);
                });
            });
        }
    });
}
function sendMessage() {
    var msg = $('#messageToSend').val();
    if (msg.trim() !== '') {
        requestAJAX("missatge.php", {
            missatge: msg
        }, "sendMessage");
    }
    $('#messageToSend').val("");
}
function addUserOnline(name) {
    var template = Handlebars.compile($("#useronline-template").html());
    var context = {
        Name: name
    };
    $("#usersHistory").append(template(context));
}
function addMessage(msg, name, data) {
    var template = Handlebars.compile($("#message-template").html());
    var context = {
        Message: msg,
        Name: name,
        Data: data
    };
    $(".new-message").remove();
    $("#chatHistory").append(template(context));
    changeDateTimes();
}
function cleanUserOnline() {
    $("#usersHistory").empty();
}
function scrollToBottom() {
    $("#chatHistory").scrollTop($("#chatHistory")[0].scrollHeight);
}
function changeDateTimes() {
    $(".new-date").remove();
    var classList = $('.hidden-data');
    $.each(classList, function(index, item) {
        var newTime = moment($(item).text(), "YYYY-MM-DD hh:mm:ss").fromNow();
        $(item).after("<span class='new-date'>" + newTime + "</span>");
    });
}
