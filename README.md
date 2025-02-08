# cbi-site

## Commands

- symfony server:start
- symfony console doctrine:database:create
- php bin/console make:entity
- php bin/console make:migration (1)
- php bin/console doctrine:migrations:migrate (2)
- php bin/console cache:clear --env=dev

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




