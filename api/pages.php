<?php
/*
 * Prediction League - pages and the menu.
 *
 * Admins write pages (About, Rules, Prizes...) in a simple text format that
 * the browser turns into HTML (headings, bold, links, lists, pictures, plus
 * live blocks such as [[table]] and [[next-match]]). Any page can be the home
 * page, and the menu can show the site's own sections and any pages, in any
 * order.
 */

declare(strict_types=1);

if (!defined('PL_APP')) {
  http_response_code(403);
  exit;
}

const PAGE_IMAGE_DIR = 'uploads/pages';
const PAGE_MAX_BYTES = 200000;
/** The site's own sections that can go in the menu, with their default labels. */
const BUILTIN_MENU = [
  'table' => 'Table', 'standings' => 'League tables', 'leagues' => 'Leagues', 'predictions' => 'Predict',
  'results' => 'Results', 'messages' => 'Messages', 'help' => 'How to play',
];
const DEFAULT_MENU = ['table', 'standings', 'leagues', 'predictions', 'results', 'messages'];

function page_routes(): array {
  return [
    'page'              => ['GET',  'route_page'],
    'admin.pages'       => ['GET',  'route_admin_pages'],
    'admin.page.save'   => ['POST', 'route_admin_page_save'],
    'admin.page.delete' => ['POST', 'route_admin_page_delete'],
    'admin.menu.save'   => ['POST', 'route_admin_menu_save'],
    'admin.page.image'  => ['POST', 'route_admin_page_image'],
  ];
}

function all_pages(): array {
  static $rows = null;
  if ($rows === null) {
    $rows = with_table('pages', fn() => q('SELECT * FROM ' . xt('pages') . ' WHERE lid = ? ORDER BY title', [lid()])->fetchAll());
  }
  return $rows;
}

function page_brief(array $p): array {
  return ['id' => (int)$p['pageid'], 'slug' => $p['slug'], 'title' => clean_text($p['title']),
          'status' => $p['status'], 'audience' => $p['audience'], 'is_home' => (bool)(int)$p['is_home'],
          'updated' => $p['updated']];
}

function page_visible(array $p, ?array $user): bool {
  if ($user && $user['is_admin']) return true;
  if ($p['status'] !== 'published') return false;
  return $p['audience'] === 'all' || $user !== null;
}

/** Saved menu: list of 'builtin:<key>' / 'page:<id>'. Null when never saved. */
function saved_menu(): ?array {
  $rows = with_table('menu', fn() => q('SELECT item FROM ' . xt('menu') . ' WHERE lid = ? ORDER BY position', [lid()])->fetchAll(PDO::FETCH_COLUMN));
  return $rows ? $rows : null;
}

/** The menu as visitors see it: only pages they can open. */
function public_menu(): array {
  $items = saved_menu() ?? array_map(fn($k) => "builtin:$k", DEFAULT_MENU);
  $pages = [];
  foreach (all_pages() as $p) $pages[(int)$p['pageid']] = $p;
  $user = current_user();
  $out = [];
  foreach ($items as $item) {
    [$type, $key] = explode(':', $item, 2) + [1 => ''];
    if ($type === 'builtin' && isset(BUILTIN_MENU[$key])) {
      $out[] = ['type' => 'builtin', 'key' => $key, 'label' => BUILTIN_MENU[$key]];
    } elseif ($type === 'page' && isset($pages[(int)$key])) {
      $p = $pages[(int)$key];
      $public = $p['status'] === 'published' && ($p['audience'] === 'all' || $user);
      if ($public) $out[] = ['type' => 'page', 'slug' => $p['slug'], 'label' => clean_text($p['title'])];
    }
  }
  return $out;
}

/** Slug of the home page, if one is set and this visitor can see it. */
function home_page_slug(): ?string {
  foreach (all_pages() as $p) {
    if ((int)$p['is_home'] && $p['status'] === 'published' && ($p['audience'] === 'all' || current_user())) return $p['slug'];
  }
  return null;
}

function route_page(): array {
  $slug = (string)($_GET['slug'] ?? '');
  $user = current_user();
  foreach (all_pages() as $p) {
    if ($p['slug'] !== $slug) continue;
    if (!page_visible($p, $user)) {
      if ($p['status'] === 'published' && !$user) fail(401, 'Log in to see this page.');
      break;
    }
    return ['page' => page_brief($p) + ['body' => (string)$p['body']]];
  }
  fail(404, 'That page doesn\'t exist.');
  return [];
}

function make_slug(string $s): string {
  $s = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s), '-'));
  return substr($s, 0, 60);
}

function route_admin_pages(): array {
  require_admin();
  $menu = saved_menu() ?? array_map(fn($k) => "builtin:$k", DEFAULT_MENU);
  $pages = array_map(fn($p) => page_brief($p) + ['body' => (string)$p['body'], 'in_menu' => in_array('page:' . $p['pageid'], $menu, true)], all_pages());
  // Everything that could be in the menu, in menu order, then the rest.
  $items = [];
  $byId = [];
  foreach ($pages as $p) $byId[$p['id']] = $p;
  foreach ($menu as $item) {
    [$type, $key] = explode(':', $item, 2) + [1 => ''];
    if ($type === 'builtin' && isset(BUILTIN_MENU[$key])) $items[] = ['item' => $item, 'label' => BUILTIN_MENU[$key], 'shown' => true, 'kind' => 'Site section'];
    if ($type === 'page' && isset($byId[(int)$key])) $items[] = ['item' => $item, 'label' => $byId[(int)$key]['title'], 'shown' => true, 'kind' => 'Page', 'status' => $byId[(int)$key]['status']];
  }
  foreach (BUILTIN_MENU as $k => $label) {
    if (!in_array("builtin:$k", $menu, true)) $items[] = ['item' => "builtin:$k", 'label' => $label, 'shown' => false, 'kind' => 'Site section'];
  }
  foreach ($pages as $p) {
    if (!$p['in_menu']) $items[] = ['item' => 'page:' . $p['id'], 'label' => $p['title'], 'shown' => false, 'kind' => 'Page', 'status' => $p['status']];
  }
  return ['pages' => $pages, 'menu' => $items, 'images' => function_exists('imagecreatefromstring')];
}

function save_menu(array $items): void {
  q('DELETE FROM ' . xt('menu') . ' WHERE lid = ?', [lid()]);
  foreach (array_values($items) as $i => $item) {
    q('INSERT INTO ' . xt('menu') . ' (lid, position, item) VALUES (?, ?, ?)', [lid(), $i, $item]);
  }
}

function route_admin_page_save(): array {
  require_admin();
  $b = body();
  $id = (int)($b['id'] ?? 0);
  $title = trim(preg_replace('/\s+/u', ' ', str_in($b, 'title', 100)));
  if ($title === '') fail(422, 'Give the page a title.');
  $slug = make_slug((string)($b['slug'] ?? '') ?: $title);
  if ($slug === '') fail(422, 'The page address needs some letters or numbers.');
  $body = str_replace("\r\n", "\n", (string)($b['body'] ?? ''));
  if (strlen($body) > PAGE_MAX_BYTES) fail(422, 'That page is too long (the limit is about 200,000 characters).');
  $status = ($b['status'] ?? '') === 'draft' ? 'draft' : 'published';
  $audience = ($b['audience'] ?? '') === 'players' ? 'players' : 'all';
  $home = !empty($b['is_home']);
  $inMenu = !empty($b['in_menu']);
  if ($home && $status === 'draft') fail(422, 'Publish the page before making it the home page.');

  with_table('pages', fn() => q('SELECT 1 FROM ' . xt('pages') . ' WHERE 1 = 0'));
  with_table('menu', fn() => q('SELECT 1 FROM ' . xt('menu') . ' WHERE 1 = 0'));
  $clash = q('SELECT pageid FROM ' . xt('pages') . ' WHERE lid = ? AND slug = ? AND pageid <> ?', [lid(), $slug, $id])->fetch();
  if ($clash) fail(409, "Another page already uses the address \"$slug\". Choose a different one.");

  $pdo = db();
  $pdo->beginTransaction();
  if ($home) q('UPDATE ' . xt('pages') . ' SET is_home = 0 WHERE lid = ?', [lid()]);
  if ($id) {
    if (!q('SELECT 1 FROM ' . xt('pages') . ' WHERE lid = ? AND pageid = ?', [lid(), $id])->fetch()) fail(404, 'That page no longer exists.');
    q('UPDATE ' . xt('pages') . ' SET slug = ?, title = ?, body = ?, status = ?, audience = ?, is_home = ?, updated = ?, updated_by = ? WHERE pageid = ?',
      [$slug, $title, $body, $status, $audience, $home ? 1 : 0, now_str(), current_user()['id'], $id]);
  } else {
    q('INSERT INTO ' . xt('pages') . ' (lid, slug, title, body, status, audience, is_home, updated, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
      [lid(), $slug, $title, $body, $status, $audience, $home ? 1 : 0, now_str(), current_user()['id']]);
    $id = (int)$pdo->lastInsertId();
  }
  $menu = saved_menu() ?? array_map(fn($k) => "builtin:$k", DEFAULT_MENU);
  $has = in_array("page:$id", $menu, true);
  if ($inMenu && !$has) save_menu(array_merge($menu, ["page:$id"]));
  if (!$inMenu && $has) save_menu(array_values(array_diff($menu, ["page:$id"])));
  $pdo->commit();
  log_event('page', ($b['id'] ?? 0) ? "Changed the page $title" : "Added the page $title");
  return ['id' => $id, 'slug' => $slug];
}

function route_admin_page_delete(): array {
  require_admin();
  $id = (int)(body()['id'] ?? 0);
  $p = with_table('pages', fn() => q('SELECT title FROM ' . xt('pages') . ' WHERE lid = ? AND pageid = ?', [lid(), $id])->fetch());
  if (!$p) fail(404, 'That page no longer exists.');
  q('DELETE FROM ' . xt('pages') . ' WHERE lid = ? AND pageid = ?', [lid(), $id]);
  $menu = saved_menu();
  if ($menu !== null) save_menu(array_values(array_diff($menu, ["page:$id"])));
  log_event('page', 'Deleted the page ' . clean_text($p['title']));
  return ['deleted' => $id];
}

function route_admin_menu_save(): array {
  require_admin();
  $items = body()['items'] ?? null;
  if (!is_array($items)) fail(422, 'No menu was sent.');
  $pageIds = array_map(fn($p) => (int)$p['pageid'], all_pages());
  $clean = [];
  foreach ($items as $item) {
    $item = (string)$item;
    [$type, $key] = explode(':', $item, 2) + [1 => ''];
    $ok = ($type === 'builtin' && isset(BUILTIN_MENU[$key])) || ($type === 'page' && in_array((int)$key, $pageIds, true));
    if ($ok && !in_array($item, $clean, true)) $clean[] = $type === 'page' ? 'page:' . (int)$key : $item;
  }
  with_table('menu', fn() => q('SELECT 1 FROM ' . xt('menu') . ' WHERE 1 = 0'));
  // An empty saved menu would mean "default", so keep a marker that hides everything.
  save_menu($clean ?: ['none:']);
  log_event('menu', 'Changed the menu');
  return route_admin_pages();
}

/** Pictures for pages: checked, resized to at most 1600px wide and re-saved. */
function route_admin_page_image(): array {
  require_admin();
  if (!function_exists('imagecreatefromstring')) fail(500, 'The server can\'t process pictures (the PHP GD extension is missing).');
  $f = uploaded_file('image', 8 * 1048576, 'picture');
  $info = @getimagesize($f['tmp_name']);
  $allowed = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP];
  if (!$info || !in_array($info[2], $allowed, true)) fail(422, 'Upload a JPEG, PNG, GIF or WebP picture.');
  [$w, $h] = $info;
  if ($w * $h > 40000000) fail(422, 'That picture is too large to process. Use one under 40 megapixels.');
  $src = @imagecreatefromstring((string)file_get_contents($f['tmp_name']));
  if (!$src) fail(422, 'That picture couldn\'t be read. Try saving it as a JPEG or PNG.');
  if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
    $exif = @exif_read_data($f['tmp_name']);
    $rotate = [3 => 180, 6 => -90, 8 => 90][(int)($exif['Orientation'] ?? 1)] ?? 0;
    if ($rotate && ($r = imagerotate($src, $rotate, 0))) { imagedestroy($src); $src = $r; $w = imagesx($src); $h = imagesy($src); }
  }
  $nw = min(1600, $w);
  $nh = max(1, (int)round($h * $nw / $w));
  $dst = imagecreatetruecolor($nw, $nh);
  $png = $info[2] !== IMAGETYPE_JPEG;  // keep transparency for PNG/GIF/WebP
  if ($png) {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
  }
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
  imagedestroy($src);
  $dir = dirname(__DIR__) . '/' . PAGE_IMAGE_DIR;
  if (!is_dir($dir)) @mkdir($dir, 0755, true);
  if (!is_dir($dir) || !is_writable($dir)) {
    imagedestroy($dst);
    fail(500, 'The server can\'t save pictures yet. Make the uploads/pages folder writable.');
  }
  $name = 'p' . date('Ymd') . '-' . bin2hex(random_bytes(6)) . ($png ? '.png' : '.jpg');
  $saved = $png ? imagepng($dst, "$dir/$name", 6) : imagejpeg($dst, "$dir/$name", 85);
  imagedestroy($dst);
  if (!$saved) fail(500, 'The picture couldn\'t be saved. Try again.');
  @chmod("$dir/$name", 0644);
  log_event('page_image', "Uploaded a page picture ($name)");
  return ['url' => PAGE_IMAGE_DIR . '/' . $name, 'width' => $nw, 'height' => $nh];
}
