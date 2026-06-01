# Архитектура проекта — Кочующий стол

## Фронтенд (Vue 3 + TypeScript + Inertia)

### Страницы (`resources/js/pages/`)

**Публичные**
- `Welcome.vue` — лендинг со ссылками на вход/регистрацию

**Авторизация (`auth/`)**
- `Login.vue`, `Register.vue`, `ForgotPassword.vue`, `ResetPassword.vue` — стандартные формы
- `TwoFactorChallenge.vue` — ввод кода 2FA или кода восстановления
- `ConfirmPassword.vue`, `VerifyEmail.vue` — вспомогательные шаги

**Резервирования (`reservations/`)**
- `Index.vue` — грид всех резервирований с участниками; кнопки редактирования/удаления — только для своих
- `Create.vue` — форма создания: дата, часы, стол, мультиселект участников с чипами
- `Show.vue` — детальный просмотр; участники выделены (текущий — "(вы)")
- `Edit.vue` — редактирование своего резервирования

**Дашборд**
- `Dashboard.vue` — три блока: входящие приглашения (принять/отклонить), форма новой заявки с мультиселектом участников, список своих заявок со статусами

**Настройки (`settings/`)**
- `Profile.vue` — имя, email, удаление аккаунта
- `Security.vue` — смена пароля, настройка 2FA
- `Appearance.vue` — выбор темы (light/dark/system)
- `Token.vue` — генерация и просмотр API-токена

**Администратор (`admin/`)**
- `tables/Index.vue`, `Create.vue`, `Edit.vue` — CRUD столов
- `reservations/Index.vue` — все резервирования + отправка приглашений
- `requests/Index.vue` — заявки: одобрить/отклонить, назначить стол

---

### Компоненты (`resources/js/components/`)

**Лейаут**
`AppShell`, `AppSidebar`, `AppHeader`, `AppContent`, `AppLogo`, `AppLogoIcon`, `Breadcrumbs`, `AppSidebarHeader`

**Навигация**
`NavMain`, `NavUser`, `NavFooter`

**Формы и UI**
`InputError`, `AlertError`, `PasswordInput`, `Heading`, `TextLink`, `PlaceholderPattern`

**Специализированные**
`TwoFactorSetupModal`, `TwoFactorRecoveryCodes`, `AppearanceTabs`, `DeleteUser`, `UserInfo`, `UserMenuContent`

---

## Контроллеры (`app/Http/Controllers/`)

### Web — Inertia-страницы

| Контроллер | Методы |
|---|---|
| `Web/DashboardController` | `index` — собирает столы, заявки пользователя, приглашения, список всех юзеров |
| `Web/ReservationWebController` | `index` — все резервирования с участниками; `create/store` — создание + прикрепление участников; `show/edit/update/destroy` |
| `Web/ReservationRequestWebController` | `store` — создание заявки с автором и списком user_ids |
| `Web/InviteWebController` | `accept`, `reject` — принятие/отклонение приглашения |

### Admin — только `is_admin`

| Контроллер | Методы |
|---|---|
| `Admin/AdminTableController` | Полный CRUD столов |
| `Admin/AdminReservationController` | `index`, `sendInvite`, `destroy` |
| `Admin/AdminReservationRequestController` | `index`, `updateStatus` (одобрение создаёт Reservation + Invites), `assignTable`, `destroy` |

### Settings

| Контроллер | Методы |
|---|---|
| `Settings/ProfileController` | `edit`, `update`, `destroy` |
| `Settings/SecurityController` | `edit`, `update` (пароль), `editToken`, `generateToken` |

### API — Sanctum-токен (`routes/api.php`)

| Контроллер | Ответственность |
|---|---|
| `ReservationController` | create, show, update, softDelete, attachUser, detachUser |
| `ReservationRequestController` | CRUD + attachUser/detachUser, associateTable |
| `TableController` | CRUD + addReservation, addReservationRequest |
| `InviteController` | create, show, setStatus, accept, revoke, softDelete |
| `UserController` | attachReservation, detachReservation, attachReservationRequest, detachReservationRequest |

---

## Маршруты

### `routes/web.php`
| Метод | Путь | Обработчик |
|---|---|---|
| GET | `/` | Welcome (Inertia) |
| GET | `/dashboard` | `DashboardController@index` |
| POST | `/reservation-requests` | `ReservationRequestWebController@store` |
| PUT | `/invites/{id}/accept` | `InviteWebController@accept` |
| PUT | `/invites/{id}/reject` | `InviteWebController@reject` |

### `routes/reservations.php`
| Метод | Путь | Обработчик |
|---|---|---|
| GET | `/reservations` | `ReservationWebController@index` |
| GET | `/reservations/create` | `ReservationWebController@create` |
| POST | `/reservations` | `ReservationWebController@store` |
| GET | `/reservations/{id}` | `ReservationWebController@show` |
| GET | `/reservations/{id}/edit` | `ReservationWebController@edit` |
| PUT | `/reservations/{id}` | `ReservationWebController@update` |
| DELETE | `/reservations/{id}` | `ReservationWebController@destroy` |

### `routes/admin.php` (middleware: `auth`, `verified`, `admin`)
| Метод | Путь | Обработчик |
|---|---|---|
| GET/POST | `/admin/tables` | `AdminTableController@index/store` |
| GET | `/admin/tables/create` | `AdminTableController@create` |
| GET/PUT/DELETE | `/admin/tables/{id}` | `AdminTableController@edit/update/destroy` |
| GET | `/admin/reservations` | `AdminReservationController@index` |
| POST | `/admin/reservations/{id}/invite` | `AdminReservationController@sendInvite` |
| DELETE | `/admin/reservations/{id}` | `AdminReservationController@destroy` |
| GET | `/admin/requests` | `AdminReservationRequestController@index` |
| PUT | `/admin/requests/{id}/status` | `AdminReservationRequestController@updateStatus` |
| PUT | `/admin/requests/{id}/table` | `AdminReservationRequestController@assignTable` |
| DELETE | `/admin/requests/{id}` | `AdminReservationRequestController@destroy` |

### `routes/settings.php`
| Метод | Путь | Обработчик |
|---|---|---|
| GET | `/settings/profile` | `ProfileController@edit` |
| PATCH | `/settings/profile` | `ProfileController@update` |
| DELETE | `/settings/profile` | `ProfileController@destroy` |
| GET | `/settings/security` | `SecurityController@edit` |
| PUT | `/settings/password` | `SecurityController@update` |
| GET/POST | `/settings/api-token` | `SecurityController@editToken/generateToken` |
| GET | `/settings/appearance` | Appearance (Inertia) |

---

## Итого

| | |
|---|---|
| Страниц | 22 |
| Компонентов | 23 |
| Контроллеров | 15 |
| Web-маршрутов | 42 |
