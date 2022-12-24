window.trans = (key, params) => {
  let msg = _.get(window.i18n, key);
  if(params !== undefined) {
    for (const param_key in params) {
      let param_value = params[param_key];
      msg = msg.replace(`${param_key}`, param_value);
    }
  }
  return msg;
}
