<?php

namespace ApiAutoPilot\ApiAutoPilot\Traits;

use Illuminate\Support\Facades\App;

trait HasDevOrTestingResponse
{
    public function endpointIsDisabledInConfig(): array
    {
        if ($this->isDevOrTestingStage()) {
            return [
                'dev_stage_tips' => [
                    'error_cause' => 'the endpoint is disabled in the config file.',
                    'action' => 'to enable the endpoint delete the model class from the exclude subarray',
                    'documentation_link' => 'https://apiautopilot.info/guide/endpoints/enabling-disabling-endpoints.html',
                    'why_are_you_showing_this?' => 'dont worry, this is showing only in local or testing app environment modes and app debug is true ¯\_(ツ)_/¯',
                ],
            ];
        }

        return [];
    }

    public function fileUrlIsRequiredResponse(): array
    {
        if ($this->isDevOrTestingStage()) {
            return [
                'dev_stage_tips' => [
                    'error_cause' => 'the request does not contain file_url index',
                    'action' => 'if the database column that stores the uploaded file\'s url is named differently, set it in the config file',
                    'documentation_link' => '',
                    'why_are_you_showing_this?' => 'dont worry, this is showing only in local or testing app environment modes and app debug is true',
                ],
            ];
        }

        return [];
    }

    protected function isDevOrTestingStage(): bool
    {
        return App::environment(['local', 'testing']) && config('app.debug');
    }
}
