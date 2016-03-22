<?php

namespace FileStorageBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class FileStorageTest
 * @package FileStorageBundle\Tests
 */
class FileStorageTest extends WebTestCase
{
    /** @var  \FileStorageBundle\Services\FileStorageService $storage */
    public static $storage;

    /** @var  string */
    public static $fileName;

    /**
     *
     */
    public static function setUpBeforeClass()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        self::$storage = $kernel->getContainer()->get('filestorage.service');
        self::$fileName =  microtime() . '.txt';
    }

    /**
     * Тестируем сохранение файла, положительным результатом считаем положительный ответ
     */
    public function testSave()
    {
        $result = self::$storage->save([
            'name' => self::$fileName,
            'content' => base64_encode('This is test'),
            'tags' => ['tests']
        ]);

        $this->assertGreaterThan(
            false,
            $result
        );
    }

    /**
     * Тестируем получение файла, положительным результатом считаем верно сформированный ответ
     */
    public function testGet()
    {
        $result = self::$storage->get([
            'name' => "revision.txt",
            'tags' => ['tests']
        ]);

        $result = array_filter($result, function($el) {
            return !empty($el);
        });

        $this->assertArrayHasKey(
            'content',
            $result
        );
    }

    /**
     * Тестируем удаление файла, положительным результатом считаем положительный ответ
     */
    public function testDel()
    {
        $result = self::$storage->delete([
            'name' => self::$fileName,
            'tags' => ['tests']
        ]);

        $this->assertGreaterThan(
            false,
            $result
        );
    }

    /**
     * Тестируем удаление файла, положительным результатом считаем положительный ответ
     */
    public function testRevision()
    {
        $result = self::$storage->save([
            'name' => "revision.txt",
            'content' => base64_encode('This is test ' . microtime()),
            'tags' => ['tests']
        ]);

        $this->assertGreaterThan(
            false,
            $result
        );
    }

    /**
     * Тестируем получение файла, положительным результатом считаем верно сформированный ответ
     */
    public function testGetRevision()
    {

        $file = self::$storage->get([
            'name' => "revision.txt",
            'tags' => ['tests']
        ]);

        if (!empty($file['revisions'])) {
            $result = self::$storage->get(
                [
                    'name' => $file['revisions'][0],
                    'tags' => ['tests'],
                    'revision' => true
                ]
            );

            $result = array_filter(
                $result,
                function ($el) {
                    return !empty($el);
                }
            );

            $this->assertArrayHasKey(
                'content',
                $result
            );
        }
    }

}
?>