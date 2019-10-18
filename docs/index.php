<?php
session_start();
include_once "../myphp/models/loginApiAmocrm.php";
use app\models\amocrm\loginApiAmocrm;
include_once "../myphp/models/workInApiAmocrm.php";
use app\models\amocrm\workInApiAmocrm;
//Проверка в сессии наличия токена, если нет, то создать
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
//Проверка проводилась ли авторизация?
if (isset($_SESSION['authAmocrm'], $_SESSION['authAmocrm']['auth']) && $_SESSION['authAmocrm']['auth']) {
  //Статус авторизации
  $status_authorization = $_SESSION['authAmocrm']['auth'];
  if (!empty($_SESSION['nameAuthUserAmocrm'])) {
    //Имя авторизованного пользователя AmoCRM
    $name_account = $_SESSION['nameAuthUserAmocrm'];
  }
  if (isset($_SESSION['loginAmocrm'], $_SESSION['loginAmocrm']['email'], $_SESSION['loginAmocrm']['userKey'], $_SESSION['loginAmocrm']['subdomain'])) {
    $subdomain = $_SESSION['loginAmocrm']['subdomain'];
    //Инициализация свойств класса, без создания экземпляра класса
    try {
      $status_authorization = loginApiAmocrm::initParams($_SESSION['loginAmocrm']['email'], $_SESSION['loginAmocrm']['userKey'], $_SESSION['loginAmocrm']['subdomain'], $_SESSION['loginAmocrm']['login_time']);
    } catch (\Exception $e) {
// echo 'Выброшено исключение: ', $e->getMessage(), "\n";
      $status_authorization = false;
    }
  }
}
//Считывание из сессии токена для POST запросов и формирование аригинальных токенов для разных форм запросов
//Хеш токена, на основе названия форм "queue_contacts" таблицы "Очередь необработанных заявок"
$hash_token_queue_contacts = hash_hmac('sha256', 'queue_contacts', $_SESSION['token']);
//Считывание из $_POST - данных "обработанного" контакта и удаление из очереди выполненной заявки этого контакта
if (isset($_POST['contact']['delete_hash']) && !empty($_SESSION['queue_contacts'])) {
  if (!empty($_POST['contact']['token']) && hash_equals($hash_token_queue_contacts, $_POST['contact']['token'])) {
    $delete_hash = htmlspecialchars(strip_tags($_POST['contact']['delete_hash']));
    if (($key = array_search($delete_hash, array_column($_SESSION['queue_contacts'], 'hash'))) !== false) {
      unset($_SESSION['queue_contacts'][$key]);
      $_SESSION['queue_contacts'] = array_values($_SESSION['queue_contacts']);
    }
    unset($delete_hash,$key);
  }
}
//Считывание очереди заявок. Пока из СЕССИИ, можно переделать на из БД
if (!empty($_SESSION['queue_contacts'])) {
  $queue_contacts = $_SESSION['queue_contacts'];
}
//Считывание информации об сущностях аккаунта AmoCRM, из СЕССИИ (!заполняется при авторизации в Аккаунте)
if (!empty($_SESSION['infoAccountAmocrm'])) {
  $infoAccountAmocrm = $_SESSION['infoAccountAmocrm'];
}
//Считывание информации о пользователях аккаунта AmoCRM, из СЕССИИ (!заполняется при авторизации в Аккаунте)
if (!empty($_SESSION['usersAccountAmocrm'])) {
  $usersAccountAmocrm = $_SESSION['usersAccountAmocrm'];
}
//Считывание из $_POST - данных контакта для поика, обработки и добавления в AmoCRM
if (isset($_POST['contact']['queue_hash'], $_POST['contact']['name'], $_POST['contact']['phone'], $_POST['contact']['email'])) {
  if (!empty($_POST['contact']['token']) && hash_equals($hash_token_queue_contacts, $_POST['contact']['token'])) {
    $queue_hash = htmlspecialchars(strip_tags($_POST['contact']['queue_hash']));
    $contact = [];
    $contact['name'] = htmlspecialchars(strip_tags($_POST['contact']['name']));
    $contact['phone'] = htmlspecialchars(strip_tags(trim($_POST['contact']['phone'])));
    $contact['email'] = htmlspecialchars(strip_tags(trim($_POST['contact']['email'])));
    $contact['searchBy'] = '';
    $contact['isFound'] = false;
    $contact['isCreated'] = false;
    $contact['isAddLeads'] = false;
    $contact['isAddTasks'] = false;
    $contact['isAddNotes'] = false;
    $contact['isCompleted'] = false;
    //Запись всех данных заявки
    if (($key = array_search($queue_hash, array_column($queue_contacts, 'hash'))) !== false) {
      $contact['request'] = $queue_contacts[$key];
      if (isset($contact['request']['isCompleted'])) {
        $contact['isCompleted'] = $contact['request']['isCompleted'];
      }
    }
    unset($key,$queue_hash);
    //Если выполнена авторизация в API AmoCRM
    if (isset($status_authorization) && $status_authorization) {
      //Проверка актуальности авторизации (Куки авторизации НЕ просрочен)
      if (! loginApiAmocrm::isRelevantAuth()) {
        try {
          //Повторная авторизация (обновление куки)
          $out = loginApiAmocrm::getNew()->loginAmocrm();
          //Перезапись времени авторизации в переменной сессии
          if ($out['response']['server_time']) {
            $_SESSION['loginAmocrm']['login_time'] = $out['response']['server_time'];
            $status_time_authorization = true;
          }
        } catch (\Exception $e) {
// echo 'Выброшено исключение: ', $e->getMessage(), "\n";
          $status_time_authorization = false;
        }
        unset($out);
      } else {
        $status_time_authorization = true;
      }
      //Поиск контакта в API AmoCRM
      if ($status_time_authorization && isset($subdomain)) {
        //Создание экземпляра класса
        $obj = new workInApiAmocrm($subdomain);
        //Поиск контакта по "телефону"
        $out = $obj->searchContactAmocrm(substr($contact['phone'], 2));
        //Обработка ответа
        if (isset($out['status']) && $out['status'] == 'ok') {
          if (isset($out['count'], $out['response'], $out['response'][0]) && $out['count'] > 0) {
            $contact['isFound'] = true;
            $contact['searchBy'] = 'phone';
            if (isset($out['response'][0]['_links'])) {
              unset($out['response'][0]['_links']);
            }
            $contact['data_contact'] = $out['response'][0];
          }
        }
        //Если контакт Не найден, продолжаем поиск...
        if (!$contact['isFound']) {
          //Поиск контакта по "адресу почты"
          $out = $obj->searchContactAmocrm($contact['email']);
          //Обработка ответа
          if (isset($out['status']) && $out['status'] == 'ok') {
            if (isset($out['count'], $out['response'], $out['response'][0]) && $out['count'] > 0) {
              $contact['isFound'] = true;
              $contact['searchBy'] = 'email';
              if (isset($out['response'][0]['_links'])) {
                unset($out['response'][0]['_links']);
              }
              $contact['data_contact'] = $out['response'][0];
            }
          }
        }
        //Если заявка не выпонена И контакт Не найден, ни по "телефону" ни "адресу почты"? Создание нового Контакта
        if (!$contact['isCompleted'] && !$contact['isFound']) {
          //Выбор ответственного за контакт
          $key_group = 0;//ID группы "Отдел продаж"
          if (isset($usersAccountAmocrm[$key_group]['queue'])) {
            $queue_manager = ($usersAccountAmocrm[$key_group]['queue'] + 1 < count($usersAccountAmocrm[$key_group]['users'])) ? $usersAccountAmocrm[$key_group]['queue'] + 1 : 0;
            $usersAccountAmocrm[$key_group]['queue'] = $queue_manager;
          }
          if (!empty($usersAccountAmocrm[$key_group]['users'][$queue_manager]['id'])) {
            $responsible_user_id = $usersAccountAmocrm[$key_group]['users'][$queue_manager]['id'];
          }
          //Массв массивов данных для создания "Контактов"
          $data = [
            [
              'name' => $contact['name'],
              'created_by' => $_SESSION['authAmocrm']['user']['id'],
              'responsible_user_id' => isset($responsible_user_id) ? $responsible_user_id : $_SESSION['authAmocrm']['user']['id'],
              'custom_fields' => [
                [
                  'code' => 'PHONE',
                  'values' => [
                    [
                      'value' => $contact['phone'],
                      'enum' => 'MOB'
                    ]
                  ]
                ],
                [
                  'code' => 'EMAIL',
                  'values' => [
                    [
                      'value' => $contact['email'],
                      'enum' => 'PRIV'
                    ]
                  ]
                ],
              ]
            ]
          ];
          unset($key_group,$queue_manager,$responsible_user_id);
          //Создаем Новый контакт
          $out = $obj->actionContactAmocrm('add', $data);
          //Обработка ответа, дозаполнение полей контакта
          if (isset($out['status']) && $out['status'] == 'ok') {
            if (isset($out['count'], $out['response'], $out['response'][0]) && $out['count'] > 0) {
              $contact['isCreated'] = true;
              $contact['data_contact'] = $data[0];
              if (!empty($out['response'][0]['id'])) {
                $contact['data_contact']['id'] = $out['response'][0]['id'];
              }
            }
          }
          unset($data);
        }
        //Если контакт найден или создан, добавление имён создателя и ответственного
        if ($contact['isFound'] || $contact['isCreated']) {
          if (!empty($usersAccountAmocrm) && is_array($usersAccountAmocrm)) {
            foreach ($usersAccountAmocrm as $key_group => $value_group) {
              if (!empty($contact['data_contact']['created_by'])) {
                if (($key = array_search($contact['data_contact']['created_by'], array_column($value_group['users'], 'id'))) !== false) {
                  $contact['data_contact']['created_name'] = $value_group['users'][$key]['name'];
                }
              }
              if (!empty($contact['data_contact']['responsible_user_id'])) {
                if (($key = array_search($contact['data_contact']['responsible_user_id'], array_column($value_group['users'], 'id'))) !== false) {
                  $contact['data_contact']['responsible_user_name'] = $value_group['users'][$key]['name'];
                }
              }
              if (isset($contact['data_contact']['created_name'], $contact['data_contact']['responsible_user_name'])) {
                break;
              }
            }
          }
          unset($key);
        }
        //Если заявка не выпонена И контакт найден или создан, то Создание сделки
        if (!$contact['isCompleted'] && ($contact['isFound'] || $contact['isCreated'])) {
          //Откуда пришла заявка
          $site = isset($contact['request']['form']['page']) ? parse_url($contact['request']['form']['page'],PHP_URL_HOST) : $_SERVER['SERVER_NAME'];
          //Массв массивов данных для создания "Сделки"
          $data = [
            [
              'name' => "Заявка с сайта $site от ".date('d.m.Y',time()),
              'created_at' => time(),
              'status_id' => isset($infoAccountAmocrm['pipelines'][0]['statuses'][0]['id']) ? $infoAccountAmocrm['pipelines'][0]['statuses'][0]['id'] : 29859331,
              'responsible_user_id' => $contact['data_contact']['responsible_user_id'],
              'contacts_id' => $contact['data_contact']['id'],
              'custom_fields' => [],
            ]
          ];
          unset($site);
          if (!empty($contact['request']['form']['callback_order'])) {
            if (($key_field = array_search('description', array_column($infoAccountAmocrm['custom_fields']['leads'], 'code', 'id'))) !== false) {
              $data[0]['custom_fields'][] = [
                'id' => $infoAccountAmocrm['custom_fields']['leads'][$key_field]['id'],
                'values' => [[
                    'value' => $contact['request']['form']['callback_order']
                ]]
              ];
            }
          }
          if (!empty($contact['request']['form']['catchpoint'])) {
            if (($key_field = array_search('description', array_column($infoAccountAmocrm['custom_fields']['leads'], 'code', 'id'))) !== false) {
              $data[0]['custom_fields'][] = [
                'id' => $infoAccountAmocrm['custom_fields']['leads'][$key_field]['id'],
                'values' => [[
                    'value' => $contact['request']['form']['catchpoint']
                ]]
              ];
            }
          }
          if (isset($contact['request']['utm']) && is_array($contact['request']['utm'])) {
            foreach ($contact['request']['utm'] as $name => $value) {
              if (($key_field = array_search($name, array_column($infoAccountAmocrm['custom_fields']['leads'], 'code', 'id'))) !== false) {
                $data[0]['custom_fields'][] = [
                  'id' => $infoAccountAmocrm['custom_fields']['leads'][$key_field]['id'],
                  'values' => [[
                      'value' => $value
                  ]]
                ];
              }
            }
          }
          if (isset($contact['request']['fields']) && is_array($contact['request']['fields'])) {
            foreach ($contact['request']['fields'] as $name => $value) {
              if (($key_field = array_search($name, array_column($infoAccountAmocrm['custom_fields']['leads'], 'code', 'id'))) !== false) {
                $arr = [];
                if (isset($infoAccountAmocrm['custom_fields']['leads'][$key_field]['enums'])) {
                  if (($key_enum = array_search($value, $infoAccountAmocrm['custom_fields']['leads'][$key_field]['enums'])) !== false) {
                    $arr['enum'] = $key_enum;
                    $arr['value'] = $value;
                  }
                } else {
                  $arr['value'] = $value;
                }
                $data[0]['custom_fields'][] = [
                  'id' => $infoAccountAmocrm['custom_fields']['leads'][$key_field]['id'],
                  'values' => [
                    $arr
                  ]
                ];
              }
            }
          }
          if (!empty($contact['request']['website'])) {
            if (($key_field = array_search('website', array_column($infoAccountAmocrm['custom_fields']['leads'], 'code', 'id'))) !== false) {
              $value = $contact['request']['website'];
              if (($key_enum = array_search($value, $infoAccountAmocrm['custom_fields']['leads'][$key_field]['enums'])) === false) {
                if (($key_enum = array_search('Не задан', $infoAccountAmocrm['custom_fields']['leads'][$key_field]['enums'])) === false) {
                  $value = 'Не задан';
                }
              }
              if (!empty($key_enum)) {
                $data[0]['custom_fields'][] = [
                  'id' => $infoAccountAmocrm['custom_fields']['leads'][$key_field]['id'],
                  'values' => [[
                      'value' => $value,
                      'enum' => $key_enum
                  ]]
                ];
              }
              unset($value);
            }
          }
          //Создаем Новую сделку
          $out = $obj->actionLeadsAmocrm('add', $data);
          //Обработка ответа, дозаполнение полей контакта
          if (isset($out['status']) && $out['status'] == 'ok') {
            if (isset($out['count'], $out['response'], $out['response'][0]) && $out['count'] > 0) {
              $contact['isAddLeads'] = true;
              $contact['data_leads'] = $data[0];
              if (!empty($out['response'][0]['id'])) {
                $contact['data_leads']['id'] = $out['response'][0]['id'];
              }
            }
          }
          unset($data,$arr);
          //Если Сделка создана, добавление имени ответственного и назания воронки
          if ($contact['isAddLeads']) {
            if (!empty($usersAccountAmocrm) && is_array($usersAccountAmocrm)) {
              foreach ($usersAccountAmocrm as $key_group => $value_group) {
                if (!empty($contact['data_leads']['responsible_user_id'])) {
                  if (($key = array_search($contact['data_leads']['responsible_user_id'], array_column($value_group['users'], 'id'))) !== false) {
                    $contact['data_leads']['responsible_user_name'] = $value_group['users'][$key]['name'];
                    break;
                  }
                }
              }
              if (isset($infoAccountAmocrm['pipelines']) && is_array($infoAccountAmocrm['pipelines'])) {
                foreach ($infoAccountAmocrm['pipelines'] as $pipelines) {
                  if (isset($pipelines['statuses']) && is_array($pipelines['statuses'])) {
                    if (($key = array_search($contact['data_leads']['status_id'], array_column($pipelines['statuses'], 'id'))) !== false) {
                      $contact['data_leads']['pipelines_name'] = $pipelines['statuses'][$key]['name'];
                      break;
                    }
                  }
                }
              }
            }
            unset($key);
          }
        }
        //Если заявка не выпонена И контакт найден ИЛИ создан, И создана сделка
        if (!$contact['isCompleted'] && ($contact['isFound'] || $contact['isCreated']) && $contact['isAddLeads']) {
          //Создание новой Задачи
          $site = isset($contact['request']['form']['page']) ? parse_url($contact['request']['form']['page'],PHP_URL_HOST) : $_SERVER['SERVER_NAME'];
          $data = [
            [
              'element_id' => $contact['data_leads']['id'],
              'element_type' => 2,
              'complete_till' => time() + 15*60,
              'text' => "Поступила заявка с сайта $site.\r\n1) Необходимо связаться с клиентом.\r\n2) Затем перевести сделку на следующий этап.",
            ]
          ];
          //Тип задачи == 'Заявка с сайта'
          if (isset($infoAccountAmocrm['task_types'])) {
            if (($key_field = array_search('Заявка с сайта', array_column($infoAccountAmocrm['task_types'], 'name'))) !== false) {
              $data[0]['task_type'] = $infoAccountAmocrm['task_types'][$key_field]['id'];
            }
          }
          //Создаем Новую ЗАДАЧУ
          $out = $obj->actionTasksAmocrm('add', $data);
          //Обработка ответа, дозаполнение полей контакта
          if (isset($out['status']) && $out['status'] == 'ok') {
            if (isset($out['count'], $out['response'], $out['response'][0]) && $out['count'] > 0) {
              $contact['isAddTasks'] = true;
              $contact['data_tasks'] = $data[0];
              if (!empty($out['response'][0]['id'])) {
                $contact['data_tasks']['id'] = $out['response'][0]['id'];
              }
            }
          }
          unset($data);
          //Создание нового ПРИМЕЧАНИЯ
          $data = [
            [
              'element_id' => $contact['data_leads']['id'],
              'element_type' => 2,
              'text' => "Данные из формы на $site.\r\n"
                       ."ФИО: {$contact['name']}\r\n"
                       ."Тел.: {$contact['phone']}\r\n"
                       ."Email: {$contact['email']}\r\n"
                       ."_________________________\r\n",
            ]
          ];
          unset($site);
          if (isset($contact['request']['form']['catchpoint'])) {
            $data[0]['text'] .= "Форма захвата: {$contact['request']['form']['catchpoint']}\r\n";
          } else if (isset($contact['request']['form']['callback_order'])) {
            $data[0]['text'] .= "Форма захвата: {$contact['request']['form']['callback_order']}\r\n";
          }
          if (isset($contact['request']['fields']['profile'])) {
            $data[0]['text'] .= "Профиль: {$contact['request']['fields']['profile']}\r\n";
          }
          if (isset($contact['request']['fields']['width'])) {
            $data[0]['text'] .= "Ширина, мм: {$contact['request']['fields']['width']}\r\n";
          }
          if (isset($contact['request']['fields']['height'])) {
            $data[0]['text'] .= "Высота, мм: {$contact['request']['fields']['height']}\r\n";
          }
          if (isset($contact['request']['fields']['mechanism'])) {
            $data[0]['text'] .= "Механизм: {$contact['request']['fields']['mechanism']}\r\n";
          }
          if (isset($contact['request']['fields']['number'])) {
            $data[0]['text'] .= "Кол-во камер: {$contact['request']['fields']['number']}\r\n";
          }
          if (isset($contact['request']['utm']) && is_array($contact['request']['utm'])) {
            $data[0]['text'] .= "_________________________\r\n";
            foreach ($contact['request']['utm'] as $key => $value) {
              $data[0]['text'] .= "$key: $value\r\n";
            }
          }
          //Тип примечания = 'COMMON'(Обычное примечание)
          if (isset($infoAccountAmocrm['note_types'])) {
            if (($key_field = array_search('COMMON', array_column($infoAccountAmocrm['note_types'], 'code', 'id'))) !== false) {
              $data[0]['task_type'] = $key_field;
            }
          }
          //Создаем Новое ПРИМЕЧАНИЕ
          $out = $obj->actionNotesAmocrm('add', $data);
          //Обработка ответа, дозаполнение полей контакта
          if (isset($out['status']) && $out['status'] == 'ok') {
            if (isset($out['count'], $out['response'], $out['response'][0]) && $out['count'] > 0) {
              $contact['isAddNotes'] = true;
              $contact['data_notes'] = $data[0];
              if (!empty($out['response'][0]['id'])) {
                $contact['data_notes']['id'] = $out['response'][0]['id'];
              }
            }
          }
          unset($data,$out,$key_field,$obj);
        }
        //Если заявка не выпонена И (контакт найден ИЛИ создан), И создана сделка И создана Задача И создано Примечание
        if (!$contact['isCompleted'] && ($contact['isFound'] || $contact['isCreated']) && $contact['isAddLeads'] && $contact['isAddTasks'] && $contact['isAddNotes']) {
          //Установить статус Заявке: ВЫПОЛНЕНО
          $contact['isCompleted'] = true;
          if (($key = array_search($contact['request']['hash'], array_column($queue_contacts, 'hash'))) !== false) {
            $queue_contacts[$key]['isCompleted'] = true;
          }
          if (($key = array_search($contact['request']['hash'], array_column($_SESSION['queue_contacts'], 'hash'))) !== false) {
            $_SESSION['queue_contacts'][$key]['isCompleted'] = true;
          }
        }
        unset($key);
      }
    }
  }
}
session_write_close();
?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="keywords" content="amocrm,api amocrm"/>
<meta name="description" content="Пример работы сайта с аккаунтом AmoCRM через API"/>
<meta name="robots" content="index,follow" >
<title>Главная</title>
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
<link href="assets/bootstrap4/css/bootstrap.css" rel="stylesheet">
<link href="css/fonts.css" rel="stylesheet">
<link href="css/index.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$active_item = 'index';
require("php/header.php");
unset($active_item,$name_account);
?>

<div class="container content">
  <div class="row justify-content-center pt-2<?= (isset($status_authorization) && $status_authorization) ? ' d-none' : '' ?>">
    <div id="result_submit" class="col-12 col-sm-10 col-md-8 col-lg-6 border border-danger rounded py-2 bg-light text-danger text-center">Внимание! Выполните авторизацию в аккаунте AmoCrm.</div>
  </div>
  <div class="row justify-content-center pt-2">
    <div class="col-12 col-sm-12 col-md-10 col-lg-8 border border-info rounded pt-2 bg-light">
      <div class="row mb-2">
        <div class="col text-center text-success">
          <h5 class="h-100 mb-0">Очередь необработанных заявок</h5>
        </div>
        <div class="col-auto">
          <a class="btn btn-info btn-sm" href="index.php">Обновить</a>
        </div>
      </div>
      <div class="row px-1">
        <table class="table table-sm text-center mb-0">
          <thead class="thead-light">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Имя</th>
              <th scope="col">Телефон</th>
              <th scope="col">E-mail</th>
              <th scope="col">Действия</th>
            </tr>
          </thead>
          <tbody><?php
            if (!empty($queue_contacts) && is_array($queue_contacts)):
              for ($j = 0; $j < count($queue_contacts); $j++):
            ?>

            <tr<?= (isset($queue_contacts[$j]['isCompleted']) && $queue_contacts[$j]['isCompleted']) ? ' class="table-success"' : '' ?>>
              <th class="align-middle" scope="row"><?= $j + 1 ?></th>
              <td class="align-middle"><?= !empty($queue_contacts[$j]['contact']['name']) ? $queue_contacts[$j]['contact']['name'] : '&nbsp;' ?></td>
              <td class="align-middle"><?= !empty($queue_contacts[$j]['contact']['phone']) ? $queue_contacts[$j]['contact']['phone'] : '&nbsp;' ?></td>
              <td class="align-middle"><?= !empty($queue_contacts[$j]['contact']['email']) ? $queue_contacts[$j]['contact']['email'] : '&nbsp;' ?></td>
              <td><?php
              if (isset($queue_contacts[$j]['isCompleted']) && $queue_contacts[$j]['isCompleted']): ?>

                <span class="btn btn-outline-info btn-sm text-font-glyphicons-halflings" data-toggle="tooltip" data-placement="left" title="Заявка выполнена">&#xE013;</span>
                <form class="d-inline-block mb-0" method="post"<?= (isset($status_authorization) && $status_authorization) ? '' : ' tabindex="'.$j.'" data-toggle="tooltip" data-placement="left" title="Удаление доступно после авторизации в аккаунте AmoCRM"' ?> novalidate>
                  <input type="hidden" name="contact[token]" value="<?= $hash_token_queue_contacts ?>" aria-label="Токен" aria-describedby="Токен">
                  <input type="hidden" name="contact[delete_hash]" value="<?= $queue_contacts[$j]['hash'] ?>" aria-label="Удалить заявку" aria-describedby="Удалить заявку">
                  <button type="submit" class="btn btn-secondary btn-sm text-font-glyphicons-halflings" <?= (isset($status_authorization) && $status_authorization) ? 'data-toggle="tooltip" data-placement="left" title="Удалить выполненную заявку из очереди"' : 'disabled' ?>>&#xE014;</button>
                </form><?php
              else: ?>

                <form class="w-100 mb-0" method="post"<?= (isset($status_authorization) && $status_authorization) ? '' : ' tabindex="'.$j.'" data-toggle="tooltip" data-placement="left" title="Поиск доступен после авторизации в аккаунте AmoCRM"' ?> novalidate>
                  <input type="hidden" name="contact[token]" value="<?= $hash_token_queue_contacts ?>" aria-label="Токен" aria-describedby="Токен">
                  <input type="hidden" name="contact[queue_hash]" value="<?= $queue_contacts[$j]['hash'] ?>" aria-label="HASH заявки" aria-describedby="HASH заявки">
                  <input type="hidden" name="contact[name]" value="<?= !empty($queue_contacts[$j]['contact']['name']) ? $queue_contacts[$j]['contact']['name'] : '' ?>" aria-label="Имя" aria-describedby="Имя">
                  <input type="hidden" name="contact[phone]" value="<?= !empty($queue_contacts[$j]['contact']['phone']) ? $queue_contacts[$j]['contact']['phone'] : '' ?>" aria-label="Телефон" aria-describedby="Телефон">
                  <input type="hidden" name="contact[email]" value="<?= !empty($queue_contacts[$j]['contact']['email']) ? $queue_contacts[$j]['contact']['email'] : '' ?>" aria-label="E-mail" aria-describedby="E-mail">
                  <button type="submit" class="btn btn-secondary btn-sm text-font-glyphicons-halflings" <?= (isset($status_authorization) && $status_authorization) ? 'data-toggle="tooltip" data-placement="left" title="Найти контакт в аккаунте AmoCRM"' : 'disabled' ?>>&#xE003;</button>
                </form><?php
              endif; ?>
              </td>
            </tr><?php
              endfor;
            else:
            ?>

            <tr>
              <td colspan="5">Нет заявок в очереди</td>
            </tr><?php
            endif;
            ?>

          </tbody>
        </table>
      </div>
      <div class="row justify-content-center border-top border-info mt-1">
        <div class="col-auto">
          <a class="btn btn-link font-italic" href="site/index.php?utm_source=yandex&utm_medium=cpc&utm_campaign=google-poisk&utm_content=banner-full&utm_term=iphone">Добавить тестовую заявку</a>
        </div>
      </div>
    </div>
  </div>
  <div class="row justify-content-center pt-2">
    <div class="col-12 col-sm-12 col-md-10 col-lg-8 text-center">
      <h4 class="mb-0">Работа с Контактами AmoCRM через API</h4>
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-auto text-center text-secondary">
      <small>Log выполнения:</small>
    </div><?php
      if (!isset($contact)): ?>

    <div class="col-auto text-center text-secondary">
      <h6 class="mb-0">Действий не выполнялось.</h6>
    </div><?php else:
        if (isset($contact['isFound']) && $contact['isFound']): ?>

    <div class="col-auto text-center text-info">
      <h6 class="mb-0">Контакт найден в AmoCRM.</h6>
    </div><?php
        else: ?>

    <div class="col-auto text-center text-danger">
      <h6 class="mb-0">Контакт не найден в AmoCRM.</h6>
    </div><?php
        endif;
        if (isset($contact['isCreated']) && $contact['isCreated']): ?>

    <div class="col-auto text-center text-info">
      <h6 class="mb-0">Создан новый контакт.</h6>
    </div><?php
        endif;
      endif; ?>

  </div>
  <div class="row justify-content-center py-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 border rounded py-2 bg-light">
      <div class="row mb-2">
        <div class="col text-center text-success">
          <h5 class="h-100 mb-0">Карточка контакта</h5>
        </div>
        <div class="col-auto">
          <a class="btn btn-info btn-sm" href="https://<?= isset($subdomain) ? $subdomain.'.amocrm.ru' : 'amocrm.ru' ?>/contacts/<?= isset($contact['data_contact']['id']) ? 'detail/'.$contact['data_contact']['id'] : 'list/contacts/' ?>" target="_blank">Контакт в AmoCRM</a>
        </div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">Название Контакта:</div>
        <div class="col-7"><?= isset($contact['data_contact']['name']) ? $contact['data_contact']['name'] : '&nbsp;' ?></div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">ID Контакта:</div>
        <div class="col-7"><?= isset($contact['data_contact']['id']) ? $contact['data_contact']['id'] : '&nbsp;' ?></div>
      </div><?php
      if (isset($contact['data_contact']['created_name'])): ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Создал Контакт:</div>
        <div class="col-7"><?= $contact['data_contact']['created_name'] ?></div>
      </div><?php
      endif; ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Ответственный:</div>
        <div class="col-7"><?= isset($contact['data_contact']['responsible_user_name']) ? $contact['data_contact']['responsible_user_name'] : '&nbsp;' ?></div>
      </div><?php
      if (isset($contact['data_contact']['custom_fields']) && is_array($contact['data_contact']['custom_fields'])):
        for ($j = 0; $j < count($contact['data_contact']['custom_fields']); $j++):
          $essence = 'contacts';
          $fieds_id = !empty($contact['data_contact']['custom_fields'][$j]['id']) ? $contact['data_contact']['custom_fields'][$j]['id'] : 0;
          $fieds_code = !empty($contact['data_contact']['custom_fields'][$j]['code']) ? $contact['data_contact']['custom_fields'][$j]['code'] : '';
          if (isset($contact['data_contact']['custom_fields'][$j]['values']) && is_array($contact['data_contact']['custom_fields'][$j]['values'])):
            for ($i = 0; $i < count($contact['data_contact']['custom_fields'][$j]['values']); $i++):
              $enum_id = isset($contact['data_contact']['custom_fields'][$j]['values'][$i]['enum']) ? $contact['data_contact']['custom_fields'][$j]['values'][$i]['enum'] : 0;
              if (is_int($enum_id)) {
                $enum_code = isset($infoAccountAmocrm['custom_fields'][$essence][$fieds_id]['enums'][$enum_id]) ? $infoAccountAmocrm['custom_fields'][$essence][$fieds_id]['enums'][$enum_id] : '';
              } else {
                $enum_code = $enum_id;
              }
              $name = workInApiAmocrm::getEnumCustomFields($essence,$fieds_code,$enum_code);
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right"><?= isset($name) ? $name.':' : '&nbsp;' ?></div>
        <div class="col-7"><?= isset($contact['data_contact']['custom_fields'][$j]['values'][$i]['value']) ? $contact['data_contact']['custom_fields'][$j]['values'][$i]['value'] : '&nbsp;' ?></div>
      </div><?php
            endfor;
          endif;
        endfor;
        unset($essence,$fieds_id,$fieds_code,$enum_id,$enum_code,$name);
        if (isset($contact['data_contact']['company']['name'])):
      ?>

      <div class="row border-top pt-1">
        <div class="col-5 text-right">Компания:</div>
        <div class="col-7"><?= $contact['data_contact']['company']['name'] ?></div>
      </div><?php
        endif;
      else: ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Телефон:</div>
        <div class="col-7">&nbsp;</div>
      </div>
      <div class="row border-top pt-1">
        <div class="col-5 text-right">Email:</div>
        <div class="col-7">&nbsp;</div>
      </div><?php
      endif; ?>

    </div>
  </div>
  <div class="row justify-content-center pt-2">
    <div class="col-12 col-sm-12 col-md-10 col-lg-8 text-center">
      <h4 class="mb-0">Работа со Сделками AmoCRM через API</h4>
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-auto text-center text-secondary">
      <small>Log выполнения:</small>
    </div><?php
    if (isset($contact['isAddLeads']) && $contact['isAddLeads']): ?>

    <div class="col-auto text-center text-info">
      <h6 class="mb-0">Создана новая Сделка</h6>
    </div><?php
    else: ?>

    <div class="col-auto text-center text-secondary">
      <h6 class="mb-0">Действий не выполнялось</h6>
    </div><?php
    endif; ?>

  </div>
  <div class="row justify-content-center py-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 border rounded py-2 bg-light">
      <div class="row mb-2">
        <div class="col text-center text-success">
          <h5 class="h-100 mb-0">Карточка Сделки</h5>
        </div>
        <div class="col-auto">
          <a class="btn btn-info btn-sm" href="https://<?= isset($subdomain) ? $subdomain.'.amocrm.ru' : 'amocrm.ru' ?>/leads/<?= isset($contact['data_leads']['id']) ? 'detail/'.$contact['data_leads']['id'] : 'pipeline/' ?>" target="_blank">Сделка в AmoCRM</a>
        </div>
      </div><?php
      if (isset($contact['data_leads']['name'])):
      ?>

      <div class="row border-top py-1">
        <div class="col text-center">
          <h5 class="text-primary mb-0"><?= $contact['data_leads']['name'] ?></h5>
        </div>
      </div><?php
      endif;
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">ID Сделки:</div>
        <div class="col-7"><?= isset($contact['data_leads']['id']) ? $contact['data_leads']['id'] : '&nbsp;' ?></div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">Статус сделки:</div>
        <div class="col-7"><?= isset($contact['data_leads']['pipelines_name']) ? $contact['data_leads']['pipelines_name'] : '&nbsp;' ?></div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">Ответственный:</div>
        <div class="col-7"><?= isset($contact['data_leads']['responsible_user_name']) ? $contact['data_leads']['responsible_user_name'] : '&nbsp;' ?></div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">Бюджет:</div>
        <div class="col-7"><?= isset($contact['data_leads']) ? isset($contact['data_leads']['sale']) ? $contact['data_leads']['sale'].' руб.' : '0 руб.' : '&nbsp;' ?></div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">Сделка Контакта:</div>
        <div class="col-7"><?= isset($contact['data_contact']['name']) ? $contact['data_contact']['name'] : '&nbsp;' ?></div>
      </div><?php
      if (!empty($contact['data_leads']['custom_fields']) && is_array($contact['data_leads']['custom_fields'])): ?>

      <div class="row border-top py-1">
        <div class="col text-center">
          <h5 class="font-italic text-secondary mb-0">Дополнительные поля</h5>
        </div>
      </div><?php
        for ($j = 0; $j < count($contact['data_leads']['custom_fields']); $j++):
          if (($key = array_search($contact['data_leads']['custom_fields'][$j]['id'],
            array_column($infoAccountAmocrm['custom_fields']['leads'], 'id', 'id'))) !== false) {
            $name = $infoAccountAmocrm['custom_fields']['leads'][$key]['name'];
          }
        ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right"><?= !empty($name) ? $name.':' : '&nbsp;' ?></div>
        <div class="col-7"><?= isset($contact['data_leads']['custom_fields'][$j]['values'][0]['value']) ? $contact['data_leads']['custom_fields'][$j]['values'][0]['value'] : '&nbsp;' ?></div>
      </div><?php
          unset($key,$name);
        endfor;
      endif;
      ?>

    </div>
  </div>
  <div class="row justify-content-center pt-2">
    <div class="col-12 col-sm-12 col-md-10 col-lg-8 text-center">
      <h4 class="mb-0">Работа с Задачами AmoCRM через API</h4>
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-auto text-center text-secondary">
      <small>Log выполнения:</small>
    </div><?php
    if (isset($contact['isAddTasks']) && $contact['isAddTasks']): ?>

    <div class="col-auto text-center text-info">
      <h6 class="mb-0">Создана новая Задача</h6>
    </div><?php
    else: ?>

    <div class="col-auto text-center text-secondary">
      <h6 class="mb-0">Действий не выполнялось</h6>
    </div><?php
    endif; ?>

  </div>
  <div class="row justify-content-center py-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 border rounded py-2 bg-light">
      <div class="row mb-2">
        <div class="col text-center text-success">
          <h5 class="h-100 mb-0">Карточка Задачи</h5>
        </div>
        <div class="col-auto">
          <a class="btn btn-info btn-sm" href="https://<?= isset($subdomain) ? $subdomain.'.amocrm.ru' : 'amocrm.ru' ?><?= isset($contact['data_leads']['id']) ? '/leads/detail/'.$contact['data_leads']['id'] : '/todo/line/' ?>" target="_blank">Задача в AmoCRM</a>
        </div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">ID Задачи:</div>
        <div class="col-7"><?= isset($contact['data_tasks']['id']) ? $contact['data_tasks']['id'] : '&nbsp;' ?></div>
      </div><?php
      if (isset($contact['data_tasks']['element_type'])) {
        $name = workInApiAmocrm::getEnumCustomFields('tasks','element_type',$contact['data_tasks']['element_type']);
      }
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Прикреплена к</div>
        <div class="col-7"><?= isset($name) ? 'Сущности: '.$name : '&nbsp;' ?></div>
      </div><?php
      unset($name);
      if (isset($contact['data_leads']['name'])):
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">&nbsp;</div>
        <div class="col-7"><?= $contact['data_leads']['name'] ?></div>
      </div><?php
      endif;
      if (isset($contact['data_tasks']['task_type'])) {
        if (($key = array_search($contact['data_tasks']['task_type'], array_column($infoAccountAmocrm['task_types'], 'id'))) !== false) {
          $name = $infoAccountAmocrm['task_types'][$key]['name'];
        }
      }
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Тип Задачи:</div>
        <div class="col-7"><?= isset($name) ? $name : '&nbsp;' ?></div>
      </div><?php
      unset($key,$name);
      if (!empty($contact['data_tasks']['text'])):
        $arr_text = explode("\r\n",$contact['data_tasks']['text']);
      ?>

      <div class="row justify-content-center px-2 pt-1">
        <div class="col border rounded bg-white py-1"><?php
        for ($i = 0; $i < count($arr_text); $i++):
        ?>

          <div><?= $arr_text[$i] ?></div><?php
        endfor;
        ?>

        </div>
      </div><?php
      unset($arr_text);
      endif;
      ?>

    </div>
  </div>
  <div class="row justify-content-center pt-2">
    <div class="col-12 col-sm-12 col-md-10 col-lg-8 text-center">
      <h4 class="mb-0">Работа с Примечаниями AmoCRM через API</h4>
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-auto text-center text-secondary">
      <small>Log выполнения:</small>
    </div><?php
    if (isset($contact['isAddNotes']) && $contact['isAddNotes']): ?>

    <div class="col-auto text-center text-info">
      <h6 class="mb-0">Создано новое Примечание</h6>
    </div><?php
    else: ?>

    <div class="col-auto text-center text-secondary">
      <h6 class="mb-0">Действий не выполнялось</h6>
    </div><?php
    endif; ?>

  </div>
  <div class="row justify-content-center py-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 border rounded py-2 bg-light">
      <div class="row mb-2">
        <div class="col text-center text-success">
          <h5 class="h-100 mb-0">Карточка Примечания</h5>
        </div>
        <div class="col-auto">
          <a class="btn btn-info btn-sm" href="https://<?= isset($subdomain) ? $subdomain.'.amocrm.ru' : 'amocrm.ru' ?><?= isset($contact['data_leads']['id']) ? '/leads/detail/'.$contact['data_leads']['id'] : '' ?>" target="_blank">Примечание в AmoCRM</a>
        </div>
      </div>
      <div class="row border-top py-1">
        <div class="col-5 text-right">ID Примечания:</div>
        <div class="col-7"><?= isset($contact['data_notes']['id']) ? $contact['data_notes']['id'] : '&nbsp;' ?></div>
      </div><?php
      if (isset($contact['data_notes']['element_type'])) {
        $name = workInApiAmocrm::getEnumCustomFields('notes','element_type',$contact['data_notes']['element_type']);
      }
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Прикреплена к</div>
        <div class="col-7"><?= isset($name) ? 'Сущности: '.$name : '&nbsp;' ?></div>
      </div><?php
      unset($name);
      if (isset($contact['data_leads']['name'])):
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">&nbsp;</div>
        <div class="col-7"><?= $contact['data_leads']['name'] ?></div>
      </div><?php
      endif;
      ?>

      <div class="row border-top py-1">
        <div class="col-5 text-right">Тип Примечания:</div>
        <div class="col-7"><?= isset($contact['data_leads']) ? 'Обычное' : '&nbsp;' ?></div>
      </div><?php
      if (isset($contact['data_notes']['text'])):
        $arr_text = explode("\r\n",$contact['data_notes']['text']);
      ?>

      <div class="row justify-content-center px-2 pt-1">
        <div class="col border rounded bg-white py-1"><?php
        for ($i = 0; $i < count($arr_text); $i++):
        ?>
          <div><?= $arr_text[$i] ?></div><?php
        endfor;
        ?>

        </div>
      </div><?php
      unset($arr_text);
      endif;
      ?>

    </div>
  </div><?php
  if (isset($contact['isCompleted']) && $contact['isCompleted']):
  ?>

  <div class="row justify-content-center py-2">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 pb-2 text-center">
      <form class="d-inline-block mb-0" method="post" novalidate>
        <input type="hidden" name="contact[token]" value="<?= $hash_token_queue_contacts ?>" aria-label="Токен" aria-describedby="Токен">
        <input type="hidden" name="contact[delete_hash]" value="<?= $contact['request']['hash'] ?>" aria-label="Удалить заявку" aria-describedby="Удалить заявку">
        <button type="submit" class="btn btn-lg btn-outline-success">Заявка выполнена полностью. Удалить из очереди?</button>
      </form>
    </div>
  </div><?php
  endif;
  unset($hash_token_queue_contacts, $status_authorization);
  ?>

</div>
<?php
$link_github = 'https://github.com/leogoodok/gt4u-tasks/tree/leogoodok/';
require("php/footer.php");
unset($link_github);
?>

<div id="butToTop">&#xE133;</div>

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> -->
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script> -->
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script> -->

<script src="assets/jquery/jquery.js" type="text/javascript"></script>
<script src="assets/bootstrap4/js/bootstrap.js" type="text/javascript"></script>
<script src="assets/bootstrap4/js/bootstrap.bundle.js" type="text/javascript"></script>
<script src="js/index.js" type="text/javascript"></script>
<script>jQuery('[data-toggle="tooltip"]').tooltip();</script>
</body>
</html>
