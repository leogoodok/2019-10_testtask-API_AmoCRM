<?php
session_start();

// echo 'var_dump($_SESSION) = '."<br>"; var_dump($_SESSION);
// echo 'var_dump(session_id()) = '."<br>"; var_dump(session_id());

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

$token = $_SESSION['token'];


session_write_close();

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="keywords" content="amocrm,api amocrm" />
    <meta name="description" content="Пример работы с аккаунтом AmoCRM через API "/>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <title>Демонстрационный сайт по внедрению CRM-системы</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
    <!-- <link href="../assets/bootstrap4/css/bootstrap.css" rel="stylesheet"> -->
    <link type="text/css" href="./css/reset.css" rel="stylesheet">
    <link type="text/css" href="./css/common.css" rel="stylesheet">
    <link type="text/css" href="./css/index.css" rel="stylesheet">
    <script type="text/javascript" src="./js/jquery.min.js"></script>
    <script type="text/javascript" src="./js/script.js"></script>
    <script type="text/javascript" src="./js/jquery.cookie.js"></script>
    <script type="text/javascript" src="./js/jquery.json.min.js"></script>
</head>
<body<?= !empty($token) ? " data-token=\"$token\"" : '' ?>>
<div id="nav" class="landingMenu">
    <div class="wrap">
        <ul>
            <!-- <li><a href="#header">Главная</a></li> -->
            <li><a href="../index.php">Главная</a></li>
            <li><a href="#calc">Рассчитать установку</a></li>
        </ul>
        <div class="clr"></div>
    </div>
</div>
<div id="header">
    <div class="wrap">
        <div class="top_header">
            <div class="logo">
                <img src="images/logo.png" alt="" title="">
            </div>
            <div class="mail_h">
                info@crmokna.ru
            </div>
            <div class="phone_h">
                <a class="call_me" data-form="call_me">Заказать обратный звонок</a>
            </div>
            <div class="clr"></div>
        </div>
    </div>
</div>
<div id="benefits">
    <div class="wrap">
        <ul>
            <li>
                <img src="images/ben1.png" alt="" title="" class="wow pulse" data-wow-duration="1500ms"><br>
                <div class="left">
                    12
                </div>
                <div class="right">
                    лет на профессиональном рынке производства пластиковых окон
                </div>
                <div class="clr"></div>
            </li>
            <li>
                <img src="images/ben2.png" alt="" title="" class="wow pulse" data-wow-duration="1200ms"><br>
                <div class="left">
                    9387
                </div>
                <div class="right">
                    оконных конструкций смонтировано
                </div>
                <div class="clr"></div>
            </li>
            <li>
                <img src="images/ben3.png" alt="" title="" class="wow pulse" data-wow-duration="1500ms"><br>
                <div class="left">
                    33
                </div>
                <div class="right">
                    высоко-квалифицированных специалиста в штате
                </div>
                <div class="clr"></div>
            </li>
        </ul>
        <div class="clr"></div>
    </div>
</div>
<div id="calc">
    <div class="wrap">
        <h2>Рассчитать установку пластиковых окон</h2>
        <div class="ben_c">
            <ul>
                <li>
                    <div class="left">
                        <img src="images/ben_c1.png" alt="" title="" class="wow pulse" data-wow-duration="1500ms">
                    </div>
                    <div class="right">
                        <p>по ценам <strong>ниже</strong> среднерыночных <strong>на 10-12%</strong></p>
                    </div>
                    <div class="clr"></div>
                </li>
                <li>
                    <div class="left">
                        <img src="images/ben_c2.png" alt="" title="" class="wow pulse" data-wow-duration="1600ms">
                    </div>
                    <div class="right">
                        <p>с заводской гарантией <strong><br>3 года</strong></p>
                    </div>
                    <div class="clr"></div>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
        <div class="calc_f">
            <ul>
                <li>
                    <h3><strong>Двухстворчатое<br> окно</strong></h3>
                    <h4><strong>от 4 200</strong><img src="images/rub_w.png" alt="" title=""></h4>
                    <img src="images/calc_f1.png" alt="" title="">
                    <form method="POST" data-utm="true">
                        <!-- Скрытые поля, которые содержат данные из UTM метки -->
                        <input type="hidden" name="form_id" value="calc_1"/>
                        <input type="hidden" name="catchpoint" value="Двухстворчатое окно">
                        <label>Высота, мм</label>
                        <input type="text" name="height" placeholder="Введите значение"><br>
                        <label>Ширина, мм</label>
                        <input type="text" name="width" placeholder="Введите значение"><br>
                        <label>Профиль</label>
                        <select name="profile">
                            <option disabled selected>Выберите из списка</option>
                            <option>REHAU</option>
                            <option>VEKA</option>
                            <option>KBE</option>
                            <option>KRAUSS</option>
                            <option>SALAMANDER</option>
                        </select><br>
                        <label>Кол-во камер</label>
                        <select name="number">
                            <option disabled selected>Выберите из списка</option>
                            <option>2 камеры</option>
                            <option>3 камеры</option>
                        </select><br>
                        <label>Механизм</label>
                        <select name="mechanism">
                            <option disabled selected>Выберите из списка</option>
                            <option>Поворотный</option>
                            <option>Поворотно-откидной</option>
                            <option>Раздвижной</option>
                        </select><br>
                        <a class="submit_calc" data-form="calc_1"></a>
                        <div class="form form-active" id="calc_1">
                            <a class="close"></a>
                            <p class="medium">Введите Ваше имя*</p>
                            <input type="text" id="form1InputSurname" name="name" class="form-control text-capitalize" placeholder="Иван Иванов*" aria-invalid="true" data-target-error="#form1ErrorSurname"/>
                            <div id="form1ErrorSurname" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <p class="medium">Введите Ваш телефон*</p>
                            <input type="text" id="form1InputPhone" name="phone" class="form-control" placeholder="+7 (123) 456-78-90*" aria-invalid="true" data-target-error="#form1ErrorPhone"/>
                            <div id="form1ErrorPhone" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <p class="medium">Введите Ваш Email*</p>
                            <input type="text" id="form1InputEmail" name="email" class="form-control" placeholder="example@email.com*" aria-invalid="true" data-target-error="#form1ErrorEmail"/>
                            <div id="form1ErrorEmail" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <input type="button" value="" class="button"/>
                            <span>Мы заботимся о конфиденциальности<br>Ваших данных</span>
                        </div>
                    </form>
                </li>
                <li>
                    <h3><strong>трехстворчатое<br>окно</strong></h3>
                    <h4><strong>от 5 900</strong><img src="images/rub_w.png" alt="" title=""></h4>
                    <img src="images/calc_f2.png" alt="" title="">
                    <form method="POST" action="" class="feedform3 ie" data-utm="true">
                        <!-- Скрытые поля, которые содержат данные из UTM метки -->
                        <input type="hidden" name="form_id" value="3"/>
                        <input type="hidden" name="catchpoint" value="Трехстворчатое окно">
                        <label>Высота, мм</label>
                        <input type="text" name="height" placeholder="Введите значение"><br>
                        <label>Ширина, мм</label>
                        <input type="text" name="width" placeholder="Введите значение"><br>
                        <label>Профиль</label>
                        <select name="profile">
                            <option disabled selected>Выберите из списка</option>
                            <option>REHAU</option>
                            <option>VEKA</option>
                            <option>KBE</option>
                            <option>KRAUSS</option>
                            <option>SALAMANDER</option>
                        </select><br>
                        <label>Кол-во камер</label>
                        <select name="number">
                            <option disabled selected>Выберите из списка</option>
                            <option>2 камеры</option>
                            <option>3 камеры</option>
                        </select><br>
                        <label>Механизм</label>
                        <select name="mechanism">
                            <option disabled selected>Выберите из списка</option>
                            <option>Поворотный</option>
                            <option>Поворотно-откидной</option>
                            <option>Раздвижной</option>
                        </select><br>
                        <a class="submit_calc" data-form="calc_2"></a>
                        <div class="form form-active" id="calc_2">
                            <a class="close"></a>
                            <p class="medium">Введите Ваше имя*</p>
                            <input type="text" id="form2InputSurname" name="name" class="form-control text-capitalize" placeholder="Иван Иванов*" aria-invalid="true" data-target-error="#form2ErrorSurname"/>
                            <div id="form2ErrorSurname" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <p class="medium">Введите Ваш телефон*</p>
                            <input type="text" id="form2InputPhone" name="phone" class="form-control" placeholder="+7 (123) 456-78-90*" aria-invalid="true" data-target-error="#form2ErrorPhone"/>
                            <div id="form2ErrorPhone" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <p class="medium">Введите Ваш Email*</p>
                            <input type="text" id="form2InputEmail" name="email" class="form-control" placeholder="example@email.com*" aria-invalid="true" data-target-error="#form2ErrorEmail"/>
                            <div id="form2ErrorEmail" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <input type="button" value="" class="button"/>
                            <span>Мы заботимся о конфиденциальности<br>Ваших данных</span>
                        </div>
                    </form>
                </li>
                <li>
                    <h3><strong>Балконный<br>блок</strong></h3>
                    <h4><strong>от 7 300</strong><img src="images/rub_w.png" alt="" title=""></h4>
                    <img src="images/calc_f3.png" alt="" title="">
                    <form method="POST" data-utm="true">
                        <!-- Скрытые поля, которые содержат данные из UTM метки -->
                        <input type="hidden" name="form_id" value="4"/>
                        <input type="hidden" name="catchpoint" value="Балконный блок">
                        <label>Высота, мм</label>
                        <input type="text" name="height" placeholder="Введите значение"><br>
                        <label>Ширина, мм</label>
                        <input type="text" name="width" placeholder="Введите значение"><br>
                        <label>Профиль</label>
                        <select name="profile">
                            <option disabled selected>Выберите из списка</option>
                            <option>REHAU</option>
                            <option>VEKA</option>
                            <option>KBE</option>
                            <option>KRAUSS</option>
                            <option>SALAMANDER</option>
                        </select><br>
                        <label>Кол-во камер</label>
                        <select name="number">
                            <option disabled selected>Выберите из списка</option>
                            <option>2 камеры</option>
                            <option>3 камеры</option>
                        </select><br>
                        <label>Механизм</label>
                        <select name="mechanism">
                            <option disabled selected>Выберите из списка</option>
                            <option>Поворотный</option>
                            <option>Поворотно-откидной</option>
                            <option>Раздвижной</option>
                        </select><br>
                        <a class="submit_calc" data-form="calc_3"></a>
                        <div class="form form-active" id="calc_3">
                            <a class="close"></a>
                            <p class="medium">Введите Ваше имя*</p>
                            <input type="text" id="form3InputSurname" name="name" class="form-control text-capitalize" placeholder="Иван Иванов*" aria-invalid="true" data-target-error="#form3ErrorSurname"/>
                            <div id="form3ErrorSurname" class="invalid-feedback">Пожалуйста, заполните поле</div>
                            <p class="medium">Введите Ваш телефон*</p>
                            <input type="text" id="form3InputPhone" name="phone" class="form-control" placeholder="+7 (123) 456-78-90*" aria-invalid="true" data-target-error="#form3ErrorPhone"/>
                            <div class="invalid-feedback" id="form3ErrorPhone">Пожалуйста, заполните поле</div>
                            <p class="medium">Введите Ваш Email*</p>
                            <input type="text" id="form3InputEmail" name="email" class="form-control" placeholder="example@email.com*" aria-invalid="true" data-target-error="#form3ErrorEmail"/>
                            <div class="invalid-feedback" id="form3ErrorEmail">Пожалуйста, заполните поле</div>
                            <input type="button" value="" class="button"/>
                            <span>Мы заботимся о конфиденциальности<br>Ваших данных</span>
                        </div>
                    </form>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
    </div>
</div>
<div id="footer">
    <div class="wrap">
        <div class="copy">
            <p>info@crmokna.ru<br><span class="medium">© 2003-2015</span><br>www.crmokna.ru</p>
        </div>
        <div class="phone_f">
            <a class="call_me" data-form="call_me">Заказать обратный звонок</a>
        </div>
        <div class="clr"></div>
    </div>
</div>
<div class="form form-active" id="call_me">
    <div class="form_content">
        <a class="close"></a>
        <h3 class="black_f">заказать<br>обратный звонок</h3>
        <form method="POST" data-utm="true">
            <!-- Скрытые поля, которые содержат данные из UTM метки -->
            <input type="hidden" name="form_id" value="call_me"/>
            <!-- <input type="hidden" name="Заказ обратного звонка"> -->
            <input type="hidden" name="callback_order" value="Заказ обратного звонка">
            <p class="medium">Введите Ваше имя*</p>
            <input type="text" id="form0InputSurname" name="name" class="form-control text-capitalize" placeholder="Иван Иванов*" aria-invalid="true" data-target-error="#form0ErrorSurname"/>
            <div id="form0ErrorSurname" class="invalid-feedback">Пожалуйста, заполните поле</div>
            <p class="medium">Введите Ваш телефон*</p>
            <input type="text" id="form0InputPhone" name="phone" class="form-control" placeholder="+7 (123) 456-78-90*" aria-invalid="true" data-target-error="#form0ErrorPhone"/>

            <!-- <input type="tel" id="form0InputPhone" name="phone" class="form-control" pattern="\+7\([0-9]{3}\)-[0-9]{3}-[0-9]{2}-[0-9]{2}" placeholder="+7 (123) 456-78-90*" aria-invalid="true" data-target-error="#form0ErrorPhone" required> -->

            <div id="form0ErrorPhone" class="invalid-feedback">Пожалуйста, заполните поле</div>
            <p class="medium">Введите Ваш Email*</p>
            <input type="text" id="form0InputEmail" name="email" class="form-control" placeholder="example@email.com*" aria-invalid="true" data-target-error="#form0ErrorEmail"/>
            <div id="form0ErrorEmail" class="invalid-feedback">Пожалуйста, заполните поле</div>
            <span>Мы заботимся<br> о конфиденциальности Ваших данных</span>
            <input type="button" id="order2" value="" class="button"/>
        </form>
    </div>
</div>
<div class="over"></div>
</body>
</html>
