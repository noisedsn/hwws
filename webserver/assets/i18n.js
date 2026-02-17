function setLang(locale = '') {
  setCookie('lang', locale, 365);
  window.location.reload();
}

function t(key, vars = {}) {
    let value = window.I18N;

    for (const part of key.split('.')) {
        if (typeof value !== 'object' || !(part in value)) {
            return key;
        }
        value = value[part];
    }

    if (typeof value !== 'string') {
        return key;
    }

    for (const k in vars) {
        value = value.replace(`{${k}}`, vars[k]);
    }

    return value;
}

$(document).ready(function() {

  for (var i = 0; i < locales.length; i++) {
    $('#locale').append('<option value="'+locales[i].locale+'">'+locales[i].code.toUpperCase()+'</option>');
  }
  $('#locale option[value='+t('locale')+']').prop('selected', true);

  $('#locale').on('change', function(){
    var locale = $(this).find('option:selected').val();
    setLang(locale);
  });

});

