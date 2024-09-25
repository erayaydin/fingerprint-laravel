# Contributing

Contributions are **welcome**.

The project accept contributions via Pull Requests on [GitHub](https://github.com/erayaydin/fingerprint-laravel).

## Issues

### Creating an Issue

If you find a bug, problem, or maybe the documentation just doesn't make sense, please create an Issue to document the
concern.

Please be descriptive in your Issue. The more info you provide, the more likely someone will be able to help.

### Code Examples

If you're experiencing an issue with the code, the most helpful thing you can do is create an example where you can
reproduce the problem. This can be an open source GitHub repo, a private repo you can share with the maintainers, or
really anything to show the issue live with code alongside of it.

## Pull Requests

The project uses **[PSR-12 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md)**.
The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

### Creating a Pull Request

If you're able to fix an active Issue, feel free to create a new Pull Request addressing the problem. There are no
guarantees that the code will be merged in "as is", but chances are, if you're willing to work with the maintainers,
everyone will be able to come up with a solution everyone can be happy with.

Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits
while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages)
before submitting.

If you want to do more than one thing, send multiple pull requests.

Please be descriptive in your Pull Request. Whether big or small, it's important to be able to see the context of a
change throughout the history of a project.

### Linking Issues
If the Pull Request is addressing an Issue, please link that issue by specifying the `Fixes #` syntax within
the Pull Request.

### Tests

Your patch won't be accepted if it does not have tests.

You can run tests with:

``` bash
$ composer test
```

Also, you can check current code coverage with:

``` bash
$ composer test-coverage
```

### Documentation

Make sure the `README.md` and any other relevant documentation are kept up-to-date.

### Branching

Create a feature branch.
