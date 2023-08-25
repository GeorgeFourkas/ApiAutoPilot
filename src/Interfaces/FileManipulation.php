<?php

namespace ApiAutoPilot\ApiAutoPilot\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileManipulation
{
    /**
     * @return array
     */
    public function setAdditionalFileData(UploadedFile $file);
}
