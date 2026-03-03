\# POS System – Project Rules (Day 1)



\## Non-negotiables

\- Stack: PHP 8.2 + MySQL/MariaDB + XAMPP local + GoDaddy cPanel deploy target

\- No heavy frameworks

\- Free-for-commercial-use libraries only

\- Use migrations for ALL schema changes

\- Auditability is mandatory (BIR-safe structure)



\## Folder ownership

\- DEV A: /config, /storage, /docs

\- DEV B: /src/Database

\- DEV C: /migrations, /src/Migrations

\- DEV D: /public, /src/Core, /src/Auth, /src/Audit, /views



\## Coding style

\- Small files, clear names

\- No “magic” auto-discovery

\- No random rewrites of other dev’s modules

\- Prefer explicit require() for now (composer autoload later)



\## Environment

\- .env is local-only (never committed)

\- .env.example must be updated when new env vars are added

