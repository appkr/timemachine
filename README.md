[![Latest Stable Version](https://poser.pugx.org/appkr/timemachine/v/stable)](https://packagist.org/packages/appkr/timemachine)
[![Total Downloads](https://poser.pugx.org/appkr/timemachine/downloads)](https://packagist.org/packages/appkr/timemachine)
[![Latest Unstable Version](https://poser.pugx.org/appkr/timemachine/v/unstable)](https://packagist.org/packages/appkr/timemachine)
[![License](https://poser.pugx.org/appkr/timemachine/license)](https://packagist.org/packages/appkr/timemachine)

# Timemachine

Manipulates application(Laravel/Lumen) server's time arbitrarily for a given minutes.     

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

## 2. How to use

There are three APIs. For conveniences, a [Postman collection](https://www.getpostman.com/collections/8bc45986fb3924c0aa77) is provided.

#### 2.1. GET /timemachine

Get time diff from current server time when `target_server_time` parameter is given. Or print current server time when nothing is given.  

```http
GET /timemachine
Accept: application/json
Content-Type: application/json
```

field|type|required|description
---|---|---|---
`target_server_time`|`date(Y-m-d H:i:s)`|optional|e.g. 2017-05-11 10:10:10

```json
{
  "current_server_time": "2017-06-02T10:54:20+0900",
  "target_server_time": "2017-05-11T10:10:10+0900",
  "add_days": null,
  "add_minutes": null,
  "sub_days": 22,
  "sub_minutes": 44
}
```

#### 2.2. PUT /timemachine 

Manipulate server's time for the given ttl.

```http
PUT /timemachine
Accept: application/json
Content-Type: application/json

{
    "add_days": null,
    "sub_days": 22,
    "add_minutes": null,
    "sub_minutes": 44,
    "ttl": 5
}
```

field|type|required|description
---|---|---|---
`add_days`|int|optional|Number of days to add to current time
`sub_days`|int|optional|Number of days to subtract from current time
`add_minutes`|int|optional|Number of minutes to add to current time
`sub_minutes`|int|optional|Number of minutes to subtract from current time
`ttl`|int|optional(default:5, max:60)|Number of minutes for settings alive

```json
{
  "current_server_time": "2017-06-02T10:54:20+0900",
  "message": "Success. The settings will be effective from next request on for 5 minutes."
}
```

#### 2.3. DELETE /timemachine

Remove time setting and restore back to the machine time.

```http
DELETE /timemachine
Accept: application/json
Content-Type: application/json
```

```json
{
  "current_server_time": "2017-06-02T10:54:20+0900",
  "message": "Success. Settings removed."
}
```

## 3. Sponsors

This project was a by-product of a company project. Thanks [MeshKorea](http://meshkorea.net/).

![MeshKorea](logo_meshkorea.png)

Open source version was created using IntelliJ IDE. Thanks [JetBrains](https://www.jetbrains.com/).

![JetBrains](logo_intellij.png)