<?php

namespace FileStorageBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('file_storage:test')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \OldSound\RabbitMqBundle\RabbitMq\RpcClient $client */
        $client = $this->getContainer()->get('old_sound_rabbit_mq.test_rpc');
        $routeKey = "isz.filestorage.save";
        $filename = "/home/eugen/Work/project/isz-vagrant/code/isz-filestorage/5meg.test1";
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        for ($i = 0; $i <= 10; $i++) {
            $messageId = 'isz-'.$routeKey.'-'.microtime(true);
            $start = time();
            $content = base64_encode($contents);

            $client->addRequest(
                json_encode(
                    [
                        "name" => $messageId."100meg.test",
                        "content" => $content,
                        "tags" => ["a", "b", "c"],
                    ]
                ),
                "isz.filestorage",
                $messageId,
                $routeKey
            );

            $replies = $client->getReplies();

            $end = time();

            dump("Потратили ".($end - $start)." sec");
            dump($replies[$messageId]);
        }
    }
}
