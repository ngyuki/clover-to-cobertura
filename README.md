# clover-to-cobertura

Clover XML to Cobertura XML for Gitlab Coverage Visualization

## Install

```sh
curl https://raw.githubusercontent.com/ngyuki/clover-to-cobertura/master/clover-to-cobertura.php \
  -o clover-to-cobertura.php
```

Or

```sh
composer require --dev ngyuki/clover-to-cobertura
```

## Usage

```sh
php clover-to-cobertura.php < clover.xml > cobertura.xml
```

## Example for Gitlab CI

```
# .gitlab-ci.yml

image: ngyuki/php-dev

stages:
  - test

test:
  stage: test
  only:
    - merge_requests
  script:
    - composer install --no-progress --ansi
    - phpdbg -qrr vendor/bin/phpunit --coverage-clover=clover.xml
    - test -e clover-to-cobertura.php ||
        curl https://raw.githubusercontent.com/ngyuki/clover-to-cobertura/master/clover-to-cobertura.php
            -o clover-to-cobertura.php
    - php clover-to-cobertura.php < clover.xml > cobertura.xml
  cache:
    paths:
      - vendor/
      - clover-to-cobertura.php
  artifacts:
    reports:
      cobertura: cobertura.xml
```

## License

- [MIT License](http://www.opensource.org/licenses/mit-license.php)
