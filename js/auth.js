$(document).ready(function() {
    $('#auth').submit(function(e) {
        e.preventDefault();
        $('.error_message').text('');
        $.ajax({
            type: "POST",
            url: '/Login',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                if (jsonData['loginResult'] === true) {
                    $('.modal_background').removeClass('transparent');
                    $('.modal_background').addClass('visible');
                    $('#email_confirm').parent().css('display', 'block');
                    $('.email_info').text(jsonData['email']);
                    $('#email_confirm input[name="login"]').val($('#auth input[name="login"]').val());
                    $('#email_confirm input[name="password"]').val($('#auth input[name="password"]').val());
                }
                else {
                    $('.error_message').text('Неверный логин или пароль.')
                }
           },
           error: function(_)
           {
            $('.error_message').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });

     $('#email_confirm').submit(function(e) {
        e.preventDefault();
        $('.error_confirm').text('');
        $.ajax({
            type: "POST",
            url: '/Login',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                if (jsonData['confirmResult'] === true) {
                    location.href = '/';
                }
                else {
                    $('.error_confirm').text('Неверный проверочный код.')
                }
           },
           error: function(_)
           {
            $('.error_confirm').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });

     $('body').on('click', "#auth a", function(e) {
        e.preventDefault();
        $('.modal_background').removeClass('transparent');
        $('.modal_background').addClass('visible');
        $('#forget_password').parent().css('display', 'block');
     });
});