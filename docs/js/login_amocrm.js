/**
 * Обёртка jQuery для избежания конфликтов  при использовании
 * псевдонима $ с другими JS библиотеками
 */
jQuery( function($) {
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
      $(this).removeAttr('aria-invalid');
      $(this).removeClass('is-invalid').addClass('is-valid');
    } else {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Введен некорректный адрес электронной почты');
    }
  } else {
  //! Т.к. по условию это обязательное поле, то закоментируем вывод ошибки
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, заполните поле');
  //! Т.к. по условию это обязательное поле, то "пустое поле" это НЕ нормально
    // $(this).removeAttr('aria-invalid');
    // $(this).removeClass('is-invalid').addClass('is-valid');
  }
});


/**
 *  Назначение обработчика события ввода символа в input "Ключ пользователя"
 */
jQuery('#inputUserKey').on('input', function() {
  var value = $(this).val();
  //Удаление НЕ разрешенных символов
  if(/[^a-f0-9]+/i.test(value)){
    value = value.replace(/[^a-f0-9]+/ig,'');
    $(this).val(value);
  }
  //Изменение регистра
  if (value.length) {
    value = value.toLowerCase();
    $(this).val(value);
  }
});


/**
 *  Назначение обработчика события потери фокуса input-а "Ключ пользователя"
 */
jQuery('#inputUserKey').blur( function() {
  var target_error = $(this).data('targetError'),
      value = $(this).val();
  $($(this).data('targetResult')).parent().addClass('d-none');
  //Валидация
  if (value.length) {
    if (value.length == 40) {
      $(this).removeAttr('aria-invalid');
      $(this).removeClass('is-invalid').addClass('is-valid');
    } else {
      $(this).attr('aria-invalid', 'true');
      $(this).removeClass('is-valid').addClass('is-invalid');
      $(target_error).text('Поле должно содержать 40 символов');
    }
  } else {
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, заполните поле');
  }
});


/**
 *  Назначение обработчика события ввода символа в input "Поддомен"
 */
jQuery('#inputSubdomain').on('input', function() {
  var value = $(this).val();
  //Удаление НЕ разрешенных символов
  if(/[^a-z0-9_]+/i.test(value)){
    value = value.replace(/[^a-z0-9_]+/ig,'');
    $(this).val(value);
  }
  //Изменение регистра
  if (value.length) {
    value = value.toLowerCase();
    $(this).val(value);
  }
});


/**
 *  Назначение обработчика события потери фокуса input-а "Поддомен"
 */
jQuery('#inputSubdomain').blur( function() {
  var target_error = $(this).data('targetError'),
      value = $(this).val();
  $($(this).data('targetResult')).parent().addClass('d-none');
  //Валидация
  if (value.length) {
    // if (value.length == 40) {
      $(this).removeAttr('aria-invalid');
      $(this).removeClass('is-invalid').addClass('is-valid');
    // } else {
    //   $(this).attr('aria-invalid', 'true');
    //   $(this).removeClass('is-valid').addClass('is-invalid');
    //   $(target_error).text('Поле должно содержать 40 символов');
    // }
  } else {
    $(this).attr('aria-invalid', 'true');
    $(this).removeClass('is-valid').addClass('is-invalid');
    $(target_error).text('Пожалуйста, заполните поле');
  }
});


/**
 *  Назначение обработчика события клика кнопки "Отправить"
 */
jQuery('#button_submit').click( function() {
  var form = $(this).parents('form'),
      valid_all = true;
  $($(this).data('targetResult')).parent().addClass('d-none');
  if (!(form instanceof jQuery)) return;
  form.find('input[name]').each(function(i,elem) {
    if ($(this).attr('aria-invalid')) {
      valid_all = false;
      $(this).removeClass('is-valid').addClass('is-invalid');
    }
  });
  if (!valid_all) return;
  //отправка формы
  form.submit();
});
});
