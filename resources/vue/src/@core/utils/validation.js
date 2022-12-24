import { isEmpty } from './index'
import { lang } from '@/common'
import moment from "moment";
const trans = lang
export const required = value => (value && value.length ? true : trans('message.required_input'))
export const requiredField = (value, field) => {
  return value && value.length > 0 || trans('message.required_input', {':field' : field})
}
export const requiredSelect = (value, field) => {
  return value && value.length > 0 || trans('message.required_select', {':field' : field})
}
export const startDateBefore = (start, end, field1, field2) => {
  if(start <= end) return true

  if(field1 !== undefined && field2 != undefined) {
    return trans('message.date_must_before_another_date', {':field1': field1, ':field2': field2})
  }
  return trans('message.start_date_must_before_end_date')
}
// export const endDateBefore = (start, end) => {
//   return start <= end || trans('end_date_must_after_start_date')
// }
export const endDateAfter = (start, end) => {
  return start <= end || trans('message.end_date_must_after_start_date')
}

export const dateRepeat = (repeat, start, end) => {
  let isValid = false
  switch (repeat){
    case '1':
      isValid = moment(start).add(6, 'days').format("YYYY-MM-DD")
      return end < isValid || trans('message.over_duration', {':duration': trans('app.one_week')})
    case '2':
      isValid = moment(start).add(1, 'months').format("YYYY-MM-DD")
      return end < isValid || trans('message.over_duration', {':duration': trans('app.one_month')})
    case '3':
      isValid = moment(start).add(1, 'years').format("YYYY-MM-DD")
      return end < isValid || trans('message.over_duration', {':duration': trans('app.one_year')})
    default:
      return false
  }
}

export const emailValidator = value => {
  if (isEmpty(value)) {
    return true
  }

  // eslint-disable-next-line
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/

  if (Array.isArray(value)) {
    return value.every(val => re.test(String(val)))
  }

  return re.test(String(value)) || trans('message.valid_email')
}

export const passwordValidator = (password) => {
  // if(password.length < minLen) {
  //   return trans('message.valid_password')
  // }

  /* eslint-disable no-useless-escape */
  const regExp = /(?=.*[a-z])(?=.*[~`!@#$%^&*()--+={}[]|:;"'<>,_₹]){8}/
  const regExp2 = /(?=.*\d)(?=.*[a-z]){8}/
  const regExp3 = /(?=.*\d)(?=.*[~`!@#$%^&*()--+={}[]|:;"'<>,_₹]){8}/
  /* eslint-enable no-useless-escape */
  const validPassword = regExp.test(password)
  const validPassword2 = regExp2.test(password)
  const validPassword3 = regExp3.test(password)

  return (validPassword || validPassword2 || validPassword3) || trans('message.valid_password')

}

export const confirmedValidator = (value, target) =>
  // eslint-disable-next-line implicit-arrow-linebreak
  value === target || trans('message.valid_password_confirm')

export const confirmedValidatorEmail = (value, target) =>
  // eslint-disable-next-line implicit-arrow-linebreak
  value === target || trans('message.valid_email_confirm')

export const between = (value, min, max, field) => () => {
  const valueAsNumber = Number(value)

  return (Number(min) <= valueAsNumber && Number(max) >= valueAsNumber) || trans('message.valid_number_between', {':field': field, ':min': min, ':max': max})
}

export const integerValidator = (value, field) => {
  if (isEmpty(value)) {
    return true
  }

  if (Array.isArray(value)) {
    return value.every(val => /^-?[0-9]+$/.test(String(val)))
  }

  return /^-?[0-9]+$/.test(String(value)) || trans('message.validate_number', {':field' : field})
}

export const regexValidator = (value, regex) => {
  if (isEmpty(value)) {
    return true
  }

  let regeX = regex
  if (typeof regeX === 'string') {
    regeX = new RegExp(regeX)
  }

  if (Array.isArray(value)) {
    return value.every(val => regexValidator(val, { regeX }))
  }

  return regeX.test(String(value)) || 'The Regex field format is invalid'
}

export const alphaValidator = (value, field) => {
  if (isEmpty(value)) {
    return true
  }

  return /^[A-Z]*$/i.test(String(value)) || trans('message.alphabetic_invalid', {':field': field})
}

export const alphaDashValidator = (value, field) => {
  if (isEmpty(value)) {
    return true
  }

  const valueAsString = String(value)

  return /^[0-9A-Z_-]*$/i.test(valueAsString) || trans('message.alphabetic_numeric_invalid', {':field': field})
}

export const urlValidator = value => {
  if (value === undefined || value === null || value.length === 0) {
    return true
  }
  /* eslint-disable no-useless-escape */
  const re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/

  return re.test(value) || trans('message.invalid_url')
}

export const lengthValidator = (value, length, field) => {
  if (isEmpty(value)) {
    return true
  }

  return value.length === length || trans('message.validate_length', {':field': field, ':length': length})
}

export const maxLengthValidator = (value, length, field) => {
  if(isEmpty(value)){
    return true
  }
  return value.length <= length || length + trans('message.max_length', {':field' : field, ':length' : length})
}
export const minLengthValidator = (value, length, field) => {
  if(isEmpty(value)){
    return true
  }
  return value.length >= length || length + trans('message.min_length', {':field' : field, ':length' : length})
}

export const maxListFileSize = (files) => {
  let maxSize = true
  if (!files) return true
  files.forEach(file => {
    if (file.size > 20971520) {
      if (file.size > 20971520) {
        maxSize = false
      }
    }
    return maxSize || trans('app.msg_max_size')
  })
}
export const maxFileSize = (file) => {
  if(file && file.size > 20971520){
    return trans('app.msg_max_size')
  }
  return true
}

export const checkStartDate = (start, field) => {
  if (!moment(start, "YYYY-MM-DD", true).isValid()) {
    return trans('message.select_start_end_date')
  }

  if (Date.parse(start) < Date.parse(moment(Date.now()).format('YYYY/MM/DD'))) {
    return trans('message.date_must_after_another_date', {':field1': field, 'field2': '本日'})
  }

  return true;
}

export const checkStartDateEdit = (start) => {
  if (!moment(start, "YYYY-MM-DD", true).isValid()) {
    return trans('message.select_start_end_date')
  }
  return true;
}

export const checkEndDate = (start, end, repeat) => {

  if (!moment(end, "YYYY-MM-DD", true).isValid()) {
    return trans('message.select_start_end_date')
  }

  if (Date.parse(end) < Date.parse(moment(Date.now()).format('YYYY/MM/DD')) && Date.parse(start) > Date.parse(end)) {
    return trans('message.end_date_must_after_start_date')
  }
  let startDate = new Date(start).getTime();
  let endDate = new Date(end).getTime();
  let totalDate = Math.ceil((endDate - startDate) / (24 * 60 * 60 * 1000));
  if (repeat == 1 && totalDate > 6) {
    return trans('message.over_duration', {':duration': trans('app.one_week')})
  }

  if (repeat == 2 && totalDate > 30) {
    return trans('message.over_duration', {':duration': trans('app.one_month')})
  }

  if (repeat == 3 && totalDate > 364) {
    return trans('message.over_duration', {':duration': trans('app.one_year')})
  }
  return true;
}

export const checkDay = (date) => {
  if (date === "" || date <= 0 || (new Date(date) == "Invalid Date" && date >= 1 && date <= 31)) {
    return trans('message.valid_number_between', {':field': '日', ':min': 1, ':max': 31});
  }
  return true;
}
