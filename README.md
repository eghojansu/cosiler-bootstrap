# Cosiler-Bootstrap

A working example of [ekok/cosiler](https://github.com/eghojansu/cosiler)

## Installation

`composer create-project ekok/cosiler-bootstrap medimom-server --repository="{\"url\": \"https://github.com/eghojansu/cosiler-bootstrap\", \"type\": \"vcs\"}" --stability=dev` 

Access setup in path `/setup.php` in your browser.

## Front End Dependencies

- [Bootstrap](https://getbootstrap.com)
- [Bootstrap Icons](https://icons.getbootstrap.com)
- [Simple DataTables](https://github.com/fiduswriter/Simple-DataTables)
- [Preactjs](https://preactjs.com)
- [HTM](https://github.com/developit/htm)
- [Clsx](https://github.com/lukeed/clsx)
- [Redaxios](https://github.com/developit/redaxios)
- [Sweetalert2](https://sweetalert2.github.io/)

## Starting server

`COSILER_ENV=test php -d variables_order=EGPCS -S 0.0.0.0:8001 public/index.php`