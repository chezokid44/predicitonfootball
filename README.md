# Prediction League: HTML/JS front end + PHP API

Version history: see [CHANGELOG.md](CHANGELOG.md). Earlier versions, including
the original PHP script, are in the commit history.

The pages are plain HTML and JavaScript. They talk to a small PHP JSON API
(`api/`) that reads and writes the same MySQL tables as the old Prediction
Football script, so an existing league's players, fixtures, predictions and
settings carry over.

## What's included

- League table (calculated exactly like the old script), next match
- Predictions: enter scores, change them until kick-off, see your points
- Results, match pages with everyone's predictions, player pages
- Log in, join the league, change email or password
- Admin: add fixtures (several at once), edit, delete, enter results
  (table updates straight away), manage players and admins, settings
- Private messages between players, with replies, an unread count and an
  admin message to everyone (uses the old script's `plmsgdata` table)
- Player pictures: uploaded, cropped to a square and re-saved by the server
- Forgotten passwords: players get a reset link by email, or an admin creates
  a link to send them
- Admin > Data: full backup and restore, CSV downloads, and adding fixtures
  from a spreadsheet
- Admin > Activity: log-ins, failed log-ins, sign-ups and every admin change
- Email choices: players opt in to emails from the admin and to an email when
  they get a private message (both off by default); an admin's message to
  everyone can also be emailed to players who opted in
- "How to play" help page, using the league's own scoring settings
- Everyone's predictions for a day's matches side by side (Results >
  Everyone's predictions by day)
- Predictions close 30 minutes before kick-off (`PRED_CUTOFF_MINS` in
  Settings; 0 means at kick-off). Everyone's predictions are shown once they
  close. Results and the table still go by the actual kick-off time.
- Automatic predictions: players can switch on "Predict for me if I forget" on
  their account page. When predictions close on a match they haven't
  predicted, one is made from both teams' recent form (last 6 results, weighted
  to the most recent: goals for and against, points per game, home advantage)
  and marked Auto, for the competitions they play. Admins can turn the option
  off with `AUTOPRED_ON`.
- Enter several results at once (Admin > Results > Save all results)
- Competitions (Admin > Competitions): put fixtures into competitions such as a
  league and its cups. Players filter the Predict and Results pages by
  competition and group them by date or by competition; matches with the same
  kick-off are grouped under one time. Fixtures can be given a competition when
  added, edited or imported (a "Competition" CSV column; new names are added).
- Leagues (the Leagues page): one account plays everywhere, and each match is
  predicted once and counts in every table that includes it.
  - *Competition tables.* Every competition has its own table next to the
    overall one. Players choose which competitions they play (all by default);
    Predict and Results show those, and they only appear in those tables.
  - *Private leagues.* Any player can create one: pick the competitions it
    covers, its own points for an exact score and a right result, and when it
    starts counting (now, a date, or every match). It gets an invite code and
    link; only people with the code can join. The creator can remove players,
    hand the league over, change it, make a new code or delete it. Admins see
    every league in Admin > Leagues. Settings: `LEAGUES_ON`,
    `MAX_LEAGUES_PER_PLAYER` (5), `MAX_LEAGUE_MEMBERS` (100). Ten wrong invite
    codes from one address in 10 minutes pause joining for a while.
- Pages (Admin > Pages): write your own pages, such as a welcome page, the
  rules or prizes, with a live preview. Pages use simple formatting (headings,
  bold, links, lists, quotes, pictures uploaded from the editor) and live blocks
  that keep themselves up to date: `[[table 10]]`, `[[next-match]]`,
  `[[results 5]]`, `[[join]]` (log-in buttons for visitors) and
  `[[button Text|#/predictions]]`. A page can be a draft, for logged-in players
  only, and the home page (the league table then moves to Table in the menu).
- The menu (Admin > Pages > Menu): show or hide the site's own sections (Table,
  Leagues, Predict, Results, Messages, How to play) and your pages, in any
  order. Admin and the account link are always shown.
- Seasons (Admin > Seasons): a season is a run of dates, such as 2026/27 from
  1 August to 31 July, and each fixture belongs to the season its kick-off
  falls in. One season is marked current. Players pick a season, or All time,
  on the Table, competition and private league tables, the Leagues page and
  Results; they see the current season unless they choose another. Predict
  always shows matches still open. Seasons can't overlap; deleting one keeps
  its fixtures.
- Teams (Admin > Teams): the list of clubs and countries, each with a short
  name and an optional badge (cropped to a square PNG and shown next to the
  team in fixtures, results and the next match). For each season, tick which
  teams are in each competition, so Leeds can be in the Championship one season
  and the Premier League the next. A fixture added to a competition must then
  use a team from that season's list (if it has one), and team names are
  corrected to the saved spelling. "Add them" fills the list from existing
  fixtures, and "Fill from this season's fixtures" puts teams into the
  competitions they played in. Renaming a team renames it in its fixtures, so
  automatic predictions still recognise it. Fixtures still store team names,
  as in the old script.
- League tables (League tables in the menu): the real table for each
  competition, worked out from the results entered: played, won, drawn, lost,
  goals, goal difference, points (3 for a win, 1 for a draw) and the last five
  results. One table per season (players pick the season); teams in the
  competition's team list appear before they've played. In Admin >
  Competitions, set a competition's type to League or Cup (cups have no table)
  and what the positions mean: any number of lines, each a range of positions
  counted from the top or the bottom, with a label and a colour (for example
  1st Champions in yellow, 2nd to 4th Champions League in green, 5th Europa
  League in blue, bottom 3 Relegation in red). Presets fill in the usual
  Premier League, Championship and League Two lines. The table shows a coloured
  bar for each position and a key underneath; where lines overlap, the first
  one wins. Pages can show a
  small table with `[[standings PL]]`. Existing sites with a saved menu can
  add "League tables" in Admin > Pages > Menu.
- A web installer for new servers

## Files

```
index.php                the page (adds version numbers to app.js/app.css so
                         browsers pick up new versions straight away)
assets/app.js, app.css   front end and styles (light and dark mode)
api/index.php            the API: the only file in api/ the browser calls
api/lib.php              shared functions (refuses direct requests)
api/features.php         messaging, pictures, resets, log, backup, CSV
api/mailer.php           sending email (PHP mail() or SMTP)
api/alerts.php           the daily email of blocked addresses
api/leagues.php          competition tables and private leagues
api/pages.php            admin-written pages and the menu
api/seasons.php          seasons, teams and team badges
api/standings.php        the real league tables worked out from results
api/config.sample.php    example settings (the installer writes api/config.php)
install/index.php        the installer (locks itself once set up)
install/installer.php    installer logic (refuses direct requests)
hp/, wp-login.php, xmlrpc.php   bot traps (see Security)
robots.txt               keeps search engines out of the trap and the API
sql/upgrade.sql          optional: manual upgrade of an old database
uploads/icons/           player pictures (must be writable by PHP)
uploads/pages/           pictures used in pages (must be writable by PHP)
uploads/badges/          team badges (must be writable by PHP)
```

## Install on a new server

1. In your hosting panel, create an empty MySQL or MariaDB **database and user**.
2. Upload everything in this zip to the site's web root, so `index.php` is at the top.
   On CloudPanel that is `/home/<site-user>/htdocs/<domain>/`.
   If an old script is there, move it away first. If you installed an earlier
   version of this site, delete its `index.html`.
3. Open `https://<your-domain>/install/` and fill in:
   - the database host, name, user and password,
   - the league name, time zone and site address,
   - whether the site is behind Cloudflare,
   - your admin username, email and password.
4. The installer creates the tables and default settings, creates your admin
   account, creates `uploads/icons/` and saves `api/config.php`. It then locks
   itself.
5. **Delete the `install` folder** from the server.

If the server won't let PHP write `api/config.php`, the installer shows the
file's contents for you to create by hand.

## Switching pl.my-serv.uk over from the old script

Use the installer with your **existing** database details (from the old
`systemvars.php`: `$dbaseHost`, `$dbaseName`, `$dbaseUsername`,
`$dbasePassword`) and **leave the admin fields empty**. It keeps every table and
row, adds only what's missing, widens the password column for bcrypt and keeps
your existing admin account. Back up the database first (phpMyAdmin > Export).

Old MD5 passwords keep working and are upgraded to bcrypt the next time each
player logs in.

## Security

**Private PHP files can't be opened directly.** `api/lib.php`,
`api/config.php` and `install/installer.php` stop with an empty 403 unless
they are loaded by the app itself.

**The API only answers the site's own pages.** `api/index.php` rejects a
request when:
- it's opened from the address bar, a bookmark or a link,
- it comes from another website (checked with the browser's `Sec-Fetch-Site`
  and `Origin` headers, or `Referer` on older browsers),
- it lacks the app's `X-PL-Client` header,
- or, for anything other than starting a session, it lacks that session's
  token.

Other websites also can't read the responses, because the API sends no CORS
headers.

A limit to be aware of: the JavaScript runs in each visitor's browser, so
someone determined can copy those requests into a script of their own. The
checks above stop casual and cross-site use. What protects the data is the
server side: logins, the admin checks on every admin action, prediction locking
at kick-off, and input validation. Turn the request check off only if a proxy in
front of the site strips these headers: `'strict_client_check' => false` in
`api/config.php`.

**Bots are kept away from log-in, sign-up and password reset** (Settings >
Rules: `BOT_CHECK`, `BOT_POW_BITS`, `BOT_BLOCK_HOURS`):
- *Honeypot field.* Each form has a field people never see (off-screen, skipped
  by the keyboard and screen readers). A bot that fills it in gets a
  normal-looking failure and its address is blocked.
- *Trap pages.* A hidden link to `hp/` (which `robots.txt` tells well-behaved
  crawlers to skip), plus `wp-login.php` and `xmlrpc.php`, the addresses attack
  bots probe on every site. Visiting one blocks the address.
- *Puzzle.* While someone fills in the form, the browser solves a small
  proof-of-work puzzle (about a quarter of a second on a computer). Each puzzle
  works once, can't be answered in under a second, and gets harder for
  addresses with recent failed log-ins. Ten failed checks block the address.
- *Strikes and permanent bans.* Every block is a strike, and so is each try
  from an address while it's blocked (a trap page, or sending the log-in,
  sign-up or reset form; at most one strike every 10 minutes). At
  `BAN_STRIKES` strikes (5 by default; 0 turns automatic bans off) the address
  is banned permanently. With `BAN_WHOLE_SITE` on (the default) a banned
  address gets nothing from the site at all; off, it's only kept out of
  log-in, sign-up and resets. Strikes are forgotten after 30 days without a
  block. In Admin > Activity you can ban any address by hand, ban a blocked
  one, or lift a ban (which clears its strikes). "Lift every temporary block"
  leaves bans alone. Your own address, and any in `never_block_ips`, can't be
  banned.
- A block lasts `BOT_BLOCK_HOURS` (24 by default) and only stops log-in,
  sign-up and password resets; the rest of the site works. See and lift blocks
  in Admin > Activity. Put your own address in `never_block_ips` in
  `api/config.php` if you test the traps yourself. Locked out? Run
  `DELETE FROM plblocklist;` in phpMyAdmin.

**Uploaded pictures are rebuilt by the server.** Each upload is checked,
cropped and saved as a new PNG with a random name, so nothing hidden in the
original file survives.

**Extra layer for nginx (CloudPanel).** In CloudPanel open the site, then
**Vhost**, and add this **above** the `location ~ \.php$` block. It blocks every
file in `api/` except `index.php`, never runs scripts from `uploads/`, and
hides `sql/` and this README:

```nginx
location ~* ^/(api/(?!index\.php$)[^/]+|install/installer\.php|sql/|README\.md$) {
  deny all;
}
location ~* ^/uploads/.*\.(php\d?|phtml|phar)$ {
  deny all;
}
```

Apache sites get the same effect from the included `.htaccess` files.

## CloudPanel Varnish Cache

If **Varnish Cache** is switched on for the site in CloudPanel, it can store the
API's replies and hand every visitor the same session and security token, so
nobody can log in ("Your session has expired"). The app tells CloudPanel's
Varnish not to cache its pages, but the safest setup is either:

- **Turn Varnish off** for this site (Sites > your site > Varnish Cache). The app
  gains nothing from it: its pages are tiny and app.js/app.css are cached by
  browsers anyway. **Or**
- keep it and add these to **Excludes**: `^/api/`, `^/install/`, `^/hp/`,
  `^/index.php`, `^/$`, `wp-login.php`, `xmlrpc.php`.

Either way, then press **Purge Entire Cache**.

## Email

Password reset emails and the admin's "Email it" button need email set up in
**Admin > Settings > Email**:

- **From address**: an address on your own domain, e.g. `league@yourdomain.co.uk`.
- **How to send**: *Server mail function* uses PHP's `mail()`. Many servers,
  including most CloudPanel ones, aren't set up to deliver mail that way, so
  **SMTP server** is usually more reliable. Use your email provider's SMTP
  details (for example port 587 with TLS). Gmail and Microsoft 365 need an
  app password.
- Press **Send a test email**. If it fails, the message says why, and failures
  also appear in Admin > Activity.

**Daily block alerts** (Admin > Settings > Block alerts) email you a list of
the addresses blocked by the bot traps, honeypot fields and failed bot checks:
one email a day at most, and none on days with no new blocks. They use their
own SMTP account (`ALERT_FROM`, `ALERT_SMTP_HOST`, `ALERT_SMTP_PORT`,
`ALERT_SMTP_SECURE`, `ALERT_SMTP_USER`, `ALERT_SMTP_PASS`), never the main email
settings, so alerts and players' emails stay separate. They go to
`BLOCK_ALERT_TO`, or every admin with an email address if that's empty. The
list is sent after the first visit to the site from `BLOCK_ALERT_HOUR` (8 by
default), after the visitor's page has been delivered, so no cron job is
needed. If sending fails, it's logged in Admin > Activity and tried again an
hour later with the same list. "Send a test alert" checks the account, and
"Send the list now" sends whatever is new straight away.

Without email, players can't reset their own password. Admins can still create
a reset link on the Players page and send it to them.

## Admin access

Admins are players with `usertype` 4 or higher, as in the old script. To make
an existing player an admin:

```sql
UPDATE pluserdata SET usertype = 4 WHERE username = 'their-username';
```

## Notes

- **Times** are entered and shown in the time zone chosen at install
  (`timezone` in `api/config.php`). The old `TZ_OFFSET` setting isn't used.
- **Scoring** matches the old script: exact score = `CORR_SCORE` points, right
  result = `CORR_RESULT` points.
- The table is calculated live and also written to `plstandings` whenever a
  result, fixture, player or setting changes.
- **New tables.** The site adds `plactivitylog`, `plresettokens`,
  `pluserprefs` (email and automatic-prediction choices), `plcompetitions`,
  `plmatchcomp` (which competition each fixture is in), `plblocklist`
  (addresses blocked by the bot traps), `plleagues`, `plleaguecomps`,
  `plleaguemembers` (private leagues), `plfollows` (the competitions each
  player plays), `plpages`, `plmenu`, `plseasons`, `plteams` and
  `plcompteams` (the teams in each competition each season). They're created automatically the first time
  they're needed, by the installer, or by `sql/upgrade.sql`. The old tables
  aren't altered apart from the wider password column.
- **Automatic predictions** are filled in the next time anyone uses the site
  after predictions close, so no cron job is needed. They only use results from
  before the match, and only cover matches whose predictions closed after the
  player switched the option on, so switching it on late can't be used to gain
  an advantage. Team names are matched exactly (ignoring case), so keep each
  team's name the same across fixtures ("Man United" and "Manchester United"
  count as different teams).
- **Activity log** entries older than `LOG_DAYS` (default 365) are removed
  automatically. Ten failed log-ins from one IP address within 15 minutes
  pause log-ins from it for a while.
- **Behind Cloudflare?** Set `'client_ip_header' => 'HTTP_CF_CONNECTING_IP'`
  in `api/config.php` (the installer does this when you tick the box), so the
  log and the log-in limit see visitors' real addresses.
- **Restoring a backup** replaces every player, fixture, prediction, message and
  setting. The site downloads a backup of the current league first. Pictures
  aren't in the backup file; copy `uploads/icons/`, `uploads/pages/` and
  `uploads/badges/` separately if you move server.
- Errors go to the PHP error log. `'debug' => true` in `api/config.php` shows
  details in API responses while troubleshooting; turn it off afterwards.
- To run the installer again, delete `api/config.php` and upload the
  `install` folder again. Your data stays in the database.
