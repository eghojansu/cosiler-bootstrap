const fun = {
  select: (selector) => {
    const elements = document.querySelectorAll(selector)
    const methods = {
      on: (event, listener) => {
        elements.forEach(el => el.addEventListener(event, (...args) => listener.call(null, el, ...args)))

        return methods
      },
      each: (callback) => {
        elements.forEach(callback)

        return methods
      },
    }

    return methods
  },
  createElements: (element) => {
    if (Array.isArray(element)) {
      return element.filter(Boolean).map(el => fun.createElements(el)[0])
    }

    if ('string' === typeof element) {
      return [document.createTextNode(element)]
    }

    return [element]
  },
  tag: (tag, attrs, children, listeners) => {
    const el = document.createElement(tag)

    for (const key in attrs) {
      if (Object.hasOwnProperty.call(attrs, key) && false !== attrs[key]) {
        el.setAttribute(key, true === attrs[key] ? '' : attrs[key])
      }
    }

    for (const key in listeners) {
      if (Object.hasOwnProperty.call(listeners, key)) {
        el.addEventListener(key, listeners[key].bind(el))
      }
    }

    if (children) {
      fun.createElements(children).forEach(child => el.appendChild(child))
    }

    return el
  },
}

;((d) => {
  const confirms = {}

  d.select('[data-confirm]').on('click', (el, ev) => confirm(confirms[el.dataset.confirm] || `Are you sure to ${el.dataset.confirm.toUpperCase()}?`) || ev.preventDefault())
  d.select('[data-generate=password]').each(el => {
    const parent = el.parentNode
    const feedback = parent.querySelector('.invalid-feedback')
    const view = (button, open) => {
      const set = open ? ['bi-eye-slash', 'bi-eye', 'text'] : ['bi-eye', 'bi-eye-slash', 'password']

      button.querySelector('i').classList.remove(set[0])
      button.querySelector('i').classList.add(set[1])
      button.closest('div').querySelector('input').type = set[2]
    }
    const update = d.tag(
      'div',
      {
        class: 'input-group',
      },
      [
        el,
        d.tag(
          'button',
          {
            class: 'btn btn-outline-danger',
            type: 'button',
          },
          d.tag('i', {class: 'bi-key'}),
          {
            click: (ev) => {
              ev.target.closest('div').querySelector('input').value = Math.random().toString(36).slice(-8).toUpperCase()
            },
          },
        ),
        d.tag(
          'button',
          {
            class: 'btn btn-outline-info',
            type: 'button',
          },
          d.tag('i', {class: 'bi-eye-slash'}),
          {
            mousedown: (ev) => {
              view(ev.target.closest('button'), true)
            },
            mouseup: (ev) => {
              view(ev.target.closest('button'), false)
            },
            blur: (ev) => {
              view(ev.target.closest('button'), false)
            }
          },
        ),
        feedback,
      ]
    )

    while (parent.firstChild) {
      parent.removeChild(parent.firstChild)
    }

    parent.appendChild(update)
  })
})(fun)