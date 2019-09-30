jQuery( function($) {
$(document).ready(function () {
  $("[data-form='call_me']").on('click', () => {
      $("#call_me.form").slideDown();
      $(".over").fadeIn();
  });
  $("[data-form='calc_1']").on('click', () => {
      $("#calc_1.form").slideDown();
      $(".over").fadeIn();
  });
  $("[data-form='calc_2']").on('click', () => {
      $("#calc_2.form").slideDown();
      $(".over").fadeIn();
  });
  $("[data-form='calc_3']").on('click', () => {
      $("#calc_3.form").slideDown();
      $(".over").fadeIn();
  });
  $(".close").on('click', () => {
      $(".form").slideUp();
      $(".over").fadeOut();
  });
  $(".over").on('click', () => {
      $(".form").slideUp();
      $(".over").fadeOut();
  });

  /**
   *  Установка куки, если их нет. Вставка скрытых input "utm_..." в формы
   */
  $(function() {
    var optionCookie = {'expires':0.125,'path':'/','domain':window.location.hostname},
        value_cookie = {utm_source:'', utm_medium:'', utm_campaign:'', utm_content:'', utm_term:'', referrer:''},
        $_GET = {};
    //Получение GET-параметров
    document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
      function Decode(s) {
        return decodeURIComponent(s.split("+").join(" "));
      }
      $_GET[Decode(arguments[1])] = Decode(arguments[2]);
    });
    //Получение адреса (домена) источника перехода
    if (document.referrer) {
      value_cookie.referrer = new URL(document.referrer).hostname;
    }
    for (var variable in value_cookie) {
      if ($_GET.hasOwnProperty(variable)) {
        value_cookie[variable] = $_GET[variable];
      }
    }
    //Чтение/Установка куки
    for (var variable in value_cookie) {
      if (!($.cookie(variable))) {
        if (value_cookie[variable]) {
          $.cookie(variable, value_cookie[variable], optionCookie);
        }
      }
    }
    //Вставка скрытых input "utm_..." в формы
    $('form[data-utm="true"]').each(function(i,elem) {
      for (var variable in value_cookie) {
        var val;
        if ($.cookie(variable)) {
          val = $.cookie(variable);
        } else if (value_cookie[variable]) {
          val = value_cookie[variable];
        }
        if (val) {
          var new_input  = document.createElement('input');
          $(new_input).attr({
            'type':'hidden',
            'name': variable,
            'value': val
          }).prependTo(elem);
        }
      }
    });
  });


  /**
   *  Назначение обработчика события ввода символа в input "Введите Ваше имя*"
   */
  jQuery('#form0InputSurname, #form1InputSurname, #form2InputSurname, #form3InputSurname').on('input', function() {
    var value = $(this).val();
    //Удаление НЕ разрешенных символов
    if(/[^A-Za-zА-Яа-я\- ]+/i.test(value)){
      value = value.replace(/[^A-Za-zА-Яа-я\- ]+/ig,'');
      $(this).val(value);
    }
    //Изменение регистра - !Выполняется классом CSS "Bootstrap4"
    // if (value.length) {
    //   value = value.toLowerCase();
    //   value = value.charAt(0).toUpperCase() + value.substr(1);
    //   $(this).val(value);
    // }
  });

  /**
   *  Назначение обработчика события потери фокуса input-а "Введите Ваше имя*"
   */
  jQuery('#form0InputSurname, #form1InputSurname, #form2InputSurname, #form3InputSurname').blur( function() {
    var target_error = $(this).data('targetError'),
        value = $(this).val();
    //Валидация
    if (value.length) {
      $(this).removeAttr('aria-invalid');
      $(this).removeClass('is-invalid').addClass('is-valid');
    } else {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Пожалуйста, заполните поле');
    }
  });


  /**
   *  Назначение обработчика события ввода символа в input "Введите Ваш телефон"
   */
  jQuery('#form0InputPhone, #form1InputPhone, #form2InputPhone, #form3InputPhone').on('input', function() {
    var value = $(this).val();
    //Удаление НЕ разрешенных символов
    if(!(/^[\+]?[0-9]*$/.test(value))){
      value = value.slice(0,1).replace(/[^\+0-9]?/,'') + value.slice(1).replace(/[^0-9]+/g,'');
      $(this).val(value);
    }
  });


  /**
   *  Назначение обработчика события потери фокуса input-а "Введите Ваш телефон"
   */
  jQuery('#form0InputPhone, #form1InputPhone, #form2InputPhone, #form3InputPhone').blur( function() {
    var target_error = $(this).data('targetError'),
        value = $(this).val();
    if (!(value.length)) {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Пожалуйста, заполните поле');
      return;
    }
    if (value.slice(0, 2) == '+7') {
      if (value.length == 12) {
        $(this).removeAttr('aria-invalid');
        $(this).removeClass('is-invalid').addClass('is-valid');
      } else {
        $(this).attr('aria-invalid', 'true');
        $(this).removeClass('is-valid').addClass('is-invalid');
        $(target_error).text('Номер должен содержать 11 цифр');
      }
    } else {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Номер должен начинатся с "+7"');
    }
  });


  /**
   *  Назначение обработчика события ввода символа в input "Введите Ваш Email"
   */
  jQuery('#form0InputEmail, #form1InputEmail, #form2InputEmail, #form3InputEmail').on('input', function() {
    var value = $(this).val();
    //Удаление НЕ разрешенных символов
    if(/[^\w\-\.@]+/i.test(value)){
      value = value.replace(/[^\w\-\.@]+/ig,'');
      $(this).val(value);
    }
  });

  /**
   *  Назначение обработчика события потери фокуса input-а "Введите Ваш Email"
   */
  jQuery('#form0InputEmail, #form1InputEmail, #form2InputEmail, #form3InputEmail').blur( function() {
    var target_error = $(this).data('targetError'),
        re = /^[a-z0-9_\-\.]+@[a-z0-9\-]+\.([a-z]{1,6}\.)?[a-z]{2,6}$/i,
        value = $(this).val();
    //Валидация
    if (value.length) {
      if (value.search(re) == 0) {
        $(this).removeAttr('aria-invalid');
        $(this).removeClass('is-invalid').addClass('is-valid');
      } else {
        $(this).attr('aria-invalid', 'true');
        $(this).removeClass('is-valid').addClass('is-invalid');
        $(target_error).text('Некорректный адрес электронной почты');
      }
    } else {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Пожалуйста, заполните поле');
    }
  });


  /**
   *  Назначение обработчика событий клика - отправка формы
   */
  $('form[data-utm="true"]').each(function(i,elem_form) {
    $(elem_form).find('input[type="button"]').each(function(j,elem_button) {
      $(elem_button).click(function() {
        var form = $(this).parents('form'),
            form_params = {
              created_at: Math.round(new Date().getTime() / 1000),//Добавление времени создания в UNIX TIMESTAMP
              form: {},
              utm: {},
              contact: {},
            },
            valid_all = true;
        $(this).siblings('input[type="text"]').each(function(y,elem_input) {
          form_params.contact[$(elem_input).attr('name')] = $(elem_input).val();
          if ($(elem_input).attr('aria-invalid')) {
            valid_all = false;
            $(elem_input).removeClass('is-valid').addClass('is-invalid');
          }
        });
        if (!valid_all) return;
        $(form).find('input[type="hidden"]').each(function(y,elem_input) {
          if (($(elem_input).attr('name') == 'referrer') || (($(elem_input).attr('name')).slice(0,4) == 'utm_')) {
            form_params.utm[$(elem_input).attr('name')] = $(elem_input).attr('value');
          } else {
            form_params.form[$(elem_input).attr('name')] = $(elem_input).attr('value');
          }
        });
        form_params.form.page = window.location.href;
        if ($(this).attr('id') != 'order2') {
          form_params.fields = {};
          $(form).children('input[type="text"]').each(function(y,elem_input) {
            form_params.fields[$(elem_input).attr('name')] = $(elem_input).val();
          });
          $(form).children('select').each(function(y,elem_select) {
            form_params.fields[$(elem_select).attr('name')] = $(elem_select).val();
          });
        }
        //Отправка POST запросом
        var str_url = window.location.protocol + '//' + window.location.hostname + ((window.location.pathname).replace(/site\/index\.php/i, "miniapi\.php"));
        $.post(
          // 'https://webhook.site/',
          str_url,
          $.toJSON({//Отправка данных в виде JSON
            token: $('body').data('token'),//Добавление токена
            params: form_params
          })
          // {//Отправка данных в виде Объекта
          //   token: $('body').data('token'),
          //   params: form_params
          // }
        );
        //Закрыть окно
        $(this).parents('div.form:first').slideUp();
        $(".over").fadeOut();
      });
    });
  });
});
});
