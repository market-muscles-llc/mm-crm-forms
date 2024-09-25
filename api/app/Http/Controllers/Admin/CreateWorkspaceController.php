<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkspaceResource;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CreateWorkspaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'location_id' => ['required', 'string'],
            'location_name' => ['required', 'string'],
        ]);

        $workspace = Workspace::query()
            ->whereJsonContains('custom_domains', $request->input("location_id"))
            ->first();

        if ($workspace instanceof Workspace) {
            $this->createUser(
                $workspace,
                $request->input("location_id"),
                $request->input("location_name"),
            );

            return response()->json([
                'message' => 'Workspace retrieved.',
                'workspace_id' => $workspace->id,
                'workspace' => new WorkspaceResource($workspace),
            ]);
        }

        // Create workspace
        $workspace = Workspace::make([
            'name' => $request->location_name,
        ]);

        $workspace->custom_domains = [$request->input("location_id")];

        $workspace->save();

        $this->createUser(
            $workspace,
            $request->input("location_id"),
            $request->input("location_name"),
        );

        return response()->json([
            'message' => 'Workspace created.',
            'workspace_id' => $workspace->id,
            'workspace' => new WorkspaceResource($workspace),
        ]);
    }

    private function createUser(Workspace $workspace, string $locationId, string $locationName) {
        // Create User
        $user = User::query()->firstOrCreate(
            [
                'email' => "{$locationId}@forms.marketmuscles.com",
            ],
            [
                'name' => $locationName,
                'email_verified_at' => now(),
                'password' => Str::random(60),
            ]
        );

        // Is this new?
        if ($user->wasRecentlyCreated) {
            // Add relation with user
            $user->workspaces()->sync([
                $workspace->id => [
                    'role' => 'admin',
                ],
            ], false);
        }

        return $user;
    }
}
