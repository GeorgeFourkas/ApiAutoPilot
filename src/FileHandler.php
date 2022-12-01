<?php

namespace ApiAutoPilot\ApiAutoPilot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileHandler
{
    protected array $urls = [];

    protected string $urlColumn;

    protected Request $request;

    protected Model $model;

    protected bool $hasFile = false;

    /**
     * @param UploadedFile|array $files
     * @param Request $request
     * @param Model $model
     * @return array
     */
    public function __construct(Model $model, string $urlColumn)
    {
        $this->model = $model;
        $this->urlColumn = $urlColumn;
    }

    public function replaceUploadedFileToUrl(array $requestData): array
    {
        if (empty($this->allFiles($requestData))) {
            return $requestData;
        }
        $values = [];
        foreach ($requestData as $key => $obj) {
            if (is_array($obj)) {
                $values[] = $this->replaceUploadedFileToUrl($obj);
            } else {
                $values[$key] = $this->addToArray($key, $obj);
            }
        }

        return $values;
    }

    public function addToArray($key, $item): array|string
    {
        if ($this->isFile($item)) {
            $this->hasFile = true;
            $values = array_merge([$key => $this->saveFileToStorage($item)], $this->execFileModelHook($item));
        } else {
            $values = $item;
        }

        return $values;
    }

    protected function saveFileToStorage($file): bool|string
    {
        $destination = $this->getModelSaveDirectory($this->model);
        $url = Storage::putFile($destination, $file);

        return str_replace('public', '/storage', $url);
    }

    protected function execFileModelHook($file): array
    {
        if (method_exists($this->model, 'setAdditionalFileData')) {
            return $this->model->setAdditionalFileData($file);
        }
        return [];
    }

    protected function allFiles(array $requestData): array
    {
        $files = [];
        foreach ($requestData as $key => $index) {
            if ($this->isFile($index)) {
                $files[$key] = $index;
            }
            if (is_array($index)) {
                $files[$key] = $this->allFiles($index);
            }
        }

        return $files;
    }

    protected function isFile($index): bool
    {
        return $index instanceof UploadedFile;
    }

    protected function getModelSaveDirectory(Model $model): string
    {
        if (property_exists($model, 'fileDestination')) {
            return $model->fileDestination;
        }

        return '/public/aap';
    }

    public function foundFile(): bool
    {
        return $this->hasFile;
    }
}
