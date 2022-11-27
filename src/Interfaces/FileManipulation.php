<?php

namespace ApiAutoPilot\ApiAutoPilot\Interfaces;

use Illuminate\Http\UploadedFile;

interface FileManipulation
{

    /**
     * @param UploadedFile $file
     * @return array
     */
    public function setAdditionalFileData(UploadedFile $file);

}
