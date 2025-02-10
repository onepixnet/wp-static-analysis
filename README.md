# WP Static Analysis

### Required

- PHP 8.1+

## CLI

#### PHP_CodeSniffer

```shell
vendor/bin/wp-static-analysis phpcs [PHPCS-ARGS] [CLI-ARGS]
```

```shell
vendor/bin/wp-static-analysis phpcbf [PHPCS-ARGS] [CLI-ARGS]
```

`[PHPCS-ARGS]`: Arguments from [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki)

`[CLI-ARGS]`:

- `--ruleset` - Path to the custom `ruleset.xml` file relative to the project

When running the command without `--ruleset`, the package looks for the config in the project at the path `config/ruleset.xml`. If it doesn't find it, it takes the default configured standard `WpOnepixStandard`.
