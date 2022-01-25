# Cosiler-Bootstrap

A working example of [ekok/cosiler](https://github.com/eghojansu/cosiler)

## Installation

`composer create-project ekok/cosiler-bootstrap medimom-server --repository="{\"url\": \"https://github.com/eghojansu/cosiler-bootstrap\", \"type\": \"vcs\"}" --stability=dev` 

Access setup in path `/setup.php` in your browser.

## Starting test server

`COSILER_ENV=test php -d variables_order=EGPCS -S localhost:8001 public/index.php`