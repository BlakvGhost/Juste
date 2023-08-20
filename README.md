# Juste

Juste is the Core of Project [Bravo, my personal PHP MVC framework](https://github.com/BlakvGhost/Bravo), it essentially ensures the understanding of Models, Controller, the basic template engine.
It also manages the Routing, the middlewares, the database, and especially my super nice homemade mini ORM without forgetting the easy sending of emails.

![Packagist Version (custom server)](https://img.shields.io/packagist/v/Blakvghost/Juste?label=stable)
![Packagist Version (custom server)](https://img.shields.io/packagist/l/Blakvghost/Juste?label=Licence)
![Packagist Version (custom server)](https://img.shields.io/packagist/dt/Blakvghost/Juste?label=download)

## Documentation:

Documentation for Bravo is currently being prepared and will be available soon. Stay tuned for updates!

## How to use:

To see an example of using Bravo, you can refer to the [Bravo](https://github.com/BlakvGhost/Bravo) project. It serves as a demonstration project and will have official documentation soon.

## Installation

To install Juste, you can follow these steps:

1. Require the package using Composer by running the following command:

    ```sh
    composer require blakvghost/juste
    ```
2. Once the package is installed, you can start integrating Juste into your PHP project.

## Requirements

- PHP 8.0 or higher

Please note that Juste requires the following packages as dependencies: `symfony/dotenv (version 6.2 or higher)` and `symfony/mailer (version 6.2 or higher)`. These dependencies will be automatically installed when you install Juste using Composer.

## Features
- Models: Define your data models and interact with the database using Juste's mini ORM.
- Controllers: Implement your application's logic and handle user requests.
- Routing: Define routes and map them to specific controller actions.
- Middlewares: Apply custom logic to incoming requests and modify the request or response.
- Database: Easily perform database operations using Juste's ORM.
- Template Engine: Utilize the basic template engine provided by Juste for rendering views.
- Email Sending: Simplify the process of sending emails within your application.

## Utility Functions

Juste provides some utility functions that you can use in your application:

### Common Facade

The `Common` facade includes various utility functions:

- `posts()`: Returns an array with all the contents of the global $_POST variable escaped with htmlentities().
- `server(string $key)`: Returns the value of the given key from the global $_SERVER array.
- `input(string $key, string $default = '')`: Returns the value of the given key from either the global $_POST or $_GET variables, with the option to specify a default value if the key is not set.
- `file(string $key)`: Returns the file uploaded with the given key from the global $_FILES variable or redirects back to the previous page with an error message if no file was uploaded.
- `redirectTo(string $path = '')`: Redirects the user to the given path using the header() function.
- `redirecTo(string $path = '')`: Redirects the user to the given path using the header() function (typo in the code, corrected as redirectTo).
- `sanitize_post(string $key, bool $strict = true)`: Returns the sanitized value of the given key from the global $_POST variable, with the option to validate that the key exists and is not empty.
- `back()`: Redirects the user back to the previous page.
- `with(string $message, $key = 'error')`: Sets a message on the session with the given key (defaulting to 'error').
- `json(array $data)`: Returns a JSON-encoded string of the given array.
- `user($attr = false)`: Returns current authenticated user information or an empty array if not authenticated.
- `store_media($file, string $newFileName)`: Stores a media file with the given name and returns the file - path or redirects back with an error message if the file upload fails.
- `setDataOnSession($key, $message)`: Sets data on the session with the given key and message.
- `getDataOnSession($key)`: Retrieves data from the session using the given key.
- `setErrorMessageOnSession($message)`: Sets an error message on the session.
- `dd($value, ...$args)`: Displays the value and additional arguments using var_dump() within a <\pre> tag and exits.
- `route(string $alias)`: Returns the route URL for a given alias.
- `redirect(string $alias)`: Redirects the user to the route URL for a given alias.

### Controller Facade

The Controller facade includes some useful functions for controller classes:

- `render($view, $title = '', $context = null)`: Returns the full view path or a 404 view path with the context data. It checks whether the file exists and returns an appropriate response.
- `html(string $html)`: Returns an array with full HTML code, useful for returning HTML responses.
- `can(array $user_type = null, string $column = 'roles')`: Checks whether the user is authenticated and has the specified user types/roles. Redirects back with an error message if the user doesn't have the required role.
- `mustAuthenticate(bool $statut = true)`: Checks whether the user is authenticated based on the provided status. Redirects with an error message if the authentication status is not met.

## Usage Examples

### Routing

```php
<?php

namespace Routes;

use App\Controllers\WelcomeController;
use Juste\Facades\Routes\Route;

Route::get("/", [WelcomeController::class, 'welcome'])->name('welcome');
Route::resource('password', WelcomeController::class);


Route::group(function () {
    
})->middlewares(['auth']);

require_once 'api.php';

```

### API Route

```php
<?php

namespace Routes;

use App\Controllers\MailsController;
use Juste\Facades\Routes\Route;

Route::post('api/mails', [MailsController::class, 'index'])->name('api')->middlewares(['cors']);
```

### Middleware

```php
<?php

namespace App\Middleware;

use Juste\Http\Middleware\MiddlewareInterface;
use Juste\Facades\Controllers\Controller as Helpers;

class Authenticate extends Helpers implements MiddlewareInterface
{

    public function handle(): mixed
    {
        if (!$this->user()) {
            return $this->redirect('login');
        }
        return 1;
    }
}
```

### Model

```php
<?php

namespace App\Models;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['nom', 'prenom', 'email', 'password'];
}
```

### Controller

```php
<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Juste\Facades\Mails\JusteMailer;

class MailsController extends Controller
{
    public function __construct()
    {
        $this->mustAuthenticate(false);
    }

    public function index()
    {
        $mail = new JusteMailer();

        $object = [
            'to' => 'dev@kabirou-alassane.com',
            'subject' => 'Message d\'un potentiel client',
        ];

        $data = [
            'name' => $this->input('name', "Anonymous"),
            'email' => $this->input('email', "anonymous@anonymous.com"),
            'subject' => $this->input('subject', "Anonyme"),
            'message' => $this->input('message', "Anonyme"),
        ];

        $mail->view('mails/contact', $data)->sendEmail($object);
        return $this->back();
    }
}
```


## Authors

- [Kabirou ALASSANE](https://github.com/BlakvGhost)


## Support

For support, you can reach out to me by email at <dev@kabirou-alassane.com>. Feel free to contact me if you have any questions or need assistance with Bravo.

## License

This project is licensed under the MIT License.
