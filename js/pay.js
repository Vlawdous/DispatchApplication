$(document).ready(function() {
    $('#meters').submit(function(e) {
        e.preventDefault();
        $('.error_message').text('');
        $.ajax({
            type: "POST",
            url: '/Pay',
            data: $(this).serialize(),
            success: function(response)
            {
                var jsonData = JSON.parse(response);
                if (jsonData['meterResult'] === false) {
                    $('.error_message').text('Такого счётчика нет.');
                }
                else {
                    resetForm($('#meters'));
                }
            },
           error: function(_)
           {
            $('.error_message').text('Неизвестная ошибка, попробуйте позже.')
          }
       });
     });
});

function resetForm($form) {
    $form.find('input:text, input:password, textarea').val('');
}