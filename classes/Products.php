<?php


class Products extends Db
{
    const FILE_NAME = 'products.json';

    public $fields = [
        'title',
        'description',
        'price',
        'image',
    ];

    protected function __construct()
    {
        $file = $this->file = $_SERVER['DOCUMENT_ROOT'] . '/db/' . $this::FILE_NAME;
        if (file_exists($file)) {
            $this->data = json_decode(file_get_contents($file), true);
        }
    }

    public function __destruct()
    {
        $data = json_encode($this->data);
        file_put_contents($this->file, $data);
    }

//    public static function add($props = [])
//    {
//
//        return parent::add($props);
//    }

}