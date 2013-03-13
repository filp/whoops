# Damnit
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/damnit.png?branch=master)](https://travis-ci.org/filp/damnit)

-----

![Damnit!](http://i.imgur.com/wi8J4Vd.png)

`Damnit` is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

This library is currently in a **heavy development phase, and not yet ready for consumption.**

## Contributing

If you want to help, great! Here's a couple of steps/guidelines:

- Fork/clone this repo, and update dev dependencies using Composer

```bash
$ git clone git@github.com:filp/damnit.git
$ cd damnit
$ composer install --dev
```

- Create a new branch for your feature or fix

```bash
$ git checkout -b feature/flames-on-the-side
```

- Add your changes & tests for those changes (in `tests/`).
- Remember to stick to the existing code style as best as possible. When in doubt, follow `PSR-2`.
- Send me a pull request!

### TODO/tasks (very short & rough list of current goals)
- Get rid of jquery in the `PrettyPage` template
- Get rid of prettify, move syntax highlighting to PHP (the idea is to have no external dependencies)
- Add extension hooks for `PrettyPage`
- Improve test coverage
