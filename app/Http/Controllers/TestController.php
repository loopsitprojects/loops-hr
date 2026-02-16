<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::latest()->paginate(10);
        return view('recruitment.tests.index', compact('tests'));
    }

    public function create()
    {
        return view('recruitment.tests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->except('attachment');

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('test-attachments', 'public');
        }

        Test::create($data);

        return redirect()->route('tests.index')->with('success', 'Test created successfully.');
    }

    public function edit(Test $test)
    {
        return view('recruitment.tests.edit', compact('test'));
    }

    public function update(Request $request, Test $test)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:10240',
        ]);

        $data = $request->except('attachment');

        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($test->attachment_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($test->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('test-attachments', 'public');
        }

        $test->update($data);

        return redirect()->route('tests.index')->with('success', 'Test updated successfully.');
    }

    public function destroy(Test $test)
    {
        // Check if test is used in assessments
        // if ($test->assessments()->exists()) {
        //     return redirect()->back()->with('error', 'Cannot delete test with existing assessments.');
        // }

        $test->delete();

        return redirect()->route('tests.index')->with('success', 'Test deleted successfully.');
    }
}
