<!DOCTYPE HTML>
<html><?php

/*** make it or break it ***/
//error_reporting(E_ALL);//!!! ВРЕМЕННО !!!

include_once("../myphp/models/db/TabUsers.php");
//include_once("../myphp/models/pager.class.php");

//Тек.страница
$page_name = htmlentities($_SERVER['PHP_SELF']);

//Создание экземпляра класса с созданием соединения с БД
$table = new \app\models\db\TabUsers;

//количество записей в БД
$total_records = (int)$table->readCountTotal();

//количество результатов на странице - можно из $_GET['limit']
$limit = 10;

//проверить номер страницы в GET
if( filter_has_var(INPUT_GET, "page") == false) {
    //нет номера страницы в GET - номер 1
    $page = 1;
//если номер страницы не является целым или не находится в пределах диапазона - номер страницы 1
} elseif (filter_var($_GET['page'], FILTER_VALIDATE_INT, array("min_range" => 1, "max_range" => ceil($total_records/$limit))) == false) {
    $page = 1;
} else {
    //если все хорошо номер страницы из $_GET
    $page = (int)$_GET['page'];
}

//Получить данные
$offset = ($page - 1) * $limit;
$dataPager = $table->readDataPager($offset, $limit);
//echo '$dataPager = '."<br>"; echo var_dump($dataPager);

$table = null;

//Для пагинатора и линкера
$optionsPager = [
  'options' => [
    'max_button' => 7,
    'firstPageLabel' => true,
    'lastPageLabel' => true,
  ],
  'currentPage' => $page,
  'offset' => $offset,
  'limit' => $limit,
  'totalCount' => $total_records,
];
$optionsPager['pageTotal'] = (int)(($total_records + $limit - 1) / $limit);
$optionsPager['pageCount'] = min($optionsPager['pageTotal'], $optionsPager['options']['max_button']);
$optionsPager['firstPage'] = max(1, $optionsPager['currentPage'] - (int)($optionsPager['pageCount'] / 2));
if (($optionsPager['lastPage'] = $optionsPager['firstPage'] + $optionsPager['pageCount'] - 1) >= $optionsPager['pageTotal']) {
    $optionsPager['lastPage'] = $optionsPager['pageTotal'];
    $optionsPager['firstPage'] = max(1, $optionsPager['lastPage'] - $optionsPager['pageCount'] + 1);
}
$optionsPager['firstPageLabelDisable'] = ($optionsPager['currentPage'] == $optionsPager['firstPage']) ? true : false;
$optionsPager['lastPageLabelDisable'] = ($optionsPager['currentPage'] == $optionsPager['lastPage']) ? true : false;

unset($table,$page,$offset,$limit,$total_records);
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="index,follow" >
<title>Тестовое задание 2</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!-- <link href="assets/bootstrap4/css/bootstrap.css" rel="stylesheet"> -->
<link href="css/fonts.css" rel="stylesheet">
<link href="css/testtask1.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$active_item = 'testtask2';
$brand = 'Тестовое задание 2';
require("php/header.php");
unset($active_item,$brand);
?>

<div class="container content">
  <div class="row justify-content-center mt-2">
    <div class="col-3 text-center"></div>
    <div class="col-6 text-center">
      <div class="d-inline-block">
        <nav aria-label="Page navigation example">
          <ul class="pagination mb-2">
            <li class="page-item<?= $optionsPager['firstPageLabelDisable'] ? ' disabled' : '' ?>">
              <a class="page-link" href="<?= $page_name.'?page=1' ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
              </a>
            </li><?php
              for ($i = 0; $i < $optionsPager['pageCount']; $i++):
                $j = $i + 1;
                $n = $i + $optionsPager['firstPage'];
              ?>

              <li class="page-item<?= ($n == $optionsPager['currentPage']) ? ' active' : '' ?>">
                <a class="page-link" href="<?= $page_name.'?page='.$n?>"><?= $n ?><?= ($j == $optionsPager['currentPage']) ? '  <span class="sr-only">(current)</span>' : '' ?></a>
              </li><?php
              endfor;
              ?>

            <li class="page-item<?= $optionsPager['lastPageLabelDisable'] ? ' disabled' : '' ?>">
              <a class="page-link" href="<?= $page_name.'?page='.$optionsPager['pageTotal'] ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <div class="col-3 text-center"></div>
  </div>
  <div class="row justify-content-center pb-2">
    <div class="col">
      <table class="table table-striped"><!--  table-bordered -->
        <caption><small>Список пользователей</small></caption>
        <thead class="thead-light">
          <tr class="sort-ordinal">
            <th scope="col">#</th>
            <th scope="col" data-sort="true">Фамилия</th>
            <th scope="col" data-sort="true">Дата рождения</th>
            <th scope="col" data-sort="true">Номер телефона</th>
            <th scope="col">Электронная почта</th>
            <th scope="col">Марка авто</th>
          </tr>
        </thead>
        <tbody><?php
          for ($i = 0, $count = count($dataPager); $i < $count; $i++):
          $j = $optionsPager['offset'] + $i + 1;
          ?>

          <tr>
            <th scope="row"><?= $j ?></th>
            <td><?= $dataPager[$i]['surname'] ?></td>
            <td><?= $dataPager[$i]['birthday'] ?></td><!-- date('d.m.Y г.', strtotime((int)$dataPager[$i]['birthday'])) -->
            <td><?= $dataPager[$i]['phoneNumber'] ?></td>
            <td><?= $dataPager[$i]['email'] ?></td>
            <td><?= $dataPager[$i]['carBrand'] ?></td>
          </tr><?php
          endfor; ?>

        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$link_github = 'https://github.com/leogoodok/testtask-good_tech';
require("php/footer.php");
unset($link_github);
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

<!-- <script src="assets/jquery/jquery.js" type="text/javascript"></script> -->
<!-- <script src="assets/bootstrap4/js/bootstrap.js" type="text/javascript"></script> -->
<!-- <script src="assets/bootstrap4/js/bootstrap.bundle.js" type="text/javascript"></script> -->
<script src="js/testtask1.js" type="text/javascript"></script>
</body>
</html>
