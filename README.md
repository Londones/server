## Créer migration et executer une migration
```
php bin/console make:migration
```

```
php bin/console doctrine:migrations:migrate
```

## Jouer les fixtures

```
php bin/console doctrine:fixtures:load
```

## Bug fixes
- Ajouter cette extension dans php.ini
```
extension=php_sodium.dll
```
- Erreur à la génération des clès SSH
```
choco install openssl
```