# TaskGRID

![CI](https://github.com/carbontwelve/task-grid/workflows/Laravel/badge.svg?branch=main)

## Install

The TaskGRID API uses [laravel/socialite](https://laravel.com/docs/8.x/socialite) for authentication alongside [laravel/sanctum](https://laravel.com/docs/8.x/sanctum) to provide SPA auth tokens. You will need to set the `GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET` and `GITHUB_REDIRECT_URI` correctly in order to use that service, or alternatively install a different [socialite provider](https://socialiteproviders.com/).

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
