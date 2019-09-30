usersAccountAmocrm<?php
session_start();

include_once "../myphp/models/loginApiAmocrm.php";
include_once "../myphp/models/workInApiAmocrm.php";

use app\models\amocrm\loginApiAmocrm;
use app\models\amocrm\workInApiAmocrm;

//!!! Массив соответствий. Пока в Аккаунте AmoCRM не присвоены 'code' для пользовательских полей
$arrMatchNamesToCodes = [
  // 'catchpoint' => 'Описание заявки',
  'description' => 'Описание заявки',
  'referrer' => 'referrer',
  'utm_term' => 'utm_term',
  'utm_content' => 'utm_content',
  'utm_campaign' => 'utm_campaign',
  'utm_medium' => 'utm_medium',
  'utm_source' => 'utm_source',
  'height' => 'ВЫСОТА, ММ',
  'width' => 'ШИРИНА, ММ',
  'profile' => 'ПРОФИЛЬ',
  'number' => 'КОЛ-ВО КАМЕР',
  'mechanism' => 'МЕХАНИЗМ',
  'website' => 'web-site',
];
// $arrMatchNamesToCodesEnum = [//!!! НЕ используется !!!
//   'profile' => [
//     'REHAU' => 'REHAU',
//     'VEKA' => 'VEKA',
//     'KBE' => 'KBE',
//     'KRAUSS' => 'KRAUSS',
//     'SALAMANDER' => 'SALAMANDER',
//   ],
//   'number' => [
//     'CAMERAS2' => '2 камеры',
//     'CAMERAS3' => '3 камеры',
//   ],
//   'mechanism' => [
//     'SWIVEL' => 'Поворотный',
//     'SWIVEL_FOLDING' => 'Поворотно-откидной',
//     'SLIDING' => 'Раздвижной',
//   ]
// ];


// echo 'var_dump(dirname(__FILE__)) = '."<br>"; var_dump(dirname(__FILE__));


// echo 'var_dump($_POST) = '."<br>"; var_dump($_POST);


//!!! Временно !!! Обнуление некоторых переменных сессии
// if (!empty($_SESSION) && is_array($_SESSION)) {
//   foreach ($_SESSION as $key => $value) {
//     // if ($key == 'token' || $key == 'queue_contacts') {
//     // } else {
//       unset($_SESSION[$key]);
//     // }
//   }
// }
// return;
//!!! Временно !!! Обнуление некоторых переменных сессии


// echo 'var_dump($_SESSION) = '."<br>"; var_dump($_SESSION);
// echo 'var_dump(session_id()) = '."<br>"; var_dump(session_id());

//Проверка в сессии наличия токена, если нет, то создать
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

//Хеш токена, на основе названия страницы (только для тек.страницы)
//$hash_token = hash_hmac('sha256', '/login_amocrm.php', $_SESSION['token']);
//Хеш токена, на основе ID сессии (для всех страниц, тек.сессии)
$hash_token = hash_hmac('sha256', session_id(), $_SESSION['token']);
// echo 'var_dump($hash_token) = '."<br>"; var_dump($hash_token);

// echo 'var_dump($_SESSION) = '."<br>"; var_dump($_SESSION);


//Проверка проводилась ли ранее авторизация?
if (isset($_SESSION['authAmocrm'], $_SESSION['authAmocrm']['auth']) && $_SESSION['authAmocrm']['auth']) {
  //Статус авторизации
  $status_authorization = $_SESSION['authAmocrm']['auth'];
  if (!empty($_SESSION['nameAuthUserAmocrm'])) {
    //Имя авторизованного пользователя AmoCRM
    $name_account = $_SESSION['nameAuthUserAmocrm'];
  }
}


if (isset($_POST['email'], $_POST['userKey'], $_POST['subdomain'])) {
  if (!empty($_POST['token']) && hash_equals($hash_token, $_POST['token'])) {
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $userKey = htmlspecialchars(strip_tags(trim($_POST['userKey'])));
    $subdomain = htmlspecialchars(strip_tags(trim($_POST['subdomain'])));

// echo 'var_dump($email) = '."<br>"; var_dump($email);
// echo 'var_dump($userKey) = '."<br>"; var_dump($userKey);
// echo 'var_dump($subdomain) = '."<br>"; var_dump($subdomain);




    if (empty($email)) {
      $status_authorization = false;
      $log_authorization = 'Ошибка! Не корректное задан E-mail';
    } else if (empty($userKey)) {
      $status_authorization = false;
      $log_authorization = 'Ошибка! Не корректное задан ключ пользователя';
    } else if (empty($subdomain)) {
      $status_authorization = false;
      $log_authorization = 'Ошибка! Не корректное задан ключ поддомен';
    } else {


/*
//Создание экземпляра класса
try {
  // $obj = new \app\models\loginApiAmocrm();
  // $obj = loginApiAmocrm::setParams();
  // $obj = new \app\models\loginApiAmocrm($email, $userKey, $subdomain);
  $obj = new loginApiAmocrm($email, $userKey, $subdomain);
} catch (\Exception $e) {
  echo 'Выброшено исключение: ', $e->getMessage(), "\n";
}

echo 'var_dump($obj) = '."<br>"; var_dump($obj);
//echo 'var_dump($obj->getEmail()) = '."<br>"; var_dump($obj->getEmail());
echo 'var_dump($obj::getEmail()) = '."<br>"; var_dump($obj::getEmail());
echo 'var_dump($obj->getUserKey()) = '."<br>"; var_dump($obj->getUserKey());
echo 'var_dump($obj->getSubdomain()) = '."<br>"; var_dump($obj->getSubdomain());
echo 'var_dump($obj->getType()) = '."<br>"; var_dump($obj->getType());


try {
  // $obj2 = new \app\models\loginApiAmocrm();
  // $obj2 = new \app\models\loginApiAmocrm($email, $userKey, $subdomain);
  $obj2 = new loginApiAmocrm();
} catch (\Exception $e) {
  echo 'Выброшено исключение: ', $e->getMessage(), "\n";
}

echo 'var_dump($obj2) = '."<br>"; var_dump($obj2);
echo 'var_dump($obj2->getEmail()) = '."<br>"; var_dump($obj2->getEmail());
//echo 'var_dump($obj2::getEmail()) = '."<br>"; var_dump($obj2::getEmail());
echo 'var_dump($obj2->getUserKey()) = '."<br>"; var_dump($obj2->getUserKey());
echo 'var_dump($obj2->getSubdomain()) = '."<br>"; var_dump($obj2->getSubdomain());
echo 'var_dump($obj2->getType()) = '."<br>"; var_dump($obj2->getType());


try {
  $out = loginApiAmocrm::initParams('leogood@email.ru', '0123456789', 'gudkov', false);

  // $obj3 = new \app\models\loginApiAmocrm();
  // $obj3 = new \app\models\loginApiAmocrm('leogood@email.ru', '0123456789', 'gudkovleonid');
  // $obj3 = \app\models\loginApiAmocrm::setParams('leogood@email.ru', '0123456789', 'gudkovleonid');
  // $obj3 = loginApiAmocrm::setParams('leogood@email.ru', '0123456789', 'gudkovleonid');
  $obj3 = loginApiAmocrm::setParams();
} catch (\Exception $e) {
  echo 'Выброшено исключение: ', $e->getMessage(), "\n";
}

echo 'var_dump($out) = '."<br>"; var_dump($out);
echo 'var_dump($obj3) = '."<br>"; var_dump($obj3);
echo 'var_dump($obj3->getEmail()) = '."<br>"; var_dump($obj3->getEmail());
//echo 'var_dump($obj3::getEmail()) = '."<br>"; var_dump($obj3::getEmail());
echo 'var_dump($obj3->getUserKey()) = '."<br>"; var_dump($obj3->getUserKey());
echo 'var_dump($obj3->getSubdomain()) = '."<br>"; var_dump($obj3->getSubdomain());
echo 'var_dump($obj3->getType()) = '."<br>"; var_dump($obj3->getType());


try {
  // $obj4 = new \app\models\loginApiAmocrm();
  // $obj4 = new \app\models\loginApiAmocrm('leogood@email.ru', '0123456789', 'gudkovleonid');
  // $obj4 = new loginApiAmocrm();
  $obj4 = loginApiAmocrm::getNew();
} catch (\Exception $e) {
  echo 'Выброшено исключение: ', $e->getMessage(), "\n";
}

echo 'var_dump($obj4) = '."<br>"; var_dump($obj4);
echo 'var_dump($obj4->getEmail()) = '."<br>"; var_dump($obj4->getEmail());
//echo 'var_dump($obj4::getEmail()) = '."<br>"; var_dump($obj4::getEmail());
echo 'var_dump($obj4->getUserKey()) = '."<br>"; var_dump($obj4->getUserKey());
echo 'var_dump($obj4->getSubdomain()) = '."<br>"; var_dump($obj4->getSubdomain());
echo 'var_dump($obj4->getType()) = '."<br>"; var_dump($obj4->getType());
*/


      //Инициализация свойств класса, создание экземпляра класса
      //и выполнение запроса авторизации
      try {
        $out = loginApiAmocrm::setParams($email, $userKey, $subdomain)->loginAmocrm();

// echo 'var_dump(loginApiAmocrm::getEmail()) = '."<br>"; var_dump(loginApiAmocrm::getEmail());
// echo 'var_dump(loginApiAmocrm::getUserKey()) = '."<br>"; var_dump(loginApiAmocrm::getUserKey());
// echo 'var_dump(loginApiAmocrm::getSubdomain()) = '."<br>"; var_dump(loginApiAmocrm::getSubdomain());
// echo 'var_dump(loginApiAmocrm::getType()) = '."<br>"; var_dump(loginApiAmocrm::getType());
// echo 'var_dump(loginApiAmocrm::getLoginTime()) = '."<br>"; var_dump(loginApiAmocrm::getLoginTime());


// echo 'var_dump($out) = '."<br>"; var_dump($out);
// echo 'var_dump($out[\'response\']) = '."<br>"; var_dump($out['response']);

        //обработка ответа и запись в сессию
        if (!empty($out) && isset($out['status'], $out['response'], $out['response']['auth']) && $out['response']['auth']) {
          $_SESSION['loginAmocrm'] = array(
            'email' => $email,
            'userKey' => $userKey,
            'subdomain' => $subdomain,
            'login_time' => !empty($out['response']['server_time']) ? $out['response']['server_time'] : 0,
          );
          //Ответ авторизации (прим. авторизация действительна только 15 мин.) может? сделать обратный отчет...
          $_SESSION['authAmocrm'] = $out['response'];
          if (isset($out['response']['accounts']) && is_array($out['response']['accounts'])) {
            foreach ($out['response']['accounts'] as $value) {
              if (!empty($value['name'])) {
                //Название аккаунта для отображения
                $name_account = $value['name'];
                $_SESSION['nameAuthUserAmocrm'] = $value['name'];
                break;
              }
            }
          }
          $status_authorization = true;
          $log_authorization = "Авторизация в аккаунте \"$name_account\" AmoCrm выполнена успешно.";



          //Получение Информации о сущностях, полях и д.р. в аккаунте:
          //- "Группам пользователей"
          //- "Пользователям"
          //- "Цифровых воронок"
          //- "Типам задач"
          //- "Дополнительным полям"
          //- "Типам дополнительных полей"
          //Создание экземпляра класса и сразу выполнение запроса (Цифровые воронки)
          $out = (new workInApiAmocrm($subdomain))->accountAmocrm('groups,users,pipelines,task_types,custom_fields,note_types');
// echo 'После запроса. $out = (new workInApiAmocrm($subdomain))->accountAmocrm(\'groups,users,pipelines,task_types,custom_fields,note_types\')'."<br>";
// echo 'var_dump($out) = '."<br>"; var_dump($out);
          //обработка ответа
          if (!empty($out) && isset($out['status']) && $out['status'] == 'ok') {

            //Получение списка групп пользователей и запись в сессию
            if (!empty($out['response']['_embedded']['groups']) && is_array($out['response']['_embedded']['groups'])) {
              $_SESSION['usersAccountAmocrm'] = $out['response']['_embedded']['groups'];
            }

            //Получение списка пользователей и запись в сессию
            $id_group_sales_department = 0;
            //обработка ответа
            if (!empty($out['response']['_embedded']['users']) && is_array($out['response']['_embedded']['users'])) {
              foreach ($out['response']['_embedded']['users'] as $id_user => $user_data) {
                if (! $user_data['is_active']) continue;
                if (!isset($_SESSION['usersAccountAmocrm'][$user_data['group_id']])) {
                  $_SESSION['usersAccountAmocrm'][$user_data['group_id']] = [];
                }
                if ($user_data['group_id'] == $id_group_sales_department && !isset($_SESSION['usersAccountAmocrm'][$user_data['group_id']]['queue'])) {
                  $_SESSION['usersAccountAmocrm'][$user_data['group_id']]['queue'] = count($out['response']['_embedded']['users']) - 1;
                }
                if (!isset($_SESSION['usersAccountAmocrm'][$user_data['group_id']]['users'])) {
                  $_SESSION['usersAccountAmocrm'][$user_data['group_id']]['users'] = [];
                }
                $arr = [];
                foreach ($user_data as $key => $value) {
                  if ($key != 'rights') {
                    $arr[$key] = $value;
                  }
                }
                $_SESSION['usersAccountAmocrm'][$user_data['group_id']]['users'][] = $arr;
              }
//              unset($arr);
            }
            unset($id_group_sales_department);

            $_SESSION['infoAccountAmocrm'] = [];

            //"Цифровые воронки"
            if (!empty($out['response']['_embedded']['pipelines']) && is_array($out['response']['_embedded']['pipelines'])) {
              $_SESSION['infoAccountAmocrm']['pipelines'] = [];
              foreach ($out['response']['_embedded']['pipelines'] as $pipelines) {
               $arr = [];
               foreach ($pipelines as $key => $value) {
                 if (!is_array($value)) {
                   $arr[$key] = $value;
                 } else if ($key != '_links') {
                   $arr[$key] = array_values($value);
                 }
               }
               $_SESSION['infoAccountAmocrm']['pipelines'][] = $arr;
              }
              unset($arr);
            }

            //"Типы задач"
            if (!empty($out['response']['_embedded']['task_types']) && is_array($out['response']['_embedded']['task_types'])) {
              //$_SESSION['infoAccountAmocrm']['task_types'] = $out['response']['_embedded']['task_types'];
              $_SESSION['infoAccountAmocrm']['task_types'] = array_values($out['response']['_embedded']['task_types']);
            }

            //"Дополнительные поля"
            if (!empty($out['response']['_embedded']['custom_fields']) && is_array($out['response']['_embedded']['custom_fields'])) {
              // $_SESSION['infoAccountAmocrm']['custom_fields_enum'] = [];
              $_SESSION['infoAccountAmocrm']['custom_fields'] = $out['response']['_embedded']['custom_fields'];
              //Добавление значений в "пустые" поля 'code'
              foreach ($_SESSION['infoAccountAmocrm']['custom_fields'] as $key_essence => &$value_essence) {
                if (!empty($value_essence) && is_array($value_essence)) {
                  foreach ($value_essence as $key_field => &$value_field) {
                    if (isset($value_field['code']) && empty($value_field['code'])) {
                      if (($key_arr = array_search($value_field['name'], $arrMatchNamesToCodes)) !== false) {
                        $value_field['code'] = $key_arr;
                      }
                    }
                  }
                }
              }
              unset($value_essence,$value_field);

//!!! НЕ ИСПОЛЬЗУЕТСЯ !!! УДАЛИТЬ !!!

              // foreach ($out['response']['_embedded']['custom_fields'] as $key_essence => $value_essence) {
              //   $key_essence_new = mb_strtoupper($key_essence);
              //   $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new] = [];
              //   if (!empty($value_essence) && is_array($value_essence)) {
              //     foreach ($value_essence as $key_field => $value_field) {
              //       if (!empty($value_field['code'])) {
              //         $key_field_new = $value_field['code'];
              //         $code_field_new = $value_field['code'];
              //       } else {
              //         if (($key_arr = array_search($value_field['name'], $arrMatchNamesToCodes)) !== false) {
              //           $key_field_new = mb_strtoupper($key_arr);
              //           $code_field_new = $key_arr;
              //         } else {
              //           $key_field_new = md5($value_field['name']);
              //           $code_field_new = md5($value_field['name']);
              //         }
              //       }
              //       $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new][$key_field_new] = [];
              //       $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new][$key_field_new]['id'] = $value_field['id'];
              //       $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new][$key_field_new]['name'] = $value_field['name'];
              //       $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new][$key_field_new]['code'] = !empty($value_field['code']) ? $value_field['code'] : $code_field_new;
              //       if (!empty($value_field['enums']) && is_array($value_field['enums'])) {
              //         // $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new][$key_field_new]['enums'] = array_flip($value_field['enums']);
              //         $_SESSION['infoAccountAmocrm']['custom_fields_enum'][$key_essence_new][$key_field_new]['enums'] = $value_field['enums'];
              //       }
              //     }
              //   }
              // }
            }

            //"Типы дополнительных полей"
            if (!empty($out['response']['_embedded']['note_types']) && is_array($out['response']['_embedded']['note_types'])) {
              $_SESSION['infoAccountAmocrm']['note_types'] = $out['response']['_embedded']['note_types'];
              // $_SESSION['infoAccountAmocrm']['task_types'] = array_values($out['response']['_embedded']['task_types']);
            }
          }




//!!! СТАРОЕ !!! УДАЛИТЬ !!!
          // //Получение списка групп пользователей и запись в сессию
          // $out = (new workInApiAmocrm($subdomain))->accountAmocrm('groups');
          // //обработка ответа
          // if (!empty($out) && isset($out['status']) && $out['status'] == 'ok' && !empty($out['response']['_embedded']['groups']) && is_array($out['response']['_embedded']['groups'])) {
          //   $_SESSION['usersAccountAmocrm'] = $out['response']['_embedded']['groups'];
          // }

          // //Получение списка пользователей и запись в сессию
          // $out = (new workInApiAmocrm($subdomain))->accountAmocrm('users');
          // $id_group_sales_department = 0;
          // //обработка ответа
          // if (!empty($out) && isset($out['status']) && $out['status'] == 'ok' && !empty($out['response']['_embedded']['users']) && is_array($out['response']['_embedded']['users'])) {
          //   foreach ($out['response']['_embedded']['users'] as $id_user => $user_data) {
          //     if (! $user_data['is_active']) continue;
          //     if (!isset($_SESSION['usersAccountAmocrm'][$user_data['group_id']])) {
          //       $_SESSION['usersAccountAmocrm'][$user_data['group_id']] = [];
          //     }
          //     if ($user_data['group_id'] == $id_group_sales_department && !isset($_SESSION['usersAccountAmocrm'][$user_data['group_id']]['queue'])) {
          //       $_SESSION['usersAccountAmocrm'][$user_data['group_id']]['queue'] = count($out['response']['_embedded']['users']) - 1;
          //     }
          //     if (!isset($_SESSION['usersAccountAmocrm'][$user_data['group_id']]['users'])) {
          //       $_SESSION['usersAccountAmocrm'][$user_data['group_id']]['users'] = [];
          //     }
          //     $arr = [];
          //     foreach ($user_data as $key => $value) {
          //       if ($key != 'rights') {
          //         $arr[$key] = $value;
          //       }
          //     }
          //     $_SESSION['usersAccountAmocrm'][$user_data['group_id']]['users'][] = $arr;
          //   }
          //   unset($arr);
          // }
          // unset($id_group_sales_department);
        }
      } catch (\Exception $e) {
// echo 'Выброшено исключение: ', $e->getMessage(), "\n";
        $status_authorization = false;
        $log_authorization = 'Ошибка при авторизации в аккаунте AmoCrm!';
      }
      unset($out);
    }
  } else {
    $status_authorization = false;
    $log_authorization = 'Ошибка при авторизации в аккаунте AmoCrm!';
  }


//!!! Проба получения информации об Аккаунте
// if (isset($status_authorization) && $status_authorization && !empty($_SESSION['loginAmocrm']['subdomain'])) {
//   //Создание экземпляра класса и сразу выполнение запроса (Контакты)
//   $out = (new workInApiAmocrm($_SESSION['loginAmocrm']['subdomain']))->accountAmocrm('custom_fields');
// echo 'var_dump($out) = '."<br>"; var_dump($out);
// echo 'var_dump($out[\'response\'][\'_embedded\'][\'custom_fields\']) = '."<br>"; var_dump($out['response']['_embedded']['custom_fields']);
// // echo 'var_dump($out[\'response\'][\'_embedded\'][\'custom_fields\'][\'contacts\']) = '."<br>"; var_dump($out['response']['_embedded']['custom_fields']['contacts']);
// foreach ($out['response']['_embedded']['custom_fields'] as $key1 => $value1) {
//   echo 'var_dump($out[\'response\'][\'_embedded\'][\'custom_fields\']['.$key1.']) = '."<br>"; var_dump($value1);
//   // if (isset($value1) && is_array($value1)) {
//   //   foreach ($value1 as $key2 => $value2) {
//   //     echo 'var_dump($out[\'response\'][\'_embedded\'][\'custom_fields\']['.$key1.']['.$key2.']) = '."<br>"; var_dump($value2);
//   //   }
//   // }
// }
//
//   //Создание экземпляра класса и сразу выполнение запроса (Цифровые воронки)
//   $out = (new workInApiAmocrm($_SESSION['loginAmocrm']['subdomain']))->accountAmocrm('pipelines');
// echo 'var_dump($out) = '."<br>"; var_dump($out);
// foreach ($out['response']['_embedded']['pipelines'] as $key => $value) {
//   echo 'var_dump($out[\'response\'][\'_embedded\'][\'pipelines\']['.$key.']) = '."<br>"; var_dump($value);
// }
//
//
// //Создание экземпляра класса и сразу выполнение запроса (типам задач в аккаунте)
// $out = (new workInApiAmocrm($_SESSION['loginAmocrm']['subdomain']))->accountAmocrm('task_types');
// echo 'var_dump($out) = '."<br>"; var_dump($out);
// foreach ($out['response']['_embedded']['task_types'] as $key => $value) {
// echo 'var_dump($out[\'response\'][\'_embedded\'][\'task_types\']['.$key.']) = '."<br>"; var_dump($value);
// }
//
//
// //Создание экземпляра класса и сразу выполнение запроса (Контакты)
// $out = (new workInApiAmocrm($_SESSION['loginAmocrm']['subdomain']))->accountAmocrm('users');
// echo 'var_dump($out) = '."<br>"; var_dump($out);
// // echo 'var_dump($out[\'response\'][\'_embedded\'][\'users\']) = '."<br>"; var_dump($out['response']['_embedded']['users']);
// foreach ($out['response']['_embedded']['users'] as $key => $value) {
//   echo 'var_dump($out[\'response\'][\'_embedded\'][\'users\']['.$key.']) = '."<br>"; var_dump($value);
//   // echo 'var_dump($out[\'response\'][\'_embedded\'][\'users\']['.$key.'][\'rights\'][\'by_status\']) = '."<br>"; var_dump($value['rights']['by_status']);
// }
//
//
// //Получение списка групп пользователей
// $out = (new workInApiAmocrm($subdomain))->accountAmocrm('groups');
// echo 'var_dump($out) = '."<br>"; var_dump($out);
// echo 'var_dump($out[\'response\'][\'_embedded\'][\'groups\']) = '."<br>"; var_dump($out['response']['_embedded']['groups']);
// // foreach ($out['response']['_embedded']['users'] as $key => $value) {
// //   echo 'var_dump($out[\'response\'][\'_embedded\'][\'users\']['.$key.']) = '."<br>"; var_dump($value);
// //   // echo 'var_dump($out[\'response\'][\'_embedded\'][\'users\']['.$key.'][\'rights\'][\'by_status\']) = '."<br>"; var_dump($value['rights']['by_status']);
// // }
//
//
// //Создание экземпляра класса и сразу выполнение запроса (типам дополнительных полей в аккаунте)
// $out = (new workInApiAmocrm($_SESSION['loginAmocrm']['subdomain']))->accountAmocrm('note_types');
// echo 'var_dump($out) = '."<br>"; var_dump($out);
// foreach ($out['response']['_embedded']['note_types'] as $key => $value) {
// echo 'var_dump($out[\'response\'][\'_embedded\'][\'note_types\']['.$key.']) = '."<br>"; var_dump($value);
// }
//
// }
//!!! Конец.Проба получения информации об Аккаунте

}//Конец. if (isset($_POST['email'], $_POST['userKey'], $_POST['subdomain']))


// unset($arrMatchNamesToCodes,$arrMatchNamesToCodesEnum);
unset($out,$arrMatchNamesToCodes);


// echo 'var_dump($_SESSION) = '."<br>"; var_dump($_SESSION);
// if (!empty($_SESSION['authAmocrm'])) {
//   echo 'var_dump($_SESSION[\'authAmocrm\']) = '."<br>"; var_dump($_SESSION['authAmocrm']);
// }
// if (!empty($_SESSION['usersAccountAmocrm'])) {
//   foreach ($_SESSION['usersAccountAmocrm'] as $key => $value) {
//     echo 'var_dump($_SESSION[\'usersAccountAmocrm\']['.$key.']) = '."<br>"; var_dump($value);
//   }
// }
// if (!empty($_SESSION['infoAccountAmocrm'])) {
//   echo 'var_dump($_SESSION[\'infoAccountAmocrm\']) = '."<br>"; var_dump($_SESSION['infoAccountAmocrm']);
// }
// if (!empty($_SESSION['infoAccountAmocrm']['custom_fields'])) {
//   foreach ($_SESSION['infoAccountAmocrm']['custom_fields'] as $key1 => $value1) {
//     echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'custom_fields\']['.$key1.']) = '."<br>"; var_dump($value1);
//     // if (is_array($value1)) {
//     //   foreach ($value1 as $key2 => $value2) {
//     //     echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'custom_fields\']['.$key1.']['.$key2.']) = '."<br>"; var_dump($value2);
//     //   }
//     // }
//   }
// }
//!!! НЕ используется !!! Удалить !!!
// if (!empty($_SESSION['infoAccountAmocrm']['custom_fields_enum'])) {
//   foreach ($_SESSION['infoAccountAmocrm']['custom_fields_enum'] as $key1 => $value1) {
//     echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'custom_fields_enum\']['.$key1.']) = '."<br>"; var_dump($value1);
//     // if (is_array($value1)) {
//     //   foreach ($value1 as $key2 => $value2) {
//     //     echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'custom_fields_enum\']['.$key1.']['.$key2.']) = '."<br>"; var_dump($value2);
//     //   }
//     // }
//   }
// }
// if (!empty($_SESSION['infoAccountAmocrm']['pipelines'])) {
//   foreach ($_SESSION['infoAccountAmocrm']['pipelines'] as $key => $value) {
//     echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'pipelines\']['.$key.']) = '."<br>"; var_dump($value);
//   }
// }
// if (!empty($_SESSION['infoAccountAmocrm']['task_types'])) {
//   echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'task_types\']['.$key.']) = '."<br>"; var_dump($_SESSION['infoAccountAmocrm']['task_types']);
// }
// if (!empty($_SESSION['infoAccountAmocrm']['note_types'])) {
//   echo 'var_dump($_SESSION[\'infoAccountAmocrm\'][\'note_types\']['.$key.']) = '."<br>"; var_dump($_SESSION['infoAccountAmocrm']['note_types']);
// }

session_write_close();

?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="keywords" content="amocrm,api amocrm"/>
<meta name="description" content="Пример работы сайта с аккаунтом AmoCRM через API"/>
<meta name="robots" content="index,follow" >
<title>Авторизация в AmoCRM</title>
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
<link href="assets/bootstrap4/css/bootstrap.css" rel="stylesheet">
<link href="css/fonts.css" rel="stylesheet">
<link href="css/login_amocrm.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$active_item = 'login_amocrm';
require("php/header.php");
unset($active_item,$name_account);
?>

<div class="container content">
  <div class="row justify-content-center pt-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 text-center">
      <h4 class="font-italic">Авторизация в аккаунте AmoCrm<br>через средства API</h4>
    </div>
  </div>
  <div class="row justify-content-center pb-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 border rounded py-2">
      <form class="mb-0" method="post" novalidate><?php /*<!--  class="was-validated" novalidate -->*/ ?>
        <div class="form-group">
          <label for="inputEmail">Адрес электронной почты *</label>
          <input type="text" id="inputEmail" name="email" class="form-control" placeholder="___@___.___" aria-invalid="true" aria-describedby="emailHelp" data-target-error="#error-email" data-target-result="#result_submit">
          <div class="invalid-feedback" id="error-email">Введен некорректный адрес электронной почты</div>
          <!-- <small id="emailHelp" class="form-text text-muted">Поле не обязательно для заполнения</small> -->
        </div>
        <div class="form-group">
          <label for="inputUserKey">Ключ пользователя *</label>
          <input type="text" id="inputUserKey" name="userKey" class="form-control" placeholder="Ключ пользователя" aria-invalid="true" aria-required="true" aria-describedby="userKeyHelp" data-target-error="#error-userKey" data-target-result="#result_submit">
          <!-- <small id="userKeyHelp" class="form-text text-muted">Поле обязательно для заполнения</small> -->
          <div class="invalid-feedback" id="error-userKey">Пожалуйста, заполните поле</div>
          <!-- <div class="invalid-feedback" id="error-userKey">Допускается ввод Латинских букв и Цифр</div> -->
        </div>
        <div class="form-group">
          <label for="inputSubdomain">Поддомен *</label>
          <input type="text" id="inputSubdomain" name="subdomain" class="form-control" placeholder="Поддомен" aria-invalid="true" aria-required="true" aria-describedby="subdomainHelp" data-target-error="#error-subdomain" data-target-result="#result_submit">
          <!-- <small id="subdomainHelp" class="form-text text-muted">Поле обязательно для заполнения</small> -->
          <div class="invalid-feedback" id="error-subdomain">Пожалуйста, заполните поле</div>
          <!-- <div class="invalid-feedback" id="error-subdomain">Допускается ввод Латинских букв и Цифр</div> -->
        </div>
        <div class="form-group">
          <label for="inputToken" class="sr-only">Токен</label>
          <input type="hidden" id="inputToken" name="token" value="<?php echo $hash_token; unset($hash_token); ?>" />
        </div>
        <div class="form-group">
          <button type="button" id="button_submit" class="btn btn-secondary" data-target-result="#result_submit">Отправить</button>
        </div>
        <small class="form-text text-muted">* Поля обязательные для заполнения</small>
      </form>
    </div>
  </div>
  <div class="row justify-content-center pb-2<?= empty($log_authorization) ? ' d-none' : '' ?>">
    <div id="result_submit" class="col-12 col-sm-10 col-md-8 col-lg-6 border border-info rounded py-2 bg-light<?= (isset($status_authorization) && !$status_authorization) ? ' text-danger' : ' text-success' ?>"><?= empty($log_authorization) ? '' : $log_authorization ?></div>
  <?php unset($status_authorization, $log_authorization); ?></div>
</div>
<?php
$link_github = 'https://github.com/leogoodok/gt4u-tasks/tree/leogoodok/task_3';
require("php/footer.php");
unset($link_github);
?>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> -->
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> -->
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script> -->

<script src="assets/jquery/jquery.js" type="text/javascript"></script>
<script src="assets/bootstrap4/js/bootstrap.js" type="text/javascript"></script>
<script src="assets/bootstrap4/js/bootstrap.bundle.js" type="text/javascript"></script>
<script src="js/login_amocrm.js" type="text/javascript"></script>
</body>
</html>
