function openMenu(menu, arrow) {
    menu.classList.add('menu_open');
    menu.classList.remove('menu_close');
    arrow.classList.add('icon_open');
    arrow.classList.remove('icon_close');
    event.preventDefault();
}
function closeMenu(menu, arrow) {
    menu.classList.remove('menu_open');
    menu.classList.add('menu_close');
    arrow.classList.remove('icon_open');
    arrow.classList.add('icon_close');
    event.preventDefault();
}

$(document).ready(function () {
    let filter = null;
    let previous = null;
    let page = 1;
    $('#filter').submit(function (e) {
        e.preventDefault();
        filter = getFilter();
        page = 1;   
        $.post({
            url: window.location.href,
            data: $(this).serialize(),
            success: response => {
                var jsonData = JSON.parse(response);
                $('.table').html(jsonData['newTable']);
                $('.prev_button').on('click', prevButtonEvent);
                $('.next_button').on('click', nextButtonEvent);
                resetForm($('#filter'));
            }
        });
    });
    $('#former').submit(function (e) {
        e.preventDefault();
        $('.error_message').text('');
        $.post({
            url: window.location.href,
            data: $(this).serialize(),
            success: function (response) {
                var jsonData = JSON.parse(response);
                if (jsonData['result'] === true) {
                    location.href = '/Dispatcher';
                }
                else {
                    $('.error_message').text(jsonData['message']);
                }
            },
            error: function (_) {
                $('.error_message').text('Неизвестная ошибка, попробуйте позже.')
            }
        });
    });
    $('.click_zone').on('click', event => {
        let element = event.target;
        let menu = element.nextElementSibling,
            arrow = element.childNodes[1];
        if (previous) {
            closeMenu(previous.previousMenu, previous.previousArrow);
            if (previous.previousMenu == menu) {
                previous = null;
                return;
            }
        }
        if (menu.classList.contains('menu_close')) {
            openMenu(menu, arrow);
        }
        else {
            closeMenu(menu, arrow);
        }
        previous = {
            previousMenu: menu,
            previousArrow: arrow
        };
    });
    let nextButtonEvent = event => {
        event.preventDefault();
        let inputData = (filter === null) ? { 'page': ++page } : { 'filter': filter, 'page': ++page }
        $.post({
            url: window.location.href,
            data: inputData,
            success: response => {
                var jsonData = JSON.parse(response);
                reloadTable(jsonData['newTable']);
            }
        });
    };
    let prevButtonEvent = event => {
        event.preventDefault();
        let inputData = (filter === null) ? { 'page': --page } : { 'filter': filter, 'page': --page }
        $.post({
            url: window.location.href,
            data: inputData,
            success: response => {
                var jsonData = JSON.parse(response);
                reloadTable(jsonData['newTable']);
            }
        });
    };
    let deleteWork = function (e) {
        e.preventDefault();
        let idWork = e.target[0].attributes[2].nodeValue;
        let inputData = (filter === null) ? { 'id_work': idWork, 'page': page } : { 'filter': filter, 'id_work': idWork, 'page': page }
        $.post({
            url: $(this).attr('action'),
            data: inputData,
            success: function (response) {
                var jsonData = JSON.parse(response);
                if (jsonData['result'] === true) {
                    reloadTable(jsonData['newTable']);
                }
            }
        });
    };
    $('.next_button').on('click', nextButtonEvent);
    $('.prev_button').on('click', prevButtonEvent);
    $('#delete_work').submit(deleteWork);

    function reloadTable(table) {
        $('.table').html(table);
        $('.prev_button').on('click', prevButtonEvent);
        $('.next_button').on('click', nextButtonEvent);
        $('#delete_work').submit(deleteWork);
    }
});

function resetForm($form) {
    $form.find('input:text, input:password, textarea').val('');
}
function getFilter() {
    let data = {};
    $('#filter').find('input[type="text"]').each(function () {
        var pattern = /(?<=\[)(.)+(?=\])/;
        name_value = pattern.exec(this.name)[0];
        data[name_value] = $(this).val();
    });
    return data;
}