<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="index,follow" >
<title>Тестовое задание 1</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<!-- <link href="assets/bootstrap4/css/bootstrap.css" rel="stylesheet"> -->
<link href="css/fonts.css" rel="stylesheet">
<link href="css/testtask1.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$active_item = 'testtask1';
$brand = 'Тестовое задание 1';
require("php/header.php");
unset($active_item,$brand);
?>

<div class="container content">
  <div class="row justify-content-center py-2">
    <div class="col-12 col-md-12 col-lg-6 border rounded py-2">
      <form novalidate><?php /*<!--  class="was-validated" novalidate -->*/ ?>

        <div class="form-group row">
          <label for="inputSurname" class="col-5 col-form-label">Фамилия *</label>
          <div class="col-7">
            <input type="text" id="inputSurname" name="surname" class="form-control" placeholder="Ваша фамилия" aria-invalid="false" aria-required="true" aria-describedby="surnameHelp" data-target-error="#error-surname" data-target-result="#result_submit">
            <!-- <small id="surnameHelp" class="form-text text-muted">Поле обязательно для заполнения</small> -->
            <div class="invalid-feedback" id="error-surname">Пожалуйста, заполните поле</div>
            <!-- <div class="invalid-feedback" id="error-surname">Допускается ввод только букв и символ «-», первая буква большая</div> -->
          </div>
        </div>
        <div class="form-group row">
          <label for="inputBirthday" class="col-5 col-form-label">Дата рождения *</label>
          <div class="col-7">
            <input type="date" id="inputBirthday" name="birthday" class="form-control" placeholder="Дата вашего рождения" aria-invalid="false" aria-required="true" aria-describedby="birthdayHelp" data-target-error="#error-birthday" data-target-result="#result_submit"><!-- required -->
            <div class="invalid-feedback" id="error-birthday">Пожалуйста, заполните поле</div>
            <!-- <small id="birthdayHelp" class="form-text text-muted">Поле обязательно для заполнения</small> -->
          </div>
        </div>
        <div class="form-group row">
          <label for="inputPhoneNumber" class="col-5 col-form-label">Номер телефона *</label>
          <div class="col-7">
            <input type="text" id="inputPhoneNumber" class="form-control" placeholder="Номер вашего телефона" aria-invalid="false" aria-required="true" aria-describedby="phoneNumberHelp" data-target="#hiddenInputPhoneNumber" data-target-error="#error-phone-number" data-target-result="#result_submit"><!-- required -->
            <div class="invalid-feedback" id="error-phone-number">Пожалуйста, заполните поле</div>
            <input type="hidden" id="hiddenInputPhoneNumber" name="phoneNumber" value="" aria-invalid="false" aria-label="Номер телефона" aria-describedby="Номер телефона" data-target="#inputPhoneNumber">
            <!-- <small id="phoneNumberHelp" class="form-text text-muted">Поле обязательно для заполнения</small> -->
          </div>
        </div>
        <div class="form-group row">
          <label for="inputEmail" class="col-5 col-form-label">Адрес электронной почты</label>
          <div class="col-7">
            <input type="text" id="inputEmail" name="email" class="form-control" placeholder="___@___.___" aria-invalid="true" aria-describedby="emailHelp" data-target-error="#error-email" data-target-result="#result_submit">
            <div class="invalid-feedback" id="error-email">Введен некорректный адрес электронной почты</div>
            <!-- <small id="emailHelp" class="form-text text-muted">Поле не обязательно для заполнения</small> -->
          </div>
        </div>
        <div class="form-group row">
          <label for="selectCarBrand" class="col-5 col-form-label">Марка авто *</label>
          <div class="col-7">
            <select id="selectCarBrand" name="carBrand" class="custom-select" aria-invalid="false" aria-required="true" aria-describedby="carBrandHelp" data-target-error="#error-car-brand" data-target-result="#result_submit">
              <option value="0" selected>Выберите...</option>
              <option value="1">BMW</option>
              <option value="2">Audi</option>
              <option value="3">Volkswagen</option>
              <option value="4">Opel</option>
              <option value="5">Lada</option>
            </select>
            <!-- <input type="text" id="inputSurname" class="form-control" placeholder="Марка вашего авто" aria-describedby="surnameHelp"> -->
            <div class="invalid-feedback py-1" id="error-car-brand">Пожалуйста, выберите марку авто</div>
            <!-- <small id="carBrandHelp" class="form-text text-muted">Поле обязательно для заполнения</small> -->
          </div>
        </div>
        <div class="form-group">
          <button type="button" id="button_submit" class="btn btn-secondary" data-target-result="#result_submit">Отправить</button>
        </div>
        <small class="form-text text-muted">* Поля обязательные для заполнения</small>
      </form>
    </div>
  </div>
  <div class="row justify-content-center pb-2 d-none">
    <div id="result_submit" class="col-12 col-md-12 col-lg-6 border border-info rounded py-2 bg-light text-danger"></div>
  </div>
</div>

<?php
/*
// Задание 1.
// 1. При помощи языка PHP создайте двумерный массив размером 6х6, заполните его числами из последовательности Фибоначчи таким образом, чтобы в углу [0][0] была единица, в ячейке [1][0] была единица, в ячейке [2][0] была цифра 2. Найдите сумму чисел назодящихся на диагонали [5][0]-[0][5]

$arr = [];
$sum = 0;
for ($j=0; $j < 6; $j++) {
  for ($i=0; $i < 6; $i++) {
    if ($i == 0 && $j < 2) {
      $arr[$j][$i] = 1;
    } else if ($i == 0) {
      $arr[$j][$i] = isset($arr[$j-2][$i]) ? $arr[$j-2][$i] : 0;
      $arr[$j][$i] += isset($arr[$j-1][$i]) ? $arr[$j-1][$i] : 0;
    } else {
      $arr[$j][$i] = isset($arr[$j][$i-2]) ? $arr[$j][$i-2] : 0;
      $arr[$j][$i] += isset($arr[$j][$i-1]) ? $arr[$j][$i-1] : 0;
    }
    if ($j + $i == 5) $sum += $arr[$j][$i];
  }
}

echo '<table border="1">'."\n";
for ($j=0; $j < 6; $j++) {
  echo '<tr>'."\n";
  for ($i=0; $i < 6; $i++) {
    echo "<td>{$arr[$j][$i]}</td>\n";
  }
  echo '</tr>'."\n";
}
echo "<tr>\n<td colspan=\"3\">Всего:</td>\n<td colspan=\"3\">{$sum}</td>\n</th>\n";
echo '</table>'."\n";
*/
?>
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
