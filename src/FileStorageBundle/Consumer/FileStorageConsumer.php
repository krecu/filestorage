<?php
namespace FileStorageBundle\Consumer;

use FileStorageBundle\Services\FileStorageService;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class FileStorageConsumer
 * @package FreshDocBundle\Consumer
 */
class FileStorageConsumer implements ConsumerInterface
{
    /** @var FileStorageService - сервис стореджа */
    protected $storage;

    /**
     * FileStorageConsumer constructor.
     * @param FileStorageService $storage
     */
    public function __construct(FileStorageService $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param AMQPMessage $msg
     * @return array
     * @throws \Exception
     */
    public function execute(AMQPMessage $msg)
    {

        $result = null;

        $routingKey = $msg->get('routing_key');
        list(, $system , $action) = explode('.', $routingKey);

        // если запрос не в нашу подсистему что мало вероятно но возможно
        // то ничего не делаем
        if ($system != "filestorage") {
            $result = [
                'success' => false,
                'result' => "FileStorage: несуществующий метод $action",
            ];
        }

        $message = json_decode($msg->body, 1);

        // если данный метод доступен то выполняем его
        if (method_exists($this->storage, $action)) {
            try {
                $result = $this->storage->$action($message);
            } catch (\Exception $e) {
                $result = [
                    'success' => false,
                    'result' => "FileStorage: Ошибка в методе $action",
                ];
            }
        }

        return $result;
    }
}