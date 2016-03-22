## Сервис FileStorage

Сервис представляет собой версионное файловое хранилище с транспортировкой файлов по RabbitMQ.

Принцип работы можно посмотреть в тестах `./src/FileStorageBundle/Tests/FileStorageTest.php` и тестовой отправки файла по шине
`./src/FileStorageBundle/Command/TestCommand.php`

#### Установка

```bash
composer install
bin/console rabbitmq:setup-fabric
bin/console rabbitmq:rpc-server filestorage
```

#### Сохранение файла
Необходимо отправить в rabbit topic с routing_key = `isz.filestorage.save` в exchange = `isz.filestorage`
Если такой файл уже был сохранен, то создаться новая ревизия, каторую можно будет так же получить
Ожидаеться следующий формат сообщения
```json
{
    name: "имя файла",
    content: "in base64",
    tags: ["save", "to", "here", "path"]
}
```

#### Получение файла
Необходимо отправить в rabbit topic с routing_key = `isz.filestorage.get` в exchange = `isz.filestorage`
Ожидаеться следующий формат сообщения
```json
{
    name: "имя файла",
    tags: ["get", "from", "here", "path"],
    revision: true
}
```
Ответ
```json
{
    name: "имя файла",
    path: "относительный путь к файлу",
    size: "размер файла",
    mime: "MIME TYPE файла",
    content: "in base64",
    revisions: [
    "my_revision_file_name"
    ]
}
```

#### Удаление файла
Необходимо отправить в rabbit topic с routing_key = `isz.filestorage.delete` в exchange = `isz.filestorage`
Ожидаеться следующий формат сообщения
```json
{
    name: "имя файла",
    tags: ["delete", "file", "from", "here"]
}