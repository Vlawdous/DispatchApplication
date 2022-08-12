<?php

namespace Application\Models;

use Application\Core\Model;

define('INHABITANS_TABLE', 1);
define('EMERGENCY_TABLE', 2);
define('PLANNED_TABLE', 3);
define('PAID_TABLE', 4);
define('ITEMS_TABLE', 5);

class DispatcherModel extends Model
{
    use SqlTrait;
    use MailerTrait;

    private const COUNT_ROWS_ON_TABLE = 8;
    private ?array $filters = [];
    private int $page = 1;

    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }
    public function setPage(int $page): void
    {
        $this->page = $page;
    }
    public function getWorkers(?array $condition = null): array
    {
        $result = [];
        $condition = ($condition === null) ? '' : "WHERE id_{$condition['typeId']}={$condition['id']}";
        $resultQuery = $this->sql->query("SELECT id_worker, full_name, name_prof FROM workers {$condition}");
        if ($resultQuery !== false) {
            $resultQuery = $resultQuery->fetchAll();
            foreach ($resultQuery as $key => $row) {
                $result[$key]['id'] = $row['id_worker'];
                $result[$key]['fio'] = $row['full_name'];
                $result[$key]['prof'] = $row['name_prof'];
            }
        }
        return $result;
    }
    public function getUpdatingTable($table, $id): array
    {
        $result = [];
        switch ($table) {
            case EMERGENCY_TABLE:
                $typeWork = 'emergency';
                break;
            case PLANNED_TABLE:
                $typeWork = 'planned';
                break;
            case PAID_TABLE:
                $typeWork = 'paid';
                break;
        }
        $resultQuery = $this->sql->query("SELECT * from {$typeWork}_works WHERE id_{$typeWork} = {$id}");
        if ($resultQuery !== false) {
            $result = $resultQuery->fetch();
        }
        return $result;
    }
    public function getUpdatingWorkers($table, $id): array
    {
        $result = [];
        switch ($table) {
            case EMERGENCY_TABLE:
                $typeWork = 'emergency';
                break;
            case PLANNED_TABLE:
                $typeWork = 'planned';
                break;
            case PAID_TABLE:
                $typeWork = 'paid';
                break;
        }
        $resultQuery = $this->sql->query("SELECT id_worker FROM {$typeWork}_workers WHERE id_{$typeWork} = {$id}");
        if ($resultQuery !== false) {
            $result = $resultQuery->fetchAll();
        }
        return $result;
    }
    public function insertIntoTable(int $table, array $values): array
    {
        switch ($table) {
            case EMERGENCY_TABLE:
                $typeWork = 'emergency';
                break;
            case PLANNED_TABLE:
                $typeWork = 'planned';
                break;
            case PAID_TABLE:
                $typeWork = 'paid';
                break;
            default:
                return ['result' => false, 'message' => 'Такой таблицы не существует'];
                break;
        }
        $needComma = false;
        $columns = '';
        foreach ($values as $key => $value) {
            if ($key === 'id_workers') {
                continue;
            }
            $columns .= $needComma ? (", '{$value}'") : ("'{$value}'");
            $needComma = true;
        }
        $resultQuery = $this->sql->query("INSERT INTO {$typeWork}_works VALUES (default, $columns) RETURNING id_{$typeWork}");
        if ($resultQuery !== false) {
            $idNewRow = $resultQuery->fetch()[0];
            foreach ($values['id_workers'] as $idWorker) {
                if ($this->sql->exec("INSERT INTO {$typeWork}_workers VALUES ({$idNewRow}, {$idWorker})") === false) {
                    return ['result' => false, 'message' => 'Ошибка вставки в таблицу. Проверьте написанные данные.'];
                }
            }
            return ['result' => true];
        } else {
            return ['result' => false, 'message' => 'Ошибка вставки в таблицу. Проверьте написанные данные.'];
        }
    }
    public function updateWorkIntoTable(int $table, array $values, string|int $idUpdating): array
    {
        switch ($table) {
            case EMERGENCY_TABLE:
                $typeWork = 'emergency';
                break;
            case PLANNED_TABLE:
                $typeWork = 'planned';
                break;
            case PAID_TABLE:
                $typeWork = 'paid';
                break;
            default:
                return ['result' => false, 'message' => 'Такой таблицы не существует'];
                break;
        }
        $needComma = false;
        $update = '';
        foreach ($values as $key => $value) {
            if ($key === 'id_workers') {
                continue;
            }
            $update .= $needComma ? (", {$key} = '{$value}'") : ("{$key} = '{$value}'");
            $needComma = true;
        }
        if ($this->sql->exec("UPDATE {$typeWork}_works SET {$update} WHERE id_{$typeWork} = {$idUpdating}") !== false) {
            $this->sql->exec("DELETE FROM {$typeWork}_workers WHERE id_{$typeWork} = {$idUpdating}");
            foreach ($values['id_workers'] as $idWorker) {
                if ($this->sql->exec("INSERT INTO {$typeWork}_workers VALUES ({$idUpdating}, {$idWorker})") === false) {
                    $error = $this->sql->errorInfo();
                    return ['result' => false, 'message' => 'Ошибка обновления данных. Проверьте написанные данные.'];
                }
            }
            return ['result' => true];
        } else {
            return ['result' => false, 'message' => 'Ошибка обновления данных. Проверьте написанные данные.'];
        }
    }
    public function updateItemIntoTable($updateInfo): array
    {
        $nameItem = $updateInfo['name_item'];
        $count = $updateInfo['count'];
        $idWorker = $updateInfo['id_worker'];
        if (
            ($this->sql->exec("INSERT INTO items_history VALUES (DEFAULT, '{$nameItem}', {$count}, {$idWorker})") !== false)
            && ($this->sql->exec("UPDATE items SET count = count + {$count} WHERE name_item = '{$nameItem}'") !== false)
        ) {
            return ['result' => true];
        } else {
            $error = $this->sql->errorInfo();
            return ['result' => false, 'message' => 'Ошибка обновления данных. Проверьте написанные данные.'];
        }
    }
    public function deleteFromTable($table, $id): array
    {
        switch ($table) {
            case EMERGENCY_TABLE:
                $typeWork = 'emergency';
                break;
            case PLANNED_TABLE:
                $typeWork = 'planned';
                break;
            case PAID_TABLE:
                $typeWork = 'paid';
                break;
            default:
                return ['result' => false];
                break;
        }
        if ($this->sql->exec("DELETE FROM {$typeWork}_works WHERE id_{$typeWork} = {$id}") !== false) {
            return ['result' => true];
        } else {
            return ['result' => false];
        }
    }
    public function getTable(int $table): array
    {
        $result = [];
        if ($this->filters !== []) {
            $this->filters['filter'] = array_filter($this->filters['filter']);
            $filter = ($this->filters['filter'] !== []) ? 'WHERE ' : '';
            $needAnd = false;
            foreach ($this->filters['filter'] as $key => $option) {
                if ($needAnd) {
                    $filter .= ' AND ';
                }
                $filter .= "{$key}::text LIKE '%{$option}%'";
                $needAnd = true;
            }
        } else {
            $filter = '';
        }
        switch ($table) {
            case INHABITANS_TABLE:
                $result['columns'] = ['Адрес', 'Номер квартиры', 'Лицевой счёт', 'Жители', 'Телефон'];
                $select = "adress, number_apartment, apartments.personal_account, inhabitans.full_name, inhabitans.number_phone FROM apartments inner join inhabitans on apartments.personal_account = inhabitans.personal_account {$filter}";
                break;
            case ITEMS_TABLE:
                $result['columns'] = ['ID', 'Предмет', 'Количество'];
                $select = "name_item, name_item, count FROM items {$filter}";
                break;
            case EMERGENCY_TABLE:
                $where = (($this->filters === []) || $this->filters['filter'] === []) ? 'WHERE ' : ' AND ';
                $result['columns'] = ['ID', 'Адрес', 'Номер квартиры', 'Описание', 'Дата и время начала', 'Дата и время конца', 'Работник'];
                $select = "emergency_works.id_emergency, emergency_works.adress, emergency_works.number_apartment, emergency_works.description, emergency_works.date_start, emergency_works.date_end, workers.full_name FROM emergency_workers 
                right join emergency_works on emergency_workers.id_emergency = emergency_works.id_emergency 
                left join workers on emergency_workers.id_worker = workers.id_worker {$filter} {$where} emergency_works.date_end IS NULL ORDER BY date_start DESC, workers.full_name NULLS FIRST";
                break;
            case PLANNED_TABLE:
                $result['columns'] = ['ID', 'Адрес', 'Описание', 'Дата и время начала', 'Дата и время конца', 'Работник'];
                $select = "planned_works.id_planned, planned_works.adress, planned_works.description, planned_works.date_start, planned_works.date_end, workers.full_name FROM planned_workers 
                right join planned_works on planned_workers.id_planned = planned_works.id_planned 
                left join workers on planned_workers.id_worker = workers.id_worker {$filter} ORDER BY date_start DESC, workers.full_name NULLS FIRST";
                break;
            case PAID_TABLE:
                $where = (($this->filters === []) || $this->filters['filter'] === []) ? 'WHERE ' : ' AND ';
                $result['columns'] = ['ID', 'Полный адрес', 'Описание', 'Дата и время начала', 'Дата и время конца', 'Цена', 'Работник'];
                $select = "paid_works.id_paid, paid_works.full_adress, paid_works.description, paid_works.date_start, paid_works.date_end, paid_works.price, workers.full_name FROM paid_workers 
                right join paid_works on paid_workers.id_paid = paid_works.id_paid 
                left join workers on paid_workers.id_worker = workers.id_worker {$filter} {$where} paid_works.date_end IS NULL ORDER BY date_start DESC, workers.full_name NULLS FIRST";
                break;
            default:
                $select = '';
                break;
        }
        $offset = ($this->page - 1) * self::COUNT_ROWS_ON_TABLE;
        $limit = self::COUNT_ROWS_ON_TABLE;
        $resultQuery = $this->sql->query("SELECT {$select} OFFSET {$offset} LIMIT {$limit}");
        $result['rows'] = ($resultQuery !== false) ?  $resultQuery->fetchAll(\PDO::FETCH_NUM) : [];
        $count = $this->sql->query("SELECT count(*) FROM (SELECT {$select} OFFSET {$offset}) AS derived_table");
        if ($count !== false) {
            $count = $count->fetchAll(\PDO::FETCH_NUM)[0][0];
        }
        if ($offset > 0) {
            $result['withPrevious'] = true;
        }
        if ($count > self::COUNT_ROWS_ON_TABLE) {
            $result['withNext'] = true;
        }
        $result['numberPage'] = $this->page;
        return $result;
    }
    public function getMessage(): array
    {
        $result = [];
        $resultQuery = $this->sql->query('SELECT messages_from_users.id_message, messages_from_users.message FROM messages_from_users ORDER BY date LIMIT 1');
        if ($resultQuery !== false) {
            $resultQuery = $resultQuery->fetch();
            if ($resultQuery !== false) {
                $result = $resultQuery;
            }
        }
        return $result;
    }
    public function rejectMessage(string|int $id): void
    {
        $this->sql->exec("DELETE FROM messages_from_users WHERE id_message={$id}");
    }
    public function acceptMessage(string|int $id): void
    {
        $resultQuery = $this->sql->query("DELETE FROM messages_from_users WHERE id_message={$id} RETURNING date, message");
        if ($resultQuery !== false) {
            $resultQuery = $resultQuery->fetch();
            $this->sendTo(
                'HappyHouseNSK@yandex.ru',
                'ООО "Счастливый дом": сообщение от пользователя.',
                "<h2>Сообщение от пользователя:</h2>
                <p>Дата сообщения: {$resultQuery['date']}</p>
                <p>Сообщение: {$resultQuery['message']}</p>"
            );
        }
    }
}
