<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'workspace_id' => ['required'],
            'form_name' => ['required', 'string'],
        ]);

        $workspace = Workspace::query()
            ->with('owners')
            ->findOrFail(
                $request->input("workspace_id")
            );

        $form = $workspace->forms()->create(
            [
                'title' => $request->input("form_name"),
                'creator_id' => $workspace->owners->first()->id,
                'no_branding' => true,
                'properties' => [
                    [
                        "name" => "Name",
                        "type" => "text",
                        "hidden" => false,
                        "required" => true,
                        "id" => (string) Str::uuid()
                    ],
                    [
                        "name" => "Email",
                        "type" => "email",
                        "hidden" => false,
                        "id" => (string) Str::uuid()
                    ],
                    [
                        "name" => "Message",
                        "type" => "text",
                        "hidden" => false,
                        "multi_lines" => true,
                        "id" => (string) Str::uuid()
                    ]
                ],
            ]
        );

        return response()->json([
            'message' => 'Form created.',
            'workspace_id' => $workspace->id,
            'form_id' => $form->id,
        ]);
    }
}
