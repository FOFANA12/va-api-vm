# VA API

API backend du projet **VA** (Value Assessment) — développée avec **Laravel 11**, sécurisée par **Laravel Sanctum**, et documentée via **Scribe**.

---

## Prérequis

| Outil | Version minimale |
|-------|-----------------|
| PHP | 8.2+ |
| Composer | 2.x |
| MySQL | 8.x |
| Node.js | 18+ (optionnel, pour les assets) |

---

## Installation

### 1. Cloner le dépôt

```bash
git clone <url-du-repo> va-mdn-api
cd va-mdn-api
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Editer `.env` et renseigner au minimum :

```env
APP_URL=http://127.0.0.1:8000
APP_URL_FRONT=http://127.0.0.1:5170

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=va_db
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:5170,127.0.0.1:5170
```

### 4. Créer la base de données et exécuter les migrations

```bash
php artisan migrate
```

### 5. (Optionnel) Alimenter la base avec les données de démarrage

```bash
php artisan db:seed
```

### 6. Lancer le serveur de développement

```bash
php artisan serve
```

L'API est accessible sur `http://127.0.0.1:8000`.

---

## Structure du projet

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/              # Authentification (login, profil, mot de passe)
│   │   ├── Settings/          # Référentiels (devises, régions, rôles, etc.)
│   │   ├── Export/            # Export Excel / Word
│   │   ├── Report/            # Rapports & tableaux de bord
│   │   └── ...                # Contrôleurs métier
│   ├── Resources/             # Transformations JSON (API Resources)
│   └── Requests/              # Validation des requêtes
├── Models/                    # Modèles Eloquent
├── Repositories/              # Couche d'accès aux données
└── Console/Commands/          # Commandes Artisan personnalisées

database/
├── migrations/                # Migrations (schéma complet)
└── seeders/

routes/
└── api.php                    # Toutes les routes API
```

---

## Authentification

L'API supporte deux modes d'authentification via **Laravel Sanctum** :

| Mode | Préfixe | Usage |
|------|---------|-------|
| Token (API) | `/api-auth` | Applications mobiles / clients externes |
| Session (SPA) | `/spa-auth` | Frontend Vue/React sur le même domaine |

### Connexion (token)

```http
POST /api-auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

Toutes les routes protégées requièrent le header :

```http
Authorization: Bearer <token>
```

### Déconnexion

```http
POST /api-auth/logout
Authorization: Bearer <token>
```

Toutes les routes sont préfixées par `/api`.


## Packages clés

| Package | Rôle |
|---------|------|
| `laravel/sanctum` | Authentification API / SPA |
| `phpoffice/phpspreadsheet` | Export Excel |
| `phpoffice/phpword` | Export Word |
| `spatie/laravel-activitylog` | Journal d'audit |
| `laravel-lang/common` | Traductions (fr / en / ar) |

---

## Localisation

L'application supporte trois langues. La locale se définit dans `.env` :

```env
APP_LOCALE=fr
SUPPORTED_LOCALES=fr,en,ar
```

---

## Tests

```bash
php artisan test
```

---

## Commandes Artisan utiles

```bash
# Vider tous les caches
php artisan optimize:clear

# Lister les routes
php artisan route:list

# Lancer les queues
php artisan queue:work

```

---

## Variables d'environnement importantes

| Variable | Description | Exemple |
|----------|-------------|---------|
| `APP_URL` | URL de l'API | `http://127.0.0.1:8000` |
| `APP_URL_FRONT` | URL du frontend | `http://127.0.0.1:5170` |
| `SANCTUM_STATEFUL_DOMAINS` | Domaines SPA autorisés | `localhost:5170` |
| `DB_DATABASE` | Nom de la base MySQL | `va_db` |
| `SESSION_LIFETIME` | Durée de session (minutes) | `600` |

---

## Développé par

**LEADERTECH-SOLUTIONS**