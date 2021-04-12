<?php


class Users extends Db
{
    protected $pregLogin = '/^[a-z0-9]{6,}$/i';
    protected $pregPassword = '/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=])(.{6,})/';

    public $validateErrors =[];

    const FILE_NAME = 'users.json';

    public $fields = [
        'login',
        'password',
        'name',
        'email',
        'groupId',
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

    public static function add($props = [])
    {
        $obj = self::getInstance();
        $isErrors = false;

        if(!preg_match($obj->pregLogin, $props['login'])){
            $obj->validateErrors['login'] = 'Логин ' . $obj->login . ' не подходит. Логин должен состоять только из букв латинского алфавита и цифр. Минимальная длина логина - 6 символов';
            $isErrors = true;
        }

        if(!preg_match($obj->pregPassword, $props['password'])){
            $obj->validateErrors['password'] = 'Пароль слишком легкий';
            $isErrors = true;
        } else{
            $props['password'] = password_hash($props['password'], PASSWORD_DEFAULT);
        }

        if(empty($props['name'])){
            $props['name'] = $props['login'];
        }

        if($isErrors){
            echo '<pre>';
            print_r($obj->validateErrors);
            echo '</pre>';
            return false;
        } else{
            return parent::add($props);
        }
    }

}