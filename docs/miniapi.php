<?php
session_start();

//! Получение JSON данных методом POST
$getJson = file_get_contents('php://input');
// echo 'var_dump(file_get_contents(\'php://input\')) = '."<br>"; var_dump(file_get_contents('php://input'));
// echo 'var_dump($getJson) = '."<br>"; var_dump($getJson);

// if (isset($_SESSION['queue'])) unset($_SESSION['queue']);
// if (isset($_SESSION['queue_contacts'])) unset($_SESSION['queue_contacts']);
// return;


if (!empty($getJson)) {
  if (!isset($_SESSION['queue_contacts'])) $_SESSION['queue_contacts'] = array();
  try {
    $data = json_decode($getJson, true);
// echo 'var_dump($data) = '."<br>"; var_dump($data);
    if (isset($data['token'])) {
      if (!empty($data['token']) && hash_equals($_SESSION['token'], $data['token'])) {
        if (!empty($data['params'])) {
          $data['params']['hash'] = md5($_SERVER['REQUEST_URI'].time());
          $data['params']['website'] = $_SERVER['SERVER_NAME'];
          $_SESSION['queue_contacts'][] = $data['params'];
        }
      }
    }
  } catch (\Exception $e) {
echo 'Выброшено исключение: ', $e->getMessage(), "\n";
  }
}


/*
//! Получение не JSON данных методом POST
//? Нет проверки инфекций !?!
echo 'var_dump($_POST) = '."<br>"; var_dump($_POST);

session_start();

if (isset($_POST['token'])) {
  if (!empty($_POST['token']) && hash_equals($_SESSION['token'], $_POST['token'])) {
    if (!isset($_SESSION['queue_contacts'])) $_SESSION['queue_contacts'] = array();
    if (!empty($_POST['params']) && is_array($_POST['params'])) {
      $arr = array('receipt_at' => time());
      foreach ($_POST['params'] as $key => $value) {
        if (!empty($value) && is_array($value)) {
          $arr[$key] = array();
          foreach ($value as $key2 => $value2) {
            if (!empty($value2) && is_array($value2)) {
              $arr[$key][$key2] = array();
              foreach ($value2 as $key3 => $value3) {
                $arr[$key][$key2][$key3] = htmlspecialchars(strip_tags(trim($value3)));
              }
            } else {
              $arr[$key][$key2] = htmlspecialchars(strip_tags(trim($value2)));
            }
          }
        } else {
          $arr[$key] = htmlspecialchars(strip_tags(trim($value)));
        }
      }
      $_SESSION['queue_contacts'][] = $arr;
    }
  }
}
*/
?>
