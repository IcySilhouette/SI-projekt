# Projekt: Gazeta Internetowa

## Opis

Projekt zaliczeniowy wykonany we frameworku Symfony.

## Instalacja i uruchomienie

1. Sklonuj repozytorium:
```bash
git clone https://github.com/IcySilhouette/SI-projekt.git
   ```
2. Wejdź do pobranego folderu w konsoli:
cd SI-projekt

3. Uruchom środowisko Docker w tle:
docker-compose up -d

4. Wejdź do powłoki kontenera PHP:
(Jeśli Twój kontener ma inną nazwę, np. php-fpm, app lub www, podmień poniższe słowo php)
docker-compose exec php bash

5. Przejdź do folderu z aplikacją Symfony:
cd app

```bash
cp .env.dev .env
echo "DEFAULT_URI=http://localhost:8000" >> .env
echo 'DATABASE_URL="mysql://symfony:symfony@mysql:3306/symfony?serverVersion=8.3&charset=utf8mb4"' >> .env
```

7. Będąc wewnątrz kontenera, zainstaluj zależności:
composer install

8. Uruchom migracje bazy danych:
php bin/console doctrine:migrations:migrate --no-interaction

9. Załaduj dane testowe (fixtures):
php bin/console doctrine:fixtures:load --no-interaction

10. Gotowa aplikacja jest dostępna w przeglądarce pod adresem: http://localhost:8000/article/

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

   
