import { h, render } from './modules/preact.module.js'
import redaxios from './modules/redaxios.module.js'
import htm from './modules/htm.module.js'

export const html = htm.bind(h)
export const lcFirst = str => `${str.charAt(0).toLowerCase()}${str.slice(1)}`
export const objFill = (keys, value = '') => Object.fromEntries(keys.map(key => [key, value]))
export const range = (size, step, from) => {
  let _step = 'number' === typeof step ? step : 1
  let _from = 'undefined' === typeof from ? 1 : from

  if ('string' === typeof _from) {
    return String.fromCharCode(...range(size, _from.charCodeAt(0), _step)).split('')
  }

  return [...Array(size).keys()].map(i => i * _from + _step)
}
export const request = redaxios.create({
  headers: {
    Accept: 'application/json',
  },
})
export const runApp = (Component, name) => {
  const n = (name || Component.name).toLowerCase()
  const dom = document.querySelector(`[data-app=${n}]`)

  if (!dom) {
    throw Error(`Selector not found: "app-${n}"`)
  }

  const search = new RegExp(`^app(.+)$`, 'i')
  const props = Object.keys(dom.dataset).reduce((props, key) => {
    if (search.test(key)) {
      props[lcFirst(key.replace(search, '$1'))] = dom.dataset[key]
    }

    return props
  }, {})

  return render(html`<${Component} ...${props} />`, dom)
}

export const Toast = (() => {
  if ('undefined' === typeof Swal) {
    return {
      fire: ({ message } = { message: 'Swal is not defined' }) => alert(message),
    }
  }

  return Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
    didOpen: toast => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
})()