<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * List all activities (admin management view).
     */
    public function index()
    {
        $this->authorizeAdmin();

        $activities = Activity::with('creator')
            ->withCount('activityLogs')
            ->withTrashed()
            ->ordered()
            ->paginate(25);

        return view('activities.index', compact('activities'));
    }

    /**
     * Show the form to create a new activity.
     */
    public function create()
    {
        $this->authorizeAdmin();

        $categories = Activity::$categories;

        return view('activities.create', compact('categories'));
    }

    /**
     * Store a new activity.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'category'     => ['required', 'in:' . implode(',', array_keys(Activity::$categories))],
            'is_recurring' => ['boolean'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
        ]);

        Activity::create([
            ...$validated,
            'is_recurring' => $request->boolean('is_recurring'),
            'is_active'    => true,
            'created_by'   => Auth::id(),
        ]);

        return redirect()->route('activities.index')
            ->with('success', 'Activity "' . $validated['title'] . '" created successfully.');
    }

    /**
     * Show the edit form for an activity.
     */
    public function edit(Activity $activity)
    {
        $this->authorizeAdmin();

        $categories = Activity::$categories;

        return view('activities.edit', compact('activity', 'categories'));
    }

    /**
     * Update an existing activity.
     */
    public function update(Request $request, Activity $activity)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'category'     => ['required', 'in:' . implode(',', array_keys(Activity::$categories))],
            'is_recurring' => ['boolean'],
            'is_active'    => ['boolean'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
        ]);

        $activity->update([
            ...$validated,
            'is_recurring' => $request->boolean('is_recurring'),
            'is_active'    => $request->boolean('is_active'),
        ]);

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Soft-delete an activity.
     */
    public function destroy(Activity $activity)
    {
        $this->authorizeAdmin();

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity archived successfully.');
    }

    /**
     * Restore a soft-deleted activity.
     */
    public function restore(int $id)
    {
        $this->authorizeAdmin();

        $activity = Activity::withTrashed()->findOrFail($id);
        $activity->restore();

        return redirect()->route('activities.index')
            ->with('success', 'Activity restored successfully.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeAdmin(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403, 'Only administrators can manage activities.');
    }
}
