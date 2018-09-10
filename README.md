# Intro

This is a chat app with basic application built with:

* [Pusher](https://pusher.com/) - Websocket service
* [Laravel](https://laravel.com) - The PHP Framework For Web Artisans
* [Vue.js](https://vuejs.org) - The Progressive JavaScript Framework

You can check demo app at: https://radiant-brook-17694.herokuapp.com

## Getting Started

Clone the project repository by running the command below if you use SSH

```bash
git clone git@github.com:ximee/jchat.git
```

If you use https, use this instead

```bash
git clone https://github.com/ximee/jchat.git
```

After cloning,run:

```bash
composer install
```

and:

```bash
npm install
```

Duplicate `.env.example` and rename it `.env`

Then run:

```bash
php artisan key:generate
```

### Prerequisites

#### Setup Pusher

For deploying you need to create a free Pusher account at [https://pusher.com/signup](https://pusher.com/signup) then login to your dashboard and create an app.

Set the `BROADCAST_DRIVER` in your `.env` file to **pusher**:

```txt
BROADCAST_DRIVER=pusher
```

Then fill in your Pusher app credentials in your `.env` file:

```txt
PUSHER_APP_ID=xxxxxx
PUSHER_APP_KEY=xxxxxxxxxxxxxxxxxxxx
PUSHER_APP_SECRET=xxxxxxxxxxxxxxxxxxxx
PUSHER_APP_CLUSTER=
```

Enable client events in App Settings page of Pusher dashboard.

#### Database Migrations

Be sure to fill in your database details in your `.env` file before running the migrations:

```bash
php artisan migrate
```

#### Image hosting

Enter Cloudinary account details in Cloudinary options section in .env file:

```txt
CLOUD_NAME=xxxxxxxxx
API_KEY=xxxxxxxxxxxxxxx
API_SECRET=xxxxxxxxxxxxxxxxxxxxxxxxxxx
```
