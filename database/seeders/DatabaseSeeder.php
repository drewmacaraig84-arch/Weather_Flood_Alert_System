<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\ClassStatus::create([
            'status' => 'SUSPENDED',
            'level_scope' => 'All Levels (JHS, SHS, College)',
            'reason' => 'Severe Weather & Rapidly Rising Water Levels along Calapan Transit Routes',
            'announcement_text' => 'Due to persistent heavy rainfall and flooding along main access roads (Rizal Ave and Lumangbayan), all afternoon and evening classes are officially suspended today. Students are advised to take safe routes and stay indoors.',
            'is_active' => true,
            'effective_date' => now()->format('F d, Y - h:i A'),
            'updated_by' => 'DWCC Office of the President / DRRMC',
        ]);

        \App\Models\Announcement::create([
            'title' => 'Classes Suspended for All Levels Today',
            'category' => 'SUSPENSION',
            'severity' => 'danger',
            'content' => 'Official directive: All classes (Junior High, Senior High, and College) are suspended. Campus gates will remain open for stranded students with emergency shelter assistance.',
            'author' => 'DWCC Administration',
            'published_at' => now()->subMinutes(30),
        ]);

        \App\Models\Announcement::create([
            'title' => 'PAGASA Weather Warning: Red Rainfall Alert',
            'category' => 'WEATHER',
            'severity' => 'warning',
            'content' => 'Heavy to intense rains with gusty winds expected across Calapan City and surrounding Oriental Mindoro municipalities over the next 3 to 6 hours.',
            'author' => 'PAGASA / CDRRMO Calapan',
            'published_at' => now()->subHours(1),
        ]);

        \App\Models\Announcement::create([
            'title' => 'Safe Transit Advisory: Governor Infantado St Passable',
            'category' => 'ROUTE',
            'severity' => 'success',
            'content' => 'Governor Infantado Street remains clear of water. Students heading towards San Vicente are advised to use this alternate route.',
            'author' => 'DWCC Campus Security',
            'published_at' => now()->subHours(2),
        ]);

        // Flood Zones in Calapan City near DWCC
        \App\Models\FloodZone::create([
            'name' => 'DWCC Main Gate & Rizal Avenue',
            'barangay' => 'Ilaya',
            'description' => 'Heavy street water pooling in front of university gates. Gutter to knee deep.',
            'risk_level' => 'HIGH',
            'water_level_cm' => 45,
            'passable_status' => 'Impassable to light vehicles (motorcycles/tricycles)',
            'latitude' => 13.4118000,
            'longitude' => 121.1805000,
            'radius_meters' => 180,
        ]);

        \App\Models\FloodZone::create([
            'name' => 'Mabini Street & Balite Intersection',
            'barangay' => 'Balite',
            'description' => 'Moderate water accumulation near market access.',
            'risk_level' => 'MODERATE',
            'water_level_cm' => 20,
            'passable_status' => 'Passable with caution for all vehicles',
            'latitude' => 13.4132000,
            'longitude' => 121.1820000,
            'radius_meters' => 140,
        ]);

        \App\Models\FloodZone::create([
            'name' => 'Lumangbayan River & Bridge Approach',
            'barangay' => 'Lumangbayan',
            'description' => 'River overflow nearing road crest. Fast current.',
            'risk_level' => 'HIGH',
            'water_level_cm' => 55,
            'passable_status' => 'Not Passable - Road Closed',
            'latitude' => 13.4075000,
            'longitude' => 121.1765000,
            'radius_meters' => 220,
        ]);

        \App\Models\FloodZone::create([
            'name' => 'Governor Infantado Street Corridor',
            'barangay' => 'Ibaba',
            'description' => 'Light gutter flow, clear drainage flow.',
            'risk_level' => 'LOW',
            'water_level_cm' => 5,
            'passable_status' => 'Passable to all vehicles',
            'latitude' => 13.4140000,
            'longitude' => 121.1780000,
            'radius_meters' => 150,
        ]);

        // Flood Reports
        \App\Models\FloodReport::create([
            'reporter_name' => 'Maria Santos',
            'role' => 'Student (College of CS)',
            'contact_number' => '09171234567',
            'location_name' => 'Rizal Ave near DWCC Gate 2 waiting shed',
            'severity' => 'HIGH',
            'description' => 'Water reached knee depth quickly after 30 mins of downpour. Tricycles refusing to cross.',
            'status' => 'Verified',
            'latitude' => 13.4119000,
            'longitude' => 121.1804000,
        ]);

        \App\Models\FloodReport::create([
            'reporter_name' => 'Engr. Jose Dela Cruz',
            'role' => 'Faculty',
            'contact_number' => '09189876543',
            'location_name' => 'Libertad St corner Mabini',
            'severity' => 'MODERATE',
            'description' => 'Ankle deep water flowing rapidly from clogged drainage canal.',
            'status' => 'Reported',
            'latitude' => 13.4128000,
            'longitude' => 121.1812000,
        ]);
    }
}
