TYPO3 Ensure Admin
==================

# Mission

The mission of this extension is to provide a tool to create and update users
for staging and development purpose. Thus, it should be only taken as development
dependencies for projects.

Additionally, it provides a simple password encoding command, which may be used
to encode a password and use it to set the TYPO3 installtool password, for example.

> :information_source: This extension only handles backend user compatible passwords.
> This means, Frontend Users passwords are not handled.

# Version compatibility

| version | TYPO3     | PHP                |
|---------|-----------|--------------------|
| 2.x     | v11 + v12 | 7.4, 8.0, 8.1, 8.2 |
| 1.x     | v10       | 7.2, 7.3, 7.4      |

# Alternatives

[TYPO3 Console](https://github.com/TYPO3-Console/TYPO3-Console) includes some commands, which (partly) can do which this
extension tries to archieve. If you are already using it, maybe existing
features suits you. Then stay with that extension.

* https://docs.typo3.org/p/helhum/typo3-console/main/en-us/CommandReference/BackendCreateadmin.html

> :information_source: We are not aware of further extensions providing similar
> abilities. Let us know if there are some, and we will add them here as alternative.

# Installation

## Composer

You probably want to install this as `--dev` dependency.

```
$ composer require --dev sbuerk/typo3-ensure-admin
```

# Usage

## ensure admin user

```shell
$ vendor/bin/typo3 sbuerk:admin:ensure [options]
```

*Synopsis:*
```
Description:
  Create or update an admin user

Usage:
  sbuerk:admin:ensure [options]

Options:
      --name=NAME            Admin username - ENV: TYPO3_ENSUREADMIN_USERNAME
      --email=EMAIL          Admin email - ENV: TYPO3_ENSUREADMIN_EMAIL
      --password=PASSWORD    Admin password (plain) - ENV: TYPO3_ENSUREADMIN_PASSWORD
      --firstname=FIRSTNAME  Admin firstname - ENV: TYPO3_ENSUREADMIN_FIRSTNAME
      --lastname=LASTNAME    Admin lastname - ENV: TYPO3_ENSUREADMIN_LASTNAME
      --json                 Response with json for tool usages.
      --force                Force admin user creation, means updating existing one.
  -h, --help                 Display help for the given command. When no command is given display help for the list command
  -q, --quiet                Do not output any message
  -V, --version              Display this application version
      --ansi|--no-ansi       Force (or disable --no-ansi) ANSI output
  -n, --no-interaction       Do not ask any interactive question
  -v|vv|vvv, --verbose       Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

This command can be used to create or update admin user. If a user exists, the
`--force` flag must be provided to update the admin user.

Additionally, this command supports `JSON Response` results instead of normal
text outputs, which may be used for integrating in other tools or workflows. Use
the `--json` flag to retrieve `JSON Results`.

Some options are required and mandatory, others are optional. Options can be
provided as command line options or as Environment Variables, except `--json`
and `--enforce` flags.

> :information_source: If for an option both variants are provided, the option
> variants preceeds the Environment Variable variant.

| option       | required | env variable                  | description                                                                                            |
|--------------|----------|-------------------------------|--------------------------------------------------------------------------------------------------------|
| --name=      | yes      | `TYPO3_ENSUREADMIN_USERNAME`  | Admin username                                                                                         |
| --password=  | yes      | `TYPO3_ENSUREADMIN_PASSWORD`  | Sets the admin `password`                                                                              |
| --email=     | no       | `TYPO3_ENSUREADMIN_EMAIL`     | Sets the admin `email` address.                                                                        |
| --firstname= | no       | `TYPO3_ENSUREADMIN_FIRSTNAME` | Will be used in combination with lastname to set the admin `realName`                                  |
| --lastname=  | no       | `TYPO3_ENSUREADMIN_LASTNAME`  | Will be used in combination with firstname to set the admin `realName`                                 |
| --json       | no       | -                             | Return messages as json objects/strings                                                                |
| --force      | no*      | -                             | Required if you want to update an existing admin user. Optional/not used if new admin user is created. |

JSON Response structure:
```json
{
    "success": true,
    "message": "Success or error message"
}
```

`Normal mode` and `JSON result mode` both provides proper exit codes, which may
be used as a first indicator without really reading the output.

| result-state | exit code |
|--------------|-----------|
| success      | 0         |
| failure      | 1         |

## password encode

```shell
$ vendor/bin/typo3 sbuerk:password:encode <plain-password> [--json]
```

*Synopsis:*

```
Description:
  Takes a plain password and returns the password hash. Can be used as install tool password.

Usage:
  sbuerk:password:encode [options] [--] <plain>

Arguments:
  plain                 Plain password to encode to be used for install tool / .env setting

Options:
      --json            Output json result
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

| argument/option | description                                                                           |
|-----------------|---------------------------------------------------------------------------------------|
| plain-password  | You must specify the plan password you want to encode. This is required.              |
| --json          | *Optional* Add this flag if you want json responses instead of human readable output. |

JSON Response structure:
```json
{
    "success": true,
    "password": "<hashed-password-string>",
    "message": "Message, for example the plain => hashed password success message"
}
```

`Normal mode` and `JSON result mode` both provides proper exit codes, which may
be used as a first indicator without really reading the output.

| result-state | exit code |
|--------------|-----------|
| success      | 0         |
| failure      | 1         |

## TYPO3 Extension Repository

For non-composer projects, the extension is available in TER as extension key
`cli_ensure_admin` and can be installed using the extension manager.

# Tagging and releasing

[packagist.org](https://packagist.org/packages/sbuerk/typo3-ensure-admin) is enabled via the casual github hook.
TER releases are created by the "publish.yml" github workflow when tagging versions
using [tailor](https://github.com/typo33/tailor). The commit message of the tagged commit is
used as TER upload comment.

```shell
$ Build/Scripts/runTests.sh -s clean
$ Build/Scripts/runTests.sh -s composerUpdate
$ composer req --dev typo3/tailor
$ .Build/bin/tailor set-version 1.0.1 --no-docs
$ composer rem --dev typo3/tailor
$ git commit -am "[RELEASE] 1.0.1 Added some basic inline foreign field related checks"
$ git tag 1.0.1
$ git push
$ git push --tags
```

# Feedback / Bug reports / Contribution

Bug reports, feature requests and pull requests are welcome in the GitHub
repository: <https://github.com/sbuerk/typo3-ensure-admin>
