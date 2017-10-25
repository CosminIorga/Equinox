<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 09/10/17
 * Time: 11:15
 */

namespace Equinox\Services\Repositories;


use Illuminate\Support\Collection;
use SplFileObject;

class FileService
{
    const FILE_DIRECTORY = 'app/volatile';
    const OPEN_MODE_FOR_WRITING = 'w';

    const DEFAULT_FILE_EXTENSION = '.csv';

    /**
     * A collection used to store already opened files this session
     * @var Collection
     */
    protected $fileMapping;

    /**
     * FileService constructor.
     */
    public function __construct()
    {
        $this->fileMapping = collect([]);
    }

    /**
     * Function used to write a record to csv file
     * @param string $fileName
     * @param array $record
     * @return bool
     */
    public function writeRecordToCSVFile(string $fileName, array $record): bool
    {
        $fileName = $this->computeFileNameWithExtension($fileName);

        $fileHandler = $this->getFileHandler($fileName);

        $fileHandler->fputcsv($record);

        return true;
    }

    /**
     * Short function used to check if file name already has extension and adds the .csv default if not
     * @param string $fileName
     * @return string
     */
    protected function computeFileNameWithExtension(string $fileName): string
    {
        $fileParts = pathinfo($fileName);

        /* Do not alter file name if it already has extension */
        if (array_key_exists('extension', $fileParts)) {
            return $fileName;
        }

        /* Otherwise add the .dot extension */
        return $fileName . self::DEFAULT_FILE_EXTENSION;
    }

    /**
     * Function used to retrieve the file handler
     * @param string $fileName
     * @return SplFileObject
     */
    protected function getFileHandler(string $fileName): SplFileObject
    {
        /* Return file resource if file already opened */
        $cachedResource = $this->getCachedFileResource($fileName);

        if (!is_null($cachedResource)) {
            return $cachedResource;
        }

        /* Otherwise open file for writing */
        $fileHandler = $this->openFile($fileName);

        $this->mapFile($fileName, $fileHandler);

        return $fileHandler;
    }

    /**
     * Return the cached file resource or null if it does not exists
     * @param string $fileName
     * @return SplFileObject|null
     */
    protected function getCachedFileResource(string $fileName)
    {
        return $this->fileMapping->get($fileName);
    }

    /**
     * Short function used to open a file for writing
     * @param string $fileName
     * @return SplFileObject
     */
    protected function openFile(string $fileName): SplFileObject
    {
        $filePath = $this->getFullFilePath($fileName);

        return new SplFileObject($filePath, self::OPEN_MODE_FOR_WRITING);
    }

    /**
     * Function used to cache the file handler
     * @param string $fileName
     * @param SplFileObject $fileHandler
     * @return FileService
     */
    protected function mapFile(string $fileName, SplFileObject $fileHandler): self
    {
        $this->fileMapping->put($fileName, $fileHandler);

        return $this;
    }

    /**
     * Short function used to retrieve the full file path given file name
     * @param string $fileName
     * @return string
     */
    public function getFullFilePath(string $fileName): string
    {
        return storage_path(self::FILE_DIRECTORY . DIRECTORY_SEPARATOR . $fileName);
    }
}