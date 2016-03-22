<?php
namespace FileStorageBundle\Services;

/**
 * Class FileStorageInterface
 * @package FileStorageBundle\Services
 */
interface FileStorageInterface
{

    /**
     * Получение обьекта файла по имени и тегам
     *
     * @param array $message = [
     *  'name' => "",
     *  'revision' => boolean,
     *  'tags' => [],
     * ]
     *
     * @return $file = [
     *  'name' => "",
     *  'path' => "",
     *  'size' => "",
     *  'mime' => "",
     *  'content' => "",
     *  'revisions' => []
     * ]
     */
    public function get(array $message);

    /**
     * Сохранение/создание файла
     *
     * @param array $message = [
     *  'name' => "",
     *  'content' => "",
     *  'tags' => []
     * ]
     *
     * @return \SplFileObject
     */
    public function save(array $message);

    /**
     * Удаление файла
     *
     * @param array $message = [
     *  'name' => "",
     *  'tags' => []
     * ]
     *
     * @return boolean
     */
    public function delete(array $message);

    /**
     * Получаем все файлы в тегах
     *
     * @param array $message = [
     *  'tags' => []
     * ]
     *
     * @return \SplFileObject[]
     */
    public function tags(array $message);
}