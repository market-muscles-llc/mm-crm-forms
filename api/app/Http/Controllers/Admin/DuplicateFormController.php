<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use Illuminate\Http\Request;

class DuplicateFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('moderator');
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'workspace_id' => ['required'],
            'form_id' => ['required'],
        ]);

        $form = Form::query()
            ->where('workspace_id', $request->get('workspace_id'))
            ->findOrFail($request->get('form_id'));

        // Create copy
        $formCopy = $form->replicate();
        $formCopy->title = 'Copy of ' . $formCopy->title;
        $formCopy->save();

        return response()->json([
            'message' => 'Form successfully duplicated',
            'form_id' => $formCopy->id,
        ]);
    }
}
