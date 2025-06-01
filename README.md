# Football Club Backoffice

A online system to help manage a football (soccer) club, primariy manage players, their contact details, health, injuries and player development.

The application is built to support a UI JPL Warrior club but is freely available for any club that wants to use it.

The application is written in PHP so that it can be run on most web hosting platforms. As most clubs use Wordpress for their website and many use cheap shared webhosting that primarily supports PHP the application has been built using PHP in combination with Laravel and Filament. Laravel and Filament are easy to use and allow rapid development of web admin tools with relatively little programming experience.

## License

This applicaion is open-source and licenced under The [MIT license](https://opensource.org/licenses/MIT). Laravel framework is a major piece of this and is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Install Notes

The app uses laravels built in database migration tools. Before the application can be logged into an admin user and basic permissions need to be put in place, so a set of database seed scripts have been created. These install a user 'admin @example.com' with the password 'password123'.

```
php artisan migrate:fresh
php artisan db:seed --class=ShieldSeeder
php artisan db:seed
```

After installation a one off command is required to allow access to server storage when writing and sending emails.

```
php artisan storage:link
```

## Migration notes

In production sync the files across and then use the following to make any database updates

```
php artisan migrate
```

## Development

To develop code ensure PHP is locally installed and use `php artisan serve`
