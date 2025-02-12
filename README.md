# cbi-site

## Commands

- symfony server:start
- symfony console doctrine:database:create
- php bin/console make:entity
- php bin/console make:migration (1)
- php bin/console doctrine:migrations:migrate (2)
- symfony console doctrine:database:drop --force
- php bin/console cache:clear --env=dev
- php bin/console security:hash-password
- php bin/console debug:router

## Tables

| **User**  |
|-----------|
| firstname |
| lastname  |
| role      |
| email      |
| password      |
| isActive      |
| isVerified      |
| biography      |

| **Gallery**  |
|-----------|
| name |
| user_id  |
| photos   |
| date     |
| uuid      |
| slug      |
| password      |
| show_exif_data      |
| isActive      |
| isPrivate      |
| createdAt      |
| updatedAt      |

| **Photo**  |
|-----------|
| name |
| isActive   |
| legend     |

| **ExifData**  |
|-----------|
| photoId |
| name  |
| value   |

| **Contest**  |
|-----------|
| year |
| name  |
| link   |


| **Bip**  |
|-----------|
| file |
| date  |


| **Article**  |
|-----------|
| title |
| content |
| author   |
| createdAt   |
| updatedAt   |
| isActive   |
| slug   |




