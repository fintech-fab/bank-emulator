Bank Emulator
===============

Service gateway emulates the banking system for card payments.
Simple and debug web interface inside.
Install and use path /bank/emulator/demo in your web project.

- PHP SDK: https://github.com/fintech-fab/money-transfer-emulator-sdk
- Public demo: Coming soon
- 3DS-auth (analogue): Coming soon
- Full debug web-form: Coming soon

# Requirements

- php >=5.3.0
- Laravel Framework 4.1.*
- MySQL Database
- Laravel queue driver configuration
- User auth identifier in your web project

# Uses

- bootstrap cdn
- jquery cdn

# Installation

## Composer

Package only:

    {
        "require": {
            "fintech-fab/bank-emulator": "dev-master"
        },
    }

Package dependency:

    {
        "require": {
	        "php": ">=5.3.0",
	        "laravel/framework": "4.1.*",
	        "iron-io/iron_mq": "dev-master"
            "fintech-fab/bank-emulator": "dev-master"
        },
	    "require-dev": {
		    "phpunit/phpunit": "3.7.*",
		    "mockery/mockery": "dev-master"
	    },
    }

Run it:

	composer update
	php artisan dump-autoload

## Local configuration

Add service provider to `config/app.php`:

	'providers' => array(
		'FintechFab\BankEmulator\BankEmulatorServiceProvider'
	)

### Queue connection named 'ff-bank-em', e.g. iron:

Add to `config/#env#/queue.php`:

	'connections' => array(
		'ff-bank-em' => array(
			'driver'  => 'iron',
			'project' => 'your-iron-project-id',
			'token'   => 'your-iron-token',
			'queue'   => 'your-iron-queue',
		),
	),

Run the queue worker:

	php artisan queue:listen --queue="ff-bank-em" ff-bank-em

### Database connection named 'ff-bank-em'

Add to `config/#env#/database.php`:

	'connections' => array(
		'ff-bank-em'  => array(
			'driver'    => 'mysql',
			'host'      => 'your-mysql-host',
			'database'  => 'your-mysql-database',
			'username'  => 'root',
			'password'  => 'your-mysql-password',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => 'your-table-prefix',
		),
	),

## Migrations

	php artisan migrate --package="fintech-fab/bank-emulator" --database="ff-bank-em"

### Custom user auth identifier:

Default, user auth id detect by `Auth::user()->getAuthIdentifier()`.
Your can set integer value (e.g. `'user_id' => 1`), or use some your function with identifier return;

For this, publish configuration from package:

	php artisan config:publish fintech-fab/bank-emulator

And change user auth identifier for your web project `app/config/packages/fintech-fab/bank-emulator/config.php`:

	'user_id' => 'user-auth-identifier',

### Optionally, external logs by loggly.com:

Add to `config/#env#/app.php`:

	'logglykey' => 'your-loggly-key',
	'logglytag' => 'your-loggly-tag',

Change `start/global.php` (`Application Error Logger` section):

	Log::useFiles(storage_path() . '/logs/laravel.log');

	if (Config::get('app.logglykey') && Config::get('app.logglytag')) {
		$handler = new \Monolog\Handler\LogglyHandler(Config::get('app.logglykey'), \Monolog\Logger::DEBUG);
		$handler->setTag(Config::get('app.logglytag'));
		$logger = Log::getMonolog();
		$logger->pushHandler($handler);
	}


## Development How to

### Workbench migrations

	php artisan migrate:reset --database="ff-bank-em"
	php artisan migrate --bench="fintech-fab/bank-emulator" --database="ff-bank-em"

	php artisan migrate:reset --database="ff-bank-em" --env="testing"
	php artisan migrate --bench="fintech-fab/bank-emulator" --database="ff-bank-em" --env="testing"

### Package migrations

	php artisan migrate:reset --database="ff-bank-em"
	php artisan migrate --package="fintech-fab/bank-emulator" --database="ff-bank-em"

	php artisan migrate:reset --database="ff-bank-em" --env="testing"
	php artisan migrate --package="fintech-fab/bank-emulator" --database="ff-bank-em" --env="testing"

### Workbench publish

	php artisan config:publish --path=workbench/fintech-fab/bank-emulator/src/config fintech-fab/bank-emulator

