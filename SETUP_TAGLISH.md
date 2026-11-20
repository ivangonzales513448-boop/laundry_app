# LabaGo — Setup at Gabay (Taglish)

Ito ang guide para patakbuhin ang **Ionic + Angular web app** at ang **Laravel API** sa Windows
gamit ang XAMPP/MySQL. Ang lumang Laravel Blade pages ay nananatili; ang Ionic ang bagong
frontend para sa mobile at desktop browser.

> Mahalaga: huwag i-run ang `php artisan migrate:fresh`, `db:wipe`, o anumang reset command sa
> MySQL database na may totoong records. Huwag ding burahin ang `database/database.sqlite`.
> Naka-backup pa rin ang orihinal na SQLite file at hindi ito ginagalaw ng import command.

---

## 1. Ano ang tumatakbo

| Bahagi | Teknolohiya | Lokal na URL |
|---|---|---|
| Ionic web app | Ionic 8 + Angular 20 + TypeScript | `http://localhost:8100` |
| Laravel API | Laravel 12 + Sanctum personal access tokens | `http://localhost:8000/api/v1` |
| API health check | Laravel | `http://localhost:8000/api/v1/health` |
| Legacy Blade app | Laravel (nananatili) | `http://localhost:8000` |
| Database | MySQL/MariaDB sa XAMPP | `localhost:3306`, database `sudsandfold` |

Ang Ionic app ay web app at may **Android project na rin (Capacitor)** sa `ionic-frontend/android`.
Tingnan ang **Section 15** para gumawa ng APK. Hindi pa ito na-test sa totoong phone.

---

## 2. Mga kailangan bago mag-start

Sa Windows machine na ito, ito ang na-test na setup:

- Windows 10/11
- XAMPP na may **MySQL/MariaDB** at **PHP 8.2+** (PHP 8.2.12 ang na-test)
- Composer 2
- Node.js 20+ at npm (Node 22.23.2 at npm 10.9.8 ang na-test)
- VS Code (optional lang, pero recommended)
- Google Chrome o Edge

Check kung available ang tools. Buksan ang **VS Code Terminal** o PowerShell:

```powershell
php -v
composer --version
node -v
npm -v
```

Kung may `command not found`, i-install muna ang tool at i-restart ang terminal/VS Code.

---

## 3. Start ang XAMPP at database

1. Buksan ang **XAMPP Control Panel**:
   - Karaniwang nasa `C:\xampp\xampp-control.exe`.
   - Pwede ring Start Menu → XAMPP Control Panel.
2. I-click ang **Start** sa **MySQL**.
   - Kailangan lang ang MySQL para sa API at Ionic.
   - Hindi kailangan ang Apache kung `php artisan serve` ang gagamitin.
   - Kailangan ang Apache kung bubuksan ang phpMyAdmin sa `http://localhost/phpmyadmin`.
3. Siguraduhing **Port 3306** ang MySQL. Kung iba ang port, palitan ang `DB_PORT` sa `.env`.

### Kung existing database na sa machine na ito

Huwag nang gumawa ng bagong database at huwag nang mag-import. Nandiyan na ang records.
Start lang ang MySQL at sundan ang **Section 6: Start commands**.

### Kung bago ang machine o bagong clone

Gumawa ng database sa phpMyAdmin:

1. Buksan ang `http://localhost/phpmyadmin`.
2. **New** → database name: `sudsandfold`.
3. Collation: `utf8mb4_unicode_ci`.
4. **Create**.

O kaya sa SQL tab:

```sql
CREATE DATABASE sudsandfold
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Pagkatapos ay gawin ang setup sa Section 4.

---

## 4. First-time Laravel setup (bagong machine lang)

Buksan ang terminal sa project root:

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk"
```

### 4.1 Install PHP dependencies

```powershell
composer install
```

### 4.2 Gumawa ng `.env`

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Buksan ang `.env` sa VS Code at i-check ang database:

```dotenv
APP_NAME="LabaGo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sudsandfold
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:8100,http://127.0.0.1:8100
SANCTUM_TOKEN_EXPIRATION=10080
```

Notes:

- Default XAMPP MySQL: username `root`, blank password. Huwag itong gamitin sa production.
- `FRONTEND_URL` ay listahan ng pinapayagang Ionic origins para sa CORS. Pwedeng
  i-comma-separate ang maraming URL.
- Ang `SANCTUM_TOKEN_EXPIRATION=10080` ay 7 araw (minutes).
- Huwag ilagay ang database password o anumang secret sa Ionic frontend.

### 4.3 Patakbuhin ang migrations

```powershell
php artisan migrate
```

**Huwag** gamitin ang `migrate:fresh` sa machine na may existing records.

### 4.4 Ilipat ang lumang records — pumili ng isang paraan

**Option A — May existing SQLite records (ito ang ginamit dito):**

```powershell
php artisan laundry:import-sqlite
```

Ito ay non-destructive:

- Binabasa ang `database/database.sqlite`.
- Kinokopya sa MySQL gamit ang upsert/IDs.
- Hindi binabago o binubura ang SQLite file.
- Pwedeng i-run ulit; may verification table ng row counts at totals.

Pwede ring i-check lang muna:

```powershell
php artisan laundry:import-sqlite --verify-only
```

**Option B — Walang lumang records, demo data lang ang kailangan:**

```powershell
php artisan db:seed
```

Importante: **huwag pagsabayin** ang `laundry:import-sqlite` at `db:seed` sa isang database.
Ang seeder ay gumagawa ng demo orders, kaya puwedeng madoble ang records kapag paulit-ulit
na pinatakbo.

### 4.5 Optional: linisin ang config cache

Kung may lumang config cache:

```powershell
php artisan optimize:clear
```

---

## 5. First-time Ionic setup

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend"
npm install
```

Ang API URL ay nasa:

- `src/environments/environment.ts` (development)
- `src/environments/environment.prod.ts` (production build)

Default:

```ts
apiBaseUrl: 'http://localhost:8000/api/v1',
apiOrigin: 'http://localhost:8000',
```

Kung ibang computer o LAN IP ang API, palitan ang `localhost` ng tamang IP. Huwag
ilagay ang database credentials dito.

---

## 6. Start commands — dalawang terminal

Kailangang tumakbo ang **dalawang server** nang sabay.

### Terminal 1 — Laravel API

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk"
php artisan serve --host=127.0.0.1 --port=8000
```

Expected:

```text
INFO  Server running on [http://127.0.0.1:8000].
```

I-check sa browser:

```text
http://localhost:8000/api/v1/health
```

Expected JSON:

```json
{
  "success": true,
  "message": "API is running.",
  "data": { "time": "..." }
}
```

### Terminal 2 — Ionic web app

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend"
npm start -- --port 8100
```

Expected:

```text
Local: http://localhost:8100/
```

Buksan sa Chrome:

```text
http://localhost:8100
```

Gamitin ang `localhost`, hindi `127.0.0.1`, para pareho ang origin ng browser at CORS config.
Pinapayagan naman ang dalawa sa `.env` ng project.

### Pag-stop

Sa bawat terminal, pindutin ang `Ctrl + C`.

---

## 7. Test accounts (local only)

| Role | Email | Password |
|---|---|---|
| Admin | `admin@sudsandfold.test` | `password` |
| Staff | `staff@sudsandfold.test` | `password` |
| Rider | `rider@sudsandfold.test` (o `0918 123 4567`) | `password` |
| Customer | `customer@sudsandfold.test` (o `0919 123 4567`) | `password` |

Pwedeng mag-login gamit ang **email o phone number**. Huwag ilagay ang mga ito sa production o
public documentation. Palitan ang passwords bago i-deploy. Ang staff at rider na nag-register ay
**Pending** hanggang ma-approve ng admin; ang customer ay pwede agad gumamit.

---

## 8. Paano gamitin ang app

### Feature checklist

- Login, register, logout; full name, email, contact number, password at confirmation.
- Unique email check, inline validation, show/hide password, loading state, at bawal ang doble
  na submit habang nagpoproseso.
- Awaiting Approval page para sa bagong staff; approve/reject sa admin.
- Admin at Staff roles; API-enforced ang access, hindi lang UI hiding.
- Customers: list, search, detail, create, edit, delete with safety checks.
- Orders: list, search/filter, create, live server-side price preview, detail, status journey.
- Pickup flow na nagtatapos sa **Claimed**; delivery flow na nagtatapos sa **Completed**.
- Payments: unpaid/partial/paid, history, balance, overpayment guard, printable receipt.
- Schedule: today/upcoming/overdue filters at rescheduling with history.
- Dashboard: operational counts and today's pickups/deliveries, walang revenue/income.
- Responsive shell: mobile bottom tabs, desktop side navigation, admin-only menu entries.
- Loading indicators, empty states, error messages, confirmation dialogs, status badges at
  progress indicators.
- Admin services CRUD at staff management/approvals.

### Roles

- **Admin**: lahat ng operations, services management, staff management, approvals.
- **Staff**: dashboard, orders, schedule, customers, payments, profile. Wala sa admin pages.
- **Pending/Rejected**: hindi makaka-access ng operational data; naka-redirect sa
  Awaiting Approval page o blocked ng API.

### Bagong staff registration

1. Pumunta sa `http://localhost:8100/register`.
2. Fill-in: full name, email, contact number, password, confirm password.
3. Submit. Lalabas ang **Awaiting Approval** page.
4. Login bilang admin → **Staff management** → approve or reject.
5. Kapag approved, makaka-login na ang staff.

Hindi pwedeng gumawa ng admin account sa public registration.

### Order flow

**Shop pickup:**

```text
Pending → Washing → Drying → Ready for Pickup → Claimed
```

**Delivery:**

```text
Pending → Washing → Drying → Ready for Delivery → Out for Delivery → Completed
```

Sa database, `completed` ang underlying status ng pickup kapag na-claim. Sa UI at API summary,
**Claimed** ang label para malinaw ang pickup flow. Puwedeng i-cancel ang order habang hindi pa
closed, may reason, at naka-record ito sa history.

### Payments

- Unpaid → Partial → Paid.
- May payment history, remaining balance, at printable receipt.
- Ang total ay kinakalkula ng server; hindi pwedeng i-trust lang ang client.
- Ang overpayment ay tinatanggihan.
- Ang delivery fee ay idinadagdag sa total kapag delivery.

### Schedule at reschedule

- May today/upcoming/overdue grouping at filters.
- Pwedeng mag-reschedule; may reason at history ng lumang schedule.

### Dashboard

Operational summary lang: active orders, pickups/deliveries today, overdue, status counts,
today's pickups/deliveries. **Walang revenue o income report** sa dashboard.

### Responsive navigation

- **Mobile**: bottom tabs — Dashboard, Orders, Schedule, More.
- **Desktop**: side navigation.
- **More**: Customers, Payments, Profile, Logout.
- **Admin only**: Services, Staff management.
- May search, filters, status badges, progress indicators, loading/empty/network error states,
  confirmation dialogs, at printable receipt.

---

## 9. API at token authentication

### Base URL

```text
http://localhost:8000/api/v1
```

### Header para sa protected calls

```http
Accept: application/json
Authorization: Bearer <access_token>
Content-Type: application/json
```

### Login example (PowerShell)

```powershell
$body = @{
  email = 'admin@sudsandfold.test'
  password = 'password'
  device_name = 'ionic-web'
} | ConvertTo-Json

$response = Invoke-RestMethod `
  -Method Post `
  -Uri 'http://localhost:8000/api/v1/auth/login' `
  -ContentType 'application/json' `
  -Body $body

$response.data.access_token
```

### Authenticated request example

```powershell
$headers = @{
  Authorization = "Bearer $($response.data.access_token)"
  Accept = 'application/json'
}

Invoke-RestMethod `
  -Uri 'http://localhost:8000/api/v1/dashboard' `
  -Headers $headers
```

### Response envelope

Lahat ng API responses ay pareho ang hugis:

```json
{
  "success": true,
  "message": "OK",
  "data": { }
}
```

Error:

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "email": ["The email field is required."]
  },
  "code": "validation_failed"
}
```

Paginated list:

```json
{
  "success": true,
  "message": "OK",
  "data": {
    "items": [],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 0,
      "from": null,
      "to": null
    }
  }
}
```

### Endpoint map

**Public**

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/auth/register` | Staff registration; creates pending staff |
| POST | `/auth/login` | Login; returns Bearer token |
| GET | `/health` | API health check |

**Authenticated (approved account required for operations)**

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/auth/me` | Current user |
| POST | `/auth/logout` | Revoke current token |
| PUT | `/auth/profile` | Update name/contact number |
| PUT | `/auth/password` | Change password; revokes other tokens |
| GET | `/dashboard` | Operational summary, no revenue |
| GET | `/schedule` | Schedule groups/filters |
| GET | `/customers` | Search/paginate customers |
| POST | `/customers` | Create customer |
| GET | `/customers/{id}` | Customer detail, orders, totals |
| PUT | `/customers/{id}` | Update customer |
| DELETE | `/customers/{id}` | Delete only if allowed |
| GET | `/services` | Services list |
| POST | `/services` | Create service (admin) |
| PUT | `/services/{id}` | Update service (admin) |
| DELETE | `/services/{id}` | Delete service (admin) |
| GET | `/orders/form-options` | Customers/services for order form |
| POST | `/orders/price-preview` | Server-side price estimate |
| GET | `/orders` | Search/filter/paginate orders |
| POST | `/orders` | Create order |
| GET | `/orders/{id}` | Order detail, journey, payments |
| POST | `/orders/{id}/status` | Update status; validates transition |
| POST | `/orders/{id}/payment` | Record payment |
| POST | `/orders/{id}/reschedule` | Reschedule with history |
| GET | `/orders/{id}/receipt` | Receipt data |
| GET | `/payments` | Payment history/filters |
| GET | `/payments/methods` | Allowed payment methods |

**Admin only**

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/admin/users` | Staff directory |
| POST | `/admin/users` | Create staff/admin account |
| PUT | `/admin/users/{id}` | Update role/status/active/password |
| DELETE | `/admin/users/{id}` | Delete account with safety checks |
| GET | `/admin/users/stats` | Staff counts |
| GET | `/admin/approvals` | Pending/rejected registrations |
| POST | `/admin/approvals/{id}/approve` | Approve registration |
| POST | `/admin/approvals/{id}/reject` | Reject registration; revoke tokens |

### Token, session, CSRF, at CORS

- Auth mode: **Sanctum personal access tokens** na ipinapadala sa `Authorization: Bearer`.
- Ang token ay naka-save sa browser `localStorage` sa key na `suds_fold_access_token`.
  User profile lang ang nasa memory, hindi ang password. Ang customer, order, payment, at
  schedule records ay galing sa API/MySQL — walang totoong records na nakaimbak sa
  `localStorage`.
- Ang API routes ay **stateless**; hindi kailangan ng CSRF cookie/session para sa `/api/*`.
- Ang legacy Blade app ay gumagamit pa rin ng normal session at CSRF protection. Huwag
  paghaluin ang dalawang auth mode.
- Expired/revoked token → `401`. Ini-clear ng Ionic ang session at bumabalik sa login.
- Pending account → `403` na may code na `pending_approval`.
- Rejected account → `403` na may code na `rejected`; binubura ang tokens.
- CORS: pinapayagan lang ang origins sa `FRONTEND_URL` (o `CORS_ALLOWED_ORIGINS`).
  Kung magdadagdag ng bagong frontend URL, i-update ang `.env` at i-restart ang API.
- `supports_credentials=false` dahil Bearer token ang gamit, hindi cookies.

---

## 10. Tests at production build

### Laravel tests

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk"
php artisan test --compact
```

Na-test na resulta:

```text
Tests: 42 passed (362 assertions)
```

Saklaw ng tests: registration, duplicate email, login, pending/rejected access, approval,
admin/staff roles, customer rules, order transitions, delivery address, cancellation,
reschedule history, payment/balance/overpayment, receipt, dashboard without revenue,
schedule filters, price preview.

Ang `phpunit.xml` ay gumagamit ng in-memory SQLite para sa tests, kaya hindi nito
ginagalaw ang MySQL records.

### Ionic production build

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend"
npm run build
```

Output:

```text
C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend\dist\ionic-frontend
```

Na-test na ring nag-build nang walang error. Para i-preview ang build, kailangan ng static
server (halimbawa `npx http-server dist/ionic-frontend/browser`) at tamang API URL sa
`environment.prod.ts`.

---

## 11. Troubleshooting

### `SQLSTATE[HY000] [2002] Connection refused`

- Hindi tumatakbo ang MySQL sa XAMPP.
- Start MySQL sa XAMPP Control Panel.
- Check `DB_HOST=127.0.0.1`, `DB_PORT=3306`.
- Kung `localhost` ang host at may socket issue, gamitin ang `127.0.0.1`.

### `Access denied for user 'root'@'localhost'`

- May password ang MySQL root. Ilagay sa `DB_PASSWORD`.
- Huwag ilagay ang password sa Ionic environment file.

### `Unknown database 'sudsandfold'`

Gumawa ng database sa phpMyAdmin o SQL, tapos:

```powershell
php artisan migrate
php artisan laundry:import-sqlite
```

### Port 8000 o 8100 is already in use

```powershell
Get-NetTCPConnection -LocalPort 8000,8100
```

Pwede ring palitan ang port:

```powershell
php artisan serve --host=127.0.0.1 --port=8001
npm start -- --port 8101
```

Kung magbabago ang Ionic port, i-update ang `FRONTEND_URL` sa `.env` at i-restart ang API.

### CORS error sa browser

- Gamitin ang `http://localhost:8100`.
- Check na kasama ang origin sa `FRONTEND_URL`.
- Pagkatapos baguhin ang `.env`, i-restart ang `php artisan serve`.
- Kung may config cache: `php artisan optimize:clear`.

### `401 Unauthorized`

- Expired/revoked token. Log out at login ulit.
- Check na `Authorization: Bearer <token>` ang header.
- Huwag i-copy ang token mula sa ibang browser storage.

### `403 pending_approval`

- Normal kapag bagong register. Kailangan ng admin approval sa Staff Management.
- Rejected account: kontakin ang admin; hindi na makaka-login.

### Walang lumalabas na records sa Ionic

- Check na tumatakbo ang API sa port 8000.
- Buksan ang `http://localhost:8000/api/v1/health`.
- Check ang browser DevTools → Network para sa failed API calls.
- Huwag burahin ang `database/database.sqlite`; backup lang ito at hindi ginagamit ng API
  habang `DB_CONNECTION=mysql`.

### Nag-cache ang browser

Hard refresh: `Ctrl + Shift + R`. Kung development server, i-restart ang `npm start`.

---

## 12. Mga limitasyon

- **Web browser lang ang na-test** (Chrome headless at desktop/mobile viewport). Hindi
  na-test sa totoong Android o iOS device.
- May Capacitor Android project na, pero hindi pa na-build/na-test sa totoong phone. Walang iOS.
- Print receipt ay browser print dialog; PDF ay via **Save as PDF**. Wala pang direct
  thermal-printer integration.
- Walang online payment gateway; records lang ng cash/GCash/bank payments ang nilalalaman.
- Walang refund/void workflow; kailangang i-record ayon sa existing na payment rules.
- In-app notifications lang (bell icon). Wala pang SMS, push notification, o email.
- Wala pang live map ng rider; may "Navigate" button na nagbubukas ng Google Maps.
- Wala pang Google/Facebook login.
- Loyalty points ay naiipon at nakikita, pero wala pang redeem.
- Hindi multi-branch; isang shop ang configuration.
- Walang offline mode. Kailangan ng network sa local API.
- Ang `localStorage` bearer token ay exposed sa XSS. Para sa production, gumamit ng HTTPS,
  maayos na CSP, at stronger security review.
- Local XAMPP defaults (`root`, blank password) ay para sa development lang.

---

## 13. Backup at data safety

Bago mag-import o mag-update sa production-like data:

```powershell
# MySQL backup (XAMPP path example)
& "C:\xampp\mysql\bin\mysqldump.exe" -u root sudsandfold > "C:\path\to\backup-sudsandfold.sql"
```

- Ang SQLite file ay hindi binabago ng import command.
- Huwag i-run ang `migrate:fresh`, `db:wipe`, o `php artisan db:seed` nang hindi nagba-backup.
- Ang tests ay gumagamit ng hiwalay na in-memory SQLite database.

---

## 14. Mabilis na checklist

```text
[ ] Start MySQL sa XAMPP
[ ] Terminal 1: php artisan serve --host=127.0.0.1 --port=8000
[ ] Terminal 2: npm start -- --port 8100
[ ] Buksan http://localhost:8100
[ ] Login: admin@sudsandfold.test / password
[ ] Check dashboard, orders, payments, schedule
[ ] Register a staff account → approve as admin
[ ] Run php artisan test --compact
[ ] Run npm run build
```

Kung may error, tingnan muna ang terminal output ng Laravel at Ionic, at ang DevTools
**Console** at **Network** tabs ng browser.

---

## 15. Gumawa ng Android APK

Ang Android app ay ang parehong Ionic app, naka-balot gamit ang **Capacitor 8**.

### Isang beses lang: i-install ang tools

1. I-install ang **Android Studio** (https://developer.android.com/studio). Kasama na rito ang
   Android SDK at ang JDK 21 na kailangan (ang Java 8 na naka-install ngayon ay hindi sapat).
2. Buksan ang Android Studio isang beses at tapusin ang setup wizard (Standard).

### Mahalaga: ang IP ng PC

Hindi maabot ng phone ang `localhost` ng PC. Kaya ang mobile build ay naka-turo sa Wi-Fi IP ng PC:
`http://192.168.100.5:8000` (nasa `ionic-frontend/src/environments/environment.mobile.ts`).

- Pareho dapat ang Wi-Fi ng phone at ng PC.
- Kung nagbago ang IP (`ipconfig` → IPv4 Address sa Wi-Fi), palitan sa file na iyon at i-build ulit.
- Patakbuhin ang Laravel na bukas sa network (hindi lang 127.0.0.1):

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk"
php artisan serve --host=0.0.0.0 --port=8000
```

- Kapag nagtanong ang Windows Firewall, i-click ang **Allow** (Private network).
- Test sa browser ng phone: `http://192.168.100.5:8000/api/v1/health` — dapat may JSON.

### I-build ang APK

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend"
npm run android
```

Bubuksan nito ang Android Studio. Doon: **Build → Build App Bundle(s) / APK(s) → Build APK(s)**.
Ang APK ay nasa `ionic-frontend\android\app\build\outputs\apk\debug\app-debug.apk`.
Kopyahin sa phone at i-install (payagan ang "Install unknown apps").

O kaya i-plug ang phone (naka-on ang USB debugging) at i-click ang **Run ▶** sa Android Studio.

| Command | Ginagawa |
|---|---|
| `npm run build:mobile` | Web build na naka-turo sa IP ng PC |
| `npm run cap:sync` | Build + kopya sa Android project (gawin pagkatapos ng code changes) |
| `npm run android` | Sync + buksan ang Android Studio |
| `npm run apk` | Sync + debug APK sa command line (kailangan ang JAVA_HOME = JDK 21) |

### Paalala

- Debug APK ito, para sa testing. Para sa Play Store, kailangan ng signed release build at
  HTTPS server para sa API.
- Kung "Network error" sa app: check ang IP, Wi-Fi, firewall, at kung `--host=0.0.0.0` ang Laravel.

---

## 16. Customer, rider, tracking at admin features

### Mga role

| Role | Saan napupunta pagka-login | Ginagawa |
|---|---|---|
| Admin / Staff | `/app/dashboard` | Orders, pagtimbang, rider assignment, payments, reports (admin) |
| Rider | `/r/tasks` | Accept/decline ng pickup at delivery, proof (photo/pirma), cash, kita |
| Customer | `/c/home` | Mag-book ng pickup, track, history, reorder, rating, addresses, points |

### Order status flow

`Order Placed → Pickup Scheduled → Rider on the Way → Picked Up → Received at Shop → Washing →
Drying → Folding / Ironing → Ready for Delivery / Ready for Claim → Out for Delivery →
Delivered / Claimed → Completed`

- Walk-in: nagsisimula sa **Received at Shop**.
- Drop-off (online o QR): **Order Placed → Received at Shop**.
- Kapag **Delivered/Claimed** at bayad na lahat, kusang nagiging **Completed** (may loyalty points).
- Ang lumang status na `pending` ay **Received at Shop** na ngayon; walang record na binago.

### Walk-in at QR code

1. Bawat order ay may **tracking number** (hal. `LGM7NVKP4G`). Nasa receipt/claim ticket na may QR.
2. **More → Shop QR codes**: i-print at ilagay sa counter.
   - "Start your laundry order" QR → `/start` (pangalan + phone lang, walang account).
   - "Track your order" QR → `/track`.
3. Pwedeng gumawa ng account ang walk-in customer sa `/signup` at ilagay ang tracking number
   para ma-link ang lumang orders (dapat parehong phone number).

Para gumana ang QR sa phone ng customer, dapat maabot ang web app: patakbuhin ang Ionic na
bukas sa network at pareho ang Wi-Fi:

```powershell
cd "C:\Users\Admin\OneDrive\Desktop\mobile apk\ionic-frontend"
npm start -- --host 0.0.0.0 --port 8100
```

Ang address ay nasa `publicWebUrl` sa `ionic-frontend/src/environments/*.ts`
(ngayon: `http://192.168.100.5:8100`). Kapag nagbago ang IP, palitan ito at ang
`FRONTEND_URL` sa `.env`.

### Riders

1. Admin: **Staff management → Add user → Role: Rider** (o mag-apply ang rider sa `/register`
   at piliin ang "Rider", tapos i-approve).
2. Sa order details: **Assign pickup rider** o **Assign delivery rider**.
3. Rider: Accept → Start (sinasabihan ang customer) → Complete na may photo/pirma at cash.
4. Kapag nag-decline ang rider, may notification ang staff at lalabas sa dashboard
   "Needs your attention".
5. Bayad sa rider bawat task: `RIDER_FEE_PER_TASK` sa `.env` (default ₱40).

### Admin

- **Reports**: sales (bayad na natanggap), order volume, by service/payment/source, rider
  performance. May daily, weekly, monthly at custom dates.
- **Promos & delivery areas**: promo codes (percent o fixed, minimum, limit, petsa) at
  delivery fee bawat area.
- **Dashboard**: revenue ngayon/linggo/buwan at listahan ng dapat asikasuhin
  (bagong online order, kailangan ng rider, kailangang timbangin).

### Backup

Bago idinagdag ang features na ito, may MySQL backup sa
`database/backups/sudsandfold-before-labago-features-*.sql`.


---

## 17. Mga update: login, payment, ETA, timeout

### Demo accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@demo.com` | `admin123` |
| Staff | `staff@demo.com` | `staff123` |
| Rider | `rider@demo.com` | `rider123` |
| Customer | `customer@demo.com` | `customer123` |

Para gawin ulit o i-reset ang mga ito (walang ibang data na ginagalaw):

```powershell
php artisan labago:demo-accounts
```

### Login at sign-up

- Login: card sa gitna, may "Forgot password?" at "Create an account".
- Sign-up: may pagpili ng role (Customer, Staff, Rider). Ang customer ay puwede agad;
  ang staff at rider ay "Waiting for admin approval" hanggang i-approve ng admin
  (sa Dashboard o Users).
- Forgot password: may request na lalabas sa admin (**Password requests**). Kung naka-set up
  ang email sa `.env` (`MAIL_MAILER=smtp` at iba pa), may reset link din na ipapadala.

### Services

Nakatago na ang Services sa app. Lahat ng order ay gumagamit ng presyo ng unang active service
(o ng `DEFAULT_SERVICE_ID` sa `.env`). Hindi nabura ang lumang data.

### Bagong order (customer)

- Dalawang opsyon: **Pick-up & Delivery** at **Track a delivery**.
- Mode of payment: Cash on Delivery, E-wallet, Credit/Debit Card (record lang, walang
  online payment).
- May tantyang oras ng pickup at delivery. Pagkatapos mag-order, lalabas ang order number
  at diretso sa tracking page.
- Naka-save ang hindi pa natapos na order kaya hindi mawawala kapag na-logout.

### Session timeout

- Admin at Staff: 15 minuto. Rider at Customer: 30 minuto.
- May babala 1 minuto bago mag-logout ("Stay Logged In" / "Log Out").
- Hindi nila-logout ang rider habang bukas ang task na nasa daan.

### Kapag nagbago ang IP ng PC

Sa login screen, i-tap ang **"Server: … Change"** at ilagay ang bagong IP (`ipconfig` →
IPv4 Address). Hindi na kailangang mag-build ng bagong APK.

---

## 18. Quality / usability acceptance (Help & FAQ, accessibility, testing)

Ginawa ito para pumasa ang app sa "Mobile Application Development Requirements and
Measurable Acceptance Criteria". Ang buong ulat ay nasa **`docs/qa/`**:

| File | Laman |
|---|---|
| `docs/qa/ACCEPTANCE_TEST_REPORT.md` | Bawat acceptance criterion: PASS / kailangan pa ng user testing, at ang ebidensya |
| `docs/qa/TEST_CASES.md` | Lahat ng test case (expected, actual, PASS/FAIL, screenshot) |
| `docs/qa/HEURISTIC_EVALUATION.md` | Nielsen's 10 heuristics: ano ang nakita at inayos |
| `docs/qa/USABILITY_TEST_KIT.md` | Tasks, benchmark times, SUS form at formulas para sa 10 participants |

### Mga bago sa app

- **Help & FAQ** sa menu ng bawat role (`/c/help`, `/r/help`, `/app/help`) at sa login screen
  (`/help`). May search at "Still need help?" na may Call/Email ng shop.
  Ang laman ay nasa `ionic-frontend/src/app/core/help-content.ts`. Kapag binago ang isang
  workflow, i-update din ang sagot doon.
- **"How this works"** na gabay sa New order (customer at staff), Order detail, Rider task at Users.
- **Malinaw na error messages:** hindi na lumalabas sa app ang teknikal na error (hal. "SQLSTATE…").
  Ang detalye ay nasa `storage/logs/laravel.log` lang.
- **"Too many attempts. Please wait N seconds and try again."** kapag sobra ang login attempts.
  Hiwalay na ang limit ng login, sign-up, forgot password at QR guest order.
- **Accessibility:** mas malinaw na kulay ng text (≥ 4.5:1), mas malalaking tap targets,
  may pangalan ang bawat button para sa TalkBack, at nakikitang focus outline.

### Patakbuhin ulit ang tests

- Server tests: `php artisan test`
- Browser tests (accessibility + functional): tingnan ang `qa/README.md`. Gumagamit ito ng
  hiwalay na SQLite test database. Hindi nito ginagalaw ang MySQL ng shop.
