<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CreateOneTimePasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'workspace_id' => ['required', 'integer'],
            'external_user_id' => 'required',
        ]);

        $workspace = Workspace::query()->with('owners')->findOrFail(
            $request->input("workspace_id")
        );

        $code = Cache::remember(
            key: "{$request->input("external_user_id")}-one-time-password",
            ttl: (60 * 15), // 15 minutes,
            callback: function () use ($workspace) {
                return [
                    'workspace_id' => $workspace->id,
                    'code' => Str::random(40),
                ];
            },
        );

        return response()->json($code);
    }
}
