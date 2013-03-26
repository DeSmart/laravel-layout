Make your controller to manage site structure.

# Installation
Add `desmart\laravel-layout` as a requirement to composer.json:

```json
{
  "require": {
    "desmart\laravel-layout": "dev-master"
  }
}
```

Update your packages with `composer update` or install with `composer install`.

# Example

*app/controllers/HomeController.php*:

```php
<?php
class HomeController extends \DeSmart\Layout\Controller {

  protected $layout = 'homepage';
  
  protected $structure = array(
    'left' => array(
      'App\Menu@showMainCategories',
      'App\Facebook@showLikeBox',
    ),
    
    'main' => array(
      'App\Banners@showHomeBanner'
    ),
  );
  
  public function show() {
    return $this->dispatch();
  }

}
```

*app/views/homepage.blade.php*:

```
<html>
<head></head>
<body>
  <div class="left" id="menu">
    {{ $left }}
  </div>
  <div id="main">
    {{ $main }}
  </div>
</body>
</html>
```

*app/routes.php*:

```php
Route::get('/', 'HomeController@show');
```

# Limits

Methods declared in `$structure` can't accept arguments from route parameters. 
It can be done with: `\Route::getCurrentRoute()->getParametersWithoutDefaults()`.

# Warning

This package is provided *as is*. For know there're no tests because concept of this package may change during development.

Just treat it as early alpha version.
