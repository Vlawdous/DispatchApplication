$(document).ready(function() {
    $('#change_login').submit(function(e) {
        e.preventDefault();
        $('#change_login .error_message').text('');
        $.ajax({
            type: "POST",
            url: '/Profile',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                if (jsonData['changeLoginResult'] === true) {
                    resetForm($('#change_login'));
                }
                else {
                    $('#change_login .error_message').text(jsonData['message']);
                }
           },
           error: function(_)
           {
            $('#change_login .error_message').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });

     $('#change_password').submit(function(e) {
        e.preventDefault();
        $('#change_password .error_message').text('');
        $.ajax({
            type: "POST",
            url: '/Profile',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                if (jsonData['changePasswordResult'] === true) {
                    resetForm($('#change_password'));
                }
                else {
                    $('#change_password .error_message').text(jsonData['message']);
                }
           },
           error: function(_)
           {
            $('#change_password .error_message').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });

     $('#change_email').submit(function(e) {
        e.preventDefault();
        $('.modal_background').removeClass('transparent');
        $('.modal_background').addClass('visible');
        $('#change_email .error_message').text('');
        $.ajax({
            type: "POST",
            url: '/Profile',
            data: $(this).serialize(),
           error: function(_)
           {
            $('.modal_background').removeClass('visible');
            $('.modal_background').addClass('transparent');
            $('#change_email .error_message').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });

     $('#email_confirm').submit(function(e) {
        e.preventDefault();
        $('#email_confirm .error_confirm').text('');
        $.ajax({
            type: "POST",
            url: '/Profile',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                if (jsonData['confirmEmail'] === true) {
                    $('.modal_background').removeClass('visible');
                    $('.modal_background').addClass('transparent');
                    resetForm($('#change_email'));
                    resetForm($('#email_confirm'));
                }
                else {
                    $('#email_confirm .error_confirm').text('Неверный проверочный код.')
                }
           },
           error: function(_)
           {
            $('#email_confirm .error_confirm').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });

     $('#wish').submit(function(e) {
        e.preventDefault();
        $('#wish .error_confirm').text('');
        $.ajax({
            type: "POST",
            url: '/Profile',
            data: $(this).serialize(),
           error: function(_)
           {
            $('#wish .error_confirm').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
       resetForm($('#wish'));
     });
});

function resetForm($form) {
    $form.find('input:text, input:password, textarea').val('');
}