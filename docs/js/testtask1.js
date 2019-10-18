/**
 * Обёртка jQuery для избежания конфликтов  при использовании
 * псевдонима $ с другими JS библиотеками
 */
jQuery( function($) {

/**
 *  Назначение обработчика события ввода символа в input "Фамилия"
 */
jQuery('#inputSurname').on('input', function() {
  var value = $(this).val();
  //Удаление НЕ разрешенных символов
  if(/[^A-Za-zА-Яа-я\-]+/i.test(value)){
    value = value.replace(/[^A-Za-zА-Яа-я\-]+/ig,'');
    $(this).val(value);
  }
  //Изменение регистра
  if (value.length) {
    value = value.toLowerCase();
    value = value.charAt(0).toUpperCase() + value.substr(1);
    $(this).val(value);
  }
});


/**
 *  Назначение обработчика события потери фокуса input-а "Фамилия"
 */
jQuery('#inputSurname').blur( function() {
  var target_error = $(this).data('targetError'),
      value = $(this).val();
  $($(this).data('targetResult')).parent().addClass('d-none');
  //Валидация
  if (value.length) {
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-invalid').addClass('is-valid');
  } else {
    $(this).attr('aria-invalid', 'false');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, заполните поле');
  }
});


/**
 *  Назначение обработчика события потери фокуса input-а "Дата рождения"
 */
jQuery('#inputBirthday').blur( function() {
  var target_error = $(this).data('targetError'),
      value = $(this).val();
  $($(this).data('targetResult')).parent().addClass('d-none');
  //Валидация
  if (value.length) {
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-invalid').addClass('is-valid');
  } else {
    $(this).attr('aria-invalid', 'false');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, заполните поле');
  }
});


/**
 *  Назначение обработчика события ввода символа в input "Номер телефона"
 */
jQuery('#inputPhoneNumber').on('input', function() {
  var value = $(this).val();
  //Удаление НЕ разрешенных символов
  if(!(/^[\+]?[0-9]*$/.test(value))){
    value = value.slice(0,1).replace(/[^\+0-9]?/,'') + value.slice(1).replace(/[^0-9]+/g,'');
    $(this).val(value);
  }
});


/**
 *  Назначение обработчика события потери фокуса input-а "Номер телефона"
 */
jQuery('#inputPhoneNumber').blur( function() {
  var target = $(this).data('target'),
      target_error = $(this).data('targetError'),
      value = $(this).val(),
      phone_number;
  $($(this).data('targetResult')).parent().addClass('d-none');
  if (!(value.length)) {
    $(this).attr('aria-invalid', 'false');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, заполните поле');
    $(target).attr('value', '');
    $(target).attr('aria-invalid', 'false');
    return;
  }
  if (value[0] == '+') {
    if (value.slice(0, 2) == '+7') {
      phone_number = '8' + value.slice(2);
    } else {
      $(this).attr('aria-invalid', 'false');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Номер должен начинатся с "+7" или с цифры');
      $(target).attr('value', '');
      $(target).attr('aria-invalid', 'false');
      return;
    }
  } else {
    phone_number = value;
  }
  if (phone_number.length == 7 || (phone_number.length == 10 && value.slice(0, 2) != '+7')
                               || (phone_number.length == 11 && phone_number[0] == 8)) {
    if (phone_number.length == 7) {
      phone_number = '8843' + phone_number;
    } else if (phone_number.length == 10) {
      phone_number = '8' + phone_number;
    }
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-invalid').addClass('is-valid');
    $(target).attr('value', phone_number);
    $(target).attr('aria-invalid', 'true');
  } else {
    $(this).attr('aria-invalid', 'false');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Номер должен содержать 7 или 11 цифр');
    $(target).attr('value', '');
    $(target).attr('aria-invalid', 'false');
  }
});


/**
 *  Назначение обработчика события ввода символа в input "Адрес электронной почты"
 */
jQuery('#inputEmail').on('input', function() {
  var value = $(this).val();
  //Удаление НЕ разрешенных символов
  if(/[^\w\-\.@]+/i.test(value)){
    value = value.replace(/[^\w\-\.@]+/ig,'');
    $(this).val(value);
  }
});


/**
 *  Назначение обработчика события потери фокуса input-а "Адрес электронной почты"
 */
jQuery('#inputEmail').blur( function() {
  var target_error = $(this).data('targetError'),
      re = /^[a-z0-9_\-\.]+@[a-z0-9\-]+\.([a-z]{1,6}\.)?[a-z]{2,6}$/i,
      value = $(this).val();
  $($(this).data('targetResult')).parent().addClass('d-none');
  //Валидация
  if (value.length) {
    if (value.search(re) == 0) {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-invalid').addClass('is-valid');
    } else {
      $(this).attr('aria-invalid', 'false');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Введен некорректный адрес электронной почты');
    }
  } else {
  //! Т.к. по условию это не обязательное поле, то закоментируем вывод ошибки
    // $(this).attr('aria-invalid', 'false');
    // $(this).removeClass('is-valid').addClass('is-invalid');
    // $(target_error).text('Пожалуйста, заполните поле');
  //! Т.к. по условию это не обязательное поле, то "пустое поле" это нормально
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-invalid').addClass('is-valid');
  }
});


/**
 *  Назначение обработчика события изменения значения select-а "Марка авто"
 */
jQuery('#selectCarBrand').change( function() {
  var target_error = $(this).data('targetError'),
      value = $(this).val();
  $($(this).data('targetResult')).parent().addClass('d-none');
  //Валидация
  if (value != 0) {
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-invalid').addClass('is-valid');
  } else {
    $(this).attr('aria-invalid', 'false');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, выберите марку авто');
  }
});


/**
 *  Назначение обработчика события клика кнопки "Отправить"
 */
jQuery('#button_submit').click( function() {
  var form = $(this).parents('form'),
      form_params = {},
      valid_all = true;
  $($(this).data('targetResult')).parent().addClass('d-none');
  if (!(form instanceof jQuery)) return;
  form.find('input[name]').each(function(i,elem) {
    if ($(this).attr('aria-invalid') != 'true') {
      valid_all = false;
      if ($(this).attr('type') == 'hidden') {
        $($(this).data('target')).removeClass('is-valid').addClass('is-invalid');
      } else {
        $(this).removeClass('is-valid').addClass('is-invalid');
      }
    } else {
      form_params[$(this).attr('name')] = $(this).val();
    }
  });
  form.find('select[name]').each(function(i,elem) {
    if ($(this).attr('aria-invalid') != 'true' || !($(this).val())) {
      valid_all = false;
      $(this).removeClass('is-valid').addClass('is-invalid');
    } else {
      form_params[$(this).attr('name')] = $(this).find('option:selected').text();
    }
  });
  if (!valid_all) return;
  form_params.key = '123456789';
  $.post(
    'php/api.php',
    form_params,
    function(response) {
      var result = $.parseJSON(response),
          target_result = '#result_submit',
          target_phone_number = '#inputPhoneNumber',
          message = (result.status == 'ok') ? result.data.message : result.error.message;
      if (result.status == 'error') {
        $(target_result).removeClass('text-success').addClass('text-danger').html(message);
        if (result.status == 'error' && result.error.code == 999) {
          $(target_phone_number).removeClass('is-valid').addClass('is-invalid');
        }
      } else {
        $(target_result).removeClass('text-danger').addClass('text-success').html(message);
      }
      $(target_result).parent().removeClass('d-none');
    },
    'text'
  );
});


/**
 *  Назначение обработчиков событий сортировки таблицы
 */
$(document).ready(function () {
  $('th[data-sort="true"]').each(function (col) {
    $(this).hover(
      function () {
        $(this).addClass('focus');
      },
      function () {
        $(this).removeClass('focus');
      }
    );
    $(this).click(function () {
      if ($(this).is('.asc')) {
        $(this).removeClass('asc');
        $(this).addClass('desc selected');
        sortOrder = -1;
      } else {
        $(this).addClass('asc selected');
        $(this).removeClass('desc');
        sortOrder = 1;
      }
      $(this).siblings().removeClass('asc selected');
      $(this).siblings().removeClass('desc selected');
      var arrData = $('table').find('tbody >tr:has(td)').get();
      arrData.sort(function (a, b) {
        var val1 = $(a).children('td').eq(col).text().toUpperCase();
        var val2 = $(b).children('td').eq(col).text().toUpperCase();
        if ($.isNumeric(val1) && $.isNumeric(val2))
          return sortOrder == 1 ? val1 - val2 : val2 - val1;
        else
          return (val1 < val2) ? -sortOrder : (val1 > val2) ? sortOrder : 0;
      });
      $.each(arrData, function (index, row) {
        $('tbody').append(row);
      });
    });
  });
});
});
