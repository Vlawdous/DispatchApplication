<?php

namespace Application\Decorators;

use Application\Core\Router;
use Application\Core\Decorator;

class DispatcherDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function makeTable(array $tableInfo, bool $withBlocks = true): string
    {
        $withID = false;
        $result = ($withBlocks) ? '<div class="table">' : '';
        $result .= '<div class="overflow_table"><table>';
        $result .= '<thead><tr>';
        foreach ($tableInfo['columns'] as $column) {
            if ($column === 'ID') {
                $result .= '<th>Изменить</th>';
                $withID = true;
                continue;
            }
            $result .= "<th>{$column}</th>";
        }
        $result .= '</tr>';
        foreach ($tableInfo['rows'] as $row) {
            $result .= '<tr>';
            if ($withID) {
                $action = $this->controller->getAction()[0];
                $result .= "<td><form method='post' action='/Dispatcher/{$action}/Update' id='update_work'><input type='hidden' name='id_work' value='{$row[0]}'><input type='submit' value='Обновить'></form>";
                if ($action !== 'Items') {
                    $result .= "<form method='post' action='/Dispatcher/{$action}/Delete' id='delete_work'><input type='hidden' name='id_work' value='{$row[0]}'><input type='submit' value='Удалить'></form> </td>";
                }
                $row = array_slice($row, 1);
            }
            $countColumns = count($row);
            for ($i = 0; $i < $countColumns; $i++) {
                $result .= "<td>{$row[$i]}</td>";
            }
            $result .= '</tr>';
        }
        $result .= '</table></div><div class="pages">';
        if (isset($tableInfo['withPrevious']) && $tableInfo['withPrevious']) {
            $result .= '<div class="previous"><a href="#" class="prev_button"><</a></div>';
        }
        $result .= "<span class='number_page'>{$tableInfo['numberPage']}</span>";
        if (isset($tableInfo['withNext']) && $tableInfo['withNext']) {
            $result .= '<div class="next"><a href="#" class="next_button">></a></div>';
        }
        $result .= '</div>';
        if ($withBlocks) {
            $result .= '</div>';
        }
        return $result;
    }
    private function makeFilter(array $inputPlaceholder, array $inputName): string
    {
        $action = $_SERVER['REQUEST_URI'];
        $result = "<div class='filter'><h2>Фильтрация</h2><form method='post' action='{$action}' id='filter'>";
        $countInputs = count($inputPlaceholder);
        for ($i = 0; $i < $countInputs; $i++) {
            $result .= "<input name='filter[{$inputName[$i]}]' type='text' placeholder='{$inputPlaceholder[$i]}'>";
        }
        $result .= '<input type="submit" value="Отфильтровать"></form></div">';
        return $result;
    }
    private function makeMessage(array $message): string
    {
        $result = '<div class="message_box">';
        if ($message !== []) {
            $result .= '<h1>Сообщение</h1>';
            $result .= "<div class = 'message'>{$message['message']}</div>";
            $result .= "<div class = 'buttons'>
                            <form method='post' action='/Dispatcher/Messages'><input type='hidden' value='{$message['id_message']}' name='reject_id'><input type='submit' value='Отклонить'></form>
                            <form method='post' action='/Dispatcher/Messages'><input type='hidden' value='{$message['id_message']}' name='accept_id'><input type='submit' value='Принять'></form>
                        </div>";
        } else {
            $result .= '<h1>На данный момент сообщений от пользователей нет</h1>';
        }
        $result .= '</div>';
        return $result;
    }
    private function makeForm(): string
    {
        $action = $this->controller->getAction();
        $actionPost = $_SERVER['REQUEST_URI'];
        $result = "<div class='form'>";
        $result .= "<form method='post' action='{$actionPost}' id='former'>";
        switch ($action[1]) {
            case 'Add':
                $result .= '<h1>Добавить</h1>';
                break;
            case 'Update':
                $result .= '<h1>Обновить</h1>';
                if (($action[0] === 'Emergency') || ($action[0] === 'Planned') || ($action[0] === 'Paid')) {
                    $id = $this->controller->getIdUpdate();
                    break;
                } elseif ($action[0] === 'Items') {
                    $idItem = $this->controller->getIdUpdate();
                }
        }
        $workers = $this->model->getWorkers();
        if (($action[0] === 'Emergency') || ($action[0] === 'Planned') || ($action[0] === 'Paid')) {
            if (isset($id)) {
                $values = $this->model->getUpdatingTable(constant(mb_strtoupper($action[0]) . '_TABLE'), $id);
                $selectWorkers = $this->model->getUpdatingWorkers(constant(mb_strtoupper($action[0]) . '_TABLE'), $id);
                if (isset($values['date_start'])) {
                    $values['date_start'] = explode(' ', $values['date_start']);
                    $values['date_start'] = $values['date_start'][0] . 'T' . $values['date_start'][1];
                }
                if (isset($values['date_end'])) {
                    $values['date_end'] = explode(' ', $values['date_end']);
                    $values['date_end'] = $values['date_end'][0] . 'T' . $values['date_end'][1];
                }
                $result .= "<input type='hidden' name='id_work' value='{$id}'>";
            }
            $result .= '<textarea placeholder="Описание" name="description" required>';
            if (isset($values['description'])) {
                $result .= $values['description'];
            }
            $result .= '</textarea>';
            $result .= '<h2>Дата и время начала работы</h2>';
            $result .= '<input ';
            if (isset($values['date_start'])) {
                $result .= "value='{$values['date_start']}'";
            }
            $result .= 'type="datetime-local" name="date_start" required>';
            $result .= '<h2>Выбрать работников</h2><select  name = "id_workers[]"" multiple="multiple" required>';
            if (isset($selectWorkers) && (!empty($selectWorkers))) {
                $index = 0;
            }
            foreach ($workers as $worker) {
                if (isset($index) && ($worker['id'] == $selectWorkers[$index]['id_worker'])) {
                    $selected = 'selected';
                    $index++;
                } else {
                    $selected = '';
                }
                $result .= "<option {$selected} value = '{$worker['id']}'>{$worker['fio']} - {$worker['prof']}</option>";
            }
            $result .= '</select>';
            switch ($action[0]) {
                case 'Emergency':
                    $result .= '<h2>Геолокация</h2><input ';
                    if (isset($values['adress'])) {
                        $result .= "value='{$values['adress']}'";
                    }
                    $result .= 'type="text" placeholder="Адрес" name="adress" required>';
                    $result .= '<input ';
                    if (isset($values['number_apartment'])) {
                        $result .= "value='{$values['number_apartment']}'";
                    }
                    $result .= 'type="number" placeholder="Номер квартиры" name="number_apartment" required min="0">';
                    break;
                case 'Planned':
                    $result .= '<h2>Геолокация</h2><input ';
                    if (isset($values['adress'])) {
                        $result .= "value='{$values['adress']}'";
                    }
                    $result .= 'type="text" placeholder="Адрес" name="adress" required>';
                    $result .= '<h2>Дата и время конца работы</h2><input ';
                    if (isset($values['date_end'])) {
                        $result .= "value='{$values['date_end']}'";
                    }
                    $result .= 'type="datetime-local" name="date_end" required>';
                    break;
                case 'Paid':
                    $result .= '<h2>Геолокация</h2><input ';
                    if (isset($values['full_adress'])) {
                        $result .= "value='{$values['full_adress']}'";
                    }
                    $result .= 'type="text" placeholder="Полный адрес" name="full_adress" required>';
                    $result .= '<h2>Цена</h2><input ';
                    if (isset($values['price'])) {
                        $result .= "value='{$values['price']}'";
                    }
                    $result .= 'type="number" placeholder="Цена" name="price" required min="0">';
                    break;
            }
        }
        if ($action[0] === 'Items') {
            if (isset($idItem)) {
                $result .= "<h2>Предмет</h2><input type='text' name='name_item' value='{$idItem}' readonly>";
                $result .= "<h2>Изменение количества</h2><input type='text' name='count' inputmode='numeric' required pattern='[-]?\d+''>";
                $result .= '<h2>Кто</h2><select name = "id_worker" required>';
                foreach ($workers as $worker) {
                    $result .= "<option value = '{$worker['id']}'>{$worker['fio']} - {$worker['prof']}</option>";
                }
                $result .= '</select>';
            }
        }
        $result .= '<span class="error_message"></span><input type="submit" value="Принять"></form></div>';
        return $result;
    }

    public function getBody(): string
    {
        $action = $this->controller->getAction();
        if (isset($action)) {
            if (isset($action[1])) {
                switch (true) {
                    case (($action[0] === 'Emergency') && ($action[1] === 'Works')):
                        return ($this->makeTable($this->model->getTable(EMERGENCY_TABLE, 1)) . $this->makeFilter(['Адрес', 'Номер квартиры', 'Описание', 'Работник'], ['adress', 'number_apartment', 'description', 'full_name']));
                            break;
                    case (($action[0] === 'Planned') && ($action[1] === 'Works')):
                        return ($this->makeTable($this->model->getTable(PLANNED_TABLE, 1)) . $this->makeFilter(['Адрес', 'Описание', 'Работник'], ['adress', 'description', 'full_name']));
                            break;
                    case (($action[0] === 'Paid') && ($action[1] === 'Works')):
                        return ($this->makeTable($this->model->getTable(PAID_TABLE, 1)) . $this->makeFilter(['Полный адрес', 'Описание', 'Работник'], ['full_adress', 'description', 'full_name']));
                            break;
                    case (($action[0] === 'Items') && ($action[1] === 'Update')):
                        return ($this->makeForm($action));
                        break;
                    case (($action[0] === 'Emergency') || ($action[0] === 'Planned') || ($action[0] === 'Paid')):
                        if (($action[1] == 'Add') || ($action[1] === 'Update')) {
                            return ($this->makeForm($action));
                        } else {
                            Router::notFound();
                        }
                        break;
                    default:
                        Router::notFound();
                        break;
                }
            } else {
                switch (true) {
                    case ($action[0] === 'Items'):
                        return ($this->makeTable($this->model->getTable(ITEMS_TABLE, 1)) . $this->makeFilter(['Название предмета'], ['name_item']));
                        break;
                    case ($action[0] === 'Messages'):
                        return $this->makeMessage($this->model->getMessage());
                        break;
                    case ($action[0] === 'Inhabitans'):
                        return ($this->makeTable($this->model->getTable(INHABITANS_TABLE, 1)) . $this->makeFilter(['Адрес', 'Номер квартиры', 'ФИО'], ['adress', 'number_apartment', 'full_name']));
                        break;
                    default:
                        Router::notFound();
                        break;
                }
            }
        } else {
            return '';
        }
    }
}
