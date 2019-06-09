/**
 * Created by antoh on 25.04.2019.
 */
$('#login_open').click(function (event) {
    event.preventDefault();
    $('.login_generate').addClass('active');
    $('.bg_login').fadeIn();

    $('.bg_login').click(function () {
        $('.login_generate').removeClass('active');
        $('.bg_login').fadeOut();
    })
});

$('#service_open').click(function (event) {
    event.preventDefault();
    $('.service_generate').addClass('active');
    $('.bg_login').fadeIn();

    $('.bg_login').click(function () {
        $('.service_generate').removeClass('active');
        $('.bg_login').fadeOut();
    })
});

$(document).ready(function () {
    $('#login_form').submit(function (event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: "/login",
            data:
                $(this).serialize(),
            async: true,
            success: function (result){
                if (result){
                    $('#login_form').html(result);

                    var element=document.getElementById('error');
                    if (!element)
                    {
                        $('.login_generate').removeClass('active');
                        $('.bg_login').fadeOut();
                        location.reload();
                    }
                    else
                    {
                        window.setTimeout(function(){
                            $('.alert-danger').alert('close');
                        },3000);
                    }
                }
                else{
                    alert('Ошибка авторизации');
                }
            }
        });
    });

    $('#service_form').submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "/application",
            data:
                $(this).serialize(),
            async: true,
            success: function (result){
                document.forms["service_form"].reset();
                if (result){
                    $('#alert-success').addClass('alert-active');
                    window.setTimeout(function(){
                        $('#alert-success').removeClass('alert-active');
                        $('.service_generate').removeClass('active');
                        $('.bg_login').fadeOut();
                    },3000);
                }
                else{
                    $('#alert-danger').addClass('alert-active');
                    window.setTimeout(function(){
                        $('#alert-danger').removeClass('alert-active');
                    },3000);
                }
            }
        });
    });

    var first_start = false;
    $('#chat_form').submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "/chat",
            data:
                $(this).serialize(),
            async: true,
            success: function (result){
                if (result){
                    $('#chat_content').html(result);
                    $url = $('#dialog_form');
                    dialog_submit();
                }
                else{
                    alert('Ошибка отправки данных');
                }
            }
        });
    });

    var $url = $('#dialog_form');

    function setDialogMessage() {
        $.ajax({
            type: "POST",
            url: $url.attr("action"),
            data:
                $($url).serialize(),
            async: true,
            success: function (result){
                if (typeof result !== 'undefined' || result !== null){
                    $('#message_block').html(result);
                    var objDiv = document.getElementById("message_block");
                    objDiv.scrollTop = objDiv.scrollHeight;
                    $($url)[0].reset();
                }
                else{
                    alert('Ошибка отправки данных');
                }
            }
        });
    }

    function getDialogMessages() {
        $.ajax({
            type: "GET",
            url: $url.attr("action"),
            success: function (data) {
                $('#message_block').html(data);
                var objDiv = document.getElementById("message_block");
                if ($('#message_block').is(':hover')) {}
                else {
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            },
            complete: function (data){
                if (data){
                    clearTimeout(timeout);
                    var timeout = setTimeout(getDialogMessages,10000);
                }
            }
        });
    }

    $('#consult_form').submit(function (event) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: $('#consult_form').attr("action"),
            data:
                $(this).serialize(),
            async: true,
            success: function (result){
                if (result){
                    $('#consult_dialog').html(result);
                    var objDiv = document.getElementById("consult_dialog");
                    objDiv.scrollTop = objDiv.scrollHeight;
                    $('#consult_form')[0].reset();
                }
                else{
                    alert('Ошибка отправки данных');
                }
            }
        });
    });

    function dialog_submit() {
        $('#dialog_form').submit(function (event) {
            event.preventDefault();
            if (!first_start)
            {
                setDialogMessage();
                getDialogMessages();
                first_start = true;
            }
            else
            {
                setDialogMessage();
            }
        });
    }

    $('#dialog_form').submit(function (event) {
        event.preventDefault();
        setDialogMessage();
    });

    var chatExists = document.getElementById("message_block");
    if (chatExists)
    {
        getDialogMessages();
    }


});


$(function () {
    var $consult_form = document.getElementById("consult_form");
    if ($consult_form){
        var $url = "/getdialoglist" + $('#consult_form').attr("action").slice(8)
    }
    else
    {
        var $url = "/getdialogs"
    }
    function getDialogList() {
        $.ajax({
            type: "GET",
            url: $url,
            success: function (data) {
                if ($('#search').is(':focus')) {}
                else {
                    $('.list').html(data);
                }
            },
            complete: function (data){
                if (data){
                    clearTimeout(timer);
                    var timer = setTimeout(getDialogList,30000);
                }
            }
        });
    }

    function getDialogConsult() {
        $.ajax({
            type: "GET",
            url: "/get" + $('#consult_form').attr("action").slice(1),
            success: function (data) {
                $('#consult_dialog').html(data);
                var objDiv = document.getElementById("consult_dialog");
                if ($('#consult_dialog').is(':hover')) {}
                else {
                    objDiv.scrollTop = objDiv.scrollHeight;
                }
            },
            complete: function (data){
                if (data){
                    clearTimeout(timer);
                    var timer = setTimeout(getDialogConsult,10000);
                }
            }
        });
    }
    var dialogListExist = document.getElementById("dialog_list");
    if (dialogListExist)
    {
        var dialogConsultExist = document.getElementById("consult_dialog");
        if (dialogConsultExist)
        {
            getDialogConsult();
            getDialogList();
        }
        else
        {
            getDialogList();
        }
    }
});

$(function(){

    //Живой поиск
    $('#search').bind("keyup click", function() {
        if(this.value.length >= 3){
            $.ajax({
                type: "POST",
                url: "/search", //Путь к обработчику
                data: {'search':this.value},
                response: 'text',
                success: function(data){
                    $(".list").html(data); //Выводим полученые данные в списке
                }
            })
        }
    });
});

$(document).ready(function(){

    var hide_chat = false;
    $('#closeChat').click(function(){
        if (!hide_chat)
        {
            $('div#chat').animate({top: -188}, 1000);
            hide_chat = true;
        }
        else
        {
            $('div#chat').animate({top: 670}, 1000);
            hide_chat = false;
        }
    });

    var hide_stat_app = false;
    $('#stat_app').click(function () {
        event.preventDefault();
        $.ajax({
            type: "GET",
            url: "/admin/applications/statistic",
            success: function (data) {
                $('#col-apps').text(data[0]['Всего']);
                var sers = {
                    series: [data[0]['Отказано'], data[0]['Поступила'], data[0]['На рассмотрении'], data[0]['В очереди'], data[0]['Выполнена']]
                };

                var options = {
                    labelInterpolationFnc: function(value) {
                        return value
                    },
                    donut: true,
                    donutWidth: 80
                };

                var responsiveOptions = [
                    ['screen and (min-width: 640px)', {
                        chartPadding: 50,
                        labelOffset: 0,
                        labelDirection: 'explode'
                    }],
                    ['screen and (min-width: 1024px)', {
                        labelOffset: 0,
                        chartPadding: 20
                    }]
                ];

                new Chartist.Pie('.ct-chart', sers, options, responsiveOptions);

                $('#chart_app').addClass('active');
                $('.bg_login').fadeIn();

                $('.bg_login').click(function () {
                    $('#chart_app').removeClass('active');
                    $('.bg_login').fadeOut();
                })
            }
        });
    })
});

$(document).ready( function () {
    $('.datatable').DataTable();
    $('#dialog_title').tooltip();
    $('.client-info').tooltip();
} );

$('.carousel').carousel({
    interval: 5000
});