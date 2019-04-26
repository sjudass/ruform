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


$(document).ready(function () {
    $('form').submit(function (event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: "/login",
            data:
                $(this).serialize(),
            cache: false,
            processData: false,
            success: function (result){
                if (result){
                    $('form').html(result);

                    var element=document.getElementById('error');
                    if (!element)
                    {
                        $('.login_generate').removeClass('active');
                        $('.bg_login').fadeOut();
                        location.reload();
                    }
                }
                else{
                    alert('Ошибка авторизации');
                }
            }
        })/*.done(function (data) {
            $('.nav').html(data);
            $('form').html(data);
        })*/;
    });
});
