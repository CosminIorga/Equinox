<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 14:24
 */

namespace Equinox\Services\Repositories;


use Equinox\Models\Column;
use Equinox\Models\Storage;
use Equinox\Repositories\DataRepository;
use Illuminate\Database\Schema\Blueprint;

class DataService
{

    /**
     * The Data repository
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * DataService constructor.
     * @param DataRepository $dataRepository
     */
    public function __construct(
        DataRepository $dataRepository
    ) {
        $this->dataRepository = $dataRepository;
    }

    /**
     * Function used to generate a new storage
     * @param Storage $storage
     */
    public function generateStorage(Storage $storage)
    {
        $storageName = $storage->getStorageName();
        $storageGeneratorClosure = $this->createStorageGenerator($storage);

        $this->dataRepository->createStorageWithClosure($storageName, $storageGeneratorClosure);
    }

    /**
     * Function used to return another function that creates the storage
     * @param Storage $storage
     * @return \Closure
     */
    protected function createStorageGenerator(Storage $storage): \Closure
    {
        return function (Blueprint $table) use ($storage) {
            /* Set table engine */
            $table->engine = 'InnoDB';

            /* Add the rest of the columns */
            $storage->columns->each(function (Column $columnModel) use (&$table) {
                $column = $table->addColumn(
                    $columnModel->data_type,
                    $columnModel->name,
                    $columnModel->extra
                );

                if ($columnModel->allow_null) {
                    /* @noinspection PhpUndefinedMethodInspection */
                    $column->nullable();
                }

                if ($columnModel->index) {
                    $table->{$columnModel->index}($columnModel->name);
                }
            });

            /* Add timestamp columns such as created_at, updated_at and deleted_at*/
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp('created_at')
                ->nullable()
                ->default(\DB::raw('CURRENT_TIMESTAMP'));
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp('updated_at')
                ->nullable()
                ->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp('deleted_at')->nullable();
        };
    }

}