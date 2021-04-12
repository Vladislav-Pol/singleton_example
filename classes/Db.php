<?php


class Db
{
    private static $instances = [];
    protected $data = [];
    public $fields = [];
    public $fileName;

    /**
     * Конструктор Одиночки всегда должен быть скрытым, чтобы предотвратить
     * создание объекта через оператор new.
     */
    protected function __construct()
    {
    }

    /**
     * Одиночки не должны быть клонируемыми.
     */
    protected function __clone()
    {
    }

    /**
     * Одиночки не должны быть восстанавливаемыми из строк.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * Это статический метод, управляющий доступом к экземпляру одиночки. При
     * первом запуске, он создаёт экземпляр одиночки и помещает его в
     * статическое поле. При последующих запусках, он возвращает клиенту объект,
     * хранящийся в статическом поле.
     *
     * Эта реализация позволяет вам расширять класс Одиночки, сохраняя повсюду
     * только один экземпляр каждого подкласса.
     */
    public static function getInstance()
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
    }

    /**
     * Наконец, любой одиночка должен содержать некоторую бизнес-логику, которая
     * может быть выполнена на его экземпляре.
     */
    public function someBusinessLogic()
    {
        // ...
    }

    /**
     * Добавляет новый элемент в бд
     * @param array $props
     * @return false|mixed
     */
    public static function add($props = [])
    {
        if (empty($props)) {
            return false;
        }

        $data = &self::getInstance()->data;
        $fields = self::getInstance()->fields;

        foreach ($props as $fieldCode => &$fieldValue) {
            if (!in_array($fieldCode, $fields)) {
                unset($props[$fieldCode]);
            }
        }

        $data[] = $props;
        $keys = array_keys($data);
        return end($keys);
    }

    /**Удаляет элемент из бд по id
     * @param $id
     */
    final public static function del($id)
    {
        $obj = self::getInstance();

        unset($obj->data[$id]);
    }

    /**
     * Метод обновляет полученные свойства у элемента по c полученным id
     * @param $id
     * @param array $props
     * @return false
     */
    public static function update($id, $props = [])
    {
        $obj = self::getInstance();
        if (!isset($obj->data[$id]) && empty($props)) {
            return false;
        }

        foreach ($props as $fieldCode => &$fieldValue) {
            if (!in_array($fieldCode, $obj->fields)) {
                unset($props[$fieldCode]);
            }
        }
        foreach ($props as $prop => $value) {
            $obj->data[$id][$prop] = $value;
        }
    }


    public static function getList($arFilter = [], $arSelect = [], $arSort = [])
    {
        $obj = self::getInstance();

        //фильтрует массив элементов
        $arRes = array_filter($obj->data, function ($item) use ($arFilter) {
            foreach ($arFilter as $prop => $value) {
                if ($item[$prop] != $value) {
                    return false;
                }
            }
            return true;
        });

        if ($arSelect != []) {

            $unsetProps = array_diff($obj->fields, $arSelect);

            if (!empty($unsetProps)) {
                foreach ($arRes as &$item) {
                    foreach ($unsetProps as $unsetProp) {
                        unset($item[$unsetProp]);
                    }
                }
            }
        }

        if ($arSort != []) {
            $arSort = array_reverse($arSort);
            foreach ($arSort as $sortProp => $sortDir){
                usort($arRes, function ($a, $b)use($sortProp, $sortDir){
                    $result = strcmp($a[$sortProp], $b[$sortProp]);
                    return $sortDir == 'ASC'? $result : $result * -1;
                });
            }
        }
        return $arRes;
    }
}
