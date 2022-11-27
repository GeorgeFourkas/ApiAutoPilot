<?php

namespace ApiAutoPilot\ApiAutoPilot;

class Relation
{
    //e.g BelongsToMany, HasOne
    protected string $type;
    //the name of the function that defines the relationship
    protected string $functionName;
    //e.g. Post related model is App\Models\Tag
    protected string $relatedModelClass;


    public function getType(): string
    {
        return $this->type;
    }


    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }


    public function getFunctionName(): string
    {
        return $this->functionName;
    }


    public function setFunctionName(string $functionName): static
    {
        $this->functionName = $functionName;
        return $this;
    }


    public function getRelatedModelClass(): string
    {
        return $this->relatedModelClass;
    }


    public function setRelatedModelClass(string $relatedModelClass): static
    {
        $this->relatedModelClass = $relatedModelClass;
        return $this;
    }

}
