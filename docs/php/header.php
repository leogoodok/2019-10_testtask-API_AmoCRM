<?php
/**
 * Header страниц сайта
 * @param $active_item активный пункт меню
 */
?>
<header class="bg-secondary">
  <div class="container text-center text-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <a class="navbar-brand" href="index.php"><?= isset($brand) ? $brand : 'Работа с аккаунтом AmoCRM через API' ?></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarHeader">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
          <li class="nav-item<?= $active_item == 'index' ? ' active' : '' ?>">
            <a class="nav-link" href="index.php">Главная<?= $active_item == 'index' ? '<span class="sr-only">(current)</span>' : '' ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="site/index.php?utm_source=yandex&utm_medium=cpc&utm_campaign=google-poisk&utm_content=banner-full&utm_term=iphone">Добавление заявки</a>
          </li>
          <li class="nav-item dropdown<?= ($active_item == 'testtask1' && $active_item == 'testtask2') ? ' active' : '' ?>">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Тестовые задания
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="testtask1.php">Задание 1<?= $active_item == 'testtask1' ? '<span class="sr-only">(current)</span>' : '' ?></a>
              <a class="dropdown-item" href="testtask2.php">Задание 2<?= $active_item == 'testtask2' ? '<span class="sr-only">(current)</span>' : '' ?></a>
            </div>
          </li>
          <li class="nav-item<?= $active_item == 'login_amocrm' ? ' active' : '' ?>">
            <a class="nav-link" href="login_amocrm.php"><?= (isset($status_authorization,$name_account) && $status_authorization) ? '('.$name_account.')' : 'Войти в AmoCRM' ?><?= $active_item == 'login_amocrm' ? '<span class="sr-only">(current)</span>' : '' ?></a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</header>
