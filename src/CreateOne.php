<?php

namespace ApiAutoPilot\ApiAutoPilot;

use ApiAutoPilot\ApiAutoPilot\Exceptions\FileUrlDatabaseColumnIndexNotPresent;
use ApiAutoPilot\ApiAutoPilot\Interfaces\CreateModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CreateOne implements CreateModel
{
    /*
     * This class is used to create just a single Model of a type.
     * It is used to create models without relating them. E.G just a fresh User data
     */
    protected Model $model;

    protected Request $request;

    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @throws FileUrlDatabaseColumnIndexNotPresent
     */
    public function create()
    {
        //Run Model Policy First
        $urlColumn = FileUrlResolver::findUrlTableColumn($this->model);

        $handler = new FileHandler($this->model, $urlColumn);
        $values = $handler->replaceUploadedFileToUrl($this->request->only($this->model->getFillable()));
        if ($handler->foundFile()) {
            if (!$this->request->has($urlColumn)) {
                throw new FileUrlDatabaseColumnIndexNotPresent($urlColumn);
            }
            $values = array_merge($values, $values[$urlColumn]);
        }
        return ($this->model)::create(
            $values
        )->toArray();
    }
}
