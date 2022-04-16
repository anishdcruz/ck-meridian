import Vue from 'vue'

import format from 'date-fns/format'
import parse from 'date-fns/parse'
import { state, config } from '@js/shared/store'

Vue.filter('trim', (value, length = 50) => {
    if(value.length > length) {
        return value.substring(0, length) + '...'
    }
    return value
})

Vue.filter('toDate', (value) => {
    const formats = {
        'd-m-Y': 'DD-MM-YYYY',
        'Y-m-d': 'YYYY-MM-DD',
        'd-M-Y': 'DD-MMM-YYYY',
        'Y-M-d': 'YYYY-MMM-DD',
        'd/m/Y': 'DD/MM/YYYY',
        'Y/m/d': 'YYYY/MM/DD'
    }

    let f = state.current_team ?  state.current_team.date_format : 'd-m-Y'

    let result = format(
        parse(value),
        formats[f]
    )

    return result
})

Vue.filter('formatDate', (value, f) => {
    const formats = {
        'd-m-Y': 'DD-MM-YYYY',
        'Y-m-d': 'YYYY-MM-DD',
        'd-M-Y': 'DD-MMM-YYYY',
        'Y-M-d': 'YYYY-MMM-DD',
        'd/m/Y': 'DD/MM/YYYY',
        'Y/m/d': 'YYYY/MM/DD'
    }

    let result = format(
        parse(value),
        formats[f]
    )

    return result
})


Vue.filter('formatMoney', (value, code = true) => {
    let currency = state.current_team.currency

    let amount = Number(value)
        .toFixed(currency.precision)
        .replace('.', currency.decimal_mark)
        .replace(/(\d)(?=(\d{3})+(?!\d))/g, `$1${currency.thousands_separator}`)

    if(code) {
        if(currency.symbol_first) {
            return `${currency.code} ${amount}`
        } else {
            return `${amount} ${currency.code}`
        }
    }

    return amount
})
Vue.filter('formatMoneyNumber', (value) => {
    let currency = state.current_team.currency

    let amount = Number(value)
        .toFixed(currency.precision)

    return amount
})


Vue.filter('toMoney', (value, currency, code = true) => {
    let amount = Number(value)
        .toFixed(currency.precision)
        .replace('.', currency.decimal_mark)
        .replace(/(\d)(?=(\d{3})+(?!\d))/g, `$1${currency.thousands_separator}`)

    if(code) {
        if(currency.symbol_first) {
            return `${currency.code} ${amount}`
        } else {
            return `${amount} ${currency.code}`
        }
    }

    return amount
})