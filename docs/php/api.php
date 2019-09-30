<?php
define ( 'READFILE', true );

if (isset($_POST['key'], $_POST['surname'], $_POST['birthday'], $_POST['phoneNumber'], $_POST['email'], $_POST['carBrand'])) {
//, $_POST['target']

  $key = htmlspecialchars(strip_tags(trim($_POST['key'])));
  $surname = htmlspecialchars(strip_tags(trim($_POST['surname'])));
  $birthday = htmlspecialchars(strip_tags(trim($_POST['birthday'])));
  $phoneNumber = intval(trim($_POST['phoneNumber']));
  $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
  $carBrand = htmlspecialchars(strip_tags(trim($_POST['carBrand'])));

  if ($key != '123456789') {
    echo json_encode(['status' => 'error', 'error' => ['code' => 888, 'message' => 'INVALID_KEY']]);
    return;
  }

  if (empty($surname) || empty($birthday) || empty($phoneNumber) || empty($carBrand)) {
    echo json_encode(['status' => 'error', 'error' => ['code' => 666, 'message' => 'INVALID_PARAM']]);
    return;
  }

  include_once("../../myphp/models/db/TabUsers.php");

  //Создание экземпляра класса с созданием соединения с БД
  $table = new \app\models\db\TabUsers;

  //проверка наличия пользователя в БД
  if ($table->isDataPhoneNumber($phoneNumber)) {
    echo json_encode(['status' => 'error', 'error' => ['code' => 999, 'message' => 'Пользователь с указанным номером телефона уже существует']]);
    return;
  }

  //Сохранение в БД нового пользователя
  $arr = [
    'surname' => $surname,
    'birthday' => $birthday,
    'phoneNumber' => $phoneNumber,
    'email' => !empty($email) ? $email : null,
    'carBrand' => $carBrand,
  ];
  if ($table->saveOne($arr)) {
    echo json_encode(['status' => 'ok', 'data' => ['message' => 'Данные пользователя успешно сохранены в БД.']]);
  } else {
    echo json_encode(['status' => 'error', 'error' => ['code' => 777, 'message' => 'Ошибка сохранения данных пользователя в БД.']]);
  }
}
?>
