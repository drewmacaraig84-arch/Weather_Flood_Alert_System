<?php

namespace App\Http\Controllers;

use App\Models\ClassStatus;
use App\Models\Announcement;
use App\Models\FloodZone;
use App\Models\FloodReport;
use Illuminate\Http\Request;

class AdminAlertController extends Controller
{
    /**
     * Admin Dashboard View
     */
    public function dashboard()
    {
        $status = ClassStatus::latest()->first() ?? new ClassStatus([
            'status' => 'NORMAL',
            'level_scope' => 'All Levels',
            'reason' => '',
            'announcement_text' => 'Normal school operations.',
            'effective_date' => now()->format('F d, Y'),
            'updated_by' => 'DWCC Administration'
        ]);

        $announcements = Announcement::latest()->get();
        $floodZones = FloodZone::latest()->get();
        $reports = FloodReport::latest()->get();

        $stats = [
            'active_alerts' => $announcements->where('severity', 'danger')->count(),
            'flooded_streets' => $floodZones->where('risk_level', 'HIGH')->count(),
            'pending_reports' => $reports->where('status', 'Reported')->count(),
            'is_suspended' => ($status->status === 'SUSPENDED')
        ];

        return view('admin.dashboard', compact('status', 'announcements', 'floodZones', 'reports', 'stats'));
    }

    /**
     * Update Class Suspension Status
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:NORMAL,SUSPENDED,ADVISORY',
            'level_scope' => 'required|string',
            'reason' => 'nullable|string',
            'announcement_text' => 'required|string',
        ]);

        $status = ClassStatus::create([
            'status' => $validated['status'],
            'level_scope' => $validated['level_scope'],
            'reason' => $validated['reason'],
            'announcement_text' => $validated['announcement_text'],
            'effective_date' => now()->format('F d, Y - h:i A'),
            'updated_by' => 'DWCC Authorized Admin',
            'is_active' => true,
        ]);

        // Automatically create an announcement record if classes are suspended or resumed
        if ($validated['status'] === 'SUSPENDED') {
            Announcement::create([
                'title' => 'CLASS SUSPENSION: ' . $validated['level_scope'],
                'category' => 'SUSPENSION',
                'severity' => 'danger',
                'content' => $validated['announcement_text'],
                'author' => 'Office of the President',
                'published_at' => now(),
            ]);
        } elseif ($validated['status'] === 'NORMAL') {
            Announcement::create([
                'title' => 'Classes Resumed / Normal Operations',
                'category' => 'GENERAL',
                'severity' => 'success',
                'content' => $validated['announcement_text'],
                'author' => 'Office of the President',
                'published_at' => now(),
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Class status and announcements have been successfully updated and broadcasted!');
    }

    /**
     * Store new Announcement
     */
    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|in:SUSPENSION,WEATHER,ROUTE,GENERAL',
            'severity' => 'required|in:danger,warning,info,success',
            'content' => 'required|string',
        ]);

        Announcement::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'severity' => $validated['severity'],
            'content' => $validated['content'],
            'author' => 'DWCC Emergency Team',
            'published_at' => now(),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'New announcement published successfully!');
    }

    /**
     * Delete Announcement
     */
    public function destroyAnnouncement($id)
    {
        Announcement::findOrFail($id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Announcement removed.');
    }

    /**
     * Update Report Status (Verify / Resolve)
     */
    public function updateReportStatus(Request $request, $id)
    {
        $report = FloodReport::findOrFail($id);
        $status = $request->input('status', 'Verified');

        $report->update(['status' => $status]);

        return redirect()->route('admin.dashboard')->with('success', "Report FR-0{$report->id} marked as {$status}.");
    }

    /**
     * Store a new Flood Hazard Zone
     */
    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'barangay' => 'nullable|string',
            'risk_level' => 'required|in:LOW,MODERATE,HIGH',
            'water_level_cm' => 'nullable|integer',
            'passable_status' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        FloodZone::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'New flood hazard zone added to the map!');
    }

    /**
     * Delete Flood Zone
     */
    public function destroyZone($id)
    {
        FloodZone::findOrFail($id)->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Hazard zone removed from map.');
    }
}
