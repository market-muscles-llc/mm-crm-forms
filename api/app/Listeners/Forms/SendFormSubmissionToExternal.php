<?php

namespace App\Listeners\Forms;

use App\Events\Forms\FormSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class SendFormSubmissionToExternal implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Sends notification to pre-defined emails on form submissions
     *
     * @param object $event
     * @return void
     */
    public function handle(FormSubmitted $event)
    {
        if (empty(config('services.external.submission_webhook'))) {
            return;
        }

        /** @var Response */
        $response = Http::asJson()->post(
            config('services.external.submission_webhook'),
            [
                "workspace_id" => $event->form->workspace_id,
                "form_id" => $event->form->id,
                "data" => $event->data,
            ]
        );

        if ($response->failed()) {
            Log::error("Failed to hand off submission");
        }
    }
}
