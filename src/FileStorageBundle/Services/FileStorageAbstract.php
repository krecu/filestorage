<?php
namespace FileStorageBundle\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Class FileStorageAbstract
 * @package FileStorageBundle\Services
 */
abstract class FileStorageAbstract implements FileStorageInterface
{
    /** @var string $path */
    public $pathOrigin;

    /** @var string $path */
    public $pathRevision;

    /**
     * FileStorageAbstract constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->pathOrigin = $path . "/origin";
        $this->pathRevision = $path . "/revision";
    }

    /**
     * @param array $message
     * @return bool
     * @throws \Exception
     */
    public function save(array $message)
    {
        $fileName = $message['name'];
        $fileContent  = base64_decode($message['content']);
        $fileTags = $message['tags'];

        $path = $this->pathOrigin . "/" . implode('/', $fileTags);
        $filePathOrigin = $path . "/" . $fileName;

        $fs = new Filesystem();

        try {
            // если каталог не существует то создаем ее
            if (!$fs->exists($path)) {
                $fs->mkdir($path);
            }

            // если файл существует
            // 1) то создаем ревизию на основании текущего
            // 2) перемещаем его в ревизии
            // 3) сохраняем новый
            if ($fs->exists(array($filePathOrigin))) {

                $pathRevision = $this->pathRevision . "/" . implode('/', $fileTags) . "/" . $fileName;
                if (!$fs->exists($pathRevision)) {
                    $fs->mkdir($pathRevision);
                }

                $filePathRevision = $pathRevision . "/" . $fileName . "+++" . microtime(true);

                $fs->copy($filePathOrigin, $filePathRevision);

                $fs->dumpFile($filePathOrigin, $fileContent);

                return true;
            } else {
                $fs->dumpFile($filePathOrigin, $fileContent);
            }

            return true;
        } catch (IOExceptionInterface $e) {
            throw new \Exception("Не удалось сохранить файл: ". $path . "/" . $fileName);
        }
    }

    /**
     * @param array $message
     * @return array
     * @throws \Exception
     */
    public function get(array $message)
    {
        $fileName = $message['name'];
        $fileTags = $message['tags'];
        $isRevision = !empty($message['revision']) ? $message['revision'] : false;

        if ($isRevision) {
            list($filePath) = explode("+++", $fileName);
            $filePath = $this->pathRevision."/".implode('/', $fileTags) . "/" . $filePath . "/" . $fileName;
        } else {
            $filePath = $this->pathOrigin."/".implode('/', $fileTags)."/".$fileName;
        }

        $fs = new Filesystem();
        try {
            if ($fs->exists(array($filePath))) {

                $handle = fopen($filePath, "r");
                $fileSize = filesize($filePath);
                $content = fread($handle, $fileSize);

                $revisions = [];
                $pathRevision = $this->pathRevision . "/" . implode('/', $fileTags) . "/" . $fileName;
                if ($fs->exists($pathRevision)) {
                    $finder = new Finder();
                    $iterator = $finder->files()->in($pathRevision);

                    foreach ($iterator as $file)
                    {
                        $revisions[] = str_replace($pathRevision . "/", "", $file->getRealpath());
                    }
                }

                return [
                    'name' => $fileName,
                    'path' => $filePath,
                    'size' => $fileSize,
                    'mime' => mime_content_type($filePath),
                    'content' => base64_encode($content),
                    'revisions' => $revisions
                ];
            } else {
                throw new \Exception("Файл не найден: ". $filePath);
            }

        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

    /**
     * @param array $message
     * @return bool
     * @throws \Exception
     */
    public function delete(array $message)
    {
        $fileName = $message['name'];
        $fileTags = $message['tags'];

        $filePath = $this->pathOrigin . "/" . implode('/', $fileTags) . "/" . $fileName;

        $fs = new Filesystem();
        try {
            if ($fs->exists(array($filePath))) {
                $fs->remove($filePath);
                return true;
            } else {
                throw new \Exception("Файл не найден: ". $filePath);
            }

        } catch (IOExceptionInterface $e) {
            echo "Ошибка при удалении файла ".$e->getPath();
        }
    }

    public function revision(array $message){
    }
}