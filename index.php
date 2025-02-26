<?php

include_once "scripts/SESSION_scripts.php";
include_once "scripts/BD_scripts.php";
include_once 'scripts/QUEST_scripts.php';

$Quest = new QUEST(BD::getInstance());
$Session = new SESSION(BD::getInstance());

//Отключение предупреждений
error_reporting(1);
//Запуск сессии
session_start();

//Запуск функций авторизации, регистрации, уничтожения сессии и выбора задания при выполнении условий внутри функций
$Session->setUserRegistration();
$Session->getUserAuthorization();
$Session->sessionDestroy();
$Quest->listQuestsToComplete();

//Подключение сценария подгрузки контента страниц
include_once $Session->getPathToPage();