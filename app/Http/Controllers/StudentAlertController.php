<?php

namespace App\Http\Controllers;

use App\Models\ClassStatus;
use App\Models\Announcement;
use App\Models\FloodZone;
use App\Models\FloodReport;
use Illuminate\Http\Request;

class StudentAlertController extends Controller
{
    /**
     * Display the Student Mobile / Public Alert View
     */
    public function index()
    {
        $status = ClassStatus::latest()->first() ?? (object)[
            'status' => 'NORMAL',
            'level_scope' => 'All Levels',
            'reason' => 'Weather is normal. No suspensions.',
            'announcement_text' => 'Regular classes are ongoing as scheduled.',
            'effective_date' => now()->format('F d, Y'),
            'updated_by' => 'DWCC Administration'
        ];

        $announcements = Announcement::latest('published_at')->take(5)->get();
        $floodZones = FloodZone::all();
        $recentReports = FloodReport::latest()->take(5)->get();

        // Calapan City Simulated Weather Data (can be connected to OpenWeather API if desired)
        $weather = [
            'location' => 'Calapan City, Oriental Mindoro',
            'temp' => 28,
            'condition' => 'Heavy Rain & Thunderstorms',
            'rainfall' => '45 mm/h',
            'humidity' => '88%',
            'wind' => 'NW 15 km/h',
            'risk_level' => 'High Rain Risk'
        ];

        return view('student.index', compact('status', 'announcements', 'floodZones', 'recentReports', 'weather'));
    }

    /**
     * Submit a Community Flood Report
     */
    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'reporter_name' => 'required|string|max:100',
            'role' => 'required|string',
            'contact_number' => 'nullable|string|max:20',
            'location_name' => 'required|string|max:150',
            'severity' => 'required|in:LOW,MODERATE,HIGH',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        FloodReport::create([
            'reporter_name' => $validated['reporter_name'],
            'role' => $validated['role'],
            'contact_number' => $validated['contact_number'] ?? null,
            'location_name' => $validated['location_name'],
            'severity' => $validated['severity'],
            'description' => $validated['description'] ?? null,
            'status' => 'Reported',
            'latitude' => $validated['latitude'] ?? 13.4118,
            'longitude' => $validated['longitude'] ?? 121.1805,
        ]);

        return redirect()->back()->with('success', 'Your flood report has been submitted to DWCC emergency coordinators!');
    }

    /**
     * JSON Endpoint for Leaflet Map Markers
     */
    public function mapData()
    {
        $zones = FloodZone::all();
        $reports = FloodReport::where('status', '!=', 'Resolved')->latest()->get();

        return response()->json([
            'zones' => $zones,
            'reports' => $reports,
            'campus' => [
                'name' => 'Divine Word College of Calapan (DWCC Main Campus)',
                'lat' => 13.4116,
                'lng' => 121.1803
            ]
        ]);
    }
}
