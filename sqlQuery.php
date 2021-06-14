<?php
/**
 * Динамическое формирование MySQL запросов.
 *
 * @package sqlQuery
 * @author dima@foreline.ru
 */

/**
 * Класс для динамического формирования sql запросов
 *
 * @package sqlQuery
 */
class sqlQuery
{

    /** @var string SQL-запрос */
    public string $query = '';

    /** @var string Тип запроса */
    public string $queryType = '';

    /** @var string Таблица базы данных */
    public string $dbTable = '';

    /** @var string Имя базы данных */
    public string $dbName = '';

    /** @var integer Сдвиг для ограничения выборки */
    public int $limitOffset = 0;

    /** @var integer Ограничение выборки */
    public int $limitCount = 0;

    /** @var array Поля запроса */
    public array $arFields = [];

    /** @var array Условия "Где" */
    public array $whereClause = [];

    /** @var array Сортировка */
    public array $orderClause = [];

    /** @var array Группировка */
    public array $groupClause = [];

    /** @var array Порядок */
    public array $order = [];

    /** @var array Подзапрос к другим таблицам */
    public array $join = [];

    /** @var array Опции запроса типа Select */
    public array $selectOptions = [];

    /**
     * Конструктор класса
     *
     * @param string $queryType Тип запроса. По умолчанию 'SELECT'
     * @return void
     */
    public function __construct(string $queryType = 'SELECT')
    {
        $this->queryType = strtoupper($queryType);
    }

    /**
     * Добавляет условие WHERE в запрос
     *
     * @param array|string $arClause
     * @param string $type
     * @return void
     */
    public function setWhere($arClause, string $type = 'AND')
    {
        if (!is_array($arClause)) {
            $arClause = array(
                'CLAUSE' => trim($arClause),
                'TYPE' => trim($type),
            );
        }

        if (!empty($arClause['CLAUSE']) && !in_array($arClause['CLAUSE'], $this->whereClause)) {

            if (empty($arClause['TYPE'])) {
                $arClause['TYPE'] = 'AND';
            }

            $this->whereClause[] = $arClause;
        }
    }

    /**
     * Задает базу данных
     *
     * @param string $dbName Название базы данных
     * @return void
     */
    public function setDatabase(string $dbName = '')
    {
        $this->dbName = trim($dbName);
    }

    /**
     * Задает таблицу
     *
     * @param string $dbTable Название таблицы
     * @return void
     */
    public function setTable(string $dbTable)
    {
        $this->dbTable = trim($dbTable);
    }

    /**
     * Задает поля для выборки
     *
     * @param array $arFields Массив с полями для выборки (необходимо передавать сразу все поля)
     * @return void
     */

    public function setFields(array $arFields = [])
    {
        $this->arFields = $arFields;
    }

    /**
     * Добавляет заданное поле в общий лист полей для выборки
     *
     * @param string $field Поле для выборки
     * @return void
     */

    public function setField(string $field = '')
    {
        if (0 < strlen($field)) {
            $this->arFields[] = $field;
        }
    }

    /**
     * Устанавливает ограничение LIMIT
     *
     * @param integer $limitCount Ограничение выборки
     * @param integer $limitOffset [optional] смещение
     *
     * @return void
     */

    public function setLimit(int $limitCount, int $limitOffset = 0)
    {
        $this->limitCount = $limitCount;
        $this->limitOffset = $limitOffset;
    }

    /**
     * Задает группу
     *
     * @TODO Сделать проверку группируемых полей на наличие в заданных полях для выборки
     *
     * @param array|string $clause
     * @param string $colName [optional]
     * @return void|bool
     */

    public function setGroup($clause = [], string $colName = '')
    {
        if (is_array($clause) && !empty($clause['COL_NAME'])) {
            $arGroup = $clause;
        } else if (!empty($clause)) {
            $arGroup['COL_NAME'] = $clause;
        } else {
            return false;
        }

        /**
         * @TODO Сделать проверку группируемых полей на наличие в заданных полях для выборки
         */

        if (!empty($arGroup['COL_NAME']) && !in_array($arGroup['COL_NAME'], $this->groupClause)) {
            $this->groupClause[] = $arGroup;
        }
    }

    /**
     * Добавляет условие ORDER в запрос
     *
     * @TODO Сделать проверку сортируемых полей на наличие в заданных полях для выборки
     *
     * @param array|string $clause
     * @param string $sortOrder [optional]
     * @return void
     */

    public function setOrder($clause = [], string $sortOrder = 'ASC')
    {

        /**
         * @TODO Сделать проверку сортируемых полей на наличие в заданных полях для выборки
         */

        if (is_array($clause) && !empty($clause['COL_NAME']) && !empty($clause['SORT_ORDER'])) {
            $arClause = array(
                'COL_NAME' => trim($clause['COL_NAME']),
                'SORT_ORDER' => strtoupper(trim($clause['SORT_ORDER'])),
            );
        } else {
            $arClause = array(
                'COL_NAME' => trim($clause),
                'SORT_ORDER' => strtoupper(trim($sortOrder)),
            );
        }

        $this->orderClause[] = $arClause;
    }

    /**
     * Задает таблицу для JOIN
     *
     * @param object $arJoin [optional]
     * @return void
     */

    public function setJoin($arJoin = [])
    {

        $join['JOIN_TYPE'] = !empty($arJoin['JOIN_TYPE']) ? $arJoin['JOIN_TYPE'] : 'JOIN';
        $join['TABLE'] = $arJoin['TABLE'];
        $join['AS'] = $arJoin['AS'];
        $join['ON'] = $arJoin['ON'];

        $this->join[] = $join;
    }

    /**
     * Ставит опции для запросов типа SELECT
     *
     * @param object $arOptions [optional]
     * @return void
     * @example SELECT SQL_CALC_FOUND_ROWS * FROM ....
     *
     */

    public function setSelectOptions($arOptions = [])
    {

        $this->selectOptions = $arOptions;
    }

    /**
     * Генерирует SQL запрос
     *
     * @return string|bool $query SQL запрос
     */

    public function buildQuery()
    {

        switch ($this->queryType) {

            case 'SELECT':
                return $this->buildSelectQuery();

            case 'UPDATE':
                return $this->buildUpdateQuery();

            case 'INSERT':
                return $this->buildInsertQuery();

            case 'DELETE':
                return $this->buildDeleteQuery();
        }

        return false;
    }

    /**
     * Метод генерирует и возвращает запрос типа UPDATE
     *
     * @return string $query Сгенерированный SQL-запрос
     */

    private function buildUpdateQuery(): string
    {
        $this->query = 'UPDATE' . ' ';

        // Database name and Table name
        $this->query .= (!empty($this->dbName) ? $this->dbName . '.' : '');
        $this->query .= $this->dbTable . ' ' . "\n";

        $this->query .= 'SET' . ' ' . "\n";

        // Fields and values
        if (is_array($this->arFields) && 0 < count($this->arFields)) {

            $arFields = $this->arFields;

            foreach ($arFields as $key => $field) {
                if (is_int($key)) {
                    $this->query .= $field . (1 < count($arFields) && ($key + 1) != count($arFields) ? ',' : '') . "\n";
                } else {
                    $this->query .= '`' . trim($key, '`') . '`' . ' = ' . $field . (1 < count($arFields) && ($key + 1) != count($arFields) ? ',' : '') . "\n";
                }
            }
            //$this->query .= implode("\n" . ', ', $this->arFields) . ' ' . "\n";
        } else {
            $this->query .= '`ID` = `ID`' . ' ' . "\n";
        }

        // WHERE CLAUSE
        if (0 < count($this->whereClause)) {

            $this->query .= 'WHERE' . ' ';

            foreach ($this->whereClause as $key => $arClause) {

                $type = $arClause['TYPE'];
                $whereClause = $arClause['CLAUSE'];

                $this->query .= (0 != $key ? "\n" . "\t" . ' ' . $type . ' ' : '') . $whereClause;
            }

            $this->query .= "\n";
        }

        return $this->query;
    }

    /**
     * Метод генерирует и возвращает запрос типа SELECT
     *
     * @return string $query Сгенерированный SQL-запрос
     */

    private function buildSelectQuery(): string
    {

        // SELECT
        $this->query = 'SELECT' . ' ';

        if (0 < count($this->selectOptions)) {
            $this->query .= implode(' ', $this->selectOptions) . ' ' . "\n";
        }

        $this->query .= implode("\n" . ', ', $this->arFields) . ' ' . "\n";

        // FROM
        $this->query .= 'FROM' . ' ';

        $this->query .= /*'`' .*/
            (!empty($this->dbName) ? $this->dbName . '.' : '');

        $this->query .= $this->dbTable /*. '`'*/ . ' ' . "\n";

        // JOIN
        if (0 < count($this->join)) {

            foreach ($this->join as $arJoin) {
                $this->query .= $arJoin['JOIN_TYPE'] . ' ' . $arJoin['TABLE'] . (!empty($arJoin['AS']) ? ' AS ' . $arJoin['AS'] : '') . ' ON ' . $arJoin['ON'] . ' ' . "\n";
            }
        }

        // WHERE CLAUSE
        if (0 < count($this->whereClause)) {

            $this->query .= 'WHERE' . ' ';

            foreach ($this->whereClause as $key => $arClause) {

                $type = $arClause['TYPE'];
                $whereClause = $arClause['CLAUSE'];

                $this->query .= (0 != $key ? "\n" . "\t" . ' ' . $type . ' ' : '') . $whereClause;
            }

            $this->query .= "\n";
        }

        // GROUP
        if (0 < count($this->groupClause)) {

            $this->query .= 'GROUP BY' . ' ';

            foreach ($this->groupClause as $key => $arClause) {

                $col_name = $arClause['COL_NAME'];

                $this->query .= (0 != $key ? "\n" . "\t" . ', ' : '') . $col_name;
            }

            $this->query .= "\n";
        }

        // ORDER
        if (0 < count($this->orderClause)) {

            $this->query .= 'ORDER BY' . ' ';

            foreach ($this->orderClause as $key => $arClause) {

                $col_name = $arClause['COL_NAME'];
                $sort_order = $arClause['SORT_ORDER'];

                $this->query .= (0 != $key ? "\n" . "\t" . ', ' : '') . $col_name . ' ' . $sort_order;
            }

            $this->query .= "\n";
        }

        // LIMIT
        if (0 < $this->limitCount) {
            $this->query .= 'LIMIT ' . (0 < $this->limitOffset ? $this->limitOffset . ', ' : '') . $this->limitCount . "\n";
        }

        return $this->query;
    }

    /**
     * Метод генерирует и возвращает запрос типа INSERT
     *
     * @return string $query MySQL запрос типа INSERT
     */

    private function buildInsertQuery(): string
    {

        $this->query = 'INSERT INTO' . ' ';

        // База данных и таблица
        $this->query .= '`' . (!empty($this->dbName) ? $this->dbName . '.' : '');
        $this->query .= $this->dbTable . '`' . ' ';

        $this->query .= "\n";

        // Поля
        $this->query .= '(' . "\n";
        $this->query .= '`' . implode('`,' . "\n" . '`', array_map('mysql_escape_string', array_keys($this->arFields))) . '`' . ' ' . "\n";
        $this->query .= ')';

        $this->query .= "\n";
        $this->query .= ' VALUES ';
        $this->query .= "\n";

        // Значения
        $this->query .= '(' . "\n";
        //$this->query .= '"' . implode( '",' . "\n" . '"', array_map('mysql_escape_string', $this->arFields)) . '"' . ' ' . "\n";
        //$this->query .= implode( ',' . "\n", array_map('mysql_escape_string', $this->arFields)) . ' ' . "\n";
        $this->query .= implode(',' . "\n", $this->arFields) . ' ' . "\n";
        $this->query .= ')';

        $this->query .= "\n";

        return $this->query;
    }

    /**
     * Метод генерирует и возвращает запрос типа DELETE
     * @return string $query Сгенерированный SQL-запрос
     */

    private function buildDeleteQuery(): string
    {

        // SELECT
        $this->query = 'DELETE' . ' ';

        $this->query .= implode("\n" . ', ', $this->arFields) . ' ' . "\n";

        // FROM
        $this->query .= 'FROM' . ' ';

        $this->query .= '`' . (!empty($this->dbName) ? $this->dbName . '.' : '');
        $this->query .= $this->dbTable . '`' . ' ' . "\n";

        // WHERE CLAUSE
        if (0 < count($this->whereClause)) {

            $this->query .= 'WHERE' . ' ';

            foreach ($this->whereClause as $key => $arClause) {

                $type = $arClause['TYPE'];
                $whereClause = $arClause['CLAUSE'];

                $this->query .= (0 != $key ? "\n" . "\t" . ' ' . $type . ' ' : '') . $whereClause;
            }

            $this->query .= "\n";
        }

        return $this->query;
    }

}
    
