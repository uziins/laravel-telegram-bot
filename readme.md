# Laravel Telegram Bot

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

## Installation

Install this package through Composer. Run this command in your project's terminal:

``` bash
composer require uziins/laravel-telegram-bot
```
## Usage
For further basic configuration of this Laravel package you do not need to create any configuration files.

Database migration
``` bash
php artisan migrate
```

Publish config file
``` bash
php artisan vendor:publish --tag="telegram-config"
```

Publish routes file
``` bash
php artisan vendor:publish --tag="telegram-routes"
```

Artisan terminal commands for the Webhook usage (remember, that you need an HTTPS server for it):
``` bash
# Use this method to specify a url and receive incoming updates via an outgoing webhook
php artisan telegram:set-webhook
# List of available options: 
# --d|drop-pending-updates : Drop all pending updates
# --a|all-update-types : Explicitly allow all updates (including "chat_member")
# --allowed-updates= : Define allowed updates (comma-seperated)

# Use this method to remove webhook integration if you decide to switch back to getUpdates
php artisan telegram:delete-webhook
# List of available options:
# --d|drop-pending-updates : Pass to drop all pending updates
```
Artisan terminal commands for the Telegram getUpdates method:
``` bash
# Fetches Telegram updates periodically
php artisan telegram:fetch 
# List of available options:
# --a|all-update-types : Explicitly allow all updates (including "chat_member")
# --allowed-updates= : Define allowed updates (comma-seperated)
```
Artisan terminal command for Telegram Server logging out:
``` bash
# Sends a logout to the currently registered Telegram Server
php artisan telegram:logout
```
Artisan terminal command for closing Telegram Server:
``` bash
# Sends a close to the currently registered Telegram Server
php artisan telegram:close
```
Artisan terminal command for publishing Telegram command folder structure in your project:
``` bash
# Publishes folder structure for Telegram Commands
# Default StartCommand class will be created
php artisan telegram:publish
```
Artisan terminal command for creating new Telegram command class in your project:
``` bash
# Create a new Telegram Bot Command class
# e.g. php artisan make:telegram-command Menu --> will make User command class MenuCommand 
# e.g. php artisan make:telegram-command Genericmessage --system --> will make System command class GenericmessageCommand
php artisan make:telegram-command
# List of available options:
# name : Name of the Telegram Command
# --a|admin : Generate a AdminCommand
# --s|system : Generate a SystemCommand
# Without admin or system option default User command will be created
```



## Credits

- [Avtandil Kikabidze aka LONGMAN](https://github.com/akalongman)
- [TiiFuchs](https://github.com/TiiFuchs)
- [All Contributors][link-contributors]

## License

Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/php-telegram-bot/laravel.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/php-telegram-bot/laravel.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/php-telegram-bot/laravel
[link-downloads]: https://packagist.org/packages/php-telegram-bot/laravel
[link-contributors]: https://github.com/php-telegram-bot/laravel/contributors
