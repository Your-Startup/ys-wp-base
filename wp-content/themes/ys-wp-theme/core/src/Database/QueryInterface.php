<?php

namespace YS\Core\Database;

interface QueryInterface
{
    /**
     * Выполняет подготовленный запрос
     */
    public function execute();

    /**
     * Подготавливает запрос к выполнению
     *
     * @param string $query Строка с SQL-запросом
     * @param array|string $args Массив переменных для подстановки в заполнители
     */
    public function prepare(string $query, $args = []);

    /**
     * Выполняет запрос, без подготовки и кэширования
     *
     * @param string $query Строка с SQL-запросом
     *
     * @return bool|int Возвращает кол-во затронутых выполнением запроса строк или false в случае ошибки
     */
    public function query(string $query);

    /**
     * Извлекает следующую строку из результирующего набора
     *
     * @return mixed Возвращает следующую строку в виде массива, индексированного именами столбцов или false,<br>
     *               в случае неудачи.
     */
    public function fetch();

    /**
     * Возвращает массив, содержащий все строки результирующего набора
     *
     * @return array
     */
    public function fetchAll(): array;

    /**
     * Возвращает данные одного столбца следующей строки результирующего набора
     *
     * @param int $colNumber Номер столбца, данные которого необходимо извлечь. Нумерация начинается с 0.<br>
     *                       Если параметр не задан, выберет данные первого столбца.
     *
     * @return mixed|null Возвращает значение одного столбца следующей строки результирующего набора или false,<br>
     *                    если больше нет строк.
     */
    public function fetchColumn(int $colNumber = 0);
}