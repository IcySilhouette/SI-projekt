# Projekt: Gazeta Internetowa

## Opis
Projekt zaliczeniowy wykonany w frameworku Symfony.

## Instalacja

1. Sklonuj repozytorium:
   ```bash
   git clone [https://github.com/IcySilhouette/SI-projekt.git](https://github.com/IcySilhouette/SI-projekt.git)
   ```
2. Instalacja zależności:
composer install

3. Konfiguracja bazy danych:
Skopiuj plik .env do .env.local i ustaw w nim dane dostępowe do swojej bazy danych.

4. Uruchomienie migracji:
php bin/console doctrine:migrations:migrate --no-interaction

5. Załadowanie danych testowych (fixtures):
php bin/console doctrine:fixtures:load --no-interaction

Dane do logowania:
Admin:
Login: admin@admin.com
Hasło: password1

User1
Login: user1@user.com
hasło: password1

User2
Login: user2@user.com
hasło: password1

User3
Login: user3@user.com
hasło: password1

   
