<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkflowStage;
use App\Models\Idea;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkflowController extends Controller
{
    public function index()
    {
        $stages = WorkflowStage::orderBy('order')->get();
        return view('admin.workflow.index', compact('stages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:workflow_stages,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'order' => 'required|integer',
            'auto_advance_threshold' => 'nullable|integer|min:0',
        ]);

        WorkflowStage::create($validated);

        return redirect()->route('admin.workflow.index')->with('success', 'Tahap workflow berhasil dibuat! ✅');
    }

    public function update(Request $request, WorkflowStage $stage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('workflow_stages')->ignore($stage->id)
            ],
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'order' => 'required|integer',
            'auto_advance_threshold' => 'nullable|integer|min:0',
        ]);

        $stage->update($validated);

        return redirect()->route('admin.workflow.index')->with('success', 'Tahap workflow berhasil diperbarui! ✅');
    }

    public function destroy(WorkflowStage $stage)
    {
        $stage->delete();
        return redirect()->route('admin.workflow.index')->with('success', 'Tahap workflow berhasil dihapus.');
    }

    public function advanceIdea(Request $request, Idea $idea)
    {
        $currentStage = WorkflowStage::where('slug', $idea->current_stage)->first();
        $currentOrder = $currentStage ? $currentStage->order : 0;

        $nextStage = WorkflowStage::active()
            ->where('order', '>', $currentOrder)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextStage) {
            $idea->update(['current_stage' => $nextStage->slug]);
            return redirect()->back()->with('success', "Ide berhasil dipindahkan ke tahap: {$nextStage->name} ✅");
        }

        return redirect()->back()->with('error', 'Tidak ada tahap selanjutnya.');
    }
}
