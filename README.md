# bitrix-async-file-service

Веб-сервис для загрузки файлов и их асинхронной обработки на базе **1С-Битрикс D7** и **RabbitMQ**.

## Архитектура

```
Пользователь
    │
    ▼
[Страница загрузки /pages/upload/]
    │  CFile::SaveFile() — сохраняет файл через Bitrix
    │  FileQueueTable::add() — запись в fp_file_queue (STATUS=pending)
    │
    ▼
[RabbitMQ Producer] ──► очередь fp_file_processing
                                   │
                         ┌─────────┼─────────┐
                         ▼         ▼         ▼
                      Worker    Worker    Worker   (масштабирование)
                         │
                         ▼
                  FileWorker::process()
                  STATUS: pending → processing → done / error
                         │
                         ▼
              [Страница списка /pages/files/]
```

## Структура проекта

```
local/
├── php_interface/init.php              — подключение Composer autoload
├── modules/fileprocessor/
│   ├── include.php                     — регистрация классов в Bitrix autoloader
│   ├── install/
│   │   ├── index.php                   — установщик модуля
│   │   └── db/mysql/
│   │       ├── install.sql             — создание таблицы fp_file_queue
│   │       └── uninstall.sql           — удаление таблицы
│   └── lib/
│       ├── config.php                  — RabbitMQ конфигурация из ENV
│       ├── orm/FileQueueTable.php      — D7 ORM таблица очереди
│       ├── queue/Producer.php          — публикация задачи в RabbitMQ
│       ├── queue/Consumer.php          — подписка и диспетчеризация сообщений
│       └── worker/FileWorker.php       — логика обработки файла
└── components/fileprocessor/
    ├── file.upload/                    — компонент загрузки файла
    └── file.list/                      — компонент списка файлов

pages/
├── upload/index.php                    — страница загрузки
└── files/index.php                     — страница списка

worker.php                              — CLI точка входа воркера
docker-compose.yml                      — локальный RabbitMQ
```

## Установка

### 1. Зависимости

```bash
composer install
```

### 2. RabbitMQ (локально через Docker)

```bash
docker-compose up -d
```

Management UI: http://localhost:15672 (guest / guest)

### 3. Конфигурация

```bash
cp .env.example .env
# отредактируйте .env под ваши параметры RabbitMQ
```

Переменные окружения необходимо также прописать в конфигурации веб-сервера (Apache SetEnv / Nginx fastcgi_param), чтобы они были доступны при веб-запросах.

### 4. Установка модуля в Битрикс

В административной панели: **Marketplace → Установленные решения → fileprocessor → Установить**.

Либо программно (один раз):

```php
\Bitrix\Main\ModuleManager::registerModule('fileprocessor');
\Bitrix\Main\Loader::includeModule('fileprocessor');
(new fileprocessor())->DoInstall();
```

### 5. Добавление страниц

Скопируйте `pages/upload/` и `pages/files/` в корень сайта Битрикс.

### 6. Запуск воркера

```bash
# Один воркер
php worker.php

# Несколько воркеров (масштабирование — RabbitMQ распределит задачи round-robin)
for i in {1..4}; do php worker.php & done
```

## Статусы файлов

| Статус | Описание |
|---|---|
| `pending` | Загружен, ожидает в очереди |
| `processing` | Воркер обрабатывает |
| `done` | Обработка завершена |
| `error` | Ошибка при обработке |

## Технологии

- **CMS**: 1С-Битрикс D7 (ORM DataManager, CFile, CBitrixComponent)
- **Message broker**: RabbitMQ 3.x (`php-amqplib/php-amqplib ^3.6`)
- **Database**: MySQL / MariaDB
- **PHP**: 7.4+
