<?php
session_start();
$getJson = file_get_contents('php://input');
if (!empty($getJson)) {
  if (!isset($_SESSION['queue_contacts'])) $_SESSION['queue_contacts'] = array();
  try {
    $data = json_decode($getJson, true);
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
// echo 'Выброшено исключение: ', $e->getMessage(), "\n";
  }
}
?>
