# Changelog

Each version is tagged in git (`git checkout v2.0.0`, or see the repository's
Tags page). Versions 1.x are the original PHP script; 2.x is the rewrite with
an HTML/JavaScript front end and a PHP + MySQL API, which still uses the
original database tables.

## v2.0.0 (10 November 2026)

The rewrite and every feature added during its development. The stages below
were built one after another, but only the finished code was kept, so they
share this one tagged version.

**Rewrite**
- Plain HTML and JavaScript pages talking to a JSON API in `api/`; the same
  MySQL tables as the old script, so players, fixtures and predictions carry
  over. Old MD5 passwords upgrade to bcrypt at the next log-in.
- The API only answers the site's own pages (client header, same-origin
  checks, per-session token); private PHP files refuse direct requests.
- Web installer for new servers (`install/`), which locks itself afterwards.
- `index.php` adds version numbers to the CSS and JavaScript so browsers pick up
  new versions; tells CloudPanel's Varnish not to cache.

**Features added in order**
1. Private messages, player pictures, password reset by email, backup and
   restore, CSV import and export, activity log, email choices.
2. Everything still missing from the old `TODO.txt`: help page, everyone's
   predictions by day, saving several results at once.
3. Competitions: filter fixtures by competition, group by date or competition,
   matches with the same kick-off grouped together.
4. Predictions close 30 minutes before kick-off (`PRED_CUTOFF_MINS`);
   automatic predictions from team form for players who opt in.
5. Bot protection for log-in, sign-up and resets: honeypot field, trap pages,
   proof-of-work puzzle, address blocking.
6. SMTP email sending, with test email.
7. Leagues: one account everywhere; players choose the competitions they play;
   a table per competition; private leagues with invite codes, their own
   scoring and start date.
8. Pages: admin-written pages with live blocks, any page as the home page,
   an editable menu.
9. Seasons and teams: tables and results per season, team badges, team lists
   for each competition each season; fixture forms only suggest a
   competition's teams.
10. Daily email of newly blocked addresses through a separate SMTP account.
11. Strikes and permanent IP bans (automatic after `BAN_STRIKES`, or by hand).

## v1.13.1 (8 November 2026)

PHP 8 fixes to the original script:
- `userclass.php`: constructor and `__toString()` (fixes "User object used as
  a string").
- `configvalues.php`: missing settings filled in automatically.
- `lang.hungarian.php`, `lang.welsh.php`: syntax errors fixed.
- `showmypredictions.php`: loads `templates/render.php` so `renderPage()` exists.
- `install-defaults.sql`: default settings for a new database.

## v1.13.0

The original Prediction Football script, unchanged (see its
`ReleaseNotes.txt` in that version for its own history).
