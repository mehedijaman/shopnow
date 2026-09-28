# Localization Plan (English + Bangla)

Phase A investigation findings and the Phase B implementation contract. Written before implementation; approved before Phase B started.

Companion documents:
- `docs/localization/glossary-bn.md` — Bangla terminology (authoritative, edit first when adding strings)
- `docs/localization/guide.md` — conventions and "how to add a language" (written in B4)
- `docs/localization/report.md` — final report (written in B4)

---

## 1. Decisions (approved)

| Topic | Decision |
|---|---|
| Switching model | Per-browser preference, **no URL prefix**. DB content not translated this pass → per-locale URLs would advertise duplicate English pages. Resolution lives in one place so URL locales can be added later. |
| Resolution order | Saved locale of the authenticated account for the current guard (storefront: `customer`, admin: `user`), then the locale cookie, then `config('app.locale')`. |
| Explicit switch | Writes the cookie; if logged in, also saves to the account. |
| Login merge | Explicit cookie wins and is saved to the account; otherwise the account locale is written back to the cookie. |
| Input safety | Only locales in `config('localization.php')['supported']`. Never pass request input to `setLocale()` or into a lang path unvalidated. |
| Persistence | `users.locale`, `customers.locale` (nullable), `orders.locale` snapshot at order creation. Migrations in the owning modules. |
| Language switch | `POST` endpoint + **full page reload** (guarantees `<html lang dir>`, JS bundle, fonts, server strings change together). Works without JS on the storefront. |
| Module ownership | New `Localization` module owns config loader, `SetLocale` middleware, switch endpoint, `LocaleManager`, login listener, console tooling. Lang **files** stay with the modules that own the strings. |
| Dual-session (admin + customer in one browser) | Guard-specific: storefront consults `customer.locale`, admin consults `user.locale`. The cookie is shared but only consulted after the current guard's account. |
| Admin-facing mail locale | `users.locale` if set, else `config('app.locale')` (`en`). Internal mail never chases the customer locale. |
| Customer mail locale | `orders.locale` snapshot (order-bound) or `customers.locale` (guest/profile mail). |
| JS mechanism | **Custom loader** (no new dependencies): Artisan `localization:export` (`Arr::dot`) → per-surface/per-locale JSON, lazy-imported by a rewritten `Translations` plugin with a minimal Laravel `MessageSelector`. |
| Bangla digits (display) | Bangla digits ০–৯ when `native_digits` is `true`, with **Western 3-digit grouping** (`১,২৩,৪৫৬` → we use `১,৪৫৬` comma-every-3). Never in inputs, URLs, data attributes, IDs, machine output. |
| Bangla font | **Noto Sans Bengali** (400/600/700) from `fonts.bunny.net` (existing origin, no new third-party), under `:lang(bn)`. |
| Out of scope | Translating DB content, admin translation editor UI, URL-prefixed locales/hreflang, RTL styling (emit `dir` from config so it's possible later). |

---

## 2. Findings that shaped the plan

### 2.1 Module wiring
- Modules: `modules/{PascalCase}/`; providers extend `Modules\Support\BaseServiceProvider`.
- `BaseServiceProvider` maps `routes/{app,site,api}.php` only. Views registered per-module: `$this->loadViewsFrom(__DIR__.'/views', '<ns>')`.
- View namespaces (lang namespaces must match): `index`, `cart`, `product`, `order`, `customer`, `page`, `contactMessage`, `blog`, `courier`, `slider`, `customer-auth`, `admin-auth`, plus (no views yet) `acl`, `settings`, `user`, `profile`, `promo-code`, `dashboard`.
- `config/view.php` adds `resources-site/views` → `@extends('site-layout')` resolves there.

### 2.2 The existing `Translations` plugin (replaced, not duplicated)
- `resources/js/Plugins/Translations.js` reads `window._translations`; injected by `<x-modular-translations>` **only** in `resources/views/app.blade.php` (admin). Storefront has no i18n at all.
- Source: `Arr::dot` of `lang/{locale}/*.php` + `lang/{locale}.json` + `lang/vendor/modular/{locale}/{locale}.json` — flat, `:param` only, no plurals, no surface split, `rememberForever` cache in production.
- The populated source `lang/vendor/modular/en/en.json` is **English-sentence-as-key** JSON — contradicts our key-based decision. Those keys are removed; the few Vue pages calling `__('English sentence')` are migrated to key-based calls.

### 2.3 Middleware / SSR / error pages
- `bootstrap/app.php` appends `HandleInertiaRequests` to `web`. `SetLocale` is inserted **before** it (runs after `StartSession`/`EncryptCookies`, before Inertia shares props).
- Inertia SSR is **not active** (no `inertia:ssr`, no SSR Vite entry). JS still reads locale/messages from props, never `window`, so enabling SSR later is safe.
- No custom error views → 404/419/500 render outside `web` in `en`. **Documented limitation** (B4).

### 2.4 Environment facts verified
- `ext-intl` loaded; `NumberFormatter` available.
- Carbon supports `bn` (translates month/day names: `সেপ্টেম্বর`, `সোমবার`); it does **not** convert digits — handled by a helper.
- `devfaysal/laravel-bangladesh-geocode`: `bn_name` populated on all four tables; both lookup endpoints already return full models, so `bn_name` is in the payload → serve `bn` names in `bn`.
- `.env`: `APP_LOCALE=en`, `APP_FALLBACK_LOCALE=en`.
- Admin user model = `Modules\User\Models\User` (guard `user`); customer = `Modules\Customer\Models\Customer` (guard `customer`).

### 2.5 Formatting / currency today
- Prices: hardcoded `৳` + `number_format($x, 2)` (storefront); emails use `number_format($x, 2).' Tk'`. **No currency setting** — symbol is hardcoded; en output must stay identical.
- Dates: `->format('d M Y, h:i A')` in ~20 controllers, emails, PDF invoice.
- `resources/js/Utils/formatMoney.js`: bare `toFixed(2)`.

### 2.6 Mail / notification / job paths
| Path | Trigger | Locale source |
|---|---|---|
| `SendOrderPlacedMail` (admin) | queued job | `users.locale` else `en` |
| `SendCustomerOrderConfirmationMail` | queued job | `orders.locale` |
| `ProductDownloadReadyMail` | queued, via `GrantDownloadsOnPayment` on `OrderPaymentConfirmed` | `orders.locale` (event gains a plain `string $locale`) |
| `ResetPassword` (AdminAuth / CustomerAuth) | request | `HasLocalePreference` on the model |
| `ContactMessageMail` | request | admin locale rule |

### 2.7 Tests baseline
- 67 test files (Feature suite globs `modules/*/Tests` + `tests/Feature`), ~41 `assertSee(...)` English-text assertions.
- `tests/TestCase.php` does not force a locale. Baseline run recorded at start of Phase B (see `tests/` output / report).

---

## 3. Architecture

### 3.1 Locale config — `config/localization.php`
```php
return [
    'supported' => [
        'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr', 'og_locale' => 'en_US', 'native_digits' => false],
        'bn' => ['name' => 'Bengali',  'native' => 'বাংলা',   'dir' => 'ltr', 'og_locale' => 'bn_BD', 'native_digits' => true],
    ],
    'cookie' => ['name' => 'locale', 'lifetime' => 31536000], // 1 year
];
```
Fallback = `config('app.fallback_locale')` (`en`). No locale list is hardcoded anywhere else.

### 3.2 Lang file layout
| Location | Purpose |
|---|---|
| `lang/{locale}/` (app) | `common.php` (shared buttons/labels/statuses/table+pagination chrome), `validation.php`, `auth.php`, `passwords.php`, `pagination.php` |
| `modules/{Module}/lang/{locale}/` | registered once in the module's provider (hook added to `BaseServiceProvider`/per-module boot) under the **same namespace as its views** |
| Per-module files | `site.php` (storefront), `admin.php` (admin UI), `messages.php` (flash/exception text), `mail.php` (emails/notifications) |

Site/admin split keeps the storefront JS payload free of admin strings.

### 3.3 Key conventions
- Lowercase snake_case, dot-nested ≤3 levels below the file (`site.cart.empty_title`).
- Placeholders Laravel syntax (`:name`, `:count`). Never concatenate translated fragments (Bangla word order differs).
- Countables use Laravel plural syntax; prefer phrasing that reads correctly without branching.
- No dynamically constructed keys (`t(`status.${x}`)`) — lookup maps with literal keys.
- Identifiers never translated (permissions, routes, enum backing values, status codes, events, config keys, DB values, CSS classes). Only display labels.
- English extracted **verbatim**; English output must not change (typos listed in report if fixed).

### 3.4 JS integration (custom loader)
1. `php artisan localization:export` reads PHP lang files with `File::getRequire` + `Arr::dot` (same robust approach as the current component) and writes:
   - `resources/js/lang/{locale}.json` (admin surface: `common`, module `admin`/`messages`/`mail`)
   - `resources-site/js/lang/{locale}.json` (site surface: `common`, module `site`, enum labels)
2. Loader (rewritten `Translations.js`): resolves locale from request data (Inertia shared props / `data-locale` script tag), dynamic-`import()`s only that surface+locale bundle, exposes `__($key, $replace)` with `:param` + minimal `MessageSelector` (`|`, `{n}`, `[a,b]`, `:count`).
3. Installed in `create-vue-app.js` (storefront) and `resources/js/app.js` (admin). Module namespaces carried into the bundle keys (`order.site.title`).
4. Dev: export runs on lang-file change (Vite full-reload); `deploy.sh` runs export before `npm run build`.
5. No `window` access at import time → SSR-safe.

Rejected alternatives: `laravel-vue-i18n` (robust plurals but adds composer+npm deps, single bundle across surfaces, forces vue-i18n compiler); keeping the current plugin (flat, no plurals, no split, stale `rememberForever` cache, English-sentence JSON source).

### 3.5 Infrastructure pieces (B0)
- `modules/Localization/`: `LocalizationServiceProvider`, `LocaleManager` (validates → sets `App::setLocale()` + Carbon locale together), `Http/Middleware/SetLocale`, `Http/Controllers/LocaleSwitchController` (`POST /locale`, `throttled:6,1`, same-origin redirect only), `Listeners/SyncLocaleOnLogin`, `Console/LocalizationCheckCommand`, `Console/LocalizationExportCommand`, trait `NormalizesBanglaDigits` (opt-in, per-field `prepareForValidation`), helpers for formatting.
- Middleware order: appended to `web` **before** `HandleInertiaRequests`.
- Inertia shared props: `locale`, `locales` (map of code → {name, native, dir}).
- `<html lang dir>` on `site-layout.blade.php`, `app.blade.php`, `CustomerAuth/views/layouts/master.blade.php`, `AdminAuth/views/layouts/master.blade.php`.
- `HasLocalePreference` on `Modules\User\Models\User` + `Modules\Customer\Models\Customer`.
- Switchers: Blade component in storefront navbar + mobile drawer (works without JS via POST form); `AppLanguageSwitcher.vue` registered in `AppComponentsResolver.js` (`Misc` group) in `AppTopBar.vue`; also on `GuestLayout.vue` and both guest/auth Blade layouts.
- Typography: `:lang(bn)` stack adding Noto Sans Bengali, `line-height` bump, no `letter-spacing`.
- Missing-key: `Translator::handleMissingKeysUsing` → `MissingTranslationKeys` **recorder**, never throws. Throwing is impossible because the framework itself probes absent `validation.custom.*`, `validation.attributes` and per-rule keys. The recorder ignores `validation.*` and any literal that is not key-shaped (`::` or dotted), since those are vendor-owned sentences (`__('Forbidden')`, `__($exception->getMessage())`). Our own dotted/namespaced keys are still caught by `localization:check`. `tests/TestCase::tearDown()` fails the test on anything recorded.
- `php artisan localization:check` (CI, non-zero exit): key parity, placeholder parity, empty values, referenced-but-missing keys (literal `__()`, `trans_choice()`, Vue `__()`), unused keys (warning only).
- ESLint: **local** `localization/no-raw-text` rule (warn) defined inside `eslint.config.js` — eslint-plugin-vue ships no such rule and `@intlify/eslint-plugin-vue-i18n` would be a new dependency. Dropped the unused `@eslint/js` import (`js.configs.recommended`); Vue + Prettier rules only. `localization:check` also skips `Tests` directories.

### 3.6 Formatting layer
- PHP/Blade: one helper set (e.g. `format_money()`, `format_number()`, `format_date()`, `format_relative()`), Carbon locale + native-digit conversion when `native_digits`.
- Vue: `useFormatting()` composable mirroring the same rules.
- `en` output byte-identical to today (same grouping/currency format).
- Bangla digits for display only; never for inputs/URLs/attrs/IDs/machine output.
- Month/day names follow locale (Carbon `bn` verified working).

### 3.7 Bangla digit input (opt-in)
Trait / `prepareForValidation` mapping **specific numeric fields only** (e.g. `phone`) through a normalizer `০১৭… → 017…`. Never blanket-convert request input (would corrupt Bangla addresses containing digits).

### 3.8 HTML/SEO
`<html lang dir>` on all layouts; `og:locale` (+ alternates in SeoService) from config; `Content-Language` header; JSON-LD `inLanguage`. No hreflang (same URL). If a CDN/full-page cache sits in front, HTML **must vary by the locale cookie** — flag at deploy time.

### 3.9 Third-party/embedded
reCAPTCHA `hl`; datepicker + TipTap UI strings; Chart.js number/date formatting. No cookie-consent banner exists in the codebase (nothing to translate). **Known limitation:** browser-native validation bubbles follow the browser language (not controllable).

---

## 4. String inventory summary (estimates; exact counts from `localization:check`)

| Surface | Files | Strings (est.) |
|---|---|---|
| Storefront Blade | 54 | ~600 |
| Storefront Vue islands | 15 | ~120 |
| Admin Vue (Inertia) | 164 | ~1,800 |
| Admin PHP (flash/FormRequests/enum labels) | ~120 | ~300 |
| JS config/stores | `menu.js`, datalayer | ~80 |
| Emails/notifications | 6 | ~120 |
| Seeded UI text (`settings.label`) | Settings seeders | ~140 |
| **Total (est.)** | | **~3,200** |

`settings.label` → render-time key derived from setting key (`settings.labels.{key}` / `settings.descriptions.{key}`) with fallback to DB value; data untouched.

**Intentional non-translations:** brand names, all DB content (products, categories, brands, tags, posts, CMS pages, sliders, setting *values*), permission names, route names, enum backing values, status codes, GA event names, config keys, DB values, social network names, courier provider names, `৳`, `ShopNow`.

**English-text-branching hunt:** grep for `=== 'Active'` / `includes('...')` / text-keyed selectors returned **no hits**; statuses come from enum `->label()` accessors. Re-swept in B4.

---

## 5. Stage order (Phase B)

- **B0 — Infrastructure.** config, `Localization` module, middleware, `LocaleManager`, switch endpoint, 3 migrations, `HasLocalePreference`, login listener, Inertia props, `<html lang dir>`, JS plumbing both front-ends, switchers (incl. guest screens), Bangla font/typography, formatting helpers, `localization:check`, missing-key handler, lint rule, docs skeleton. Switching works end-to-end for already-externalized strings.
- **B1 — Framework + shared.** `bn` validation/auth/passwords/pagination, `common.php`, shared components (`AppDataTable`, `AppPaginator`, `AppConfirmDialog`, `AppFlashMessage`, breadcrumbs, empty states).
- **B2 — Storefront module by module.** layout/header/footer first → Index → Product (variation selector, bundle display) → Cart → Order/checkout (login-gate for downloadable items) → CustomerAuth → account/downloads → ContactMessage → Blog → every customer-facing email.
- **B3 — Admin module by module.** layout/topbar/sidebar (`Configs/menu.js`) → AdminAuth → Dashboard → each CRUD module → Settings → Profile → ACL → admin flash.
- **B4 — Verification + docs.** full suite, leftover-string sweep, bn review, `guide.md`, `report.md`.

Per-batch recipe: extract → key → `en` verbatim → `bn` → replace usages → `localization:check` + relevant tests. Full suite green before the next stage. Commit per module batch.

---

## 6. Risks

| Risk | Mitigation |
|---|---|
| Bangla overflow in navbar/buttons/table headers/chips/labels at mobile | typography pass in B2/B3; visual check of key flows |
| Formatting regressions (dates/numbers) | helper layer + tests under both locales; `en` byte-identical |
| Queued-job locale leakage | explicit `locale` on jobs/mail; `orders.locale` snapshot; event `locale` field |
| Layout regressions from inline-string replacement | verbatim-`en` extraction; full suite green per stage |
| Stale `rememberForever` translation cache | replaced by export-at-build + no in-prod cache of flat maps |
| Plural-rule edge cases in hand-rolled selector | prefer non-branching phrasing; `localization:check` parity |
| Bn review quality | glossary first; flag high-risk strings (legal, payment, amounts/dates) for native review |

---

## 7. Test strategy (Pest)

- Resolution order (account → cookie → default); invalid/malicious locale rejected (never reaches `setLocale`/lang path).
- Switch endpoint sets cookie, persists to account, redirects same-origin only.
- Login-time preference merge (cookie wins → saved to account; else account → cookie).
- `TestCase` forces `en` + enables missing-key failures → existing assertions keep passing.
- `bn` smoke: key storefront routes (home, shop, product, cart, checkout, login/signup, contact, blog, account/downloads) + key admin screens → 200, `lang="bn"`, no missing-key exceptions.
- Key/placeholder parity across all lang dirs (asserted in a test as well as the command).
- Emails: `bn` customer mail renders Bangla; queued mail keeps locale; download-ready uses `orders.locale` even from an `en` admin session.
- Formatting helpers under both locales; digit normalization on a phone field.
- No JS test runner — Vue coverage via lint rule + static key scan in `localization:check`.

---

## 8. Known limitations (re-stated in final report)

- Error pages (404/419/500) outside `web` middleware render in `en`.
- Browser-native validation bubbles follow browser language.
- DB-stored content (products, posts, pages, sliders, setting values) stays English.
- No hreflang/per-locale URLs (by decision).
- Admin translation editing UI: none (by decision).
- CDN/full-page cache must vary by locale cookie if introduced.
