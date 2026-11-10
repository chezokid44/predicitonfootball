/* Prediction League front end: plain JavaScript, no build step.
 * Talks to api/index.php?r=<route>. */
(() => {
  'use strict';

  const API = 'api/index.php';
  const state = { user: null, csrf: '', settings: {}, firstAdmin: false, unread: 0 };
  const main = document.getElementById('main');

  // ------------------------------------------------------------------
  // Helpers
  // ------------------------------------------------------------------

  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  class ApiError extends Error {
    constructor(message, status) { super(message); this.status = status; }
  }

  async function api(route, { method = 'GET', body, query = {}, form } = {}, retried = false) {
    const params = new URLSearchParams({ r: route, ...query });
    const opts = {
      method,
      credentials: 'same-origin',
      headers: { Accept: 'application/json', 'X-PL-Client': '1', 'X-CSRF-Token': state.csrf },
    };
    if (form) {
      opts.method = 'POST';
      opts.body = form;  // the browser sets the multipart Content-Type
    } else if (method !== 'GET') {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body || {});
    }
    let res;
    try {
      res = await fetch(`${API}?${params}`, opts);
    } catch (e) {
      throw new ApiError('Can\'t reach the server. Check your connection and try again.', 0);
    }
    let data = null;
    try { data = await res.json(); } catch (e) { /* not JSON */ }
    // The session expired: get a fresh token and retry once.
    if (res.status === 403 && data && data.code === 'token' && route !== 'session' && !retried) {
      applySession(await api('session'));
      return api(route, { method, body, query, form }, true);
    }
    // A banned address: show only that, everywhere.
    if (res.status === 403 && data && data.code === 'banned') {
      document.body.innerHTML = `<main class="banned"><h1>Access denied</h1><p>${esc(data.error)}</p></main>`;
      throw new ApiError(data.error, 403);
    }
    if (!res.ok) {
      const err = new ApiError((data && data.error) || `The server returned an error (${res.status}).`, res.status);
      err.code = data && data.code;
      throw err;
    }
    return data;
  }

  function applySession(s) {
    state.user = s.user;
    state.csrf = s.csrf;
    state.settings = s.settings;
    state.firstAdmin = s.first_user_becomes_admin;
    document.title = s.settings.title;
    document.getElementById('brand').textContent = s.settings.title;
    renderFoot();
  }

  let toastTimer;
  function toast(message, bad = false) {
    const el = document.getElementById('toast');
    el.textContent = message;
    el.classList.toggle('bad', bad);
    el.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { el.hidden = true; }, 3800);
  }

  const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

  // Kick-off strings are league-local "YYYY-MM-DD HH:MM:SS"; format without time-zone shifts.
  function parts(kickoff) {
    const [d, t = '00:00:00'] = kickoff.split(' ');
    const [y, m, day] = d.split('-').map(Number);
    const [hh, mm] = t.split(':').map(Number);
    const weekday = new Date(Date.UTC(y, m - 1, day)).getUTCDay();
    return { y, m, day, hh, mm, weekday, date: d };
  }
  function fmtTime(kickoff) {
    const p = parts(kickoff);
    const mm = String(p.mm).padStart(2, '0');
    if (state.settings.use_24hr) return `${String(p.hh).padStart(2, '0')}:${mm}`;
    const h12 = p.hh % 12 || 12;
    return `${h12}:${mm}${p.hh < 12 ? 'am' : 'pm'}`;
  }
  function fmtDay(kickoff) {
    const p = parts(kickoff);
    const thisYear = new Date().getFullYear();
    return `${DAYS[p.weekday]} ${p.day} ${MONTHS[p.m - 1]}${p.y !== thisYear ? ' ' + p.y : ''}`;
  }
  function fmtShort(kickoff) {
    const p = parts(kickoff);
    return `${DAYS[p.weekday].slice(0, 3)} ${p.day} ${MONTHS[p.m - 1]}, ${fmtTime(kickoff)}`;
  }
  function groupByDay(items, key = 'kickoff') {
    const groups = [];
    for (const item of items) {
      const day = item[key].slice(0, 10);
      const last = groups[groups.length - 1];
      if (last && last.day === day) last.items.push(item);
      else groups.push({ day, label: fmtDay(item[key]), items: [item] });
    }
    return groups;
  }
  const plural = (n, one, many = one + 's') => `${n} ${n === 1 ? one : many}`;

  // ------------------------------------------------------------------
  // Competitions: filter and grouping shared by the fixture lists
  // ------------------------------------------------------------------

  const autoTag = (p) => (p && p.auto ? '<span class="auto-tag" title="Made automatically from the teams\' recent form">Auto</span>' : '');
  // "Closes 14:30", or with the day when that isn't the kick-off day.
  const ppTag = (f) => (f.postponed ? `<span class="pp-tag">Postponed</span>` : '');
  const closesText = (f) => `Closes ${f.closes.slice(0, 10) === f.kickoff.slice(0, 10) ? fmtTime(f.closes) : fmtShort(f.closes)}`;
  const compTag = (c) => (c ? `<span class="comp-tag" title="${esc(c.name)}">${esc(c.short || c.name)}</span>` : '');

  function viewPrefs() {
    let p = {};
    try { p = JSON.parse(localStorage.getItem('pl.fixtureView') || '{}') || {}; } catch (e) { /* storage unavailable */ }
    return { group: p.group === 'competition' ? 'competition' : 'date', comp: p.comp || 'all' };
  }
  function saveViewPrefs(p) {
    try { localStorage.setItem('pl.fixtureView', JSON.stringify(p)); } catch (e) { /* storage unavailable */ }
  }
  const hasCompetitions = (fixtures) => (state.settings.competitions || []).length > 0 || fixtures.some((f) => f.competition);

  // Filter and group controls. onChange is called after the choice is saved.

  // ---- Seasons and team badges ----

  const hasSeasons = () => (state.settings.seasons || []).length > 0;
  function seasonChoice() {
    let v = '';
    try { v = localStorage.getItem('pl.season') || ''; } catch (e) { /* storage unavailable */ }
    if (v !== 'all' && v !== '' && !(state.settings.seasons || []).some((x) => String(x.id) === v)) v = '';
    return v;
  }
  // The season id a choice means ('' = current); null = all time.
  function chosenSeasonId() {
    const v = seasonChoice();
    if (v === 'all') return null;
    return v ? Number(v) : state.settings.current_season;
  }
  const seasonQuery = () => (hasSeasons() ? { season: seasonChoice() } : {});
  function seasonName(s) { return s ? s.name : 'All time'; }
  function seasonPicker() {
    if (!hasSeasons()) return '';
    const v = seasonChoice();
    const cur = state.settings.current_season;
    return `<label class="season-pick">Season<select data-season>
      ${state.settings.seasons.map((x) => `<option value="${x.id === cur ? '' : x.id}"${(v === '' && x.id === cur) || v === String(x.id) ? ' selected' : ''}>${esc(x.name)}${x.id === cur ? ' (current)' : ''}</option>`).join('')}
      ${cur === null ? `<option value=""${v === '' ? ' selected' : ''}>Current</option>` : ''}
      <option value="all"${v === 'all' ? ' selected' : ''}>All time</option></select></label>`;
  }
  // Fixtures still open for predictions always show; finished ones follow the season choice.
  function inSeason(f) {
    if (!hasSeasons() || !f.locked) return true;
    const id = chosenSeasonId();
    return id === null || f.season === id;
  }
  function teamName(f, side) {
    const name = esc(f[`${side}_team`]);
    const badge = f[`${side}_badge`];
    if (!badge) return name;
    const img = `<img class="crest" src="${esc(badge)}" alt="" width="22" height="22" loading="lazy">`;
    return side === 'home' ? `${name}${img}` : `${img}${name}`;
  }

  function fixtureToolbar(fixtures) {
    if (!hasCompetitions(fixtures)) {
      return hasSeasons() ? `<div class="fixture-tools" role="group" aria-label="Show fixtures">${seasonPicker()}</div>` : '';
    }
    const p = viewPrefs();
    const used = new Set(fixtures.map((f) => (f.competition ? String(f.competition.id) : 'none')));
    const comps = (state.settings.competitions || []).filter((c) => used.has(String(c.id)));
    const picky = state.user && Array.isArray(state.user.follows);
    return `<div class="fixture-tools" role="group" aria-label="Show fixtures">${seasonPicker()}
      <label>Competition<select data-view="comp">
        <option value="all">${picky ? 'Competitions you play' : 'All competitions'}</option>
        ${picky ? `<option value="every"${p.comp === 'every' ? ' selected' : ''}>Every competition</option>` : ''}
        ${comps.map((c) => `<option value="${c.id}"${p.comp === String(c.id) ? ' selected' : ''}>${esc(c.name)}</option>`).join('')}
        ${used.has('none') ? `<option value="none"${p.comp === 'none' ? ' selected' : ''}>Other fixtures</option>` : ''}
      </select></label>
      <fieldset class="segmented"><legend>Group by</legend>
        <label><input type="radio" name="group-by" value="date"${p.group === 'date' ? ' checked' : ''}><span>Date</span></label>
        <label><input type="radio" name="group-by" value="competition"${p.group === 'competition' ? ' checked' : ''}><span>Competition</span></label>
      </fieldset>
    </div>`;
  }
  function bindToolbar(onChange) {
    const tools = main.querySelector('.fixture-tools');
    if (!tools) return;
    tools.addEventListener('change', (e) => {
      if (e.target.matches('[data-season]')) return;  // handled for the whole page
      const comp = tools.querySelector('[data-view=comp]').value;
      const group = tools.querySelector('input[name=group-by]:checked').value;
      saveViewPrefs({ comp, group });
      onChange();
    });
  }
  function applyCompFilter(all) {
    const items = all.filter(inSeason);
    const { comp } = viewPrefs();
    if (comp === 'every') return items;
    if (comp === 'all') return items.filter(plays);
    return items.filter((f) => (comp === 'none' ? !f.competition : f.competition && String(f.competition.id) === comp));
  }

  /*
   * Fixture list grouped by competition (then date) or by date, with matches
   * that kick off at the same time grouped together.
   * rowFn(fixture, { showTime, showComp }) returns one <li>.
   * newestFirst puts later days first (results); kick-offs within a day stay in order.
   */
  function fixtureGroups(items, rowFn, { newestFirst = false, headingLevel = 3 } = {}) {
    const p = viewPrefs();
    const compsOn = hasCompetitions(items);
    const order = Object.fromEntries((state.settings.competitions || []).map((c, i) => [c.id, i]));
    const compRank = (f) => (f.competition ? order[f.competition.id] ?? 900 : 999);
    const days = (list) => {
      const byDay = new Map();
      for (const f of list) {
        const d = f.kickoff.slice(0, 10);
        if (!byDay.has(d)) byDay.set(d, []);
        byDay.get(d).push(f);
      }
      const keys = [...byDay.keys()].sort();
      if (newestFirst) keys.reverse();
      return keys.map((d) => {
        const rows = byDay.get(d).sort((a, b) => a.kickoff.localeCompare(b.kickoff) || compRank(a) - compRank(b) || a.id - b.id);
        let html = `<h${headingLevel} class="day">${esc(fmtDay(rows[0].kickoff))}</h${headingLevel}><ul class="fixtures">`;
        rows.forEach((f, i) => {
          const sameKickoff = i > 0 && rows[i - 1].kickoff === f.kickoff;
          html += rowFn(f, { showTime: !sameKickoff, showComp: compsOn && p.group === 'date', sameKickoff });
        });
        return html + '</ul>';
      }).join('');
    };
    if (!compsOn || p.group === 'date') return days(items);

    const groups = new Map();
    for (const f of [...items].sort((a, b) => compRank(a) - compRank(b))) {
      const key = f.competition ? f.competition.id : 'none';
      if (!groups.has(key)) groups.set(key, { name: f.competition ? f.competition.name : 'Other fixtures', items: [] });
      groups.get(key).items.push(f);
    }
    return [...groups.values()].map((g) => `<section class="comp-group">
        <h${headingLevel - 1 || 2} class="comp-head">${esc(g.name)} <span class="muted">${esc(plural(g.items.length, 'match', 'matches'))}</span></h${headingLevel - 1 || 2}>
        ${days(g.items)}</section>`).join('');
  }

  // The time cell: shown once per kick-off, with the competition tag when grouping by date.
  const timeCell = (f, o) => `<span class="time">${o.showTime ? esc(fmtTime(f.kickoff)) : '<span class="visually-hidden">' + esc(fmtTime(f.kickoff)) + '</span>'}${o.showComp ? compTag(f.competition) : ''}</span>`;
  const rowClass = (o) => `fixture${o.sameKickoff ? ' same-ko' : ''}`;

  // A player's picture, or their initials on a coloured square.
  function avatar(u, size = '') {
    const name = (u && u.username) || '?';
    const cls = `avatar${size ? ' ' + size : ''}`;
    if (u && u.icon) return `<img class="${cls}" src="${esc(u.icon)}" alt="" data-name="${esc(name)}" loading="lazy">`;
    return initials(name, cls);
  }
  function initials(name, cls) {
    const letters = name.split(/[\s._-]+/).filter(Boolean).slice(0, 2).map((w) => w[0]).join('').toUpperCase() || '?';
    let h = 0;
    for (const ch of name) h = (h * 31 + ch.charCodeAt(0)) % 997;
    return `<span class="${cls} initials tone-${h % 4}" aria-hidden="true">${esc(letters)}</span>`;
  }
  // Replace pictures that fail to load (e.g. after a restore) with initials.
  document.addEventListener('error', (e) => {
    const img = e.target;
    if (img.tagName === 'IMG' && img.classList.contains('avatar')) {
      img.outerHTML = initials(img.dataset.name || '?', img.className);
    }
  }, true);

  function download(filename, content, mime) {
    const url = URL.createObjectURL(new Blob([content], { type: mime }));
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(url), 2000);
  }

  async function refreshUnread() {
    if (!state.user || !state.settings.messaging) { state.unread = 0; return; }
    try {
      const { unread } = await api('messages.count');
      if (unread !== state.unread) {
        state.unread = unread;
        const badge = document.getElementById('unread-badge');
        if (badge) { badge.textContent = unread; badge.hidden = !unread; }
      }
    } catch (e) { /* not important enough to show */ }
  }

  function outcomePill(points, outcome) {
    if (points === null || points === undefined) return '';
    const label = outcome === 'exact' ? 'Exact score' : outcome === 'result' ? 'Right result' : 'Missed';
    return `<span class="pill ${esc(outcome)}" title="${esc(label)}">${esc(plural(points, 'pt'))}</span>`;
  }

  function setPage(html) {
    main.innerHTML = html;
  }
  function errorPage(err) {
    if (err.code === 'not_installed') {
      setPage(`<h1>Set up your league</h1><p class="lede">This site hasn't been set up yet.</p>
        <p><a class="btn" href="install/">Run the installer</a></p>`);
      return;
    }
    setPage(`<h1>That didn't load</h1><div class="notice"><p>${esc(err.message)}</p></div>
      <p><button class="btn quiet" type="button" data-action="retry">Try again</button></p>`);
    main.querySelector('[data-action=retry]').addEventListener('click', route);
  }
  function needLogin(what) {
    setPage(`<h1>Log in first</h1>
      <p class="lede">You need to be logged in to ${esc(what)}.</p>
      <p><a class="btn" href="#/login">Log in</a> ${state.settings.registration_open ? '<a class="btn quiet" href="#/register">Join the league</a>' : ''}</p>`);
  }

  function formError(form, message) {
    let el = form.querySelector('.error');
    if (!el) {
      el = document.createElement('p');
      el.className = 'error';
      el.setAttribute('role', 'alert');
      form.prepend(el);
    }
    el.textContent = message;
  }

  async function submitting(button, fn) {
    const label = button.textContent;
    button.disabled = true;
    try { return await fn(); } finally { button.disabled = false; button.textContent = label; }
  }

  // A two-step button for destructive actions instead of a blocking dialog.
  function armed(button, confirmLabel, action) {
    if (button.dataset.armed === '1') {
      button.dataset.armed = '';
      button.classList.remove('armed');
      return action();
    }
    const original = button.textContent;
    button.dataset.armed = '1';
    button.classList.add('armed');
    button.textContent = confirmLabel;
    setTimeout(() => {
      if (button.dataset.armed === '1') {
        button.dataset.armed = '';
        button.classList.remove('armed');
        button.textContent = original;
      }
    }, 4000);
    return null;
  }

  // ------------------------------------------------------------------
  // Chrome: nav and footer
  // ------------------------------------------------------------------

  function renderNav(active) {
    const builtin = { table: '#/table', standings: '#/standings', leagues: '#/leagues', predictions: '#/predictions', results: '#/results', messages: '#/messages', help: '#/help' };
    const menu = state.settings.menu || ['table', 'standings', 'leagues', 'predictions', 'results', 'messages'].map((key) => ({ type: 'builtin', key, label: { table: 'Table', standings: 'League tables', leagues: 'Leagues', predictions: 'Predict', results: 'Results', messages: 'Messages' }[key] }));
    const links = [];
    for (const m of menu) {
      if (m.type === 'page') {
        // The home page is reached from the site name, but can be in the menu too.
        links.push([m.slug === state.settings.home_page ? 'home' : `page:${m.slug}`, m.slug === state.settings.home_page ? '#/' : `#/page/${encodeURIComponent(m.slug)}`, m.label]);
        continue;
      }
      if (m.key === 'messages' && !(state.user && state.settings.messaging)) continue;
      if (m.key === 'predictions' && !state.user) continue;
      if (m.key === 'leagues' && !state.user && !(state.settings.competitions || []).length) continue;
      if (m.key === 'standings' && !tableComps().length) continue;
      links.push([m.key, builtin[m.key], m.label]);
    }
    if (state.user && state.user.is_admin) links.push(['admin', '#/admin', 'Admin']);
    let html = links.map(([key, href, label]) =>
      `<a href="${href}"${active === key ? ' aria-current="page"' : ''}>${label}${key === 'messages'
        ? ` <span class="badge" id="unread-badge"${state.unread ? '' : ' hidden'}><span class="visually-hidden">unread: </span>${state.unread}</span>` : ''}</a>`).join('');
    if (state.user) {
      html += `<a class="nav-account" href="#/account"${active === 'account' ? ' aria-current="page"' : ''}>${avatar(state.user, 'xs')}${esc(state.user.username)}</a>`;
    } else {
      html += `<a class="nav-account" href="#/login"${active === 'login' ? ' aria-current="page"' : ''}>Log in</a>`;
    }
    document.getElementById('nav').innerHTML = html;
  }

  function renderFoot() {
    const s = state.settings;
    const bits = [`<span>Exact score ${esc(plural(s.points_exact, 'point'))}. Right result ${esc(plural(s.points_result, 'point'))}.</span>`,
      '<a href="#/help">How to play</a>'];
    if (s.home_page_url) {
      bits.push(`<a href="${esc(s.home_page_url)}" rel="noopener">${esc(s.home_page_title || s.home_page_url)}</a>`);
    }
    document.getElementById('foot').innerHTML = `<div class="foot-inner">${bits.join('')}</div>`;
  }

  // ------------------------------------------------------------------
  // Views
  // ------------------------------------------------------------------

  async function viewTable() {
    renderNav('table');
    const [data, hub] = await Promise.all([api('standings', { query: seasonQuery() }), hubData()]);
    let html = `${tableSwitcher(hub, 'overall')}<div class="title-row"><h1>League table</h1>${seasonPicker()}</div>`;

    if (data.next_match) {
      const m = data.next_match;
      html += `<section class="next-match" aria-label="Next match">
        <div class="team home">${teamName(m, 'home')}</div>
        <div class="when">${esc(fmtShort(m.kickoff))}</div>
        <div class="team away">${teamName(m, 'away')}</div>
        <div class="meta">Next match${m.competition ? `, ${esc(m.competition.name)}` : ''}. ${state.user
          ? (m.locked ? 'Predictions have closed.' : `<a href="#/predictions">Make your prediction</a> (${esc(closesText(m).toLowerCase())}).`)
          : '<a href="#/login">Log in</a> to predict.'}</div>
      </section>`;
    }

    if (!data.table.length) {
      html += `<div class="notice"><p>The table fills in once the first match kicks off.</p>
        ${state.user ? '<p><a href="#/predictions">Get your predictions in</a> before then.</p>' : ''}</div>`;
      setPage(html);
      return;
    }

    html += tableHtml(data.table);
    setPage(html);
  }

  async function viewPredictions() {
    renderNav('predictions');
    if (!state.user) return needLogin('make predictions');
    const { fixtures } = await api('fixtures');

    if (!fixtures.length) {
      setPage(`<h1>Your predictions</h1><div class="notice"><p>There are no fixtures yet.
        ${state.user.is_admin ? '<a href="#/admin/fixtures">Add fixtures</a>.' : 'Check back once the admin has added some.'}</p></div>`);
      return;
    }

    // Unsaved scores survive changing the filter or grouping.
    const initial = {};
    for (const f of fixtures) initial[f.id] = f.my_prediction ? { home: String(f.my_prediction.home), away: String(f.my_prediction.away) } : { home: '', away: '' };
    const pending = JSON.parse(JSON.stringify(initial));
    const changed = () => Object.keys(pending).filter((id) => pending[id].home !== initial[id].home || pending[id].away !== initial[id].away);

    const row = (f, o) => {
      const p = f.my_prediction;
      let score;
      let status;
      if (!f.locked) {
        const box = (side, team) => {
          const v = pending[f.id][side];
          return `<input class="box${v !== '' ? ' filled' : ''}${v !== initial[f.id][side] ? ' changed' : ''}" type="number" inputmode="numeric" min="0" max="99"
            name="${side}-${f.id}" data-match="${f.id}" data-side="${side}" value="${esc(v)}"
            aria-label="${esc(team)} goals${f.competition ? ', ' + esc(f.competition.name) : ''}">`;
        };
        score = `${box('home', f.home_team)}<span class="dash" aria-hidden="true">–</span>${box('away', f.away_team)}`;
        status = `<a href="#/match/${f.id}">${esc(plural(f.prediction_count, 'prediction'))}</a><br><span class="muted closes">${esc(closesText(f))}</span>`;
      } else {
        const shown = (side) => `<span class="box locked">${p ? p[side] : '·'}</span>`;
        score = `${shown('home')}<span class="dash" aria-hidden="true">–</span>${shown('away')}`;
        status = f.postponed
          ? `${ppTag(f)}<br><span class="muted">New date to come${p ? '. Your prediction is kept' : ''}</span>`
          : f.result
          ? `<a href="#/match/${f.id}">Final ${f.result.home}–${f.result.away}</a><br>${p ? autoTag(p) + outcomePill(f.my_points, f.my_outcome) : '<span class="muted">No prediction</span>'}`
          : `<a href="#/match/${f.id}">${p ? (f.started ? 'Kicked off' : 'Predictions closed') : 'Missed'}</a>${p && p.auto ? '<br>' + autoTag(p) : ''}`;
      }
      return `<li class="${rowClass(o)}">
        ${timeCell(f, o)}
        <span class="team home">${teamName(f, 'home')}</span>
        <span class="score">${score}</span>
        <span class="team away">${teamName(f, 'away')}</span>
        <span class="status">${status}</span>
      </li>`;
    };

    const draw = () => {
      const shown = applyCompFilter(fixtures);
      const open = shown.filter((f) => !f.locked);
      const postponed = shown.filter((f) => f.postponed);
      const played = shown.filter((f) => f.locked && !f.postponed);
      const allOpen = fixtures.filter((f) => !f.locked && plays(f));
      const missing = allOpen.filter((f) => !f.my_prediction).length;
      const cut = state.settings.cutoff_minutes || 0;
      const autoOn = state.settings.auto_predict && state.user.prefs && state.user.prefs.auto_predict;
      let html = `<h1>Your predictions</h1>
        <p class="lede">${allOpen.length
          ? `Fill in a score for each match. You can change a prediction until ${cut ? `${esc(plural(cut, 'minute'))} before kick-off` : 'kick-off'}.${missing ? ` ${esc(plural(missing, 'open match', 'open matches'))} still need a prediction.` : ' Every open match has a prediction.'}`
          : 'Predictions have closed for every fixture. Here is how yours did.'}</p>
        ${allOpen.length && state.settings.auto_predict ? `<p class="auto-note">${autoOn
          ? 'Automatic predictions are on: any match you leave empty gets a prediction from the teams\' recent form when predictions close.'
          : 'Worried you\'ll forget? <a href="#/account">Switch on automatic predictions</a> and we\'ll predict any match you leave empty.'}</p>` : ''}
        ${fixtureToolbar(fixtures)}
        <form id="predict-form" novalidate>`;
      if (open.length) {
        html += fixtureGroups(open, row);
      } else if (allOpen.length) {
        html += `<p class="muted">No open matches in this competition.</p>`;
      }
      if (allOpen.length) {
        const n = changed().length;
        html += `<div class="savebar"><button class="btn" type="submit" id="save-preds">Save predictions</button>
          <span class="msg" id="save-msg" aria-live="polite">${n ? `${plural(n, 'match', 'matches')} changed, not saved yet.` : 'No unsaved changes.'}</span></div>`;
      }
      if (postponed.length) {
        html += `<h2 class="section-head">Postponed</h2><p class="muted tight">Predictions reopen when ${postponed.length === 1 ? 'this match gets its' : 'these matches get their'} new date.</p>${fixtureGroups(postponed, row)}`;
      }
      if (played.length) {
        html += `<h2 class="section-head">Closed and played</h2>${fixtureGroups(played, row, { newestFirst: true })}`;
      }
      html += `</form>`;
      setPage(html);
      bindToolbar(draw);
      bindForm();
    };

    const bindForm = () => {
      const form = document.getElementById('predict-form');
      form.addEventListener('input', (e) => {
        const input = e.target;
        if (!input.classList.contains('box')) return;
        if (input.value.length > 2) input.value = input.value.slice(0, 2);
        const { match, side } = input.dataset;
        pending[match][side] = input.value;
        input.classList.toggle('filled', input.value !== '');
        input.classList.toggle('changed', input.value !== initial[match][side]);
        const n = changed().length;
        const msg = document.getElementById('save-msg');
        if (msg) msg.textContent = n ? `${plural(n, 'match', 'matches')} changed, not saved yet.` : 'No unsaved changes.';
      });
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const ids = changed();
        if (!ids.length) { toast('Nothing has changed since you last saved.'); return; }
        const predictions = [];
        for (const id of ids) {
          const { home, away } = pending[id];
          if ((home === '') !== (away === '')) {
            const f = fixtures.find((x) => String(x.id) === id);
            const box = form.querySelector(`[data-match="${id}"][data-side=${home === '' ? 'home' : 'away'}]`);
            if (box) box.focus();
            toast(`Enter both scores for ${f.home_team} v ${f.away_team}, or clear both.`, true);
            return;
          }
          predictions.push({ match_id: Number(id), home, away });
        }
        try {
          const res = await submitting(document.getElementById('save-preds'), () => api('predictions', { method: 'POST', body: { predictions } }));
          if (res.locked.length) toast(`Saved. ${plural(res.locked.length, 'match', 'matches')} closed before you saved, so ${res.locked.length === 1 ? 'it was' : 'they were'} skipped.`, true);
          else toast(`Saved ${plural(res.saved, 'prediction')}.`);
          await viewPredictions();
        } catch (err) {
          if (err.status === 403) await refreshSession();
          toast(err.message, true);
        }
      });
    };
    draw();
  }

  async function viewResults() {
    renderNav('results');
    const { fixtures } = await api('fixtures');
    const row = (f, o) => {
      const mine = state.user ? (f.my_prediction ? `You said ${f.my_prediction.home}–${f.my_prediction.away}` : 'No prediction') : '';
      return `<li class="${rowClass(o)}">
        ${timeCell(f, o)}
        <span class="team home">${teamName(f, 'home')}</span>
        <span class="score"><span class="box result">${f.result.home}</span><span class="dash" aria-hidden="true">–</span><span class="box result">${f.result.away}</span></span>
        <span class="team away">${teamName(f, 'away')}</span>
        <span class="status"><a href="#/match/${f.id}">${esc(mine || 'Predictions')}</a>${state.user && f.my_prediction ? '<br>' + autoTag(f.my_prediction) + outcomePill(f.my_points, f.my_outcome) : ''}</span>
      </li>`;
    };
    const draw = () => {
      const shown = applyCompFilter(fixtures);
      const done = shown.filter((f) => f.result);
      const waiting = shown.filter((f) => f.started && !f.result);
      let html = `<div class="title-row"><h1>Results</h1><a class="btn quiet small" href="#/matchday">Everyone's predictions by day</a></div>
        ${fixtureToolbar(fixtures.filter((f) => f.started))}`;
      if (waiting.length) {
        html += `<div class="notice"><p>${esc(plural(waiting.length, 'match', 'matches'))} kicked off and ${waiting.length === 1 ? 'is' : 'are'} waiting for a result.${state.user && state.user.is_admin ? ' <a href="#/admin/results">Enter results</a>.' : ''}</p></div>`;
      }
      html += done.length ? fixtureGroups(done, row, { newestFirst: true })
        : `<p class="lede">No results yet${!['all', 'every'].includes(viewPrefs().comp) ? ' in this competition' : ''}${hasSeasons() && chosenSeasonId() !== null ? ' this season' : ''}. They appear here once the admin enters the final scores.</p>`;
      setPage(html);
      bindToolbar(draw);
    };
    draw();
  }

  async function viewMatch(id) {
    renderNav('');
    const data = await api('match', { query: { id } });
    const m = data.match;
    let html = `<p><a href="#/results">Results</a> / <a href="#/predictions">Predictions</a></p>
      ${m.competition ? `<p class="match-comp">${esc(m.competition.name)}</p>` : ''}
      <section class="next-match" aria-label="Match">
        <div class="team home">${teamName(m, 'home')}</div>
        <div class="when${m.result ? ' final' : ''}">${m.result ? `${m.result.home}–${m.result.away}` : m.postponed ? 'Postponed' : esc(fmtShort(m.kickoff))}</div>
        <div class="team away">${teamName(m, 'away')}</div>
        <div class="meta">${m.postponed ? `Postponed${m.postponed_reason ? ` (${esc(m.postponed_reason)})` : ''}. It was due to kick off ${esc(fmtShort(m.kickoff))}. Predictions reopen when it's rearranged.` : m.result ? `Final score. Kicked off ${esc(fmtShort(m.kickoff))}.` : m.started ? 'Kicked off. Waiting for the result.' : m.locked ? `Predictions closed at ${esc(fmtTime(m.closes))}.` : `Predictions close ${esc(closesText(m).replace('Closes ', '')).replace(/^(\d)/, 'at $1')}.`}</div>
      </section>`;

    if (data.hidden_count) {
      html += `<div class="notice"><p>${esc(plural(data.hidden_count, 'prediction'))} ${data.hidden_count === 1 ? 'stays' : 'stay'} hidden until kick-off.</p></div>`;
    }
    if (!data.predictions.length) {
      html += `<p class="lede">No predictions to show${data.hidden_count ? ' yet' : ''}.</p>`;
    } else {
      html += `<div class="table-wrap"><table class="league"><thead><tr>
        <th scope="col" class="name">Player</th><th scope="col">Prediction</th>${m.result ? '<th scope="col">Points</th>' : ''}
      </tr></thead><tbody>`;
      const rows = [...data.predictions].sort((a, b) => (b.points ?? -1) - (a.points ?? -1) || a.username.localeCompare(b.username));
      for (const p of rows) {
        const me = state.user && p.user_id === state.user.id;
        html += `<tr${me ? ' class="me"' : ''}>
          <td class="name"><a class="who" href="#/player/${p.user_id}">${avatar(p)}${esc(p.username)}</a></td>
          <td>${p.home}–${p.away}${autoTag(p)}</td>
          ${m.result ? `<td>${outcomePill(p.points, p.outcome)}</td>` : ''}
        </tr>`;
      }
      html += `</tbody></table></div>`;
    }
    setPage(html);
  }

  async function viewPlayer(id) {
    renderNav('');
    const data = await api('player', { query: { id } });
    const s = data.standing;
    const canMessage = state.user && state.settings.messaging && state.user.id !== data.player.id;
    let html = `<p><a href="#/table">League table</a></p>
      <div class="player-head">${avatar(data.player, 'lg')}<h1>${esc(data.player.username)}</h1></div>
      <p class="lede">${s ? `${ordinal(s.position)} in the table with ${esc(plural(s.points, 'point'))}: ${esc(plural(s.exact, 'exact score'))} and ${esc(plural(s.result, 'right result'))} from ${esc(plural(s.played, 'scored prediction'))}.` : 'Not in the table yet.'}</p>
      ${canMessage ? `<p><a class="btn quiet small" href="#/compose/${data.player.id}">Send ${esc(data.player.username)} a message</a></p>` : ''}`;
    if (data.hidden_count) {
      html += `<div class="notice"><p>${esc(plural(data.hidden_count, 'prediction'))} for upcoming matches ${data.hidden_count === 1 ? 'stays' : 'stay'} hidden until kick-off.</p></div>`;
    }
    if (!data.predictions.length) {
      setPage(html + `<p>No predictions to show.</p>`);
      return;
    }
    for (const g of groupByDay(data.predictions)) {
      html += `<h2 class="day">${esc(g.label)}</h2><ul class="fixtures">`;
      for (const f of g.items) {
        html += `<li class="fixture">
          <span class="time">${esc(fmtTime(f.kickoff))}${compTag(f.competition)}</span>
          <span class="team home">${teamName(f, 'home')}</span>
          <span class="score"><span class="box locked">${f.prediction.home}</span><span class="dash" aria-hidden="true">–</span><span class="box locked">${f.prediction.away}</span></span>
          <span class="team away">${teamName(f, 'away')}</span>
          <span class="status"><a href="#/match/${f.id}">${f.result ? `Final ${f.result.home}–${f.result.away}` : (f.started ? 'Kicked off' : f.locked ? 'Predictions closed' : 'Upcoming')}</a>${f.result || f.prediction.auto ? '<br>' : ''}${autoTag(f.prediction)}${f.result ? outcomePill(f.points, f.outcome) : ''}</span>
        </li>`;
      }
      html += `</ul>`;
    }
    setPage(html);
  }

  function ordinal(n) {
    const s = ['th', 'st', 'nd', 'rd'];
    const v = n % 100;
    return n + (s[(v - 20) % 10] || s[v] || s[0]);
  }

  function viewLogin() {
    renderNav('login');
    if (state.user) { location.hash = '#/predictions'; return; }
    setPage(`<h1>Log in</h1>
      <form class="stack" id="login-form">
        <label>Username<input type="text" name="username" autocomplete="username" required maxlength="32"></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
        ${honeypotField()}
        <div><button class="btn" type="submit">Log in</button></div>
      </form>
      <p class="form-foot">${state.settings.registration_open ? 'New here? <a href="#/register">Join the league</a>.' : 'Registration is closed. Ask the league admin to add you.'}
      ${state.settings.email ? '<a href="#/forgot">Forgotten your password?</a>' : 'Forgotten your password? Ask the league admin for a reset link.'}</p>`);
    const form = document.getElementById('login-form');
    const check = botCheck('login');
    form.username.focus();
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const s = await submitting(form.querySelector('button'), () => check.send('login', form,
          { username: form.username.value, password: form.password.value }));
        applySession(s);
        if (!(await cookiesWork())) return;
        toast(`Logged in as ${s.user.username}.`);
        location.hash = takeJoinRedirect() || '#/predictions';
      } catch (err) {
        if (err.status === 403) await refreshSession();
        formError(form, err.message);
        form.password.select();
      }
    });
  }

  function viewRegister() {
    renderNav('login');
    if (state.user) { location.hash = '#/predictions'; return; }
    if (!state.settings.registration_open) {
      setPage(`<h1>Registration is closed</h1><p class="lede">Ask the league admin to add you, then <a href="#/login">log in</a>.</p>`);
      return;
    }
    setPage(`<h1>Join the league</h1>
      ${state.firstAdmin ? '<div class="notice"><p>No admin exists yet, so this account will be the league admin.</p></div>' : ''}
      <form class="stack" id="register-form">
        <label>Username<span class="hint">This is what other players see in the table.</span>
          <input type="text" name="username" autocomplete="username" required maxlength="32"></label>
        <label>Email address<input type="email" name="email" autocomplete="email" required maxlength="60"></label>
        <label>Password<span class="hint">At least 8 characters.</span>
          <input type="password" name="password" autocomplete="new-password" required minlength="8"></label>
        ${honeypotField()}
        <div><button class="btn" type="submit">Join the league</button></div>
      </form>
      <p class="form-foot">Already playing? <a href="#/login">Log in</a>. New to prediction leagues? Read <a href="#/help">how to play</a>.</p>`);
    const form = document.getElementById('register-form');
    const check = botCheck('register');
    form.username.focus();
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const s = await submitting(form.querySelector('button'), () => check.send('register', form, {
          username: form.username.value, email: form.email.value, password: form.password.value }));
        applySession(s);
        if (!(await cookiesWork())) return;
        toast(`Welcome to the league, ${s.user.username}.`);
        location.hash = takeJoinRedirect() || '#/predictions';
      } catch (err) {
        if (err.status === 403) await refreshSession();
        formError(form, err.message);
      }
    });
  }

  function viewAccount() {
    renderNav('account');
    if (!state.user) return needLogin('change your account');
    setPage(`<h1>Your account</h1>
      <p class="lede">Logged in as <strong>${esc(state.user.username)}</strong>${state.user.is_admin ? ', league admin' : ''}.</p>
      ${state.settings.icons || state.user.is_admin ? `<h2>Your picture</h2>
      <div class="picture-row">${avatar(state.user, 'lg')}
        <form id="icon-form" class="picture-actions">
          <label class="btn quiet small file-btn">Choose a picture<input type="file" name="icon" accept="image/jpeg,image/png,image/gif,image/webp" class="visually-hidden"></label>
          ${state.user.icon ? '<button class="btn small danger" type="button" id="icon-remove">Remove picture</button>' : ''}
          <span class="muted">JPEG, PNG, GIF or WebP, up to 5 MB. It's cropped to a square.</span>
        </form></div>
      <h2>Your details</h2>` : ''}
      <form class="stack" id="account-form">
        <label>Email address<input type="email" name="email" autocomplete="email" required maxlength="60" value="${esc(state.user.email)}"></label>
        <label>New password<span class="hint">Leave empty to keep your current password.</span>
          <input type="password" name="new_password" autocomplete="new-password" minlength="8"></label>
        <label>Current password<span class="hint">Needed to save any change.</span>
          <input type="password" name="current_password" autocomplete="current-password" required></label>
        <div><button class="btn" type="submit">Save changes</button></div>
      </form>
      ${state.settings.auto_predict ? `<h2>Automatic predictions</h2>
      <form class="stack" id="auto-form">
        <label class="check"><input type="checkbox" name="auto_predict"${state.user.prefs && state.user.prefs.auto_predict ? ' checked' : ''}>
          <span>Predict for me if I forget <span class="hint">When predictions close on a match you haven't predicted, we'll predict it from both teams' last ${6} results: goals scored and conceded, points won and home advantage. It scores like any other prediction and is marked Auto.</span></span></label>
      </form>` : ''}
      ${state.settings.email ? `<h2>Emails</h2>
      <form class="stack" id="prefs-form">
        <label class="check"><input type="checkbox" name="notify_messages"${state.user.prefs && state.user.prefs.notify_messages ? ' checked' : ''}>
          <span>Email me when I get a private message</span></label>
        <label class="check"><input type="checkbox" name="allow_email"${state.user.prefs && state.user.prefs.allow_email ? ' checked' : ''}>
          <span>Let the league admin email me news and reminders</span></label>
        <p class="muted tight">Emails go to ${esc(state.user.email || 'your email address')}. Password reset emails are always sent when you ask for one.</p>
      </form>` : ''}
      <h2>Log out</h2>
      <p><button class="btn quiet" type="button" id="logout">Log out</button></p>`);
    const form = document.getElementById('account-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const res = await submitting(form.querySelector('button'), () => api('account', { method: 'POST', body: {
          email: form.email.value, new_password: form.new_password.value, current_password: form.current_password.value } }));
        state.user = res.user;
        form.new_password.value = '';
        form.current_password.value = '';
        toast('Changes saved.');
      } catch (err) {
        formError(form, err.message);
      }
    });
    const autoForm = document.getElementById('auto-form');
    if (autoForm) {
      autoForm.addEventListener('change', async () => {
        try {
          const res = await api('prefs.save', { method: 'POST', body: { auto_predict: autoForm.auto_predict.checked } });
          state.user.prefs = res.prefs;
          toast(res.prefs.auto_predict ? 'Automatic predictions switched on.' : 'Automatic predictions switched off.');
        } catch (err) { toast(err.message, true); autoForm.auto_predict.checked = !autoForm.auto_predict.checked; }
      });
    }
    const prefsForm = document.getElementById('prefs-form');
    if (prefsForm) {
      prefsForm.addEventListener('change', async () => {
        try {
          const res = await api('prefs.save', { method: 'POST', body: {
            allow_email: prefsForm.allow_email.checked, notify_messages: prefsForm.notify_messages.checked } });
          state.user.prefs = res.prefs;
          toast('Email choices saved.');
        } catch (err) { toast(err.message, true); }
      });
    }
    const iconForm = document.getElementById('icon-form');
    if (iconForm) {
      iconForm.icon.addEventListener('change', async () => {
        if (!iconForm.icon.files.length) return;
        const fd = new FormData();
        fd.append('icon', iconForm.icon.files[0]);
        try {
          const res = await api('icon.upload', { form: fd });
          state.user = res.user;
          toast('Picture updated.');
          viewAccount();
        } catch (err) { toast(err.message, true); iconForm.icon.value = ''; }
      });
      const rm = document.getElementById('icon-remove');
      if (rm) rm.addEventListener('click', async () => {
        try {
          state.user = (await api('icon.remove', { method: 'POST' })).user;
          toast('Picture removed.');
          viewAccount();
        } catch (err) { toast(err.message, true); }
      });
    }
    document.getElementById('logout').addEventListener('click', async () => {
      try {
        applySession(await api('logout', { method: 'POST' }));
        toast('Logged out.');
        location.hash = '#/';
      } catch (err) { toast(err.message, true); }
    });
  }

  // ------------------------------------------------------------------
  // Admin views
  // ------------------------------------------------------------------

  function adminShell(tab, inner) {
    const tabs = [['fixtures', 'Fixtures'], ['competitions', 'Competitions'], ['seasons', 'Seasons'], ['teams', 'Teams'], ['leagues', 'Leagues'], ['pages', 'Pages'], ['results', 'Results'], ['players', 'Players'], ['settings', 'Settings'], ['data', 'Data'], ['activity', 'Activity']];
    setPage(`<h1>Admin</h1>
      <nav class="tabs" aria-label="Admin">${tabs.map(([k, l]) =>
        `<a href="#/admin/${k}"${tab === k ? ' aria-current="page"' : ''}>${l}</a>`).join('')}</nav>${inner}`);
  }

  async function viewAdmin(tab = 'fixtures') {
    renderNav('admin');
    if (!state.user) return needLogin('use the admin pages');
    if (!state.user.is_admin) {
      setPage(`<h1>Admins only</h1><p class="lede">Your account isn't a league admin.</p>`);
      return;
    }
    if (tab === 'results') return adminResults();
    if (tab === 'players') return adminPlayers();
    if (tab === 'settings') return adminSettings();
    if (tab === 'data') return adminData();
    if (tab === 'competitions') return adminCompetitions();
    if (tab === 'activity') return adminActivity();
    if (tab === 'leagues') return adminLeagues();
    if (tab === 'pages') return adminPages();
    if (tab === 'seasons') return adminSeasons();
    if (tab === 'teams') return adminTeams();
    if (tab.startsWith('page-')) return adminPageEdit(tab.slice(5));
    return adminFixtures();
  }

  const competitionsOn = () => (state.settings.competitions || []).length > 0;
  function lastCompetition() {
    try { return localStorage.getItem('pl.lastComp') || ''; } catch (e) { return ''; }
  }
  function compSelect(i, selected) {
    if (!competitionsOn()) return '';
    return `<select name="competition_id" aria-label="Competition, row ${i + 1}">
      <option value="">No competition</option>
      ${state.settings.competitions.map((c) => `<option value="${c.id}"${String(c.id) === String(selected) ? ' selected' : ''}>${esc(c.name)}</option>`).join('')}
    </select>`;
  }
  function fixtureInputs(i, f = {}) {
    const kickoff = f.kickoff ? f.kickoff.slice(0, 16).replace(' ', 'T') : '';
    const comp = f.id ? (f.competition ? f.competition.id : '') : lastCompetition();
    return `<input type="datetime-local" name="kickoff" value="${esc(kickoff)}" aria-label="Kick-off, row ${i + 1}">
      ${compSelect(i, comp)}
      <input type="text" name="home_team" maxlength="30" value="${esc(f.home_team || '')}" placeholder="Home team" list="team-names" aria-label="Home team, row ${i + 1}">
      <input type="text" name="away_team" maxlength="30" value="${esc(f.away_team || '')}" placeholder="Away team" list="team-names" aria-label="Away team, row ${i + 1}">`;
  }
  function fixtureRow(i, f = {}) {
    return `<div class="grid-row${competitionsOn() ? ' with-comp' : ''}" data-row="${i}">${fixtureInputs(i, f)}<span></span></div>`;
  }

  // ---- Season and competition filter for the admin fixture and result lists ----

  function adminFilter() {
    let f = {};
    try { f = JSON.parse(localStorage.getItem('pl.adminFilter') || '{}') || {}; } catch (e) { /* storage unavailable */ }
    const seasons = state.settings.seasons || [];
    let season = f.season === undefined ? '' : String(f.season);   // '' = current season
    if (season !== 'all' && season !== '' && !seasons.some((x) => String(x.id) === season)) season = '';
    let comp = f.comp === undefined ? 'all' : String(f.comp);
    if (!['all', 'none'].includes(comp) && !(state.settings.competitions || []).some((c) => String(c.id) === comp)) comp = 'all';
    return { season, comp, show: f.show === 'all' ? 'all' : 'todo' };
  }
  function saveAdminFilter(f) {
    try { localStorage.setItem('pl.adminFilter', JSON.stringify(f)); } catch (e) { /* storage unavailable */ }
  }
  function adminFiltered(fixtures, f) {
    const sid = f.season === 'all' || !hasSeasons() ? null : (f.season === '' ? state.settings.current_season : Number(f.season));
    return fixtures.filter((x) => (sid === null || x.season === sid)
      && (f.comp === 'all' || (f.comp === 'none' ? !x.competition : x.competition && String(x.competition.id) === f.comp)));
  }
  function adminFilterBar(f, { show = false } = {}) {
    const seasons = state.settings.seasons || [];
    const comps = state.settings.competitions || [];
    if (!seasons.length && !comps.length && !show) return '';
    const cur = state.settings.current_season;
    return `<div class="fixture-tools admin-filter" role="group" aria-label="Show fixtures">
      ${seasons.length ? `<label>Season<select data-af="season">
        ${seasons.map((x) => `<option value="${x.id === cur ? '' : x.id}"${(f.season === '' && x.id === cur) || f.season === String(x.id) ? ' selected' : ''}>${esc(x.name)}${x.id === cur ? ' (current)' : ''}</option>`).join('')}
        ${cur === null ? `<option value=""${f.season === '' ? ' selected' : ''}>Current</option>` : ''}
        <option value="all"${f.season === 'all' ? ' selected' : ''}>All seasons</option></select></label>` : ''}
      ${comps.length ? `<label>Competition<select data-af="comp"><option value="all">All competitions</option>
        ${comps.map((c) => `<option value="${c.id}"${f.comp === String(c.id) ? ' selected' : ''}>${esc(c.name)}</option>`).join('')}
        <option value="none"${f.comp === 'none' ? ' selected' : ''}>No competition</option></select></label>` : ''}
      ${show ? `<label>Show<select data-af="show"><option value="todo"${f.show === 'todo' ? ' selected' : ''}>Needing a result first</option>
        <option value="all"${f.show === 'all' ? ' selected' : ''}>Newest first</option></select></label>` : ''}
    </div>`;
  }
  function bindAdminFilter(onChange) {
    const bar = main.querySelector('.admin-filter');
    if (!bar) return;
    bar.addEventListener('change', () => {
      const get = (k, d) => { const el = bar.querySelector(`[data-af=${k}]`); return el ? el.value : d; };
      const f = adminFilter();
      saveAdminFilter({ season: get('season', f.season), comp: get('comp', f.comp), show: get('show', f.show) });
      onChange();
    });
  }

  async function adminFixtures() {
    const { fixtures: all } = await api('fixtures');
    const filter = adminFilter();
    const fixtures = adminFiltered(all, filter);
    const postponedList = fixtures.filter((f) => f.postponed).sort((a, b) => a.kickoff.localeCompare(b.kickoff));
    const upcoming = fixtures.filter((f) => !f.started && !f.postponed).sort((a, b) => a.kickoff.localeCompare(b.kickoff));
    const past = fixtures.filter((f) => f.started).sort((a, b) => b.kickoff.localeCompare(a.kickoff));
    const rows = state.settings.multi_fixture_rows || 10;
    const hidden = all.length - fixtures.length;

    const list = (items) => items.map((f) => `<li class="admin-item" data-id="${f.id}">
        <label class="pick"><input type="checkbox" data-pick="${f.id}" aria-label="Select ${esc(f.home_team)} v ${esc(f.away_team)}"></label>
        <span class="grow"><strong>${esc(f.home_team)} v ${esc(f.away_team)}</strong> ${ppTag(f)}${f.postponed && f.postponed_reason ? ` <span class="muted">${esc(f.postponed_reason)}</span>` : ''}<br>
        <span class="muted">${compTag(f.competition)}${esc(fmtShort(f.kickoff))}${f.result ? `, final ${f.result.home}–${f.result.away}` : ''}, ${esc(plural(f.prediction_count, 'prediction'))}</span></span>
        <span class="actions">
          ${f.postponed ? `<button class="btn small" type="button" data-edit="${f.id}" data-rearrange>Set new date</button>
            <button class="btn small quiet" type="button" data-unpostpone="${f.id}">Not postponed</button>`
            : `<button class="btn small quiet" type="button" data-edit="${f.id}">Edit</button>
            ${f.result ? '' : `<button class="btn small quiet" type="button" data-postpone="${f.id}">Postpone</button>`}`}
          <button class="btn small danger" type="button" data-delete="${f.id}">Delete</button>
        </span></li>`).join('');

    adminShell('fixtures', `
      <h2>Add fixtures</h2>
      <p class="muted">Times are in ${esc(state.settings.timezone)}. Leave rows you don't need empty.
        ${competitionsOn() ? '' : 'To sort fixtures into competitions, <a href="#/admin/competitions">add a competition</a> first.'}</p>
      <form id="bulk-form" class="grid-form" novalidate>
        <div class="grid-row grid-head${competitionsOn() ? ' with-comp' : ''}" aria-hidden="true"><span>Kick-off</span>${competitionsOn() ? '<span>Competition</span>' : ''}<span>Home team</span><span>Away team</span><span></span></div>
        ${Array.from({ length: rows }, (_, i) => fixtureRow(i)).join('')}
        <div><button class="btn" type="submit">Add fixtures</button></div>
      </form>
      <h2 id="fixture-lists">Fixtures</h2>
      ${adminFilterBar(filter)}
      ${hidden ? `<p class="muted tight">${esc(plural(fixtures.length, 'fixture'))} shown; ${esc(plural(hidden, 'other'))} hidden by the filter.</p>` : ''}
      ${fixtures.length ? `<div class="bulk-bar"><label class="check"><input type="checkbox" id="pick-all"><span>Select all shown</span></label>
        <button class="btn small danger" type="button" id="remove-picked" disabled>Remove selected</button>
        <span class="muted" id="picked-msg" aria-live="polite"></span></div>` : ''}
      <div id="fixture-picks">${postponedList.length ? `<h3>Postponed</h3>
      <p class="muted tight">Predictions are closed until you give each one its new date and time with "Set new date".</p>
      <ul class="admin-list">${list(postponedList)}</ul>` : ''}<h3>Upcoming</h3>
      ${upcoming.length ? `<ul class="admin-list">${list(upcoming)}</ul>` : '<p class="muted">No upcoming fixtures.</p>'}
      <h3>Kicked off</h3>
      ${past.length ? `<ul class="admin-list">${list(past)}</ul>` : '<p class="muted">None yet.</p>'}</div>`);
    addTeamDatalist();
    bindAdminFilter(() => adminFixtures().then(() => document.getElementById('fixture-lists').scrollIntoView()));

    // Removing several fixtures at once
    const picks = () => [...main.querySelectorAll('[data-pick]:checked')].map((i) => Number(i.dataset.pick));
    const removeBtn = document.getElementById('remove-picked');
    const updatePicked = () => {
      const ids = picks();
      const preds = ids.reduce((n, id) => n + (all.find((x) => x.id === id).prediction_count || 0), 0);
      removeBtn.disabled = !ids.length;
      removeBtn.textContent = ids.length ? `Remove ${plural(ids.length, 'fixture')}` : 'Remove selected';
      document.getElementById('picked-msg').textContent = ids.length && preds ? `Their ${plural(preds, 'prediction')} will be removed too.` : '';
      const allBox = document.getElementById('pick-all');
      const boxes = main.querySelectorAll('[data-pick]');
      allBox.checked = ids.length === boxes.length;
      allBox.indeterminate = ids.length > 0 && ids.length < boxes.length;
    };
    if (removeBtn) {
      document.getElementById('fixture-picks').addEventListener('change', (e) => { if (e.target.matches('[data-pick]')) updatePicked(); });
      document.getElementById('pick-all').addEventListener('change', (e) => {
        main.querySelectorAll('[data-pick]').forEach((i) => { i.checked = e.target.checked; });
        updatePicked();
      });
      removeBtn.addEventListener('click', () => {
        const ids = picks();
        armed(removeBtn, `Click again to remove ${plural(ids.length, 'fixture')} for good`, async () => {
          try {
            const res = await submitting(removeBtn, () => api('admin.deletemany', { method: 'POST', body: { ids } }));
            toast(`Removed ${plural(res.deleted, 'fixture')}${res.predictions ? ` and ${plural(res.predictions, 'prediction')}` : ''}. Tables updated.`);
            adminFixtures();
          } catch (err) { toast(err.message, true); }
        });
      });
    }

    const form = document.getElementById('bulk-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fixturesIn = [];
      for (const row of form.querySelectorAll('[data-row]')) {
        const v = (n) => { const el = row.querySelector(`[name=${n}]`); return el ? el.value.trim() : ''; };
        if (!v('kickoff') && !v('home_team') && !v('away_team')) continue;
        fixturesIn.push({ kickoff: v('kickoff'), home_team: v('home_team'), away_team: v('away_team'), competition_id: v('competition_id') || null });
      }
      if (!fixturesIn.length) { formError(form, 'Fill in at least one row.'); return; }
      try { localStorage.setItem('pl.lastComp', fixturesIn[fixturesIn.length - 1].competition_id || ''); } catch (e2) { /* ignore */ }
      try {
        const res = await submitting(form.querySelector('button[type=submit]'), () =>
          api('admin.fixtures', { method: 'POST', body: { fixtures: fixturesIn } }));
        toast(`Added ${plural(res.added, 'fixture')}.`);
        adminFixtures();
      } catch (err) { formError(form, err.message); }
    });

    const byId = Object.fromEntries(all.map((f) => [String(f.id), f]));
    main.querySelectorAll('[data-delete]').forEach((btn) => btn.addEventListener('click', () => {
      const f = byId[btn.dataset.delete];
      armed(btn, f.prediction_count ? `Delete with ${plural(f.prediction_count, 'prediction')}?` : 'Confirm delete', async () => {
        try {
          await api('admin.delete', { method: 'POST', body: { id: f.id } });
          toast(`Deleted ${f.home_team} v ${f.away_team}.`);
          adminFixtures();
        } catch (err) { toast(err.message, true); }
      });
    }));
    main.querySelectorAll('[data-postpone]').forEach((btn) => btn.addEventListener('click', () => {
      const f = byId[btn.dataset.postpone];
      const actions = btn.closest('.actions');
      actions.innerHTML = `<form class="pp-form inline-form"><label>Reason (optional)<input type="text" name="reason" maxlength="80" placeholder="e.g. waterlogged pitch"></label>
        <button class="btn small danger" type="submit">Postpone</button><button class="btn small quiet" type="button" data-cancel>Cancel</button></form>`;
      const pf = actions.querySelector('form');
      pf.reason.focus();
      pf.querySelector('[data-cancel]').addEventListener('click', adminFixtures);
      pf.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
          await submitting(pf.querySelector('[type=submit]'), () => api('admin.postpone', { method: 'POST', body: { id: f.id, postponed: true, reason: pf.reason.value } }));
          toast(`${f.home_team} v ${f.away_team} is postponed. Predictions are closed until it has a new date.`);
          adminFixtures();
        } catch (err) { toast(err.message, true); }
      });
    }));
    main.querySelectorAll('[data-unpostpone]').forEach((btn) => btn.addEventListener('click', () => {
      const f = byId[btn.dataset.unpostpone];
      armed(btn, 'Keep the same date?', async () => {
        try {
          await api('admin.postpone', { method: 'POST', body: { id: f.id, postponed: false } });
          toast(`${f.home_team} v ${f.away_team} is back on at its original time.`);
          adminFixtures();
        } catch (err) { toast(err.message, true); }
      });
    }));
    main.querySelectorAll('[data-edit]').forEach((btn) => btn.addEventListener('click', () => {
      const f = byId[btn.dataset.edit];
      const item = btn.closest('.admin-item');
      item.innerHTML = `<form class="grid-row edit-row${competitionsOn() ? ' with-comp' : ''}">${fixtureInputs(0, f)}
        <span class="actions"><button class="btn small" type="submit">Save</button><button class="btn small quiet" type="button" data-cancel>Cancel</button></span></form>`;
      const ef = item.querySelector('form');
      if (f.postponed) ef.insertAdjacentHTML('afterbegin', '<p class="hint pp-hint">Change the kick-off to the new date and time to put this match back on.</p>');
      ef.querySelector('[name=kickoff]').focus();
      ef.querySelector('[data-cancel]').addEventListener('click', adminFixtures);
      ef.addEventListener('submit', async (e) => {
        e.preventDefault();
        const v = (n) => { const el = ef.querySelector(`[name=${n}]`); return el ? el.value.trim() : ''; };
        try {
          const res = await submitting(ef.querySelector('[type=submit]'), () => api('admin.fixture', { method: 'POST', body: {
            id: f.id, kickoff: v('kickoff'), home_team: v('home_team'), away_team: v('away_team'), competition_id: v('competition_id') || null } }));
          toast(f.postponed ? (res.fixture.postponed ? 'Saved, but it\'s still postponed: change the kick-off to its new date and time.'
            : `Rearranged for ${fmtShort(res.fixture.kickoff)}. Predictions are open again.`) : 'Fixture saved.');
          adminFixtures();
        } catch (err) { toast(err.message, true); }
      });
    }));
  }

  const PLACE_COLOURS = [['green', 'Green'], ['darkgreen', 'Dark green'], ['blue', 'Blue'], ['teal', 'Teal'], ['purple', 'Purple'],
    ['amber', 'Yellow'], ['orange', 'Orange'], ['red', 'Red'], ['grey', 'Grey']];
  const PLACE_PRESETS = {
    'premier-league': ['Premier League style', [[1, 4, false, 'Champions League', 'green'], [5, 5, false, 'Europa League', 'blue'], [6, 6, false, 'Conference League', 'teal'], [1, 3, true, 'Relegation', 'red']]],
    championship: ['Championship / League One style', [[1, 2, false, 'Promotion', 'green'], [3, 6, false, 'Play-offs', 'blue'], [1, 3, true, 'Relegation', 'red']]],
    'league-two': ['League Two style', [[1, 3, false, 'Promotion', 'green'], [4, 7, false, 'Play-offs', 'blue'], [1, 2, true, 'Relegation', 'red']]],
    champions: ['Champions only', [[1, 1, false, 'Champions', 'amber']]],
  };
  // "1st", "2nd to 4th", "bottom 3", "bottom 2 to 3"
  function placeRange(p) {
    if (p.from_bottom) {
      if (p.from === 1) return p.to === 1 ? 'Bottom' : `Bottom ${p.to}`;
      return `${ordinal(p.from)} to ${ordinal(p.to)} from bottom`;
    }
    return p.from === p.to ? ordinal(p.from) : `${ordinal(p.from)} to ${ordinal(p.to)}`;
  }
  function placesText(places) {
    return places.length ? `: ${places.map((p) => `${placeRange(p)} ${p.label || p.colour}`).join(', ')}` : '';
  }
  function placeRowHtml(p) {
    const v = p || { from: '', to: '', from_bottom: false, label: '', colour: 'green' };
    return `<div class="place-row">
      <label>From<input type="number" name="p_from" min="1" max="60" value="${esc(String(v.from))}" required></label>
      <label>To<input type="number" name="p_to" min="1" max="60" value="${esc(String(v.to))}"></label>
      <label>Counting<select name="p_end"><option value="top"${v.from_bottom ? '' : ' selected'}>from the top</option><option value="bottom"${v.from_bottom ? ' selected' : ''}>from the bottom</option></select></label>
      <label class="grow">Means<input type="text" name="p_label" maxlength="40" value="${esc(v.label)}" placeholder="e.g. Champions League"></label>
      <label>Colour<span class="colour-pick"><span class="swatch zc-${esc(v.colour)}" aria-hidden="true"></span><select name="p_colour">${PLACE_COLOURS.map(([k, n]) => `<option value="${k}"${k === v.colour ? ' selected' : ''}>${n}</option>`).join('')}</select></span></label>
      <button class="btn small quiet" type="button" data-remove-place aria-label="Remove this place">Remove</button>
    </div>`;
  }
  // Type and marked places, shared by the add and edit forms.
  function compTableFields(c) {
    const cup = c && !c.has_table;
    return `<label>Type<select name="table_type">
        <option value="league"${cup ? '' : ' selected'}>League: show a league table</option>
        <option value="cup"${cup ? ' selected' : ''}>Cup or tournament: no league table</option></select></label>
      <fieldset class="places-editor"${cup ? ' disabled' : ''}><legend>What the positions mean <span class="hint">Optional. Each line colours some positions in the table and explains them in a key, e.g. 1st to 4th Champions League, bottom 3 Relegation. If lines overlap, the first one wins.</span></legend>
        <div class="place-rows">${(c ? c.places : []).map(placeRowHtml).join('')}</div>
        <div class="place-tools"><button class="btn small quiet" type="button" data-add-place>Add a line</button>
          <select data-preset aria-label="Start from a preset"><option value="">Start from a preset…</option>
            ${Object.entries(PLACE_PRESETS).map(([k, [n]]) => `<option value="${k}">${esc(n)}</option>`).join('')}</select></div>
      </fieldset>`;
  }
  const compTableBody = (f) => ({
    table_type: f.table_type.value,
    places: [...f.querySelectorAll('.place-row')].map((r) => ({
      from: Number(r.querySelector('[name=p_from]').value), to: Number(r.querySelector('[name=p_to]').value || r.querySelector('[name=p_from]').value),
      from_bottom: r.querySelector('[name=p_end]').value === 'bottom', label: r.querySelector('[name=p_label]').value, colour: r.querySelector('[name=p_colour]').value,
    })).filter((p) => p.from > 0),
  });
  function bindTypeSelect(f) {
    const box = f.querySelector('.places-editor');
    const rows = box.querySelector('.place-rows');
    f.table_type.addEventListener('change', () => { box.disabled = f.table_type.value === 'cup'; });
    box.addEventListener('click', (e) => {
      if (e.target.closest('[data-add-place]')) {
        rows.insertAdjacentHTML('beforeend', placeRowHtml(null));
        rows.lastElementChild.querySelector('[name=p_from]').focus();
      }
      if (e.target.closest('[data-remove-place]')) e.target.closest('.place-row').remove();
    });
    box.addEventListener('change', (e) => {
      if (e.target.name === 'p_colour') e.target.previousElementSibling.className = `swatch zc-${e.target.value}`;
      if (e.target.matches('[data-preset]') && e.target.value) {
        const [, list] = PLACE_PRESETS[e.target.value];
        rows.innerHTML = list.map(([from, to, fb, label, colour]) => placeRowHtml({ from, to, from_bottom: fb, label, colour })).join('');
        e.target.value = '';
      }
    });
  }

  async function adminCompetitions() {
    const render = (d) => {
      state.settings = d.public;
      const n = d.competitions.length;
      const items = d.competitions.map((c, i) => `<li class="admin-item" data-comp="${c.id}">
          <span class="comp-tag">${esc(c.short)}</span>
          <span class="grow"><strong>${esc(c.name)}</strong><br><span class="muted">${esc(plural(c.fixtures, 'fixture'))} · ${c.has_table ? `league table${esc(placesText(c.places))}` : 'cup, no table'}</span></span>
          <span class="actions">
            <button class="btn small quiet" type="button" data-move="-1"${i === 0 ? ' disabled' : ''} aria-label="Move ${esc(c.name)} up">Up</button>
            <button class="btn small quiet" type="button" data-move="1"${i === n - 1 ? ' disabled' : ''} aria-label="Move ${esc(c.name)} down">Down</button>
            <button class="btn small quiet" type="button" data-rename>Edit</button>
            <button class="btn small danger" type="button" data-drop>Delete</button>
          </span></li>`).join('');
      adminShell('competitions', `
        <p class="lede">Sort fixtures into competitions, such as a league and its cups. Players can then filter and group fixtures by competition.
          The order here is the order competitions are listed in.</p>
        ${n ? `<ul class="admin-list">${items}</ul>` : '<p class="muted">No competitions yet. Fixtures are listed by date only.</p>'}
        ${d.uncategorised ? `<p class="muted">${esc(plural(d.uncategorised, 'fixture'))} ${d.uncategorised === 1 ? 'isn\'t' : 'aren\'t'} in a competition. Set it on Admin > Fixtures with Edit, or they show as "Other fixtures".</p>` : ''}
        <h2>Add a competition</h2>
        <form class="stack" id="comp-form">
          <label>Name<input type="text" name="name" maxlength="40" required placeholder="Premier League"></label>
          <label>Short name<span class="hint">Up to 8 characters, shown as a tag next to fixtures. Leave empty and we'll make one, e.g. PL.</span>
            <input type="text" name="short_name" maxlength="8" placeholder="PL"></label>
          ${compTableFields(null)}
          <div><button class="btn" type="submit">Add competition</button></div>
        </form>`);
      const order = d.competitions.map((c) => c.id);
      main.querySelectorAll('[data-move]').forEach((btn) => btn.addEventListener('click', async () => {
        const id = Number(btn.closest('[data-comp]').dataset.comp);
        const i = order.indexOf(id);
        const j = i + Number(btn.dataset.move);
        [order[i], order[j]] = [order[j], order[i]];
        try {
          render(await api('admin.competitionorder', { method: 'POST', body: { ids: order } }));
          main.querySelector(`[data-comp="${id}"] [data-move="${btn.dataset.move}"]`)?.focus();
        } catch (err) { toast(err.message, true); }
      }));
      main.querySelectorAll('[data-rename]').forEach((btn) => btn.addEventListener('click', () => {
        const li = btn.closest('[data-comp]');
        const c = d.competitions.find((x) => x.id === Number(li.dataset.comp));
        li.innerHTML = `<form class="comp-edit">
          <label>Name<input type="text" name="name" maxlength="40" required value="${esc(c.name)}"></label>
          <label>Short name<input type="text" name="short_name" maxlength="8" value="${esc(c.short)}"></label>
          ${compTableFields(c)}
          <span class="actions"><button class="btn small" type="submit">Save</button><button class="btn small quiet" type="button" data-cancel>Cancel</button></span></form>`;
        const f = li.querySelector('form');
        f.name.focus();
        bindTypeSelect(f);
        f.querySelector('[data-cancel]').addEventListener('click', () => render(d));
        f.addEventListener('submit', async (e) => {
          e.preventDefault();
          try {
            render(await submitting(f.querySelector('[type=submit]'), () => api('admin.competition', { method: 'POST', body: { id: c.id, name: f.name.value, short_name: f.short_name.value, ...compTableBody(f) } })));
            toast('Competition saved.');
          } catch (err) { toast(err.message, true); }
        });
      }));
      main.querySelectorAll('[data-drop]').forEach((btn) => btn.addEventListener('click', () => {
        const c = d.competitions.find((x) => x.id === Number(btn.closest('[data-comp]').dataset.comp));
        armed(btn, c.fixtures ? `Delete? ${plural(c.fixtures, 'fixture')} will be kept` : 'Confirm delete', async () => {
          try {
            render(await api('admin.competitiondelete', { method: 'POST', body: { id: c.id } }));
            toast(`Deleted ${c.name}.`);
          } catch (err) { toast(err.message, true); }
        });
      }));
      const form = document.getElementById('comp-form');
      bindTypeSelect(form);
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
          render(await submitting(form.querySelector('[type=submit]'), () => api('admin.competition', { method: 'POST', body: { name: form.name.value, short_name: form.short_name.value, ...compTableBody(form) } })));
          toast('Competition added.');
          document.querySelector('#comp-form [name=name]').focus();
        } catch (err) { formError(form, err.message); }
      });
    };
    render(await api('admin.competitions'));
  }

  async function adminResults() {
    const { fixtures: all } = await api('fixtures');
    const filter = adminFilter();
    const allStarted = all.filter((f) => f.started);
    const started = adminFiltered(allStarted, filter)
      .sort((a, b) => (filter.show === 'todo' ? (!!a.result - !!b.result) : 0) || b.kickoff.localeCompare(a.kickoff));
    if (!allStarted.length) {
      adminShell('results', `<p class="lede">No matches have kicked off yet. Results can be entered once a match starts.</p>`);
      return;
    }
    const waiting = started.filter((f) => !f.result).length;
    const elsewhere = allStarted.filter((f) => !f.result).length - waiting;
    const ppCount = all.filter((f) => f.postponed).length;
    let html = `<p class="lede">${waiting ? `${esc(plural(waiting, 'match', 'matches'))} here need a result.` : 'Every match shown has a result.'}
      The table updates as soon as you save. Clear both scores to remove a result.</p>
      ${adminFilterBar(filter, { show: true })}
      ${elsewhere ? `<p class="muted tight">${esc(plural(elsewhere, 'other match', 'other matches'))} outside this season or competition ${elsewhere === 1 ? 'needs' : 'need'} a result.</p>` : ''}
      ${ppCount ? `<p class="muted tight">${esc(plural(ppCount, 'postponed match', 'postponed matches'))} ${ppCount === 1 ? 'isn\'t' : 'aren\'t'} listed. <a href="#/admin/fixtures">Give ${ppCount === 1 ? 'it its' : 'them their'} new date</a> first.</p>` : ''}
      ${started.length ? '' : '<p class="muted">No matches that have kicked off match these choices.</p>'}<ul class="fixtures">`;
    for (const f of started) {
      const box = (side) => `<input class="box${f.result ? ' filled' : ''}" type="number" inputmode="numeric" min="0" max="99"
        data-side="${side}" value="${f.result ? f.result[side] : ''}" aria-label="${esc(side === 'home' ? f.home_team : f.away_team)} goals">`;
      html += `<li class="fixture" data-id="${f.id}">
        <span class="time">${esc(fmtShort(f.kickoff).split(',')[0])}${compTag(f.competition)}</span>
        <span class="team home">${teamName(f, 'home')}</span>
        <span class="score">${box('home')}<span class="dash" aria-hidden="true">–</span>${box('away')}</span>
        <span class="team away">${teamName(f, 'away')}</span>
        <span class="status"><button class="btn small" type="button" data-save>${f.result ? 'Update' : 'Save result'}</button></span>
      </li>`;
    }
    html += `</ul><div class="savebar"><button class="btn" type="button" id="save-all">Save all results</button>
      <span class="msg" id="results-msg" aria-live="polite">Fill in several scores, then save them together.</span></div>`;
    adminShell('results', html + '');
    bindAdminFilter(adminResults);
    if (!started.length) return;
    const original = Object.fromEntries(started.map((f) => [String(f.id), f.result ? `${f.result.home}-${f.result.away}` : '-']));
    const changedRows = () => [...main.querySelectorAll('li.fixture[data-id]')].filter((li) =>
      `${li.querySelector('[data-side=home]').value}-${li.querySelector('[data-side=away]').value}` !== original[li.dataset.id]);
    main.querySelector('ul.fixtures').addEventListener('input', (e) => {
      if (!e.target.classList.contains('box')) return;
      e.target.classList.toggle('filled', e.target.value !== '');
      const n = changedRows().length;
      document.getElementById('results-msg').textContent = n ? `${plural(n, 'result')} changed, not saved yet.` : 'No unsaved changes.';
    });
    document.getElementById('save-all').addEventListener('click', async (e) => {
      const rows = changedRows();
      if (!rows.length) { toast('No results have changed.'); return; }
      const results = rows.map((li) => ({ id: Number(li.dataset.id),
        home: li.querySelector('[data-side=home]').value, away: li.querySelector('[data-side=away]').value }));
      try {
        const res = await submitting(e.currentTarget, () => api('admin.results', { method: 'POST', body: { results } }));
        toast(`Saved ${plural(res.saved, 'result')}. Table updated.`);
        adminResults();
      } catch (err) { toast(err.message, true); }
    });
    main.querySelectorAll('[data-save]').forEach((btn) => btn.addEventListener('click', async () => {
      const li = btn.closest('[data-id]');
      const home = li.querySelector('[data-side=home]').value;
      const away = li.querySelector('[data-side=away]').value;
      try {
        await submitting(btn, () => api('admin.result', { method: 'POST', body: { id: Number(li.dataset.id), home, away } }));
        toast(home === '' ? 'Result cleared. Table updated.' : `Result saved: ${home}–${away}. Table updated.`);
        adminResults();
      } catch (err) { toast(err.message, true); }
    }));
  }

  async function adminPlayers() {
    const data = await api('admin.users');
    const render = (d) => {
      const items = d.users.map((u) => `<li class="admin-item" data-player="${u.id}">
        ${avatar(u)}<span class="grow"><strong>${esc(u.username)}</strong> ${u.is_admin ? '<span class="tag">Admin</span>' : ''}<br>
          <span class="muted">${esc(u.email || 'No email')}, ${esc(plural(u.points, 'point'))}${u.since ? `, joined ${esc(u.since)}` : ''}</span></span>
        <span class="actions">${u.id === state.user.id ? '<span class="muted">This is you</span>' : `
          <button class="btn small quiet" type="button" data-reset="${u.id}">Reset password</button>
          <button class="btn small quiet" type="button" data-admin="${u.id}" data-make="${u.is_admin ? 0 : 1}">${u.is_admin ? 'Remove admin' : 'Make admin'}</button>
          <button class="btn small danger" type="button" data-remove="${u.id}">Delete player</button>`}
        </span></li>`).join('');
      adminShell('players', `
        <p class="lede">${esc(plural(d.users.length, 'player'))}. Up to ${esc(plural(d.max_admins, 'admin'))} allowed.</p>
        <ul class="admin-list">${items}</ul>
        <h2>Add a player</h2>
        <form class="stack" id="add-player">
          <label>Username<input type="text" name="username" required maxlength="32" autocomplete="off"></label>
          <label>Email address<input type="email" name="email" required maxlength="60" autocomplete="off"></label>
          <label>Password<span class="hint">At least 8 characters. Tell the player so they can log in and change it.</span>
            <input type="text" name="password" required minlength="8" autocomplete="off"></label>
          <div><button class="btn" type="submit">Add player</button></div>
        </form>`);
      main.querySelectorAll('[data-admin]').forEach((btn) => btn.addEventListener('click', async () => {
        try {
          render(await submitting(btn, () => api('admin.user', { method: 'POST', body: { id: Number(btn.dataset.admin), is_admin: btn.dataset.make === '1' } })));
          toast('Player updated.');
        } catch (err) { toast(err.message, true); }
      }));
      main.querySelectorAll('[data-reset]').forEach((btn) => btn.addEventListener('click', async () => {
        const item = btn.closest('.admin-item');
        const u = d.users.find((x) => x.id === Number(btn.dataset.reset));
        try {
          const res = await submitting(btn, () => api('admin.resetlink', { method: 'POST', body: { id: u.id } }));
          item.querySelector('.reset-panel')?.remove();
          item.insertAdjacentHTML('beforeend', `<div class="reset-panel">
            <label>Reset link for ${esc(u.username)}<span class="hint">Works once, until ${esc(res.expires)}. Send it to them however suits you.</span>
              <input type="text" readonly value="${esc(res.link)}"></label>
            <div class="actions"><button class="btn small" type="button" data-copy>Copy link</button>
            ${state.settings.email && u.email ? `<button class="btn small quiet" type="button" data-mail>Email it to ${esc(u.email)}</button>` : ''}</div></div>`);
          const panel = item.querySelector('.reset-panel');
          const field = panel.querySelector('input');
          field.select();
          panel.querySelector('[data-copy]').addEventListener('click', async () => {
            try { await navigator.clipboard.writeText(field.value); toast('Link copied.'); }
            catch (e) { field.select(); toast('Press Ctrl+C (or Cmd+C) to copy the selected link.'); }
          });
          panel.querySelector('[data-mail]')?.addEventListener('click', async (e) => {
            try {
              await submitting(e.currentTarget, () => api('admin.resetlink', { method: 'POST', body: { id: u.id, send: true } }));
              toast(`Emailed a reset link to ${u.email}.`);
              panel.remove();
            } catch (err) { toast(err.message, true); }
          });
        } catch (err) { toast(err.message, true); }
      }));
      main.querySelectorAll('[data-remove]').forEach((btn) => btn.addEventListener('click', () => {
        armed(btn, 'Delete player and predictions?', async () => {
          try {
            render(await api('admin.deleteuser', { method: 'POST', body: { id: Number(btn.dataset.remove) } }));
            toast('Player deleted.');
          } catch (err) { toast(err.message, true); }
        });
      }));
      const form = document.getElementById('add-player');
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
          const res = await submitting(form.querySelector('button'), () => api('register', { method: 'POST', body: {
            username: form.username.value, email: form.email.value, password: form.password.value } }));
          toast(`Added ${res.created.username}.`);
          render(await api('admin.users'));
        } catch (err) { formError(form, err.message); }
      });
    };
    render(data);
  }

  async function adminSettings() {
    const { settings } = await api('admin.settings');
    const groups = { 1: 'Scoring', 2: 'League', 3: 'Rules', 5: 'Email', 6: 'Block alerts' };
    const relevant = settings.filter((s) => !/^(Old script only|Not used in scoring)/.test(s.description) &&
      !['DEF_ICON', 'ADMIN_SIGNATURE', 'CHATROOM_URL', 'LOGFILE', 'DEFAULT_LANGUAGE', 'TZ_OFFSET'].includes(s.key));
    let html = `<p class="lede">Changes take effect straight away. Scoring changes recalculate the whole table.</p><form id="settings-form">`;
    for (const [g, title] of Object.entries(groups)) {
      const items = relevant.filter((s) => String(s.group) === g);
      if (!items.length) continue;
      html += `<fieldset class="settings-group"><legend>${title}</legend>`;
      for (const s of items) {
        const id = `set-${s.key}`;
        if (s.type === 'select') {
          html += `<label for="${id}">${esc(s.description)} <span class="setting-key">${esc(s.key)}</span>
            <select id="${id}" name="${esc(s.key)}">${Object.entries(s.options).map(([v, l]) =>
              `<option value="${esc(v)}"${v === s.value ? ' selected' : ''}>${esc(l)}</option>`).join('')}</select></label>`;
        } else if (s.type === 'secret') {
          html += `<label for="${id}">${esc(s.description)} <span class="setting-key">${esc(s.key)}</span>
            <input type="password" id="${id}" name="${esc(s.key)}" value="" maxlength="90" autocomplete="new-password"
              placeholder="${s.is_set ? 'Saved. Leave empty to keep it.' : 'Not set'}"></label>`;
        } else if (s.type === 'bool') {
          html += `<label class="check" for="${id}"><input type="checkbox" id="${id}" name="${esc(s.key)}" ${s.value === 'TRUE' ? 'checked' : ''}>
            <span>${esc(s.description)} <span class="setting-key">${esc(s.key)}</span></span></label>`;
        } else {
          const type = s.type === 'number' ? 'number' : s.type === 'email' ? 'email' : s.type === 'url' ? 'url' : 'text';
          html += `<label for="${id}">${esc(s.description)} <span class="setting-key">${esc(s.key)}</span>
            <input type="${type}" id="${id}" name="${esc(s.key)}" value="${esc(s.value)}" maxlength="90"${type === 'number' ? ' step="any"' : ''}></label>`;
        }
      }
      if (g === '5') {
        html += `<div class="test-mail"><button class="btn quiet small" type="button" id="test-mail">Send a test email to ${esc(state.user.email || 'me')}</button>
          <span class="muted">Save your changes first.</span></div>`;
      }
      if (g === '6') {
        html += `<p class="muted tight">A separate email account, so these alerts never go through the league's normal email. Up to one email a day, only on days with new blocks.</p>
          <div class="test-mail"><button class="btn quiet small" type="button" id="test-alert">Send a test alert</button>
          <button class="btn quiet small" type="button" id="send-alert">Send the list now</button>
          <span class="muted">Save your changes first.</span></div>`;
      }
      html += `</fieldset>`;
    }
    html += `<div class="savebar"><button class="btn" type="submit">Save settings</button>
      <button class="btn quiet" type="button" id="recalc">Recalculate table</button></div></form>`;
    adminShell('settings', html);

    const form = document.getElementById('settings-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const values = {};
      form.querySelectorAll('input[name], select[name]').forEach((i) => {
        values[i.name] = i.type === 'checkbox' ? (i.checked ? 'TRUE' : 'FALSE') : i.value;
      });
      try {
        const res = await submitting(form.querySelector('[type=submit]'), () => api('admin.savesettings', { method: 'POST', body: { values } }));
        state.settings = res.public;
        applySession({ user: state.user, csrf: state.csrf, settings: res.public, first_user_becomes_admin: state.firstAdmin });
        form.querySelectorAll('input[type=password]').forEach((i) => { if (i.value) { i.value = ''; i.placeholder = 'Saved. Leave empty to keep it.'; } });
        renderNav('admin');
        toast('Settings saved.');
      } catch (err) { formError(form, err.message); }
    });
    document.getElementById('test-mail').addEventListener('click', async (e) => {
      try {
        const res = await submitting(e.currentTarget, () => api('admin.testmail', { method: 'POST', body: {} }));
        toast(`Test email sent to ${res.sent_to}. Check that inbox (and its spam folder).`);
      } catch (err) { toast(err.message, true); }
    });
    const testAlert = document.getElementById('test-alert');
    if (testAlert) {
      testAlert.addEventListener('click', async (e) => {
        try {
          const res = await submitting(e.currentTarget, () => api('admin.alerttest', { method: 'POST', body: {} }));
          toast(`Test alert sent to ${res.sent_to.join(', ')}. Check that inbox (and its spam folder).`);
        } catch (err) { toast(err.message, true); }
      });
      document.getElementById('send-alert').addEventListener('click', async (e) => {
        try {
          const res = await submitting(e.currentTarget, () => api('admin.alertsend', { method: 'POST', body: {} }));
          toast(res.status === 'sent' ? `Sent the list of ${plural(res.count, 'blocked address', 'blocked addresses')} to ${res.to.join(', ')}.`
            : 'No addresses have been blocked since the last list, so nothing was sent.');
        } catch (err) { toast(err.message, true); }
      });
    }
    document.getElementById('recalc').addEventListener('click', async (e) => {
      try {
        const res = await submitting(e.currentTarget, () => api('admin.recalculate', { method: 'POST' }));
        toast(`Table recalculated for ${plural(res.rows, 'player')}.`);
      } catch (err) { toast(err.message, true); }
    });
  }

  // ------------------------------------------------------------------
  // Messages
  // ------------------------------------------------------------------

  function messagesShell(tab, inner) {
    setPage(`<div class="title-row"><h1>Messages</h1><a class="btn" href="#/compose">New message</a></div>
      <nav class="tabs" aria-label="Messages">
        <a href="#/messages"${tab === 'inbox' ? ' aria-current="page"' : ''}>Inbox</a>
        <a href="#/messages/sent"${tab === 'sent' ? ' aria-current="page"' : ''}>Sent</a>
      </nav>${inner}`);
  }

  async function viewMessages(box) {
    renderNav('messages');
    if (!state.user) return needLogin('read messages');
    const data = await api('messages', { query: { box } });
    state.unread = data.unread;
    renderNav('messages');
    if (!data.messages.length) {
      messagesShell(box, `<p class="lede">${box === 'sent' ? 'You haven\'t sent any messages yet.' : 'No messages yet. When another player messages you, it shows up here.'}</p>`);
      return;
    }
    const rows = data.messages.map((m) => {
      const who = box === 'sent' ? m.to : m.from;
      return `<li class="msg-row${m.unread ? ' unread' : ''}">
        <a href="#/message/${m.id}">
          ${avatar(who)}
          <span class="msg-who">${box === 'sent' ? 'To ' : ''}${esc(who.username)}${m.unread ? ' <span class="visually-hidden">(unread)</span>' : ''}</span>
          <span class="msg-subject">${esc(m.subject)}</span>
          <span class="msg-preview">${esc(m.preview)}</span>
          <span class="msg-time">${esc(fmtShort(m.sent))}</span>
        </a></li>`;
    }).join('');
    messagesShell(box, `<ul class="msg-list">${rows}</ul>`);
  }

  async function viewMessage(id) {
    renderNav('messages');
    if (!state.user) return needLogin('read messages');
    const data = await api('message', { query: { id } });
    state.unread = data.unread;
    renderNav('messages');
    const m = data.message;
    const canReply = data.other.id !== null;
    const items = data.thread.map((t) => `<article class="msg${t.mine ? ' mine' : ''}" id="m${t.id}">
        <header>${avatar(t.from)}<strong>${t.mine ? 'You' : esc(t.from.username)}</strong>
          <span class="muted">${esc(fmtShort(t.sent))}</span></header>
        <div class="msg-body">${esc(t.body)}</div>
      </article>`).join('');
    const received = !m.mine;
    setPage(`<p><a href="#/messages${m.mine ? '/sent' : ''}">${m.mine ? 'Sent messages' : 'Inbox'}</a></p>
      <h1 class="msg-title">${esc(m.subject)}</h1>
      <p class="lede">Conversation with ${data.other.id ? `<a href="#/player/${data.other.id}">${esc(data.other.username)}</a>` : esc(data.other.username)}.</p>
      <div class="thread">${items}</div>
      ${canReply ? `<form class="stack reply" id="reply-form">
        <label>Reply to ${esc(data.other.username)}<textarea name="body" rows="5" maxlength="5000" required></textarea></label>
        <div class="actions"><button class="btn" type="submit">Send reply</button>
        ${received ? '<button class="btn quiet danger" type="button" id="delete-msg">Delete message</button>' : ''}</div>
      </form>` : '<p class="muted">This player has left the league, so you can\'t reply.</p>'}`);

    const last = main.querySelector('.thread .msg:last-child');
    if (last && data.thread.length > 2) last.scrollIntoView({ block: 'nearest' });
    const form = document.getElementById('reply-form');
    if (form) {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
          const res = await submitting(form.querySelector('[type=submit]'), () =>
            api('message.send', { method: 'POST', body: { reply_to: m.id, body: form.body.value } }));
          toast('Reply sent.');
          location.hash = `#/message/${res.id}`;
        } catch (err) { formError(form, err.message); }
      });
    }
    const del = document.getElementById('delete-msg');
    if (del) del.addEventListener('click', () => armed(del, 'Confirm delete', async () => {
      try {
        await api('message.delete', { method: 'POST', body: { id: m.id } });
        toast('Message deleted.');
        location.hash = '#/messages';
      } catch (err) { toast(err.message, true); }
    }));
  }

  async function viewCompose(toId) {
    renderNav('messages');
    if (!state.user) return needLogin('send messages');
    const { players, email_optin: emailOptin = 0 } = await api('players');
    const others = players.filter((p) => p.id !== state.user.id);
    const options = others.map((p) => `<option value="${p.id}"${String(p.id) === String(toId) ? ' selected' : ''}>${esc(p.username)}</option>`).join('');
    setPage(`<p><a href="#/messages">Messages</a></p><h1>New message</h1>
      <form class="stack wide-form" id="compose-form">
        <label>To<select name="to" required>
          <option value="">Choose a player</option>
          ${state.user.is_admin ? '<option value="all">Every player</option>' : ''}
          ${options}</select></label>
        <label>Subject<input type="text" name="subject" maxlength="120" required></label>
        <label>Message<textarea name="body" rows="8" maxlength="5000" required></textarea></label>
        ${state.user.is_admin && state.settings.email ? `<label class="check" id="email-too-row" hidden><input type="checkbox" name="email_too">
          <span>Also email it to the ${esc(plural(emailOptin, 'player'))} who ${emailOptin === 1 ? 'allows' : 'allow'} emails from the admin</span></label>` : ''}
        <div><button class="btn" type="submit">Send message</button></div>
      </form>`);
    const form = document.getElementById('compose-form');
    (toId ? form.subject : form.to).focus();
    const emailRow = document.getElementById('email-too-row');
    if (emailRow) form.to.addEventListener('change', () => { emailRow.hidden = form.to.value !== 'all'; });
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const toAll = form.to.value === 'all';
      if (!form.to.value) { formError(form, 'Choose who to send the message to.'); return; }
      try {
        const res = await submitting(form.querySelector('[type=submit]'), () => api('message.send', { method: 'POST', body: {
          to: toAll ? 0 : Number(form.to.value), to_all: toAll, email_too: toAll && !!(form.email_too && form.email_too.checked),
          subject: form.subject.value, body: form.body.value } }));
        toast(toAll ? `Message sent to ${plural(res.sent, 'player')}${res.emailed ? `, and emailed to ${res.emailed}` : ''}.` : 'Message sent.');
        location.hash = toAll ? '#/messages/sent' : `#/message/${res.id}`;
      } catch (err) { formError(form, err.message); }
    });
  }

  // ------------------------------------------------------------------
  // Password reset
  // ------------------------------------------------------------------

  function viewForgot() {
    renderNav('login');
    if (!state.settings.email) {
      setPage(`<h1>Forgotten your password?</h1><p class="lede">Ask the league admin for a reset link. They can create one from the Players page.</p>`);
      return;
    }
    setPage(`<h1>Forgotten your password?</h1>
      <p class="lede">Enter your username or email address. We'll email you a link to choose a new password.</p>
      <form class="stack" id="forgot-form">
        <label>Username or email address<input type="text" name="login" autocomplete="username" required maxlength="60"></label>
        ${honeypotField()}
        <div><button class="btn" type="submit">Email me a reset link</button></div>
      </form>
      <p class="form-foot"><a href="#/login">Back to log in</a></p>`);
    const form = document.getElementById('forgot-form');
    const check = botCheck('reset');
    form.login.focus();
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const res = await submitting(form.querySelector('button'), () => check.send('reset.request', form, { login: form.login.value }));
        setPage(`<h1>Check your email</h1><p class="lede">${esc(res.message)}</p>
          <p class="muted">Nothing after a few minutes? Check your spam folder, or ask the league admin for a reset link.</p>
          <p><a href="#/login">Back to log in</a></p>`);
      } catch (err) { formError(form, err.message); }
    });
  }

  async function viewReset(token) {
    renderNav('login');
    const check = await api('reset.check', { query: { token } });
    if (!check.valid) {
      setPage(`<h1>This link has expired</h1>
        <p class="lede">Reset links work once and only for a limited time.</p>
        <p>${state.settings.email ? '<a class="btn" href="#/forgot">Get a new link</a>' : 'Ask the league admin for a new reset link.'}</p>`);
      return;
    }
    setPage(`<h1>Choose a new password</h1>
      <p class="lede">For <strong>${esc(check.username)}</strong>.</p>
      <form class="stack" id="reset-form">
        <input type="text" name="username" value="${esc(check.username)}" autocomplete="username" hidden>
        <label>New password<span class="hint">At least 8 characters.</span>
          <input type="password" name="password" autocomplete="new-password" required minlength="8"></label>
        <label>New password again<input type="password" name="password2" autocomplete="new-password" required minlength="8"></label>
        <div><button class="btn" type="submit">Save password and log in</button></div>
      </form>`);
    const form = document.getElementById('reset-form');
    form.password.focus();
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (form.password.value !== form.password2.value) { formError(form, 'The two passwords don\'t match.'); return; }
      try {
        const s = await submitting(form.querySelector('button'), () => api('reset.confirm', { method: 'POST', body: { token, password: form.password.value } }));
        applySession(s);
        toast('Password changed. You\'re logged in.');
        location.hash = '#/predictions';
      } catch (err) { formError(form, err.message); }
    });
  }

  // ------------------------------------------------------------------
  // Admin: data (backup, restore, CSV) and activity log
  // ------------------------------------------------------------------

  async function adminData() {
    adminShell('data', `
      <section class="data-section">
        <h2>Backup</h2>
        <p>Download everything in the league: players (including their password hashes), fixtures, predictions, messages and settings. Keep the file somewhere safe.</p>
        <p><button class="btn" type="button" id="backup">Download backup</button></p>
      </section>
      <section class="data-section">
        <h2>Spreadsheets</h2>
        <p>Download as CSV files that open in Excel, Numbers or Google Sheets.</p>
        <p class="actions">
          <button class="btn quiet small" type="button" data-csv="table">League table</button>
          <button class="btn quiet small" type="button" data-csv="fixtures">Fixtures and results</button>
          <button class="btn quiet small" type="button" data-csv="predictions">All predictions</button>
          <button class="btn quiet small" type="button" data-csv="players">Players</button>
        </p>
      </section>
      <section class="data-section">
        <h2>Import fixtures and results from a spreadsheet</h2>
        <p>Use a CSV file with these column headings in the first row: <strong>Date, Time, Home, Away</strong>, and optionally
          <strong>Competition</strong> and <strong>Result</strong> (like 2 - 1). Fixture downloads such as fixturedownload.com's
          (Date, Home Team, Away Team, Result) work as they are. Dates like 26/12/2026 15:00 or 2026-12-26 both work.</p>
        <p class="muted">Importing an updated copy of the same file is safe: fixtures already here get their new kick-off time and results instead of being added twice.
          You'll see what will change before anything is saved.</p>
        <form id="import-form" class="stack wide-form">
          <label>Competition<span class="hint">For rows without a Competition column.</span>
            <select name="competition_id"><option value="">None</option>
              ${(state.settings.competitions || []).map((c) => `<option value="${c.id}">${esc(c.name)}</option>`).join('')}
              <option value="new">A new competition…</option></select></label>
          <label hidden data-new-comp>New competition's name<input type="text" name="competition_name" maxlength="40" placeholder="Championship"></label>
          <label class="check"><input type="checkbox" name="add_to_list" checked><span>Add the teams to the competition's team list for each season
            <span class="hint">So fixtures you add by hand later only suggest these teams.</span></span></label>
          <div><label class="btn quiet file-btn">Choose a CSV file<input type="file" name="csv" accept=".csv,text/csv" class="visually-hidden"></label></div>
        </form>
        <div id="import-preview"></div>
      </section>
      <section class="data-section danger-zone">
        <h2>Restore a backup</h2>
        <p>Replaces <strong>all</strong> players, fixtures, predictions, messages and settings with the ones in a backup file.
          You'll be asked to download a backup of the current league first. The activity log is kept.</p>
        <form class="stack" id="restore-form">
          <label>Backup file<input type="file" name="backup" accept=".json,application/json" required></label>
          <label>Type RESTORE to confirm<input type="text" name="confirm" autocomplete="off" required></label>
          <div><button class="btn danger" type="submit">Restore backup</button></div>
        </form>
      </section>`);

    const getBackup = async (btn) => {
      const res = await submitting(btn, () => api('admin.export'));
      download(res.filename, JSON.stringify(res.backup, null, 1), 'application/json');
      return res;
    };
    document.getElementById('backup').addEventListener('click', async (e) => {
      try { await getBackup(e.currentTarget); toast('Backup downloaded.'); } catch (err) { toast(err.message, true); }
    });
    main.querySelectorAll('[data-csv]').forEach((btn) => btn.addEventListener('click', async () => {
      try {
        const res = await submitting(btn, () => api('admin.exportcsv', { query: { what: btn.dataset.csv } }));
        download(res.filename, res.content, 'text/csv;charset=utf-8');
        toast(`Downloaded ${plural(res.rows, 'row')}.`);
      } catch (err) { toast(err.message, true); }
    }));

    const importForm = document.getElementById('import-form');
    const preview = document.getElementById('import-preview');
    const importData = (file, dry) => {
      const fd = new FormData();
      fd.append('csv', file);
      if (dry) fd.append('dry_run', '1');
      const comp = importForm.competition_id.value;
      if (comp === 'new') fd.append('competition_name', importForm.competition_name.value.trim());
      else if (comp) fd.append('competition_id', comp);
      fd.append('add_to_list', importForm.add_to_list.checked ? '1' : '0');
      return fd;
    };
    const runPreview = async () => {
      const file = importForm.csv.files[0];
      if (!file) return;
      if (importForm.competition_id.value === 'new' && !importForm.competition_name.value.trim()) {
        formError(importForm, 'Type the new competition\'s name first.');
        importForm.csv.value = '';
        return;
      }
      preview.innerHTML = '<p class="muted">Reading the file…</p>';
      try {
        const res = await api('admin.importfixtures', { form: importData(file, true) });
        const label = { new: 'New', update: 'Update', same: 'No change', error: 'Problem' };
        const parts = [];
        if (res.new) parts.push(`<strong>${esc(plural(res.new, 'new fixture'))}</strong>`);
        if (res.updates) parts.push(`<strong>${esc(plural(res.updates, 'fixture'))} to update</strong>`);
        if (res.results) parts.push(`${esc(plural(res.results, 'result'))} to save`);
        if (res.same) parts.push(`${esc(plural(res.same, 'fixture'))} already up to date`);
        if (res.errors) parts.push(`<span class="error">${esc(plural(res.errors, 'row'))} with problems</span>`);
        const changing = res.new + res.updates;
        // Problems and changes first; unchanged rows are folded away.
        const order = { error: 0, update: 1, new: 2, same: 3 };
        const rows = [...res.rows].sort((x, y) => order[x.status] - order[y.status] || x.line - y.line);
        const shown = rows.filter((r) => r.status !== 'same');
        const rowHtml = (r) => `<tr class="st-${r.status}"><td>${r.line}</td>
            <td class="name">${r.competition ? `<span class="comp-tag">${esc(r.competition)}</span>` : ''}${esc(r.home_team)} v ${esc(r.away_team)}</td>
            <td>${r.kickoff ? esc(fmtShort(r.kickoff)) : '–'}</td><td>${r.result ? `${r.result[0]}–${r.result[1]}` : ''}</td>
            <td class="name">${esc(label[r.status])}${r.changes && r.changes.length ? `: ${esc(r.changes.join(', '))}` : ''}${r.note ? ` <span class="muted">${esc(r.note)}</span>` : ''}</td></tr>`;
        const head = '<thead><tr><th scope="col">Line</th><th scope="col" class="name">Fixture</th><th scope="col">Kick-off</th><th scope="col">Result</th><th scope="col" class="name">What happens</th></tr></thead>';
        preview.innerHTML = `<p>${parts.join(', ')}.</p>
          ${res.new_competitions && res.new_competitions.length ? `<p>New ${res.new_competitions.length === 1 ? 'competition' : 'competitions'} to add: <strong>${res.new_competitions.map(esc).join(', ')}</strong>.</p>` : ''}
          ${res.errors ? '<p class="muted">Fix those rows in the spreadsheet and choose the file again.</p>'
            : changing ? `<p><button class="btn" type="button" id="do-import">Import: ${esc([res.new ? plural(res.new, 'new fixture') : '', res.updates ? plural(res.updates, 'update') : ''].filter(Boolean).join(' and '))}</button></p>`
            : '<p class="muted">Nothing to change: everything in this file is already on the site.</p>'}
          ${shown.length ? `<div class="table-wrap import-table"><table class="league preview">${head}<tbody>${shown.map(rowHtml).join('')}</tbody></table></div>` : ''}
          ${res.same ? `<details class="same-rows"><summary>${esc(plural(res.same, 'fixture'))} with no change</summary>
            <div class="table-wrap import-table"><table class="league preview">${head}<tbody>${rows.filter((r) => r.status === 'same').map(rowHtml).join('')}</tbody></table></div></details>` : ''}`;
        const go = document.getElementById('do-import');
        if (go) go.addEventListener('click', async () => {
          try {
            const done = await submitting(go, () => api('admin.importfixtures', { form: importData(file, false) }));
            await refreshSession();
            const msg = [done.added ? `added ${plural(done.added, 'fixture')}` : '', done.updated ? `updated ${plural(done.updated, 'fixture')}` : '',
              done.results ? `saved ${plural(done.results, 'result')}` : ''].filter(Boolean).join(', ');
            toast(`Imported: ${msg}.`);
            preview.innerHTML = `<p>Done: ${esc(msg)}${done.teams_listed ? `, and put ${esc(plural(done.teams_listed, 'team'))} into team lists` : ''}.
              <a href="#/admin/results">Results</a> · <a href="#/standings">League tables</a></p>`;
            importForm.csv.value = '';
          } catch (err) { toast(err.message, true); }
        });
      } catch (err) {
        preview.innerHTML = `<p class="error">${esc(err.message)}</p>`;
        importForm.csv.value = '';
      }
    };
    importForm.csv.addEventListener('change', runPreview);
    importForm.competition_id.addEventListener('change', () => {
      importForm.querySelector('[data-new-comp]').hidden = importForm.competition_id.value !== 'new';
      if (importForm.csv.files[0]) runPreview();
    });
    importForm.add_to_list.addEventListener('change', () => { if (importForm.csv.files[0]) runPreview(); });

    const restore = document.getElementById('restore-form');
    restore.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (restore.confirm.value !== 'RESTORE') { formError(restore, 'Type RESTORE in capitals to confirm.'); return; }
      const button = restore.querySelector('[type=submit]');
      try {
        if (!restore.dataset.backedUp) {
          await getBackup(button);
          restore.dataset.backedUp = '1';
          formError(restore, 'We downloaded a backup of the current league first. Check it saved, then press Restore backup again.');
          return;
        }
        const fd = new FormData();
        fd.append('backup', restore.backup.files[0]);
        fd.append('confirm', 'RESTORE');
        const res = await submitting(button, () => api('admin.restore', { form: fd }));
        toast(`Restored ${plural(res.restored.users, 'player')}, ${plural(res.restored.matches, 'fixture')} and ${plural(res.restored.predictions, 'prediction')}.`);
        await refreshSession();
        location.hash = res.logged_out ? '#/login' : '#/';
      } catch (err) { formError(restore, err.message); }
    });
  }

  const ACTION_LABELS = {
    ban: 'Banned', block_alert: 'Block alert sent',
    login: 'Logged in', login_failed: 'Failed log-in', logout: 'Logged out', register: 'Joined', account: 'Account change',
    reset_request: 'Reset requested', password_reset: 'Password reset', reset_link: 'Reset link', icon: 'Picture',
    message_all: 'Message to all', fixture_add: 'Fixture added', fixture_edit: 'Fixture edited', fixture_delete: 'Fixture deleted',
    fixtures_import: 'Fixtures imported', result: 'Result', settings: 'Settings', player_add: 'Player added',
    player_admin: 'Admin rights', player_delete: 'Player deleted', recalculate: 'Recalculated', backup: 'Backup',
    restore: 'Restore', mail_test: 'Test email', mail_failed: 'Email failed',
    bot_trap: 'Bot trap', bot_honeypot: 'Bot caught (hidden field)', bot_check_failed: 'Bot check failed', unblock: 'Block lifted',
    auto_predict: 'Automatic predictions', competition: 'Competition', prefs: 'Preferences',
  };

  async function adminActivity(page = 1, action = '', q = '') {
    const [data, blocks] = await Promise.all([api('admin.log', { query: { page, action, q } }), api('admin.blocks')]);
    const banned = blocks.blocks.filter((b) => b.permanent);
    const temp = blocks.blocks.filter((b) => !b.permanent);
    const strikeText = (b) => (b.strikes > 1 ? `, ${plural(b.strikes, 'strike')}` : '');
    const item = (b) => `<li class="admin-item${b.permanent ? ' is-banned' : ''}">
          <span class="grow"><strong>${esc(b.ip)}</strong>${b.ip === blocks.your_ip ? ' <span class="tag">You</span>' : ''}${b.permanent ? ' <span class="page-badge ban">Banned</span>' : ''}<br>
          <span class="muted">${esc(b.reason || '')}, ${b.permanent ? `banned ${esc(fmtShort(b.created))}` : `blocked ${esc(fmtShort(b.created))}, until ${esc(fmtShort(b.expires))}`}${esc(strikeText(b))}</span></span>
          <span class="actions">${b.permanent ? '' : `<button class="btn small quiet danger" type="button" data-ban="${esc(b.ip)}">Ban permanently</button>`}
            <button class="btn small quiet" type="button" data-unblock="${esc(b.ip)}">${b.permanent ? 'Lift ban' : 'Lift block'}</button></span></li>`;
    const blockList = `<section class="blocks">
        <div class="title-row"><h2>Blocked addresses</h2>
          ${temp.length ? '<button class="btn quiet small danger" type="button" data-unblock="all">Lift every temporary block</button>' : ''}</div>
        <p class="muted">Blocked addresses can't log in, sign up or reset a password until the time shown.
          ${blocks.ban_strikes ? `After ${esc(plural(blocks.ban_strikes, 'strike'))} an address is banned permanently` : 'Addresses are only banned by hand'}${blocks.whole_site ? ', which shuts it out of the whole site' : ', which keeps it out of log-in, sign-up and resets for good'}.
          Every block is a strike, and so is each try from a blocked address. Lifting a block or ban clears its strikes. Your address is ${esc(blocks.your_ip)}.</p>
        ${banned.length ? `<h3>Banned permanently</h3><ul class="admin-list">${banned.map(item).join('')}</ul>` : ''}
        ${temp.length ? `${banned.length ? '<h3>Blocked for now</h3>' : ''}<ul class="admin-list">${temp.map(item).join('')}</ul>` : '<p class="muted">No addresses are blocked right now.</p>'}
        <form class="inline-form" id="ban-form">
          <label>Ban an address<input type="text" name="ip" required placeholder="203.0.113.7" inputmode="decimal" autocomplete="off" spellcheck="false"></label>
          <label>Reason (optional)<input type="text" name="reason" maxlength="80" placeholder="e.g. spam sign-ups"></label>
          <div><button class="btn small danger" type="submit">Ban permanently</button></div>
        </form>
      </section>`;
    const opts = data.actions.map((a) => `<option value="${esc(a)}"${a === action ? ' selected' : ''}>${esc(ACTION_LABELS[a] || a)}</option>`).join('');
    const rows = data.rows.map((r) => `<tr class="${r.action === 'login_failed' || r.action === 'mail_failed' ? 'warn' : ''}">
        <td class="nowrap">${esc(fmtShort(r.created))}</td>
        <td class="name">${esc(r.username || '–')}</td>
        <td class="name">${esc(ACTION_LABELS[r.action] || r.action)}</td>
        <td class="name detail">${esc(r.detail || '')}</td>
        <td class="nowrap muted">${esc(r.ip || '')}</td></tr>`).join('');
    adminShell('activity', `${blockList}
      <h2 class="${blockList ? '' : 'visually-hidden'}">Activity</h2>
      <form class="log-filter" id="log-filter">
        <label>Show<select name="type"><option value="">Everything</option>${opts}</select></label>
        <label>Search<input type="text" name="q" value="${esc(q)}" placeholder="Name, IP or detail"></label>
        <div><button class="btn small" type="submit">Filter</button></div>
      </form>
      ${data.total ? `<p class="muted">${esc(plural(data.total, 'event'))}, newest first.</p>
      <div class="table-wrap"><table class="league log"><thead><tr><th scope="col">When</th><th scope="col" class="name">Who</th><th scope="col" class="name">What</th><th scope="col" class="name">Details</th><th scope="col">IP address</th></tr></thead>
        <tbody>${rows}</tbody></table></div>
      ${data.pages > 1 ? `<p class="pager">${page > 1 ? '<button class="btn quiet small" type="button" data-page="prev">Newer</button>' : ''}
        <span class="muted">Page ${page} of ${data.pages}</span>
        ${page < data.pages ? '<button class="btn quiet small" type="button" data-page="next">Older</button>' : ''}</p>` : ''}`
      : '<p class="lede">Nothing logged yet. Log-ins, sign-ups and admin changes appear here.</p>'}`);
    const form = document.getElementById('log-filter');
    form.addEventListener('submit', (e) => { e.preventDefault(); adminActivity(1, form.type.value, form.q.value.trim()).catch(errorPage); });
    form.type.addEventListener('change', () => adminActivity(1, form.type.value, form.q.value.trim()).catch(errorPage));
    main.querySelectorAll('[data-page]').forEach((b) => b.addEventListener('click', () =>
      adminActivity(page + (b.dataset.page === 'next' ? 1 : -1), action, q).catch(errorPage)));
    main.querySelectorAll('[data-ban]').forEach((b) => b.addEventListener('click', () => armed(b, 'Click again to ban for good', async () => {
      try {
        await api('admin.ban', { method: 'POST', body: { ip: b.dataset.ban } });
        toast(`${b.dataset.ban} is banned permanently.`);
        adminActivity(page, action, q).catch(errorPage);
      } catch (err) { toast(err.message, true); }
    })));
    const banForm = document.getElementById('ban-form');
    banForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await submitting(banForm.querySelector('button'), () => api('admin.ban', { method: 'POST', body: { ip: banForm.ip.value.trim(), reason: banForm.reason.value } }));
        toast(`${banForm.ip.value.trim()} is banned permanently.`);
        adminActivity(page, action, q).catch(errorPage);
      } catch (err) { formError(banForm, err.message); }
    });
    main.querySelectorAll('[data-unblock]').forEach((b) => b.addEventListener('click', async () => {
      try {
        await submitting(b, () => api('admin.unblock', { method: 'POST', body: { ip: b.dataset.unblock } }));
        toast(b.dataset.unblock === 'all' ? 'Every temporary block lifted.' : `Lifted ${b.textContent.trim() === 'Lift ban' ? 'the ban' : 'the block'} on ${b.dataset.unblock}.`);
        adminActivity(page, action, q).catch(errorPage);
      } catch (err) { toast(err.message, true); }
    }));
  }


  // ------------------------------------------------------------------
  // Bot check: honeypot field + a proof-of-work puzzle solved in the background
  // ------------------------------------------------------------------

  // A field people never see. Bots that fill it in are caught.
  const honeypotField = () => `<div class="hp-field" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>`;

  // Small SHA-256 for browsers without crypto.subtle (pages not served over HTTPS).
  function sha256Fallback(bytes) {
    const K = [0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3,
      0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da,
      0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13,
      0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070,
      0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208,
      0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2];
    const H = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19];
    const len = bytes.length;
    const padded = new Uint8Array(((len + 9 + 63) >> 6) << 6);
    padded.set(bytes);
    padded[len] = 0x80;
    new DataView(padded.buffer).setUint32(padded.length - 4, len * 8);
    const w = new Uint32Array(64);
    const rotr = (x, n) => (x >>> n) | (x << (32 - n));
    for (let off = 0; off < padded.length; off += 64) {
      const view = new DataView(padded.buffer, off, 64);
      for (let i = 0; i < 16; i++) w[i] = view.getUint32(i * 4);
      for (let i = 16; i < 64; i++) {
        const s0 = rotr(w[i - 15], 7) ^ rotr(w[i - 15], 18) ^ (w[i - 15] >>> 3);
        const s1 = rotr(w[i - 2], 17) ^ rotr(w[i - 2], 19) ^ (w[i - 2] >>> 10);
        w[i] = (w[i - 16] + s0 + w[i - 7] + s1) | 0;
      }
      let [a, b, c, d, e, f, g, h] = H;
      for (let i = 0; i < 64; i++) {
        const t1 = (h + (rotr(e, 6) ^ rotr(e, 11) ^ rotr(e, 25)) + ((e & f) ^ (~e & g)) + K[i] + w[i]) | 0;
        const t2 = ((rotr(a, 2) ^ rotr(a, 13) ^ rotr(a, 22)) + ((a & b) ^ (a & c) ^ (b & c))) | 0;
        h = g; g = f; f = e; e = (d + t1) | 0; d = c; c = b; b = a; a = (t1 + t2) | 0;
      }
      H[0] = (H[0] + a) | 0; H[1] = (H[1] + b) | 0; H[2] = (H[2] + c) | 0; H[3] = (H[3] + d) | 0;
      H[4] = (H[4] + e) | 0; H[5] = (H[5] + f) | 0; H[6] = (H[6] + g) | 0; H[7] = (H[7] + h) | 0;
    }
    const out = new Uint8Array(32);
    H.forEach((v, i) => new DataView(out.buffer).setUint32(i * 4, v));
    return out;
  }

  function leadingZeroBits(bytes) {
    let n = 0;
    for (const b of bytes) {
      if (b === 0) { n += 8; continue; }
      return n + Math.clz32(b) - 24;
    }
    return n;
  }

  // Find a number so that SHA-256("challenge:number") starts with `bits` zero bits.
  async function solvePuzzle(challenge, bits) {
    const enc = new TextEncoder();
    const subtle = window.crypto && window.crypto.subtle;
    for (let nonce = 0; ; nonce += 256) {
      if (subtle) {
        const batch = [];
        for (let i = 0; i < 256; i++) batch.push(subtle.digest('SHA-256', enc.encode(`${challenge}:${nonce + i}`)));
        const hashes = await Promise.all(batch);
        const hit = hashes.findIndex((h) => leadingZeroBits(new Uint8Array(h)) >= bits);
        if (hit >= 0) return nonce + hit;
      } else {
        for (let i = 0; i < 256; i++) {
          if (leadingZeroBits(sha256Fallback(enc.encode(`${challenge}:${nonce + i}`))) >= bits) return nonce + i;
        }
        await new Promise((r) => setTimeout(r, 0));  // keep the page responsive
      }
    }
  }

  // Starts solving as soon as a form opens; each answer works once.
  function botCheck(purpose) {
    const fresh = async () => {
      const ch = await api('challenge', { query: { for: purpose } });
      if (!ch.enabled) return {};
      const issued = Date.now();
      const nonce = await solvePuzzle(ch.challenge, ch.bits);
      const wait = ch.min_ms + 250 - (Date.now() - issued);  // nobody fills a form in under a second
      if (wait > 0) await new Promise((r) => setTimeout(r, wait));
      return { pow: { challenge: ch.challenge, nonce: String(nonce) } };
    };
    let job = state.settings.bot_check ? fresh() : Promise.resolve({});
    job.catch(() => {});
    return {
      // Send `body` plus the answer and honeypot; retry once with a new puzzle if the old one was refused.
      async send(route, form, body) {
        const attempt = async () => {
          const extra = await job.catch(() => ({}));
          try {
            return await api(route, { method: 'POST', body: { ...body, ...extra, website: form.website ? form.website.value : '' } });
          } finally {
            // Each answer works once: start the next puzzle after this one is used.
            job = state.settings.bot_check ? fresh() : Promise.resolve({});
            job.catch(() => {});
          }
        };
        try {
          return await attempt();
        } catch (err) {
          if (err.code === 'bot_check') return attempt();
          throw err;
        }
      },
    };
  }

  // ------------------------------------------------------------------
  // Help, matchday grid and cookie check
  // ------------------------------------------------------------------

  // Logged in but the next request isn't? The browser refused the session cookie.
  async function cookiesWork() {
    const check = await api('session');
    applySession(check);
    if (check.user) return true;
    setPage(`<h1>Your browser blocked the log-in</h1>
      <div class="notice"><p>Your password was right, but your browser didn't keep the log-in cookie, so you'd be logged out straight away.</p>
      <p>Allow cookies for this site (in your browser's privacy settings), or switch off any extension blocking them, then log in again.</p></div>
      <p><a class="btn" href="#/login">Try again</a></p>`);
    return false;
  }

  function viewHelp() {
    renderNav('');
    const s = state.settings;
    setPage(`<h1>How to play</h1>
      <div class="help">
        <p class="lede">Predict the score of every match. The closer you get, the more points you score. The player with the most points tops the table.</p>
        <h2>Joining</h2>
        <p>${s.registration_open ? '<a href="#/register">Join the league</a> with a username, your email address and a password.' : 'The admin adds players to this league. Ask them to add you, then log in.'}
          Your username is what other players see in the table.</p>
        <h2>Making predictions</h2>
        <ul>
          <li>Open <a href="#/predictions">Predict</a> and type a score in the boxes for each match, then press <strong>Save predictions</strong>.</li>
          <li>You can change a prediction as often as you like until ${s.cutoff_minutes ? `<strong>${esc(plural(s.cutoff_minutes, 'minute'))} before kick-off</strong>` : 'kick-off'}. Then it locks.</li>
          <li>${s.view_user_predictions ? 'Other players can see your predictions before they close.' : 'Other players can\'t see your predictions until predictions close.'}</li>
        </ul>
        ${s.auto_predict ? `<h2>Automatic predictions</h2>
        <p>Switch on <a href="#/account">Predict for me if I forget</a> on your account page. When predictions close on a match you haven't predicted, the site predicts it for you from both teams' recent form:</p>
        <ul>
          <li>each team's last 6 results, with the most recent counting most,</li>
          <li>goals they scored and conceded, and points won per game,</li>
          <li>a boost for the home side, based on how home teams have done in this league.</li>
        </ul>
        <p>Teams with few results lean towards the league average. Automatic predictions score points like any other and are marked <span class="auto-tag">Auto</span>. They only use results from before the match.</p>` : ''}
        <h2>Scoring</h2>
        <ul>
          <li><strong>Exact score: ${esc(plural(s.points_exact, 'point'))}.</strong> You predict 3–1 and it finishes 3–1.</li>
          <li><strong>Right result: ${esc(plural(s.points_result, 'point'))}.</strong> You pick the winner, or a draw, but not the exact score. You predict 1–1 and it finishes 2–2.</li>
          <li>Anything else scores nothing.</li>
        </ul>
        <h2>Goal difference</h2>
        <p>For each team, you get <strong>+goals</strong> when you predicted its score exactly, and <strong>−goals</strong> for how far out you were when you didn't.
          For example, you predict 2–1 and it finishes 2–3: +2 for the home team, −2 for the away team.</p>
        <h2>The table</h2>
        <p>Players are ranked by points, then goal difference, then the number of predictions scored.
          Use <a href="#/matchday">everyone's predictions by day</a> to compare picks for a round of matches.</p>
        ${s.messaging ? '<h2>Messages</h2><p>Send another player a private message from their page in the table, or from <a href="#/messages">Messages</a>.</p>' : ''}
        <h2>Forgotten your password?</h2>
        <p>${s.email ? 'Use <a href="#/forgot">Forgotten your password?</a> on the log-in page and we\'ll email you a link.' : 'Ask the league admin for a reset link.'}</p>
      </div>`);
  }

  async function viewMatchday(date) {
    renderNav('results');
    const data = await api('matchday', { query: date ? { date } : {} });
    if (!data.date) {
      setPage(`<h1>Predictions by day</h1><p class="lede">There are no fixtures yet.</p>`);
      return;
    }
    const nav = `<p class="pager">${data.prev ? `<a class="btn quiet small" href="#/matchday/${data.prev}">Previous day</a>` : ''}
      ${data.next ? `<a class="btn quiet small" href="#/matchday/${data.next}">Next day</a>` : ''}</p>`;
    let html = `<p><a href="#/results">Results</a></p><h1>${esc(fmtDay(data.date + ' 00:00:00'))}</h1>
      <p class="lede">Everyone's predictions for ${esc(plural(data.matches.length, 'match', 'matches'))}.</p>${nav}`;
    if (!data.players.length) {
      setPage(html + `<p>Nobody has predicted these matches yet.</p>`);
      return;
    }
    const me = state.user ? state.user.id : null;
    html += `<div class="table-wrap grid-wrap"><table class="league matchday"><thead><tr><th scope="col" class="name">Player</th>
      ${data.matches.map((m) => `<th scope="col" class="match-col"><a href="#/match/${m.id}">
        <span class="mteam">${esc(m.home_team)}</span><span class="mteam">${esc(m.away_team)}</span>
        <span class="mres">${m.result ? `${m.result.home}–${m.result.away}` : esc(fmtTime(m.kickoff))}</span>${compTag(m.competition)}</a></th>`).join('')}
      <th scope="col">Pts</th></tr></thead><tbody>`;
    for (const p of data.players) {
      html += `<tr${p.id === me ? ' class="me"' : ''}><th scope="row" class="name"><a class="who" href="#/player/${p.id}">${avatar(p)}${esc(p.username)}</a></th>`;
      for (const m of data.matches) {
        const c = p.cells[m.id];
        if (!c) html += `<td class="cell empty"><span class="visually-hidden">No prediction</span>·</td>`;
        else if (c.hidden) html += `<td class="cell hidden-pred" title="Hidden until kick-off">?</td>`;
        else html += `<td class="cell ${esc(c.outcome || '')}${c.auto ? ' auto' : ''}" title="${c.outcome === 'exact' ? 'Exact score' : c.outcome === 'result' ? 'Right result' : c.outcome === 'miss' ? 'Missed' : 'Not played yet'}${c.auto ? ', automatic prediction' : ''}">${c.home}–${c.away}${c.auto ? '<sup aria-hidden="true">A</sup>' : ''}</td>`;
      }
      html += `<td class="pts">${p.points}</td></tr>`;
    }
    html += `</tbody></table></div>
      <p class="muted key"><span class="cell exact">2–1</span> exact score <span class="cell result">1–0</span> right result <span class="cell miss">0–2</span> missed <span class="cell">1–1<sup>A</sup></span> automatic prediction</p>`;
    setPage(html);
  }

  // ------------------------------------------------------------------
  // Leagues: official competition tables and private leagues
  // ------------------------------------------------------------------

  // Does the player play this fixture's competition? Fixtures outside a competition count for everyone.
  const plays = (f) => !f.competition || !state.user || !Array.isArray(state.user.follows) || state.user.follows.includes(f.competition.id);
  const fmtCode = (c) => (c ? `${c.slice(0, 4)}-${c.slice(4)}` : '');
  const joinLink = (c) => `${location.origin}${location.pathname}#/join/${fmtCode(c)}`;

  function takeJoinRedirect() {
    let code = '';
    try { code = sessionStorage.getItem('pl.join') || ''; sessionStorage.removeItem('pl.join'); } catch (e) { /* storage unavailable */ }
    return code ? `#/join/${code}` : '';
  }

  async function copyText(text, button) {
    try {
      await navigator.clipboard.writeText(text);
    } catch (e) {
      const input = button.parentElement.querySelector('input');
      if (input) { input.select(); document.execCommand('copy'); }
    }
    toast('Copied.');
  }

  function tableHtml(rows, { note = '' } = {}) {
    const me = state.user ? state.user.id : null;
    let html = `<div class="table-wrap"><table class="league">
      <caption class="muted" style="caption-side:bottom;text-align:left;padding-top:10px">
        ${note ? esc(note) + ' ' : ''}P played, E exact scores, R right results, F and A goals for and against, GD goal difference.
      </caption>
      <thead><tr>
        <th scope="col"><span class="visually-hidden">Position</span></th>
        <th scope="col" class="name">Player</th>
        <th scope="col" title="Predictions scored">P</th>
        <th scope="col" title="Exact scores">E</th>
        <th scope="col" title="Right results">R</th>
        <th scope="col" class="wide" title="Goals for">F</th>
        <th scope="col" class="wide" title="Goals against">A</th>
        <th scope="col" title="Goal difference">GD</th>
        <th scope="col" title="Points">Pts</th>
      </tr></thead><tbody>`;
    for (const r of rows) {
      const cls = [r.user_id === me ? 'me' : '', r.position === 1 && r.points > 0 ? 'leader' : ''].filter(Boolean).join(' ');
      html += `<tr${cls ? ` class="${cls}"` : ''}>
        <td class="pos">${r.position}</td>
        <td class="name"><a class="who" href="#/player/${r.user_id}">${avatar(r)}${esc(r.username)}</a></td>
        <td>${r.played}</td><td>${r.exact}</td><td>${r.result}</td>
        <td class="wide">${r.for}</td><td class="wide">${r.against}</td>
        <td>${r.diff > 0 ? '+' : ''}${r.diff}</td>
        <td class="pts">${r.points}</td>
      </tr>`;
    }
    return html + `</tbody></table></div>`;
  }

  // The leagues hub, loaded once per page. Failing to load it never breaks the page.
  async function hubData() {
    try { return await api('leagues'); } catch (e) { return null; }
  }

  // Overall / competitions you play / your private leagues.
  function tableSwitcher(hub, active) {
    if (!hub) return '';
    const items = [['overall', '#/table', 'Overall']];
    for (const c of hub.competitions) {
      if (!state.user || c.plays) items.push([`comp-${c.id}`, `#/table/${c.id}`, c.name]);
    }
    for (const l of hub.mine) items.push([`league-${l.id}`, `#/league/${l.id}`, l.name, true]);
    if (items.length < 2 && !state.user) return '';
    return `<nav class="tabs table-switch" aria-label="Tables">${items.map(([k, href, label, priv]) =>
      `<a href="${href}"${active === k ? ' aria-current="page"' : ''}${priv ? ' class="private"' : ''}>${esc(label)}</a>`).join('')}
      ${state.user ? `<a href="#/leagues" class="more"${active === 'hub' ? ' aria-current="page"' : ''}>All leagues</a>` : ''}</nav>`;
  }

  async function viewCompTable(id) {
    renderNav('table');
    const [data, hub] = await Promise.all([api('table', { query: { type: 'comp', id, ...seasonQuery() } }), hubData()]);
    let html = tableSwitcher(hub, `comp-${id}`) + `<div class="title-row"><h1>${esc(data.title)}</h1>${seasonPicker()}</div>
      <p class="lede">The prediction table for ${esc(data.title)} fixtures only, for everyone who plays it.${tableComps().some((x) => x.id === data.competition.id) ? ` <a href="#/standings/${data.competition.id}">${esc(data.title)} league table</a>.` : ''}
      ${state.user && data.plays === false ? ` You don't play ${esc(data.title)}. <a href="#/leagues">Choose your competitions</a>.` : ''}</p>`;
    html += data.table.length ? tableHtml(data.table)
      : `<div class="notice"><p>This table fills in once the first ${esc(data.title)} match kicks off.</p></div>`;
    setPage(html);
  }

  function positionText(me) {
    if (!me) return '<span class="muted">Not in the table yet</span>';
    return `<strong>${ordinal(me.position)}</strong> of ${me.of} <span class="muted">· ${esc(plural(me.points, 'pt'))}</span>`;
  }

  async function viewLeagues(arg) {
    renderNav('leagues');
    if (arg === 'new') return viewLeagueCreate();
    const hub = await api('leagues', { query: seasonQuery() });
    const u = state.user;
    let html = `${tableSwitcher(hub, 'hub')}<div class="title-row"><h1>Leagues</h1>${seasonPicker()}</div>
      <p class="lede">One account plays everywhere. You predict each match once, and it counts in every table that includes it.</p>`;

    if (u) {
      html += `<h2>Your private leagues</h2>`;
      if (hub.mine.length) {
        html += `<ul class="league-cards">${hub.mine.map((l) => `<li><a class="league-card" href="#/league/${l.id}">
          <span class="lc-name">${esc(l.name)}${l.is_owner ? ' <span class="muted">(yours)</span>' : ''}</span>
          <span class="lc-meta">${esc(plural(l.members, 'player'))} · ${l.competitions ? esc(l.competitions.map((c) => c.short || c.name).join(', ')) : 'All competitions'}</span>
          <span class="lc-pos">${positionText(l.me)}</span></a></li>`).join('')}</ul>`;
      } else {
        html += `<p class="muted">You're not in any private leagues yet. Start one for friends, family or work, or join one with an invite code.</p>`;
      }
      if (hub.leagues_on) {
        html += `<div class="league-actions">
          <form class="join-form" id="join-form">
            <label>Invite code<input type="text" name="code" maxlength="12" autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="ABCD-2345" required></label>
            <button class="btn" type="submit">Join</button>
          </form>
          <a class="btn quiet" href="#/leagues/new">Create a private league</a>
        </div>`;
      }
    }

    html += `<h2>Competition tables</h2>`;
    const comps = hub.competitions;
    html += `<ul class="league-cards">
      <li><a class="league-card" href="#/table"><span class="lc-name">Overall</span>
        <span class="lc-meta">Every fixture · ${esc(plural(hub.overall.players, 'player'))}</span>
        ${u ? `<span class="lc-pos">${positionText(hub.overall.me)}</span>` : ''}</a></li>
      ${comps.map((c) => `<li><a class="league-card${u && !c.plays ? ' not-playing' : ''}" href="#/table/${c.id}"><span class="lc-name">${esc(c.name)}</span>
        <span class="lc-meta">${esc(plural(c.players, 'player'))}${u && !c.plays ? ' · you don\'t play this' : ''}</span>
        ${u && c.plays ? `<span class="lc-pos">${positionText(c.me)}</span>` : ''}</a></li>`).join('')}
    </ul>`;

    if (u && comps.length) {
      const all = hub.follows === null;
      html += `<h2 id="play">Competitions you play</h2>
        <form class="stack" id="follows-form">
          <p class="muted tight">You're only asked to predict, and only appear in the tables of, the competitions you play. Fixtures outside a competition are for everyone.</p>
          <label class="check"><input type="checkbox" name="all"${all ? ' checked' : ''}><span>Play every competition <span class="hint">Including ones added later.</span></span></label>
          <fieldset class="comp-choices"${all ? ' disabled' : ''}><legend class="visually-hidden">Competitions</legend>
            ${comps.map((c) => `<label class="check"><input type="checkbox" name="comp" value="${c.id}"${all || c.plays ? ' checked' : ''}><span>${esc(c.name)}</span></label>`).join('')}
          </fieldset>
        </form>`;
    }
    if (!u) html += `<div class="notice"><p><a href="#/login">Log in</a> to see your private leagues, join one with a code, or start your own.</p></div>`;
    setPage(html);

    const joinForm = document.getElementById('join-form');
    if (joinForm) {
      joinForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const code = joinForm.code.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        if (code.length !== 8) { formError(joinForm, 'Invite codes have 8 letters and numbers, like ABCD-2345.'); return; }
        location.hash = `#/join/${fmtCode(code)}`;
      });
    }
    const follows = document.getElementById('follows-form');
    if (follows) {
      follows.addEventListener('change', async (e) => {
        const set = follows.querySelector('.comp-choices');
        if (e.target.name === 'all') {
          set.disabled = follows.all.checked;
          if (follows.all.checked) set.querySelectorAll('input').forEach((i) => { i.checked = true; });
        }
        const ids = [...set.querySelectorAll('input:checked')].map((i) => Number(i.value));
        if (!follows.all.checked && !ids.length) {
          toast('Choose at least one competition.', true);
          e.target.checked = true;
          return;
        }
        try {
          const res = await api('follows.save', { method: 'POST', body: { all: follows.all.checked, competition_ids: ids } });
          state.user.follows = res.follows;
          toast('Saved. Predict and Results now show the competitions you play.');
          viewLeagues('');
        } catch (err) { toast(err.message, true); }
      });
    }
  }

  // Create and edit share one form.
  function leagueFormHtml(l) {
    const comps = state.settings.competitions || [];
    const chosen = new Set(l && l.competitions ? l.competitions.map((c) => c.id) : []);
    const all = !l || l.competitions === null;
    const today = new Date();
    const iso = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    const startNote = l ? (l.start_at ? `Now counting matches from ${fmtDay(l.start_at)}.` : 'Now counting every match.') : '';
    return `<form class="stack" id="league-form">
      <label>League name<input type="text" name="name" maxlength="60" required value="${esc(l ? l.name : '')}" placeholder="The Red Lion Sunday League"></label>
      ${comps.length ? `<fieldset class="stack-tight"><legend>Competitions</legend>
        <label class="check"><input type="radio" name="comp_mode" value="all"${all ? ' checked' : ''}><span>All competitions <span class="hint">Every fixture on the site, including competitions added later.</span></span></label>
        <label class="check"><input type="radio" name="comp_mode" value="some"${all ? '' : ' checked'}><span>Only these:</span></label>
        <div class="comp-choices indent">${comps.map((c) => `<label class="check"><input type="checkbox" name="comp" value="${c.id}"${chosen.has(c.id) ? ' checked' : ''}><span>${esc(c.name)}</span></label>`).join('')}</div>
        <p class="hint">Players who join start playing these competitions, so they're asked to predict them.</p>
      </fieldset>` : '<input type="hidden" name="comp_mode" value="all">'}
      <fieldset class="points-row"><legend>Scoring</legend>
        <label>Exact score<input type="number" name="pts_exact" min="0" max="20" required value="${l ? l.scoring.exact : state.settings.points_exact}"></label>
        <label>Right result<input type="number" name="pts_result" min="0" max="20" required value="${l ? l.scoring.result : state.settings.points_result}"></label>
        <p class="hint">Points for each prediction in this league. The site's own tables give ${esc(plural(state.settings.points_exact, 'point'))} and ${esc(plural(state.settings.points_result, 'point'))}.</p>
      </fieldset>
      <fieldset class="stack-tight"><legend>Count matches from</legend>
        ${l ? `<label class="check"><input type="radio" name="start" value="keep" checked><span>Leave as it is <span class="hint">${esc(startNote)}</span></span></label>` : ''}
        <label class="check"><input type="radio" name="start" value="now"${l ? '' : ' checked'}><span>Now <span class="hint">Only matches that kick off from now on.</span></span></label>
        <label class="check"><input type="radio" name="start" value="date"><span>A date: <input type="date" name="start_date" value="${esc(l && l.start_at ? l.start_at.slice(0, 10) : iso)}" aria-label="Start date"></span></label>
        <label class="check"><input type="radio" name="start" value="all"><span>Every match <span class="hint">Including ones already played.</span></span></label>
      </fieldset>
      <div><button class="btn" type="submit">${l ? 'Save changes' : 'Create league'}</button></div>
    </form>`;
  }

  function readLeagueForm(form) {
    const mode = form.querySelector('[name=comp_mode]:checked') || form.comp_mode;
    return {
      name: form.name.value,
      all_competitions: mode.value === 'all',
      competition_ids: [...form.querySelectorAll('[name=comp]:checked')].map((i) => Number(i.value)),
      pts_exact: form.pts_exact.value,
      pts_result: form.pts_result.value,
      start: form.querySelector('[name=start]:checked').value,
      start_date: form.start_date.value,
    };
  }

  function bindLeagueForm(form) {
    // Picking a competition switches to "Only these"; picking a date ticks "A date".
    form.addEventListener('change', (e) => {
      if (e.target.name === 'comp' && e.target.checked) { const r = form.querySelector('[name=comp_mode][value=some]'); if (r) r.checked = true; }
      if (e.target.name === 'start_date') form.querySelector('[name=start][value=date]').checked = true;
    });
    form.start_date.addEventListener('focus', () => { form.querySelector('[name=start][value=date]').checked = true; });
  }

  function viewLeagueCreate() {
    if (!state.user) return needLogin('create a private league');
    if (!state.settings.leagues) {
      setPage(`<h1>Private leagues are off</h1><p class="lede">The league admin has turned off private leagues.</p>`);
      return;
    }
    setPage(`<p><a href="#/leagues">Leagues</a></p><h1>Create a private league</h1>
      <p class="lede">Choose what it covers and how it scores. You'll get an invite code and link to send to friends. Only people with the code can join.</p>
      ${leagueFormHtml(null)}`);
    const form = document.getElementById('league-form');
    bindLeagueForm(form);
    form.name.focus();
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const res = await submitting(form.querySelector('button[type=submit]'), () => api('league.create', { method: 'POST', body: readLeagueForm(form) }));
        await refreshSession();
        toast(`${res.league.name} is ready. Send your friends the invite link.`);
        location.hash = `#/league/${res.league.id}`;
      } catch (err) { formError(form, err.message); }
    });
  }

  async function viewLeague(id) {
    renderNav('leagues');
    if (!state.user) return needLogin('see private leagues');
    const [data, hub] = await Promise.all([api('table', { query: { type: 'league', id, ...seasonQuery() } }), hubData()]);
    const l = data.league;
    const owner = l.is_owner || state.user.is_admin;
    const covers = l.competitions ? l.competitions.map((c) => c.name).join(', ') : 'every competition';
    let html = `${tableSwitcher(hub, `league-${l.id}`)}
      <div class="title-row"><h1>${esc(l.name)}</h1><span class="private-tag">Private league</span>${seasonPicker()}</div>
      <p class="lede">Run by ${esc(l.owner.username)}. ${esc(plural(l.members, 'player'))}. Covers ${esc(covers)},
        counting ${l.start_at ? `matches from ${esc(fmtDay(l.start_at))}` : 'every match'}.
        Exact score ${esc(plural(l.scoring.exact, 'point'))}, right result ${esc(plural(l.scoring.result, 'point'))}.</p>`;
    html += tableHtml(data.table);

    if (l.code) {
      html += `<h2>Invite friends</h2>
        <p class="muted tight">Anyone with the code or link can join. They'll need an account on this site; one account plays in every league.</p>
        <div class="invite">
          <div class="invite-code" aria-label="Invite code">${esc(fmtCode(l.code))}</div>
          <div class="copy-row"><input type="text" readonly value="${esc(joinLink(l.code))}" aria-label="Invite link"><button class="btn small" type="button" data-copy="link">Copy link</button></div>
        </div>`;
    }

    if (owner) {
      const others = data.table.filter((r) => r.user_id !== l.owner.id);
      html += `<h2>Players</h2>${others.length ? `<ul class="admin-list">${others.map((r) => `<li class="admin-item" data-user="${r.user_id}">
          <span class="grow"><a class="who" href="#/player/${r.user_id}">${avatar(r)}${esc(r.username)}</a></span>
          <span class="actions"><button class="btn small quiet" type="button" data-transfer>Hand over league</button>
          <button class="btn small danger" type="button" data-remove>Remove</button></span></li>`).join('')}</ul>`
        : '<p class="muted">No one else has joined yet. Send them the invite link.</p>'}
        <h2>League settings</h2>${leagueFormHtml(l)}
        <h2>Invite code</h2>
        <p class="muted tight">A new code stops the old code and link working. Players already in the league stay in it.</p>
        <p><button class="btn quiet" type="button" id="new-code">Make a new invite code</button></p>
        <h2>Delete league</h2>
        <p class="muted tight">Deleting the league removes its table for everyone. Nobody's predictions or other tables change.</p>
        <p><button class="btn danger" type="button" id="delete-league">Delete ${esc(l.name)}</button></p>`;
    } else {
      html += `<h2>Leave league</h2><p class="muted tight">Your predictions stay; you just won't appear in this table. You can rejoin with the code.</p>
        <p><button class="btn quiet danger" type="button" id="leave-league">Leave ${esc(l.name)}</button></p>`;
    }
    setPage(html);

    const copy = main.querySelector('[data-copy]');
    if (copy) copy.addEventListener('click', () => copyText(joinLink(l.code), copy));
    const leave = document.getElementById('leave-league');
    if (leave) {
      leave.addEventListener('click', () => armed(leave, 'Click again to leave', async () => {
        try {
          await api('league.leave', { method: 'POST', body: { id: l.id } });
          toast(`You've left ${l.name}.`);
          location.hash = '#/leagues';
        } catch (err) { toast(err.message, true); }
      }));
    }
    if (!owner) return;

    main.querySelectorAll('[data-remove], [data-transfer]').forEach((btn) => btn.addEventListener('click', () => {
      const uid = Number(btn.closest('[data-user]').dataset.user);
      const name = data.table.find((r) => r.user_id === uid).username;
      const transfer = btn.hasAttribute('data-transfer');
      armed(btn, transfer ? `Make ${name} the creator?` : `Remove ${name}?`, async () => {
        try {
          await api(transfer ? 'league.transfer' : 'league.remove', { method: 'POST', body: { id: l.id, user_id: uid } });
          toast(transfer ? `${name} now runs ${l.name}.` : `${name} was removed from ${l.name}.`);
          viewLeague(id);
        } catch (err) { toast(err.message, true); }
      });
    }));
    const form = document.getElementById('league-form');
    bindLeagueForm(form);
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        await submitting(form.querySelector('button[type=submit]'), () => api('league.update', { method: 'POST', body: { id: l.id, ...readLeagueForm(form) } }));
        toast('League settings saved.');
        viewLeague(id);
      } catch (err) { formError(form, err.message); }
    });
    const newCode = document.getElementById('new-code');
    newCode.addEventListener('click', () => armed(newCode, 'Click again: the old code stops working', async () => {
      try {
        await api('league.newcode', { method: 'POST', body: { id: l.id } });
        toast('New invite code made.');
        viewLeague(id);
      } catch (err) { toast(err.message, true); }
    }));
    const del = document.getElementById('delete-league');
    del.addEventListener('click', () => armed(del, 'Click again to delete for everyone', async () => {
      try {
        await api('league.delete', { method: 'POST', body: { id: l.id } });
        toast(`${l.name} was deleted.`);
        location.hash = state.user.is_admin && !l.is_member ? '#/admin/leagues' : '#/leagues';
      } catch (err) { toast(err.message, true); }
    }));
  }

  async function viewJoin(code) {
    renderNav('leagues');
    code = decodeURIComponent(code || '').replace(/[^A-Za-z0-9]/g, '').toUpperCase();
    if (!state.user) {
      try { sessionStorage.setItem('pl.join', fmtCode(code)); } catch (e) { /* storage unavailable */ }
      setPage(`<h1>You've been invited to a private league</h1>
        <p class="lede">Log in or create an account first, and you'll come straight back here to join. One account plays in every league on this site.</p>
        <p><a class="btn" href="#/login">Log in</a> ${state.settings.registration_open ? '<a class="btn quiet" href="#/register">Create an account</a>' : ''}</p>`);
      return;
    }
    let data;
    try {
      data = await api('league.preview', { query: { code } });
    } catch (err) {
      setPage(`<p><a href="#/leagues">Leagues</a></p><h1>Invite code not found</h1><div class="notice"><p>${esc(err.message)}</p></div>
        <p><a class="btn quiet" href="#/leagues">Enter a code</a></p>`);
      return;
    }
    const l = data.league;
    if (!data.league.can_join) { location.hash = `#/league/${l.id}`; return; }
    const covers = l.competitions ? l.competitions.map((c) => c.name).join(', ') : 'every competition';
    setPage(`<p><a href="#/leagues">Leagues</a></p>
      <h1>Join ${esc(l.name)}?</h1>
      <div class="join-card">
        <p><strong>${esc(l.name)}</strong> is a private league run by ${esc(l.owner.username)}, with ${esc(plural(l.members, 'player'))}.</p>
        <p>It covers ${esc(covers)} and counts ${l.start_at ? `matches from ${esc(fmtDay(l.start_at))}` : 'every match'}.
          Exact score ${esc(plural(l.scoring.exact, 'point'))}, right result ${esc(plural(l.scoring.result, 'point'))}.</p>
        <p class="muted">Your predictions count here automatically; you don't predict anything twice.${l.competitions ? ' Joining also adds these competitions to the ones you play.' : ''}</p>
        <p><button class="btn" type="button" id="join-btn">Join the league</button> <a class="btn quiet" href="#/leagues">Not now</a></p>
      </div>`);
    const btn = document.getElementById('join-btn');
    btn.addEventListener('click', async () => {
      try {
        const res = await submitting(btn, () => api('league.join', { method: 'POST', body: { code } }));
        await refreshSession();
        toast(`Welcome to ${res.league.name}.`);
        location.hash = `#/league/${res.league.id}`;
      } catch (err) { toast(err.message, true); }
    });
  }

  async function adminLeagues() {
    const { leagues } = await api('admin.leagues');
    adminShell('leagues', `
      <p class="lede">Private leagues players have made. Settings > Rules has <code>LEAGUES_ON</code>, <code>MAX_LEAGUES_PER_PLAYER</code> and <code>MAX_LEAGUE_MEMBERS</code>.
        As an admin you can open any league to see its table, change it or delete it.</p>
      ${leagues.length ? `<ul class="admin-list">${leagues.map((l) => `<li class="admin-item">
        <span class="grow"><a href="#/league/${l.id}"><strong>${esc(l.name)}</strong></a><br>
          <span class="muted">By ${esc(l.owner.username)} · ${esc(plural(l.members, 'player'))} · code ${esc(fmtCode(l.code))} · made ${esc(fmtDay(l.created))}</span></span>
        <span class="actions"><a class="btn small quiet" href="#/league/${l.id}">Open</a></span></li>`).join('')}</ul>`
        : '<p class="muted">No private leagues yet.</p>'}`);
  }

  // ------------------------------------------------------------------
  // Pages written by the admin, and the menu
  // ------------------------------------------------------------------

  // Links in pages may go to web addresses, email, the site's own pages and uploaded pictures.
  function safeUrl(u) {
    const url = String(u).trim();
    if (/^(https?:\/\/|mailto:|#\/|uploads\/pages\/)/i.test(url)) return url;
    if (/^\/(?!\/)/.test(url)) return url;
    return '#/';
  }

  // Bold, italic, code, links and pictures inside a line (text is escaped first).
  function inline(text) {
    let s = esc(text);
    const codes = [];
    s = s.replace(/`([^`]+)`/g, (m, c) => { codes.push(c); return `\u0000${codes.length - 1}\u0000`; });
    s = s.replace(/!\[([^\]]*)\]\(([^)\s]+)\)/g, (m, alt, url) => `<img src="${safeUrl(url)}" alt="${alt}" loading="lazy">`);
    s = s.replace(/\[([^\]]+)\]\(([^)\s]+)\)/g, (m, label, url) => {
      const href = safeUrl(url);
      return `<a href="${href}"${/^https?:/i.test(href) ? ' rel="noopener" target="_blank"' : ''}>${label}</a>`;
    });
    s = s.replace(/\*\*(?=\S)(.+?)\*\*/g, '<strong>$1</strong>');
    s = s.replace(/(^|[^*\w])\*(?=\S)([^*]+?)\*(?!\*)/g, '$1<em>$2</em>');
    return s.replace(/\u0000(\d+)\u0000/g, (m, i) => `<code>${codes[i]}</code>`);
  }

  /*
   * The page format: # headings, **bold**, *italic*, [link](address),
   * ![picture](address), - lists, 1. lists, > quotes, --- lines, and live
   * blocks on a line of their own: [[table 10]], [[next-match]], [[results 5]],
   * [[join]], [[button Text|address]].
   */
  function renderContent(src) {
    const lines = String(src || '').replace(/\r\n?/g, '\n').split('\n');
    const out = [];
    let para = [];
    let list = null;
    let quote = [];
    const flushPara = () => { if (para.length) out.push(`<p>${para.map(inline).join('<br>')}</p>`); para = []; };
    const flushList = () => { if (list) out.push(`<${list.tag}>${list.items.map((i) => `<li>${inline(i)}</li>`).join('')}</${list.tag}>`); list = null; };
    const flushQuote = () => { if (quote.length) out.push(`<blockquote><p>${quote.map(inline).join('<br>')}</p></blockquote>`); quote = []; };
    const flush = () => { flushPara(); flushList(); flushQuote(); };
    for (const raw of lines) {
      const line = raw.replace(/\s+$/, '');
      let m;
      if (!line.trim()) { flush(); continue; }
      if ((m = line.match(/^\s*\[\[([a-z-]+)(?:\s+([^\]]*))?\]\]\s*$/))) {
        flush();
        out.push(`<div class="widget" data-widget="${esc(m[1])}" data-arg="${esc(m[2] || '')}"></div>`);
      } else if ((m = line.match(/^(#{1,3})\s+(.*)$/))) {
        flush();
        const level = m[1].length + 1;
        out.push(`<h${level}>${inline(m[2])}</h${level}>`);
      } else if (/^\s*(-{3,}|\*{3,})\s*$/.test(line)) {
        flush(); out.push('<hr>');
      } else if ((m = line.match(/^\s*[-*]\s+(.*)$/))) {
        flushPara(); flushQuote();
        if (!list || list.tag !== 'ul') { flushList(); list = { tag: 'ul', items: [] }; }
        list.items.push(m[1]);
      } else if ((m = line.match(/^\s*\d+[.)]\s+(.*)$/))) {
        flushPara(); flushQuote();
        if (!list || list.tag !== 'ol') { flushList(); list = { tag: 'ol', items: [] }; }
        list.items.push(m[1]);
      } else if ((m = line.match(/^>\s?(.*)$/))) {
        flushPara(); flushList(); quote.push(m[1]);
      } else {
        flushList(); flushQuote(); para.push(line.trim());
      }
    }
    flush();
    return out.join('\n');
  }

  // Data for live blocks is loaded once per page view.
  let widgetCache = {};
  const cached = (key, fn) => (widgetCache[key] = widgetCache[key] || fn());

  async function fillWidgets(root) {
    for (const el of root.querySelectorAll('.widget:not([data-done])')) {
      el.dataset.done = '1';
      const arg = el.dataset.arg || '';
      try {
        el.innerHTML = await widgetHtml(el.dataset.widget, arg);
      } catch (err) {
        el.innerHTML = `<p class="muted">${esc(err.message)}</p>`;
      }
    }
  }

  async function widgetHtml(kind, arg) {
    const n = Math.max(1, Math.min(50, parseInt(arg, 10) || 0));
    if (kind === 'table') {
      const data = await cached('standings', () => api('standings'));
      if (!data.table.length) return '<p class="muted">The table fills in once the first match kicks off.</p>';
      return tableHtml(data.table.slice(0, parseInt(arg, 10) ? n : 10)) + '<p><a href="#/table">Full table</a></p>';
    }
    if (kind === 'next-match') {
      const data = await cached('standings', () => api('standings'));
      const m = data.next_match;
      if (!m) return '<p class="muted">No matches coming up.</p>';
      return `<section class="next-match" aria-label="Next match">
        <div class="team home">${teamName(m, 'home')}</div>
        <div class="when">${esc(fmtShort(m.kickoff))}</div>
        <div class="team away">${teamName(m, 'away')}</div>
        <div class="meta">Next match${m.competition ? `, ${esc(m.competition.name)}` : ''}. ${state.user
          ? (m.locked ? 'Predictions have closed.' : `<a href="#/predictions">Make your prediction</a> (${esc(closesText(m).toLowerCase())}).`)
          : '<a href="#/login">Log in</a> to predict.'}</div></section>`;
    }
    if (kind === 'results') {
      const { fixtures } = await cached('fixtures', () => api('fixtures'));
      const done = fixtures.filter((f) => f.result).sort((a, b) => b.kickoff.localeCompare(a.kickoff)).slice(0, parseInt(arg, 10) ? n : 5);
      if (!done.length) return '<p class="muted">No results yet.</p>';
      return `<ul class="mini-results">${done.map((f) => `<li>
        <span class="mr-date">${esc(fmtShort(f.kickoff).split(',')[0])}${compTag(f.competition)}</span>
        <a class="mr-match" href="#/match/${f.id}">${esc(f.home_team)} <strong>${f.result.home}–${f.result.away}</strong> ${esc(f.away_team)}</a></li>`).join('')}</ul>
        <p><a href="#/results">All results</a></p>`;
    }
    if (kind === 'standings') {
      const comps = tableComps();
      const want = arg.trim().toLowerCase();
      const c = comps.find((x) => String(x.id) === want || x.short.toLowerCase() === want || x.name.toLowerCase() === want) || (want ? null : comps[0]);
      if (!c) return `<p class="muted">No league called "${esc(arg)}". Use a competition's short name, like [[standings PL]].</p>`;
      const data = await cached(`teamtable-${c.id}`, () => api('teamtable', { query: { comp: c.id } }));
      if (!data.table.length) return `<p class="muted">The ${esc(c.name)} table fills in as results are entered.</p>`;
      return `<h3 class="widget-title">${esc(c.name)}</h3>${teamTableHtml(data.table, data.competition.places, { compact: true })}
        <p><a href="#/standings/${c.id}">Full ${esc(c.name)} table</a></p>`;
    }
    if (kind === 'join') {
      if (state.user) return '';
      return `<p class="widget-buttons"><a class="btn" href="#/login">Log in</a>${state.settings.registration_open ? ' <a class="btn quiet" href="#/register">Join the league</a>' : ''}</p>`;
    }
    if (kind === 'button') {
      const [label, href = '#/predictions'] = arg.split('|');
      return `<p class="widget-buttons"><a class="btn" href="${esc(safeUrl(href))}">${esc(label.trim() || 'Go')}</a></p>`;
    }
    return `<p class="muted">Unknown block [[${esc(kind)}]].</p>`;
  }

  async function viewPage(slug, isHome = false) {
    renderNav(isHome ? 'home' : `page:${slug}`);
    let data;
    try {
      data = await api('page', { query: { slug } });
    } catch (err) {
      if (err.status === 401) return needLogin('see this page');
      if (err.status === 404) {
        setPage(`<h1>Page not found</h1><p class="lede">That page doesn't exist. <a href="#/table">Go to the league table</a>.</p>`);
        return;
      }
      throw err;
    }
    const p = data.page;
    const notes = [];
    if (p.status === 'draft') notes.push('This page is a draft, so only admins can see it.');
    if (p.audience === 'players') notes.push('Only logged-in players can see this page.');
    setPage(`${state.user && state.user.is_admin ? `<p class="page-admin">${notes.length ? `<span class="muted">${esc(notes.join(' '))}</span> ` : ''}<a class="btn small quiet" href="#/admin/page-${p.id}">Edit page</a></p>` : ''}
      <h1>${esc(p.title)}</h1><div class="page-body">${renderContent(p.body)}</div>`);
    await fillWidgets(main);
  }

  // ---- Admin > Pages ----

  async function adminPages() {
    const data = await api('admin.pages');
    const badge = (t, cls = '') => `<span class="page-badge ${cls}">${esc(t)}</span>`;
    const list = data.pages.map((p) => `<li class="admin-item" data-page="${p.id}">
        <span class="grow"><strong>${esc(p.title)}</strong> ${p.is_home ? badge('Home page', 'home') : ''}${p.status === 'draft' ? badge('Draft') : ''}${p.audience === 'players' ? badge('Players only') : ''}${p.in_menu ? badge('In menu') : ''}<br>
          <span class="muted">#/page/${esc(p.slug)} · changed ${esc(fmtShort(p.updated))}</span></span>
        <span class="actions"><a class="btn small quiet" href="#/page/${esc(p.slug)}">View</a>
          <a class="btn small quiet" href="#/admin/page-${p.id}">Edit</a>
          <button class="btn small danger" type="button" data-drop>Delete</button></span></li>`).join('');
    const menu = data.menu.map((m) => `<li class="admin-item menu-item" data-item="${esc(m.item)}">
        <label class="check"><input type="checkbox"${m.shown ? ' checked' : ''}><span>${esc(m.label)} <span class="hint">${esc(m.kind)}${m.status === 'draft' ? ', draft: hidden until published' : ''}</span></span></label>
        <span class="actions"><button class="btn small quiet" type="button" data-move="-1" aria-label="Move ${esc(m.label)} up">Up</button>
          <button class="btn small quiet" type="button" data-move="1" aria-label="Move ${esc(m.label)} down">Down</button></span></li>`).join('');
    adminShell('pages', `
      <div class="title-row"><p class="lede">Write your own pages, such as a welcome page, the rules or prizes. Any page can be the home page.</p>
        <a class="btn" href="#/admin/page-new">New page</a></div>
      ${data.pages.length ? `<ul class="admin-list">${list}</ul>` : '<p class="muted">No pages yet. The league table is the home page.</p>'}
      ${data.pages.some((p) => p.is_home) ? '' : '<p class="muted">No home page is set, so visitors start on the league table.</p>'}
      <h2>Menu</h2>
      <p class="muted tight">Tick what appears in the menu and put it in order. Admin and your account are always shown. Predict and Messages only show to logged-in players.</p>
      <ul class="admin-list" id="menu-list">${menu}</ul>
      <p><button class="btn" type="button" id="save-menu">Save menu</button></p>`);

    main.querySelectorAll('[data-drop]').forEach((btn) => btn.addEventListener('click', () => {
      const id = Number(btn.closest('[data-page]').dataset.page);
      armed(btn, 'Click again to delete', async () => {
        try {
          await api('admin.page.delete', { method: 'POST', body: { id } });
          await refreshSession();
          toast('Page deleted.');
          adminPages();
        } catch (err) { toast(err.message, true); }
      });
    }));
    const ul = document.getElementById('menu-list');
    ul.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-move]');
      if (!btn) return;
      const li = btn.closest('li');
      if (btn.dataset.move === '-1' && li.previousElementSibling) ul.insertBefore(li, li.previousElementSibling);
      if (btn.dataset.move === '1' && li.nextElementSibling) ul.insertBefore(li.nextElementSibling, li);
      btn.focus();
    });
    document.getElementById('save-menu').addEventListener('click', async (e) => {
      const items = [...ul.querySelectorAll('li')].filter((li) => li.querySelector('input').checked).map((li) => li.dataset.item);
      try {
        await submitting(e.target, () => api('admin.menu.save', { method: 'POST', body: { items } }));
        await refreshSession();
        renderNav('admin');
        toast('Menu saved.');
      } catch (err) { toast(err.message, true); }
    });
  }

  const BLOCKS = [
    ['[[table 10]]', 'Prediction table (top 10)'], ['[[standings PL]]', 'A competition\'s league table'], ['[[next-match]]', 'Next match'], ['[[results 5]]', 'Latest results'],
    ['[[join]]', 'Log in / join buttons (for visitors)'], ['[[button Make your predictions|#/predictions]]', 'Button'],
  ];

  async function adminPageEdit(id) {
    const data = await api('admin.pages');
    const p = id === 'new' ? null : data.pages.find((x) => String(x.id) === String(id));
    if (id !== 'new' && !p) { adminShell('pages', '<p class="lede">That page no longer exists. <a href="#/admin/pages">Back to pages</a>.</p>'); return; }
    const v = p || { title: '', slug: '', body: '', status: 'published', audience: 'all', is_home: false, in_menu: true };
    adminShell('pages', `
      <p><a href="#/admin/pages">Pages</a> / ${p ? esc(p.title) : 'New page'}</p>
      <form id="page-form" class="page-editor">
        <div class="page-meta">
          <label>Title<input type="text" name="title" maxlength="100" required value="${esc(v.title)}" placeholder="Welcome"></label>
          <label>Address<span class="hint">The page opens at #/page/<span id="slug-show">${esc(v.slug || 'welcome')}</span></span>
            <input type="text" name="slug" maxlength="60" value="${esc(v.slug)}" placeholder="made from the title"></label>
        </div>
        <div class="editor-tools" role="toolbar" aria-label="Formatting">
          <button class="btn small quiet" type="button" data-wrap="## |" title="Heading">Heading</button>
          <button class="btn small quiet" type="button" data-wrap="**|**" title="Bold"><strong>B</strong></button>
          <button class="btn small quiet" type="button" data-wrap="*|*" title="Italic"><em>I</em></button>
          <button class="btn small quiet" type="button" data-wrap="[|](https://)" title="Link">Link</button>
          <button class="btn small quiet" type="button" data-wrap="- |" title="Bulleted list">List</button>
          ${data.images ? `<label class="btn small quiet file-btn">Picture<input type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="visually-hidden" id="page-image"></label>` : ''}
          <select id="block-pick" aria-label="Add a live block"><option value="">Add a live block…</option>
            ${BLOCKS.map(([code, label]) => `<option value="${esc(code)}">${esc(label)}</option>`).join('')}</select>
        </div>
        <div class="editor-panes">
          <label class="pane"><span class="visually-hidden">Page text</span>
            <textarea name="body" rows="22" spellcheck="true">${esc(v.body)}</textarea></label>
          <div class="pane preview" aria-label="Preview"><div class="preview-label">Preview</div><h1 id="preview-title"></h1><div class="page-body" id="preview"></div></div>
        </div>
        <details class="format-help"><summary>How to format a page</summary>
          <ul>
            <li><code># Heading</code>, <code>## Smaller heading</code></li>
            <li><code>**bold**</code>, <code>*italic*</code>, <code>[link text](https://example.com)</code> or a page on this site: <code>[the rules](#/page/rules)</code></li>
            <li>Lines starting <code>- </code> make a list, <code>1. </code> a numbered list, <code>&gt; </code> a quote, and <code>---</code> a line across</li>
            <li>A blank line starts a new paragraph</li>
            <li>Live blocks go on a line of their own: <code>[[table 10]]</code> (prediction table), <code>[[standings PL]]</code> (a competition's league table, by short name), <code>[[next-match]]</code>, <code>[[results 5]]</code>, <code>[[join]]</code> (log in buttons, only shown to visitors), <code>[[button Text|#/predictions]]</code></li>
          </ul>
        </details>
        <fieldset class="page-options"><legend class="visually-hidden">Options</legend>
          <label class="check"><input type="radio" name="status" value="published"${v.status === 'published' ? ' checked' : ''}><span>Published</span></label>
          <label class="check"><input type="radio" name="status" value="draft"${v.status === 'draft' ? ' checked' : ''}><span>Draft <span class="hint">Only admins can see it</span></span></label>
          <label class="check"><input type="checkbox" name="players_only"${v.audience === 'players' ? ' checked' : ''}><span>Only for logged-in players</span></label>
          <label class="check"><input type="checkbox" name="in_menu"${v.in_menu ? ' checked' : ''}><span>Show in the menu</span></label>
          <label class="check"><input type="checkbox" name="is_home"${v.is_home ? ' checked' : ''}><span>Make this the home page <span class="hint">The league table moves to Table in the menu</span></span></label>
        </fieldset>
        <div class="savebar"><button class="btn" type="submit">${p ? 'Save page' : 'Create page'}</button>
          ${p ? `<a class="btn quiet" href="#/page/${esc(p.slug)}">View page</a>` : ''}<span class="msg" id="page-msg" aria-live="polite"></span></div>
      </form>`);

    const form = document.getElementById('page-form');
    const ta = form.body;
    const slugOf = (s) => s.normalize('NFKD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 60);
    let slugEdited = !!v.slug;
    let dirty = false;
    let timer;
    const preview = () => {
      document.getElementById('preview-title').textContent = form.title.value || 'Untitled';
      const el = document.getElementById('preview');
      el.innerHTML = renderContent(ta.value);
      fillWidgets(el);
    };
    const changed = () => {
      dirty = true;
      document.getElementById('page-msg').textContent = 'Not saved yet.';
      clearTimeout(timer);
      timer = setTimeout(preview, 250);
    };
    form.title.addEventListener('input', () => {
      if (!slugEdited) form.slug.value = slugOf(form.title.value);
      document.getElementById('slug-show').textContent = slugOf(form.slug.value) || 'welcome';
      changed();
    });
    form.slug.addEventListener('input', () => {
      slugEdited = form.slug.value !== '';
      document.getElementById('slug-show').textContent = slugOf(form.slug.value || form.title.value) || 'welcome';
      changed();
    });
    ta.addEventListener('input', changed);
    form.addEventListener('change', (e) => { if (e.target.type === 'radio' || e.target.type === 'checkbox') dirty = true; });

    const insert = (before, after = '', placeholder = '') => {
      const { selectionStart: a, selectionEnd: b, value } = ta;
      const chosen = value.slice(a, b) || placeholder;
      // Line-level syntax goes at the start of a line.
      const atLineStart = !/^(##|- )/.test(before) || a === 0 || value[a - 1] === '\n';
      const pre = atLineStart ? before : `\n${before}`;
      ta.value = value.slice(0, a) + pre + chosen + after + value.slice(b);
      ta.focus();
      ta.setSelectionRange(a + pre.length, a + pre.length + chosen.length);
      changed();
    };
    form.querySelectorAll('[data-wrap]').forEach((btn) => btn.addEventListener('click', () => {
      const [before, after] = btn.dataset.wrap.split('|');
      insert(before, after, btn.title === 'Link' ? 'link text' : btn.title === 'Heading' ? 'Heading' : 'text');
    }));
    // Live blocks and pictures go on a line of their own, after the line the cursor is on.
    const insertLine = (text) => {
      const v = ta.value;
      let end = v.indexOf('\n', ta.selectionEnd);
      if (end === -1) end = v.length;
      const before = v.slice(0, end);
      const lead = before === '' ? '' : before.endsWith('\n\n') ? '' : before.endsWith('\n') ? '\n' : '\n\n';
      const after = v.slice(end).replace(/^\n*/, '');
      ta.value = before + lead + text + '\n\n' + after;
      const pos = (before + lead + text).length;
      ta.focus();
      ta.setSelectionRange(pos, pos);
      changed();
    };
    const pick = document.getElementById('block-pick');
    pick.addEventListener('change', () => {
      if (!pick.value) return;
      insertLine(pick.value);
      pick.value = '';
    });
    const img = document.getElementById('page-image');
    if (img) {
      img.addEventListener('change', async () => {
        if (!img.files.length) return;
        const fd = new FormData();
        fd.append('image', img.files[0]);
        try {
          toast('Uploading picture…');
          const res = await api('admin.page.image', { form: fd });
          const alt = img.files[0].name.replace(/\.[a-z0-9]+$/i, '').replace(/[-_]+/g, ' ');
          insertLine(`![${alt}](${res.url})`);
          toast('Picture added.');
        } catch (err) { toast(err.message, true); }
        img.value = '';
      });
    }
    const leave = (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } };
    window.addEventListener('beforeunload', leave);
    window.addEventListener('hashchange', () => window.removeEventListener('beforeunload', leave), { once: true });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const res = await submitting(form.querySelector('button[type=submit]'), () => api('admin.page.save', { method: 'POST', body: {
          id: p ? p.id : 0, title: form.title.value, slug: slugOf(form.slug.value || form.title.value), body: ta.value,
          status: form.querySelector('[name=status]:checked').value, audience: form.players_only.checked ? 'players' : 'all',
          in_menu: form.in_menu.checked, is_home: form.is_home.checked } }));
        dirty = false;
        await refreshSession();
        renderNav('admin');
        toast('Page saved.');
        if (!p) location.hash = `#/admin/page-${res.id}`;
        else { document.getElementById('page-msg').textContent = 'Saved.'; form.slug.value = res.slug; }
      } catch (err) { formError(form, err.message); }
    });
    preview();
  }

  // ------------------------------------------------------------------
  // Admin: seasons and teams
  // ------------------------------------------------------------------

  // A sensible next season: August to July, following the latest one.
  function suggestSeason(seasons) {
    let y = new Date().getMonth() >= 6 ? new Date().getFullYear() : new Date().getFullYear() - 1;
    if (seasons.length) y = Math.max(y, Number(seasons[0].end.slice(0, 4)));
    return { name: `${y}/${String((y + 1) % 100).padStart(2, '0')}`, start: `${y}-08-01`, end: `${y + 1}-07-31` };
  }

  function seasonForm(s, suggestion) {
    const v = s || suggestion;
    return `<form class="season-form inline-form" data-season-form="${s ? s.id : 'new'}">
      <label>Name<input type="text" name="name" maxlength="30" required value="${esc(v.name)}"></label>
      <label>Starts<input type="date" name="start" required value="${esc(v.start)}"></label>
      <label>Ends<input type="date" name="end" required value="${esc(v.end)}"></label>
      <label class="check"><input type="checkbox" name="is_current"${s ? (s.is_current ? ' checked' : '') : ''}><span>Current season</span></label>
      <div><button class="btn${s ? ' small' : ''}" type="submit">${s ? 'Save' : 'Add season'}</button>${s ? ' <button class="btn small quiet" type="button" data-cancel>Cancel</button>' : ''}</div>
    </form>`;
  }

  async function adminSeasons(data) {
    data = data || await api('admin.seasons');
    const list = data.seasons.map((s) => `<li class="admin-item" data-season-id="${s.id}">
        <span class="grow"><strong>${esc(s.name)}</strong>${s.current ? ' <span class="page-badge home">Current</span>' : ''}<br>
          <span class="muted">${esc(fmtDay(s.start))} to ${esc(fmtDay(s.end))} · ${esc(plural(s.fixtures, 'fixture'))}</span></span>
        <span class="actions">${s.current ? '' : '<button class="btn small quiet" type="button" data-make-current>Make current</button>'}
          <button class="btn small quiet" type="button" data-edit-season>Edit</button>
          <button class="btn small danger" type="button" data-delete-season>Delete</button></span></li>`).join('');
    adminShell('seasons', `
      <p class="lede">A season is a run of dates. Each fixture belongs to the season its kick-off falls in, so tables and results can be shown season by season.
        Players see the current season unless they choose another.</p>
      ${data.seasons.length ? `<ul class="admin-list" id="season-list">${list}</ul>` : '<p class="muted">No seasons yet, so tables cover every match ever played.</p>'}
      ${data.outside ? `<p class="notice">${esc(plural(data.outside, 'fixture'))} ${data.outside === 1 ? 'isn\'t' : 'aren\'t'} in any season. They still count in "All time" tables.</p>` : ''}
      <h2>Add a season</h2>
      ${seasonForm(null, suggestSeason(data.seasons))}
      <p class="muted">Seasons can't overlap. Deleting a season keeps its fixtures and predictions; only the season and its team lists go.</p>`);

    const save = async (form, id) => {
      try {
        const res = await submitting(form.querySelector('button[type=submit]'), () => api('admin.season.save', { method: 'POST', body: {
          id, name: form.name.value, start: form.start.value, end: form.end.value, is_current: form.is_current.checked } }));
        await refreshSession();
        toast('Season saved.');
        adminSeasons(res);
      } catch (err) { formError(form, err.message); }
    };
    const addForm = main.querySelector('[data-season-form=new]');
    addForm.addEventListener('submit', (e) => { e.preventDefault(); save(addForm, 0); });

    main.querySelectorAll('[data-season-id]').forEach((li) => {
      const s = data.seasons.find((x) => String(x.id) === li.dataset.seasonId);
      const btn = (sel) => li.querySelector(sel);
      if (btn('[data-make-current]')) {
        btn('[data-make-current]').addEventListener('click', async () => {
          try {
            const res = await api('admin.season.save', { method: 'POST', body: { ...s, is_current: true } });
            await refreshSession();
            toast(`${s.name} is now the current season.`);
            adminSeasons(res);
          } catch (err) { toast(err.message, true); }
        });
      }
      btn('[data-edit-season]').addEventListener('click', () => {
        li.innerHTML = seasonForm(s);
        const f = li.querySelector('form');
        f.addEventListener('submit', (e) => { e.preventDefault(); save(f, s.id); });
        f.querySelector('[data-cancel]').addEventListener('click', () => adminSeasons(data));
        f.name.focus();
      });
      btn('[data-delete-season]').addEventListener('click', (e) => armed(e.currentTarget, 'Click again to delete', async () => {
        try {
          const res = await api('admin.season.delete', { method: 'POST', body: { id: s.id } });
          await refreshSession();
          toast(`${s.name} deleted. Its fixtures were kept.`);
          adminSeasons(res);
        } catch (err) { toast(err.message, true); }
      }));
    });
  }

  const crest = (t, size = 28) => (t.badge
    ? `<img class="crest" src="${esc(t.badge)}" alt="" width="${size}" height="${size}">`
    : `<span class="crest blank" style="width:${size}px;height:${size}px" aria-hidden="true">${esc((t.short || t.name).slice(0, 3))}</span>`);

  let teamsSeason = '';
  async function adminTeams(data) {
    data = data || await api('admin.teams', { query: teamsSeason ? { season: teamsSeason } : {} });
    const seasons = state.settings.seasons || [];
    const season = data.season;
    const teams = data.teams;
    const byId = Object.fromEntries(teams.map((t) => [t.id, t]));
    const comps = data.assign;

    let html = `<p class="lede">Your teams, their badges, and which teams are in each competition each season.
      Fixtures added to a competition then have to use teams from its list for that season, which keeps names spelled the same.</p>`;
    if (data.unlisted_fixture_teams) {
      html += `<div class="notice"><p>${esc(plural(data.unlisted_fixture_teams, 'team name'))} in your fixtures ${data.unlisted_fixture_teams === 1 ? 'isn\'t' : 'aren\'t'} in the team list yet.
        <button class="btn small" type="button" id="import-teams">Add them${season ? ` and fill ${esc(season.name)}'s competitions` : ''}</button></p></div>`;
    }

    html += `<h2>Competitions${season ? ` in ${esc(season.name)}` : ''}</h2>`;
    if (!seasons.length) {
      html += `<p class="muted">Add a season in <a href="#/admin/seasons">Seasons</a> first, then choose which teams are in each competition.</p>`;
    } else if (!comps.length) {
      html += `<p class="muted">Add competitions in <a href="#/admin/competitions">Competitions</a> first.</p>`;
    } else {
      html += `<div class="fixture-tools"><label>Season<select id="teams-season">${seasons.map((s) =>
        `<option value="${s.id}"${season && s.id === season.id ? ' selected' : ''}>${esc(s.name)}${s.id === state.settings.current_season ? ' (current)' : ''}</option>`).join('')}</select></label>
        ${data.unlisted_fixture_teams ? '' : `<button class="btn small quiet" type="button" id="fill-from-fixtures">Fill from ${esc(season.name)}'s fixtures</button>`}</div>
        <ul class="comp-team-list">${comps.map((a) => `<li data-comp="${a.competition.id}">
          <details${comps.length === 1 ? ' open' : ''}><summary><strong>${esc(a.competition.name)}</strong>
            <span class="muted">${a.team_ids.length ? esc(plural(a.team_ids.length, 'team')) : 'No team list: any team can be used'}</span>
            <span class="crest-row" aria-hidden="true">${a.team_ids.slice(0, 24).map((id) => byId[id] ? crest(byId[id], 20) : '').join('')}</span></summary>
            <form class="team-picks">
              <input type="search" class="team-filter" placeholder="Find a team" aria-label="Find a team in the list">
              <div class="pick-grid">${teams.map((t) => `<label class="check" data-name="${esc(t.name.toLowerCase())}"><input type="checkbox" name="team" value="${t.id}"${a.team_ids.includes(t.id) ? ' checked' : ''}><span>${crest(t, 20)} ${esc(t.name)}</span></label>`).join('')}</div>
              <div class="inline-form"><label>Add a new team<input type="text" name="new_team" maxlength="30" placeholder="Team name"></label>
                <button class="btn small quiet" type="button" data-add-team>Add to ${esc(a.competition.name)}</button></div>
              <div><button class="btn small" type="submit">Save ${esc(a.competition.name)} for ${esc(season.name)}</button> <span class="muted picked-count"></span></div>
            </form></details></li>`).join('')}</ul>`;
    }

    html += `<h2>All teams</h2>
      <form class="inline-form" id="add-team">
        <label>Team name<input type="text" name="name" maxlength="30" required placeholder="e.g. Brighton"></label>
        <label>Short name<input type="text" name="short_name" maxlength="5" placeholder="BHA" class="short-input"></label>
        <div><button class="btn" type="submit">Add team</button></div>
      </form>
      ${teams.length ? `<ul class="admin-list team-list">${teams.map((t) => `<li class="admin-item" data-team="${t.id}">
        <span class="grow team-line">${crest(t)}<span><strong>${esc(t.name)}</strong> <span class="muted">${esc(t.short)}</span><br>
          <span class="muted">${esc(plural(t.fixtures, 'fixture'))}</span></span></span>
        <span class="actions">
          ${data.badges ? `<label class="btn small quiet file-btn">${t.badge ? 'Change badge' : 'Add badge'}<input type="file" accept="image/png,image/jpeg,image/gif,image/webp" class="visually-hidden" data-badge></label>` : ''}
          ${t.badge ? '<button class="btn small quiet" type="button" data-unbadge>Remove badge</button>' : ''}
          <button class="btn small quiet" type="button" data-edit-team>Edit</button>
          <button class="btn small danger" type="button" data-delete-team>Delete</button></span></li>`).join('')}</ul>`
        : '<p class="muted">No teams yet.</p>'}
      <p class="muted">Badges are cropped into a square and saved as a fresh PNG; a PNG with a see-through background looks best. Renaming a team renames it in all its fixtures. Deleting a team keeps its fixtures.</p>`;
    adminShell('teams', html);

    const reload = (res) => adminTeams(res);
    const sel = document.getElementById('teams-season');
    if (sel) sel.addEventListener('change', () => { teamsSeason = sel.value; adminTeams(); });
    const imp = document.getElementById('import-teams') || document.getElementById('fill-from-fixtures');
    if (imp) {
      imp.addEventListener('click', async () => {
        try {
          const res = await submitting(imp, () => api('admin.teams.import', { method: 'POST', body: { season_id: season ? season.id : 0 } }));
          toast(`Added ${plural(res.added, 'team')}${season ? `, and placed ${plural(res.placed, 'team')} in ${season.name}'s competitions` : ''}.`);
          reload(res);
        } catch (err) { toast(err.message, true); }
      });
    }

    main.querySelectorAll('.comp-team-list [data-comp]').forEach((li) => {
      const compId = Number(li.dataset.comp);
      const form = li.querySelector('form');
      const count = () => { form.querySelector('.picked-count').textContent = `${plural(form.querySelectorAll('[name=team]:checked').length, 'team')} ticked`; };
      count();
      form.addEventListener('change', count);
      form.querySelector('.team-filter').addEventListener('input', (e) => {
        const q = e.target.value.trim().toLowerCase();
        form.querySelectorAll('.pick-grid > label').forEach((l) => { l.hidden = q !== '' && !l.dataset.name.includes(q); });
      });
      const ids = () => [...form.querySelectorAll('[name=team]:checked')].map((i) => Number(i.value));
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
          const res = await submitting(form.querySelector('button[type=submit]'), () => api('admin.compteams.save', { method: 'POST', body: { season_id: season.id, competition_id: compId, team_ids: ids() } }));
          toast('Team list saved.');
          reload(res);
        } catch (err) { formError(form, err.message); }
      });
      form.querySelector('[data-add-team]').addEventListener('click', async (e) => {
        const name = form.new_team.value.trim();
        if (!name) { form.new_team.focus(); return; }
        // A team that already exists is just ticked.
        const existing = teams.find((t) => t.name.toLowerCase() === name.toLowerCase());
        if (existing) {
          form.querySelector(`[name=team][value="${existing.id}"]`).checked = true;
          form.new_team.value = '';
          count();
          form.requestSubmit();
          return;
        }
        try {
          // Save the ticks first so they aren't lost, then add the team into this competition.
          await api('admin.compteams.save', { method: 'POST', body: { season_id: season.id, competition_id: compId, team_ids: ids() } });
          const res = await submitting(e.target, () => api('admin.team.save', { method: 'POST', body: { name, season_id: season.id, competition_id: compId } }));
          toast(`${name} added.`);
          await reload(res);
          const again = main.querySelector(`[data-comp="${compId}"] details`);
          if (again) again.open = true;
        } catch (err) { formError(form, err.message); }
      });
    });

    const addForm = document.getElementById('add-team');
    addForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      try {
        const res = await submitting(addForm.querySelector('button'), () => api('admin.team.save', { method: 'POST', body: { name: addForm.name.value, short_name: addForm.short_name.value, season_id: season ? season.id : 0 } }));
        toast(`${addForm.name.value.trim()} added.`);
        reload(res);
      } catch (err) { formError(addForm, err.message); }
    });

    main.querySelectorAll('[data-team]').forEach((li) => {
      const t = byId[li.dataset.team];
      const q = (s) => li.querySelector(s);
      const badge = q('[data-badge]');
      if (badge) {
        badge.addEventListener('change', async () => {
          if (!badge.files.length) return;
          const fd = new FormData();
          fd.append('id', t.id);
          fd.append('badge', badge.files[0]);
          try {
            await api('admin.team.badge', { form: fd });
            toast(`Badge saved for ${t.name}.`);
            adminTeams();
          } catch (err) { toast(err.message, true); }
        });
      }
      if (q('[data-unbadge]')) {
        q('[data-unbadge]').addEventListener('click', async () => {
          try { await api('admin.team.unbadge', { method: 'POST', body: { id: t.id } }); adminTeams(); } catch (err) { toast(err.message, true); }
        });
      }
      q('[data-edit-team]').addEventListener('click', () => {
        li.innerHTML = `<form class="inline-form grow">
          <label>Team name<input type="text" name="name" maxlength="30" required value="${esc(t.name)}"></label>
          <label>Short name<input type="text" name="short_name" maxlength="5" value="${esc(t.short)}" class="short-input"></label>
          <div><button class="btn small" type="submit">Save</button> <button class="btn small quiet" type="button" data-cancel>Cancel</button></div>
          ${t.fixtures ? `<p class="hint">A new name is used in all ${esc(plural(t.fixtures, 'fixture'))} too.</p>` : ''}</form>`;
        const f = li.querySelector('form');
        f.name.focus();
        f.querySelector('[data-cancel]').addEventListener('click', () => adminTeams(data));
        f.addEventListener('submit', async (e) => {
          e.preventDefault();
          try {
            const res = await submitting(f.querySelector('button[type=submit]'), () => api('admin.team.save', { method: 'POST', body: { id: t.id, name: f.name.value, short_name: f.short_name.value, season_id: season ? season.id : 0 } }));
            toast('Team saved.');
            reload(res);
          } catch (err) { formError(f, err.message); }
        });
      });
      q('[data-delete-team]').addEventListener('click', (e) => armed(e.currentTarget, 'Click again to delete', async () => {
        try {
          const res = await api('admin.team.delete', { method: 'POST', body: { id: t.id, season_id: season ? season.id : 0 } });
          toast(`${t.name} deleted. Its fixtures were kept.`);
          reload(res);
        } catch (err) { toast(err.message, true); }
      }));
    });
  }

  let narrowTeamRow = null;
  let teamListeners = false;
  // Team names to pick from when typing fixtures. When a row's competition has a
  // team list for the season its kick-off is in, only those teams are suggested.
  async function addTeamDatalist() {
    let data;
    try { data = await api('admin.teamlists'); } catch (e) { return; }  // suggestions are optional
    if (!data.teams.length) return;
    const form = main.querySelector('#bulk-form') ? main : null;
    if (!form) return;
    const holder = document.createElement('div');
    holder.hidden = true;
    main.appendChild(holder);
    const made = new Set();
    const listFor = (names, id) => {
      if (!made.has(id)) {
        holder.insertAdjacentHTML('beforeend', `<datalist id="${id}">${names.map((n) => `<option value="${esc(n)}">`).join('')}</datalist>`);
        made.add(id);
      }
      return id;
    };
    listFor(data.teams, 'team-names');
    const seasonOf = (day) => {
      if (!day) return state.settings.current_season;
      const s = data.seasons.find((x) => x.start <= day && day <= x.end);
      return s ? s.id : null;
    };
    const narrow = (row) => {
      const get = (n) => row.querySelector(`[name=${n}]`);
      if (!get('home_team')) return;
      const comp = get('competition_id') ? get('competition_id').value : '';
      const sid = seasonOf(get('kickoff') ? get('kickoff').value.slice(0, 10) : '');
      const names = comp && sid !== null && data.lists[sid] ? data.lists[sid][comp] : null;
      const id = names && names.length ? listFor(names, `team-names-${sid}-${comp}`) : 'team-names';
      for (const side of ['home', 'away']) {
        const input = get(`${side}_team`);
        input.setAttribute('list', id);
        const label = side === 'home' ? 'Home team' : 'Away team';
        input.placeholder = id === 'team-names' ? label : `${label} (${names.length} in list)`;
      }
    };
    main.querySelectorAll('[data-row], .edit-row').forEach(narrow);
    narrowTeamRow = narrow;
    if (teamListeners) return;
    teamListeners = true;
    const rowOf = (el) => (narrowTeamRow && main.querySelector('#bulk-form') ? el.closest('[data-row], .edit-row') : null);
    main.addEventListener('change', (e) => {
      if (e.target.matches('[name=kickoff], [name=competition_id]') && rowOf(e.target)) narrowTeamRow(rowOf(e.target));
    });
    // Rows added later, and the edit form, are narrowed when a team box is used.
    main.addEventListener('focusin', (e) => {
      if (e.target.matches('[name=home_team], [name=away_team]') && rowOf(e.target)) narrowTeamRow(rowOf(e.target));
    });
  }

  // ------------------------------------------------------------------
  // The real league tables (teams), from the results entered
  // ------------------------------------------------------------------

  const tableComps = () => (state.settings.competitions || []).filter((c) => c.has_table);

  function formChips(form) {
    const word = { W: 'Won', D: 'Drawn', L: 'Lost' };
    return `<span class="form-chips" aria-label="Last ${form.length}: ${form.map((f) => word[f]).join(', ')}">${form.map((f) =>
      `<span class="chip ${f.toLowerCase()}" aria-hidden="true">${f}</span>`).join('')}</span>`;
  }

  // Places are coloured by row (1st, 2nd...), as in a printed table, even when teams are level.
  function placeFor(row, total, places) {
    for (const p of places || []) {
      const hit = p.from_bottom ? row >= total - p.to + 1 && row <= total - p.from + 1 : row >= p.from && row <= p.to;
      if (hit) return p;
    }
    return null;
  }

  function teamTableHtml(rows, places, { compact = false } = {}) {
    const total = rows.length;
    const head = compact
      ? '<th scope="col" title="Played">P</th><th scope="col" title="Goal difference">GD</th><th scope="col" title="Points">Pts</th>'
      : `<th scope="col" title="Played">P</th><th scope="col" title="Won">W</th><th scope="col" title="Drawn">D</th><th scope="col" title="Lost">L</th>
         <th scope="col" class="wide" title="Goals for">F</th><th scope="col" class="wide" title="Goals against">A</th>
         <th scope="col" title="Goal difference">GD</th><th scope="col" title="Points">Pts</th><th scope="col" class="form-col">Form</th>`;
    let prev = null;
    const body = rows.map((r, i) => {
      const p = placeFor(i + 1, total, places);
      const z = [p ? `zc-${p.colour}` : '', i > 0 && p !== prev ? 'place-edge' : ''].filter(Boolean).join(' ');
      prev = p;
      const gd = `${r.diff > 0 ? '+' : ''}${r.diff}`;
      const cells = compact
        ? `<td>${r.played}</td><td>${gd}</td><td class="pts">${r.points}</td>`
        : `<td>${r.played}</td><td>${r.won}</td><td>${r.drawn}</td><td>${r.lost}</td><td class="wide">${r.for}</td><td class="wide">${r.against}</td>
           <td>${gd}</td><td class="pts">${r.points}</td><td class="form-col">${formChips(r.form)}</td>`;
      const crestImg = r.badge ? `<img class="crest" src="${esc(r.badge)}" alt="" width="22" height="22" loading="lazy">` : '<span class="crest" aria-hidden="true"></span>';
      return `<tr${z ? ` class="${z}"` : ''}${p && p.label ? ` title="${esc(p.label)}"` : ''}><td class="pos">${r.position}</td>
        <td class="name"><span class="team-cell">${crestImg}<span class="tname">${esc(r.team)}</span><abbr class="tshort" title="${esc(r.team)}">${esc(r.short || r.team.slice(0, 3).toUpperCase())}</abbr></span></td>${cells}</tr>`;
    }).join('');
    return `<div class="table-wrap"><table class="league teams${compact ? ' compact' : ''}">
      <thead><tr><th scope="col"><span class="visually-hidden">Position</span></th><th scope="col" class="name">Team</th>${head}</tr></thead>
      <tbody>${body}</tbody></table></div>`;
  }

  function placesLegend(places) {
    if (!places || !places.length) return '';
    return `<ul class="zone-legend">${places.map((p) => `<li class="legend-item"><span class="swatch zc-${esc(p.colour)}" aria-hidden="true"></span>
      <strong>${esc(placeRange(p))}</strong>${p.label ? ` ${esc(p.label)}` : ''}</li>`).join('')}</ul>`;
  }

  async function viewStandings(arg) {
    renderNav('standings');
    const data = await api('teamtable', { query: { comp: arg || '', ...seasonQuery() } });
    if (!data.competition) {
      setPage(`<h1>League tables</h1><div class="notice"><p>There are no league competitions yet.${state.user && state.user.is_admin
        ? ' Add one in <a href="#/admin/competitions">Admin > Competitions</a> and set its type to League.' : ''}</p></div>`);
      return;
    }
    const c = data.competition;
    const tabs = data.competitions.length > 1 ? `<nav class="tabs table-switch" aria-label="Competitions">${data.competitions.map((x) =>
      `<a href="#/standings/${x.id}"${x.id === c.id ? ' aria-current="page"' : ''}>${esc(x.name)}</a>`).join('')}</nav>` : '';
    let html = `${tabs}<div class="title-row"><h1>${esc(c.name)}</h1>${seasonPicker()}</div>
      <p class="lede">The ${esc(c.name)} table${data.season ? ` for ${esc(data.season.name)}` : ''}, from the results entered on this site. 3 points for a win, 1 for a draw.
        <a href="#/table/${c.id}">Prediction table</a></p>`;
    if (!data.table.length) {
      html += `<div class="notice"><p>No results yet${data.season ? ` in ${esc(data.season.name)}` : ''}. The table fills in as results are entered.</p></div>`;
    } else {
      html += teamTableHtml(data.table, c.places) + placesLegend(c.places);
    }
    setPage(html);
  }

  // ------------------------------------------------------------------
  // Router
  // ------------------------------------------------------------------

  async function refreshSession() {
    applySession(await api('session'));
  }

  async function route() {
    widgetCache = {};
    const [, page = '', arg = ''] = (location.hash || '#/').split('/');
    try {
      switch (page) {
        case '': if (state.settings.home_page) await viewPage(state.settings.home_page, true); else await viewTable(); break;
        case 'table': if (arg) await viewCompTable(arg); else await viewTable(); break;
        case 'page': await viewPage(decodeURIComponent(arg)); break;
        case 'standings': await viewStandings(arg); break;
        case 'leagues': await viewLeagues(arg); break;
        case 'league': await viewLeague(arg); break;
        case 'join': await viewJoin(arg); break;
        case 'predictions': await viewPredictions(); break;
        case 'results': await viewResults(); break;
        case 'match': await viewMatch(arg); break;
        case 'player': await viewPlayer(arg); break;
        case 'login': viewLogin(); break;
        case 'register': viewRegister(); break;
        case 'account': viewAccount(); break;
        case 'admin': await viewAdmin(arg || 'fixtures'); break;
        case 'messages': await viewMessages(arg === 'sent' ? 'sent' : 'inbox'); break;
        case 'message': await viewMessage(arg); break;
        case 'compose': await viewCompose(arg); break;
        case 'forgot': viewForgot(); break;
        case 'help': viewHelp(); break;
        case 'matchday': await viewMatchday(arg); break;
        case 'reset': await viewReset(arg); break;
        default:
          renderNav('');
          setPage(`<h1>Page not found</h1><p class="lede">That page doesn't exist. <a href="#/table">Go to the league table</a>.</p>`);
      }
    } catch (err) {
      if (err.status === 401) { state.user = null; renderNav(''); needLogin('see that'); return; }
      errorPage(err);
    }
    refreshUnread();
  }

  let lastPage = null;
  // The season picker on any page: remember the choice and redraw.
  main.addEventListener('change', (e) => {
    if (!e.target.matches('[data-season]')) return;
    try { localStorage.setItem('pl.season', e.target.value); } catch (err) { /* storage unavailable */ }
    route();
  });
  window.addEventListener('hashchange', () => {
    const page = (location.hash || '#/').split('/')[1] || '';
    if (page !== lastPage) {
      window.scrollTo(0, 0);
      main.focus({ preventScroll: true });
    }
    lastPage = page;
    route();
  });

  (async () => {
    try {
      await refreshSession();
    } catch (err) {
      renderNav('');
      errorPage(err);
      return;
    }
    route();
  })();
})();
