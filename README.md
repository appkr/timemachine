[![Latest Stable Version](https://poser.pugx.org/appkr/timemachine/v/stable)](https://packagist.org/packages/appkr/timemachine)
[![Total Downloads](https://poser.pugx.org/appkr/timemachine/downloads)](https://packagist.org/packages/appkr/timemachine)
[![Latest Unstable Version](https://poser.pugx.org/appkr/timemachine/v/unstable)](https://packagist.org/packages/appkr/timemachine)
[![License](https://poser.pugx.org/appkr/timemachine/license)](https://packagist.org/packages/appkr/timemachine)

# Timemachine

Manipulates application(Laravel/Lumen) server's time arbitrarily for a given minutes.

> **`CAVEAT`**
>
> USE THIS ONLY FOR TEST PURPOSE. DO NOT MAKE THIS AVAILABLE IN PUBLICLY ACCESSIBLE SERVICES. 
>
> - Provided apis are not protected by authz. 
> - While setting is alive, it affects all time related functions of the application. e.g. `created_at` written in the DB tables.

## 1. Install

Pull the library to your project.

```bash
composer install appkr/timemachine 
```

Append service provider in the providers array.

```php
<?php // config/app.php (Laravel)

return [
    'providers' => [
        Appkr\Timemachine\TimemachineServiceProvider::class,
    ],
];

// boostrap/app.php (Lumen)
$app->register(Appkr\Timemachine\TimemachineServiceProvider::class);
```

Optionally publish config.

```bash
php artisan vendor:publish --provider="Appkr\Timemachine\TimemachineServiceProvider"
```

## 2. How to use

There are three APIs. For conveniences, a [Postman collection](https://www.getpostman.com/collections/8bc45986fb3924c0aa77) is provided.

#### 2.1. GET /timemachine

Returns time diff from current server time when `target_server_time` parameter is given. Or print current server time when nothing is given.  

```http
GET /timemachine
Accept: application/json
Content-Type: application/json
```

field|type|required|description
---|---|---|---
`target_server_time`|`date(Y-m-d H:i:s)`|optional|e.g. 2017-06-01 12:05:00

```http
HTTP/1.1 200 OK
Content-Type: application/json
Date: Thu, 01 Jun 2017 12:00:00 +0900

{
  "current_server_time": "2017-06-01T12:00:00+0900",
  "target_server_time": "2017-06-01T12:05:00+0900",
  "add_days": 0,
  "add_minutes": 5,
  "sub_days": null,
  "sub_minutes": null
}
```

#### 2.2. PUT /timemachine 

Manipulates server's time for the given ttl.

```http
PUT /timemachine
Accept: application/json
Content-Type: application/json

{
    "add_minutes": 5,
    "ttl": 5
}
```

field|type|required|description
---|---|---|---
`add_days`|`int`|optional(max:365)|Number of days to add to current time
`sub_days`|`int`|optional(max:365)|Number of days to subtract from current time
`add_minutes`|`int`|optional(max:1440)|Number of minutes to add to current time
`sub_minutes`|`int`|optional(max:1440)|Number of minutes to subtract from current time
`ttl`|`int`|optional(default:5, max:60)|Number of minutes for settings to live

```http
HTTP/1.1 200 OK
Content-Type: application/json
Date: Thu, 01 Jun 2017 12:00:00 +0900

{
  "current_server_time": "2017-06-01T12:00:00+0900",
  "message": "Success. The settings will be effective from next request on for 5 minutes."
}
```

#### 2.3. DELETE /timemachine

Removes time setting and restore back to the machine time.

```http
DELETE /timemachine
Accept: application/json
Content-Type: application/json
```

```http
HTTP/1.1 200 OK
Content-Type: application/json
Date: Thu, 01 Jun 2017 12:00:00 +0900

{
  "current_server_time": "2017-06-01T12:00:00+0900",
  "message": "Success. Settings removed."
}
```

## 3. Sponsors

This library is a by-product of a company project. Thanks [MeshKorea](http://meshkorea.net/).

![MeshKorea](logo_meshkorea.png)

Open source version was created using IntelliJ IDE sponsored by [JetBrains](https://www.jetbrains.com/).

![JetBrains](logo_intellij.png)

## 4. Note on HTTP Date response header

In Nginx, `Date` header can be settable from application side. So the following was possible:

```php
<?php

header('Date: ' . Carbon\Carbon::now()->addMinutes(5)->toRfc2822String());
echo $httpResponseBody;
```

While in Apache, it is not doable. https://laracasts.com/discuss/channels/servers/how-can-i-override-http-date-response-header-under-apache24