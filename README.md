Controller which defines page structure.

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

In `app/config/app.php` add `'DeSmart\Layout\LayoutServiceProvider',` to providers and `'Layout'          => 'DeSmart\Layout\Facades\Layout',` to aliases.

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
  
  /**
   * Just show main page
   */
  public function show() {
    return $this->dispatch();
  }

  /**
   * Show products in main block
   */
  public function showTopProducts() {
    $this->structure['main'] = array(
      'App\Products@showTopProducts',
    );

    return $this->dispatch();
  }

}
```

*app/views/homepage.blade.php*:

```html
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
<?php
Route::get('/products', 'HomeController@showTopProducts');
Route::get('/', 'HomeController@show');
```

# Limits

* Methods declared in `$structure` can't accept arguments from route parameters. 
  It can be done with: `\Route::getCurrentRoute()->getParametersWithoutDefaults()`.
* Probably it's possible to pass method from controller in `$structure` but it won't work fully as expected (no controller magic (like filters etc) will happen)

# Warning

This package is provided *as is*. For now it's only a concept and the whole idea can change. 
Also there're no unit tests.

Just treat it as early alpha version.
