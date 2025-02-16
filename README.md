# WP Static Analysis

### Install

```shell
composer require --dev onepix/wp-static-analysis
```

### Required

- PHP 8.1+

## CLI

#### PHP_CodeSniffer

```shell
vendor/bin/wp-static-analysis phpcs [OPTIONS] [--] [<PHPCS-ARGS>...]
```

```shell
vendor/bin/wp-static-analysis phpcbf [OPTIONS] [--] [<PHPCS-ARGS>...]
```

`[PHPCS-ARGS]`: Arguments from [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Usage)

`[OPTIONS]`:

- `--ruleset` - Path to the custom `ruleset.xml` file relative to the project

If `--ruleset` is not present, it checks the files in order of priority:
1. `.config/.phpcs.xml`
2. `.config/phpcs.xml`
3. `.config/.phpcs.xml.dist`
4. `.config/phpcs.xml.dist`

If none found → `WpOnepixStandard` is automatically applied
