<?php
/**
 * Footer страниц сайта
 */
?>
<footer class="bg-secondary">
  <div class="container text-center text-light">
    <nav class="navbar navbar-expand-lg navbar-dark">
      <span class="navbar-brand mb-0">&copy; 2013–<?= date("Y") ?> WotSkill.ru</span>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarFooter" aria-controls="navbarFooter" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarFooter">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="https://www.amocrm.ru/" target="_blank">AmoCRM.ru</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= isset($link_github) ? $link_github : '#' ?>" target="_blank">GitHub.com</a>
          </li>
          <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Написать разработчикам">
            <a class="nav-link text-font-glyphicons-halflings" href="mailto:wotskill@wotskill.ru" target="_blank">&#x2709;</a>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</footer>
