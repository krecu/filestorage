<?php
namespace FileStorageBundle\Services;

/**
 * Class FileStorageService
 * @package FileStorageBundle\Services
 */
class FileStorageService extends FileStorageAbstract
{
    public function save(array $message)
    {
        return parent::save($message);
    }

    public function delete(array $message)
    {
        return parent::delete($message);
    }

    public function get(array $message)
    {
        return parent::get($message);
    }

    public function tags(array $message)
    {
        // TODO: Implement delete() method.
    }
}