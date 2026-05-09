# Postgraduate Digital Administration System

Laravel 11 application with Inertia.js and React for postgraduate workflows (submissions, supervisor review, auditing).

## Quick start

See [SETUP.txt](SETUP.txt) for prerequisites, `composer install`, migrations, seed accounts, and `npm run dev`.

This repository includes a committed **`.env`** so collaborators can run the app quickly after clone. It uses SQLite by default and a **shared development `APP_KEY`**—fine for local coursework or internal demos. **Do not use this file as-is in production.** Deployments must use their own secrets: copy `.env.example`, run `php artisan key:generate`, and configure real mail/DB credentials.

## License

MIT
