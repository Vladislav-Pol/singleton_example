<?php

include 'classes/Utils.php';
include 'classes/Db.php';
include 'classes/Users.php';


//Utils::prent(Users::getList());

//Users::add([
//    'login' => 'User05',
//    'password' => '123jJ(',
//    'name' => 'Пользователь5',
//    'email' => 'my@email.net',
//    'groupId' => '1',
//]);

//Users::del(3);
//Users::update(1,['login' => 'User#1']);
Utils::prent(Users::getList([], [], ['name' => 'd', 'groupId' => 'DESC']));


