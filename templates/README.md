# Page Templates

Page controllers keep authentication, request handling, and data preparation. They load `templates/render.php` and call `renderPage()` with a layout and a content template:

```php
require "templates/render.php";
renderPage("public", array(
  "title" => "Results",
  "content" => "match-results.php"
));
```

Available layouts:

- `public`: league navigation, optional login panel, matchday, stats, and prize regions.
- `admin`: the dashboard layout without the login panel; set `setup_menu` for the setup navigation.
- `setup`: setup navigation without match stats or matchday display.
- `standalone`: a centered content page without dashboard rails.

Content files live under `templates/pages/`. Use `scripts` to attach page-specific JavaScript and `data` to pass page-specific values into the content template. Keep database and authorization logic in the controller/functions, and keep page markup in the content template.

The home, standings, results, predictions, messaging, user list, help, password reminder, icon selection, release notes, logfile viewer, configuration, and database setup pages currently use this template path. Other legacy pages can be migrated by moving their page markup into a content template and replacing the document shell with `renderPage()`.