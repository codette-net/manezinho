# 📘 ** CMSOJ Lightweight PHP Framework – Documentation**

This document describes the core architecture of the lightweight PHP framework used for the Manezinho website, including routing, views, template engine, partials, components, and cache busting.

---

# 📁 **1. Project Structure**

```
manezinho/
│
├─ public/                   # Web root (only folder exposed to browser)
│   ├─ index.php             # Front controller (loads Router + Template + routes)
│   ├─ router.php            # Built-in PHP server router
│   └─ assets/
│       ├─ css/
│       ├─ js/
│       └─ img/
│
├─ CMSOJ/
│   ├─ Template.php          # Template engine
│   ├─ Router.php            # Router class
│   ├─ Routes/
│   │   ├─ web.php           # Frontend routes
│   │   └─ admin.php         # Admin routes
│   ├─ Views/                # Frontend views
│   │   ├─ layout.html
│   │   ├─ index.html
│   │   ├─ events.html
│   │   ├─ 404.html
│   │   ├─ partials/
│   │   │   ├─ nav.html
│   │   │   └─ footer.html
│   │   └─ components/
│   │       └─ card.html
│   └─ Admin/
│       └─ Views/            # Admin dashboard templates
│           ├─ dashboard.html
│           └─ login.html
│
└─ cache/                    # Compiled templates
```

---

# 🌐 **2. Routing System**

Routes are defined in:

```
CMSOJ/Routes/web.php
CMSOJ/Routes/admin.php
```

Each route is registered using:

```php
$router->get('events', function() {
    Template::view('CMSOJ/Views/events.html');
});
```

### **Dynamic parameters**

```php
$router->get('blog/{id}', function($id) {
    Template::view('CMSOJ/Views/blog.html', ['id' => $id]);
});
```

### **404 fallback**

If no route matches, Router sends:

```
CMSOJ/Views/404.html
```

---

# 🚦 **3. Front Controller**

`public/index.php` bootstraps the framework:

```php
require '../CMSOJ/Template.php';
require '../CMSOJ/Router.php';

$router = new Router();

require '../CMSOJ/Routes/web.php';
require '../CMSOJ/Routes/admin.php';

$router->dispatch();
```

---

# 🔌 **4. PHP Built-in Server Support**

Because `.htaccess` is not supported, a router file is used:

**public/router.php**

```php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // serve static file
}

require __DIR__ . '/index.php';
```

Run server:

```
php -S localhost:8000 router.php
```

---

# 🖼️ **5. Template Engine**

The custom engine supports:

| Feature              | Syntax                                   |
| -------------------- | ---------------------------------------- |
| Echo                 | `{{ variable }}`                         |
| Escaped echo         | `{{{ variable }}}`                       |
| PHP code             | `{% if ... %}`                           |
| Template inheritance | `{% extends 'layout' %}`                 |
| Blocks               | `{% block name %} ... {% endblock %}`    |
| Inserting blocks     | `{% yield name %}`                       |
| Partials             | `{% partial 'nav' %}`                    |
| Components           | `{% component 'card', { title: "X" } %}` |
| Cache busting        | `{{ "/assets/css/app.css" }}`            |

---

# 🧱 **6. Layout Inheritance**

### layout.html

```twig
<head>
  <title>Manezinho | {% yield title %}</title>
  {% yield meta %}
  {% yield css %}
</head>

<body>
  {% yield nav %}
  {% yield content %}
  {% yield scripts %}
  {% yield footer %}
</body>
```

### page.html

```twig
{% extends 'CMSOJ/Views/layout.html' %}

{% block title %}Events{% endblock %}
{% block meta %}@parent<meta name="description" content="...">{% endblock %}
{% block css %}@parent<link rel="stylesheet" href="/assets/css/calendar.css">{% endblock %}

{% block content %}
  {% partial 'nav' %}
  <h1>Events</h1>
{% endblock %}

{% block scripts %}
  @parent
  <script src="/assets/js/calendar.js"></script>
{% endblock %}
```

---

# 🧩 **7. Partials**

Reusable fragments inside:

```
CMSOJ/Views/partials/
```

Use in templates:

```twig
{% partial 'nav' %}
{% partial 'footer' %}
```

---

# 🧱 **8. Components**

Reusable UI elements with props.

### Example usage:

```twig
{% component 'card', { title: "Live Music", img: "/assets/img/ewi.jpg" } %}
```

### card.html example:

```html
<div class="card">
  <img src="{{ img }}" alt="{{ title }}">
  <h3>{{ title }}</h3>
</div>
```

---

# ⚡ **9. Cache Busting**

All `{{ "/assets/.../file" }}` paths become:

```
/assets/.../file?v=1698259387
```

where `1698259387` = filemtime().

### Usage

```html
<link rel="stylesheet" href="{{ "/assets/css/main.css" }}">
<script src="{{ "/assets/js/app.js" }}"></script>
<img src="{{ "/assets/img/photo.jpg" }}">
```

### How it works

`compileEchos()` is modified so every `{{ "/something" }}` is passed through:

```php
Template::asset("/something");
```

---

# 🗂️ **10. View Include (Extends + Includes)**

### Extending a layout:

```twig
{% extends 'CMSOJ/Views/layout.html' %}
```

### Including a raw file inside content:

```twig
{% include 'CMSOJ/Views/somefile.html' %}
```

---

# 🧹 **11. Cache Directory**

Compiled templates are stored in `/cache/`.

Clear cache:

```php
Template::clearCache();
```

Or delete files inside `/cache`.

---

# 🧰 **12. File Path Resolving (Important)**

Because views are outside `/public`, paths must be resolved manually.

Template engine now resolves paths using:

```php
dirname(__DIR__) . '/' . $file
```

This ensures:

* PHP built-in server works
* XAMPP works
* Apache/Nginx work

---

# 🔒 **13. Admin Routes Structure**

Admin dashboard templates live in:

```
CMSOJ/Admin/Views/
```

Routes declared in:

```php
$router->get('admin', function() {
    Template::view('CMSOJ/Admin/Views/dashboard.html');
});
```
