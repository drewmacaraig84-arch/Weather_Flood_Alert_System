@extends('layouts.app')

@section('title', 'Admin Hub - DWCC Weather & Flood Alert System')

@section('content')
<div class="container-fluid bg-light min-vh-100 p-0">

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom px-4 py-2 sticky-top">
        <div class="container-fluid p-0">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; background-color: #0b3954 !important;">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">DWCC Alert Hub</h6>
                    <small class="text-muted" style="font-size: 11px;">Calapan City Emergency & Suspension Administration</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('student.index') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 12px;">
                    <i class="bi bi-phone me-1"></i> Open Student Mobile View
                </a>
                <div class="vr my-1"></div>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px;">
                        AD
                    </div>
                    <div class="d-none d-md-block text-start lh-1">
                        <div class="fw-semibold text-dark" style="font-size: 13px;">School Administrator</div>
                        <small class="text-muted" style="font-size: 11px;">Office of the President</small>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-3">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 py-2 px-3 small mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-3">

            <!-- Left Navigation Sidebar (Desktop) -->
            <div class="col-lg-2 d-none d-lg-block">
                <div class="card card-custom p-3 shadow-sm h-100">
                    <div class="nav flex-column nav-pills gap-1" style="font-size: 13px;">
                        <a class="nav-link active rounded-3 py-2 fw-semibold" href="{{ route('admin.dashboard') }}" style="background-color: #0b3954;">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a class="nav-link text-secondary rounded-3 py-2" href="#map-hazard-section">
                            <i class="bi bi-map me-2"></i> Hazard Zones
                        </a>
                        <a class="nav-link text-secondary rounded-3 py-2" href="#suspension-section">
                            <i class="bi bi-megaphone me-2"></i> Class Status
                        </a>
                        <a class="nav-link text-secondary rounded-3 py-2" href="#reports-section">
                            <i class="bi bi-chat-left-dots me-2"></i> Flood Reports
                            @if($stats['pending_reports'] > 0)
                                <span class="badge bg-danger rounded-pill float-end">{{ $stats['pending_reports'] }}</span>
                            @endif
                        </a>
                        <a class="nav-link text-secondary rounded-3 py-2" href="#announcements-section">
                            <i class="bi bi-bell me-2"></i> Announcements
                        </a>
                    </div>

                    <div class="mt-auto pt-3 border-top text-muted small" style="font-size: 11px;">
                        <div>Calapan Weather: <strong>28°C</strong></div>
                        <div>Rain: <strong>Heavy Rain</strong></div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-10">

                <!-- 1. Class Suspension Manager Card -->
                <div class="card card-custom p-4 mb-3 shadow-sm border-start border-4" style="border-left-color: @if($status->status === 'SUSPENDED') #dc2626 @elseif($status->status === 'ADVISORY') #d97706 @else #16a34a @endif !important;" id="suspension-section">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold text-dark mb-0">Class Suspension & Advisory Broadcast</h5>
                            <span class="badge @if($status->status === 'SUSPENDED') bg-danger @elseif($status->status === 'ADVISORY') bg-warning text-dark @else bg-success @endif px-3 py-1">
                                CURRENT: {{ $status->status }}
                            </span>
                        </div>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Updated: {{ $status->effective_date ?? 'Today' }}</small>
                    </div>

                    <form action="{{ route('admin.status.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Status</label>
                                <select name="status" class="form-select form-select-sm rounded-3 fw-bold" required>
                                    <option value="NORMAL" @if($status->status === 'NORMAL') selected @endif>NORMAL (Classes Ongoing)</option>
                                    <option value="SUSPENDED" @if($status->status === 'SUSPENDED') selected @endif>SUSPENDED (Classes Cancelled)</option>
                                    <option value="ADVISORY" @if($status->status === 'ADVISORY') selected @endif>ADVISORY (Travel Caution)</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Level / Scope</label>
                                <select name="level_scope" class="form-select form-select-sm rounded-3" required>
                                    <option value="All Levels (JHS, SHS, College)" @if($status->level_scope == 'All Levels (JHS, SHS, College)') selected @endif>All Levels (JHS, SHS, College)</option>
                                    <option value="Junior High School Only" @if($status->level_scope == 'Junior High School Only') selected @endif>Junior High School Only</option>
                                    <option value="Senior High School Only" @if($status->level_scope == 'Senior High School Only') selected @endif>Senior High School Only</option>
                                    <option value="College Only" @if($status->level_scope == 'College Only') selected @endif>College Only</option>
                                    <option value="Basic Education (JHS & SHS)" @if($status->level_scope == 'Basic Education (JHS & SHS)') selected @endif>Basic Education (JHS & SHS)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Reason / Weather Trigger</label>
                                <input type="text" name="reason" class="form-control form-control-sm rounded-3" value="{{ $status->reason }}" placeholder="e.g. Flooded streets along Rizal Ave and heavy downpour">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold text-muted">Public Announcement Message to Students</label>
                                <textarea name="announcement_text" rows="2" class="form-control form-control-sm rounded-3" placeholder="Write the official directive message that will appear on students' mobile phones..." required>{{ $status->announcement_text }}</textarea>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-sm" style="background-color: #0b3954; border: none;">
                                    <i class="bi bi-broadcast me-1"></i> Update & Broadcast to Students
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 2. Summary Statistics Row -->
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card card-custom p-3 shadow-sm">
                            <span class="text-muted small fw-semibold">Active Warnings</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <h3 class="fw-bold mb-0 text-danger">{{ $stats['active_alerts'] }}</h3>
                                <div class="rounded-circle bg-danger-subtle text-danger p-2"><i class="bi bi-bell"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card card-custom p-3 shadow-sm">
                            <span class="text-muted small fw-semibold">Flooded Streets</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <h3 class="fw-bold mb-0 text-warning">{{ $stats['flooded_streets'] }}</h3>
                                <div class="rounded-circle bg-warning-subtle text-warning p-2"><i class="bi bi-water"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card card-custom p-3 shadow-sm">
                            <span class="text-muted small fw-semibold">Weather Status</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <h5 class="fw-bold mb-0 text-dark">Moderate Rain</h5>
                                <div class="rounded-circle bg-info-subtle text-info p-2"><i class="bi bi-cloud-rain"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card card-custom p-3 shadow-sm">
                            <span class="text-muted small fw-semibold">Pending Reports</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <h3 class="fw-bold mb-0 text-primary">{{ $stats['pending_reports'] }}</h3>
                                <div class="rounded-circle bg-primary-subtle text-primary p-2"><i class="bi bi-inbox"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Interactive Hazard Map & Flood Zones Row -->
                <div class="row g-3 mb-3" id="map-hazard-section">
                    <div class="col-lg-8">
                        <div class="card card-custom p-3 shadow-sm h-100">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-map-fill text-primary me-2"></i>Calapan City Flood Hazard Map</h6>
                                <button class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addZoneModal" style="font-size: 12px; background-color: #0b3954; border: none;">
                                    <i class="bi bi-plus-circle me-1"></i> Add Hazard Zone
                                </button>
                            </div>
                            <div id="admin-map" class="border"></div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-custom p-3 shadow-sm h-100">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-signpost-split text-primary me-2"></i>Monitored Road Corridors</h6>
                            <div class="overflow-auto" style="max-height: 330px;">
                                @forelse($floodZones as $zone)
                                    <div class="p-2 mb-2 border rounded-3 bg-white small">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong class="text-dark">{{ $zone->name }}</strong>
                                                <div class="text-muted" style="font-size: 11px;">Brgy. {{ $zone->barangay ?? 'Calapan' }} • ~{{ $zone->water_level_cm ?? 0 }} cm</div>
                                            </div>
                                            <span class="badge @if($zone->risk_level === 'HIGH') bg-danger @elseif($zone->risk_level === 'MODERATE') bg-warning text-dark @else bg-success @endif" style="font-size: 9px;">
                                                {{ $zone->risk_level }}
                                            </span>
                                        </div>
                                        <div class="text-muted mt-1" style="font-size: 11px;">{{ $zone->passable_status }}</div>
                                        <div class="text-end mt-1">
                                            <form action="{{ route('admin.zones.destroy', $zone->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this zone from the map?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-link text-danger p-0 small" style="font-size: 11px;">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted small">No monitored zones added yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Community Flood Reports Management Table -->
                <div class="card card-custom p-3 mb-3 shadow-sm" id="reports-section">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-chat-quote text-primary me-2"></i>Community & Student Flood Reports</h6>
                        <span class="text-muted small">Real-time reports submitted via mobile</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Reporter</th>
                                    <th>Location</th>
                                    <th>Severity</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td class="fw-semibold">FR-0{{ $report->id }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $report->reporter_name }}</div>
                                            <small class="text-muted">{{ $report->role }} • {{ $report->contact_number ?? 'No contact' }}</small>
                                        </td>
                                        <td>{{ $report->location_name }}</td>
                                        <td>
                                            <span class="badge @if($report->severity === 'HIGH') bg-danger @elseif($report->severity === 'MODERATE') bg-warning text-dark @else bg-success @endif">
                                                {{ $report->severity }}
                                            </span>
                                        </td>
                                        <td style="max-width: 250px;">{{ $report->description ?? 'No extra notes.' }}</td>
                                        <td>
                                            <span class="badge @if($report->status === 'Verified') bg-primary-subtle text-primary border border-primary-subtle @elseif($report->status === 'Resolved') bg-success-subtle text-success border border-success-subtle @else bg-warning-subtle text-warning border border-warning-subtle @endif">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if($report->status !== 'Verified')
                                                <form action="{{ route('admin.reports.status', $report->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Verified">
                                                    <button class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 11px;">Verify</button>
                                                </form>
                                            @endif
                                            @if($report->status !== 'Resolved')
                                                <form action="{{ route('admin.reports.status', $report->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Resolved">
                                                    <button class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 11px;">Resolve</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">No reports submitted.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Published Announcements Management -->
                <div class="card card-custom p-3 shadow-sm" id="announcements-section">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-megaphone text-primary me-2"></i>Official Announcements Log</h6>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal" style="font-size: 12px;">
                            <i class="bi bi-plus-circle me-1"></i> New Announcement
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle small mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>Content</th>
                                    <th>Published</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $item)
                                    <tr>
                                        <td>
                                            <span class="badge @if($item->severity === 'danger') bg-danger @elseif($item->severity === 'warning') bg-warning text-dark @elseif($item->severity === 'success') bg-success @else bg-primary @endif">
                                                {{ $item->category }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $item->title }}</td>
                                        <td style="max-width: 320px;">{{ $item->content }}</td>
                                        <td class="text-muted">{{ $item->created_at ? $item->created_at->format('M d, Y - h:i A') : 'N/A' }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.announcements.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 11px;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No announcements published.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Hazard Zone -->
<div class="modal fade" id="addZoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-geo-alt text-primary me-2"></i>Add Hazard Zone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.zones.store') }}" method="POST">
                @csrf
                <div class="modal-body small">
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1">Zone / Street Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g., J.P. Rizal Ave near Gate 1" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Barangay</label>
                            <input type="text" name="barangay" class="form-control form-control-sm" placeholder="e.g., Ilaya">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Risk Level</label>
                            <select name="risk_level" class="form-select form-select-sm" required>
                                <option value="LOW">LOW (Passable)</option>
                                <option value="MODERATE">MODERATE (Caution)</option>
                                <option value="HIGH" selected>HIGH (Severe / Impassable)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Estimated Water Level (cm)</label>
                            <input type="number" name="water_level_cm" class="form-control form-control-sm" placeholder="e.g., 40" value="30">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Passable Status</label>
                            <input type="text" name="passable_status" class="form-control form-control-sm" placeholder="e.g., Impassable to tricycles" value="Impassable to light vehicles" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Latitude</label>
                            <input type="text" name="latitude" class="form-control form-control-sm" value="13.4118" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Longitude</label>
                            <input type="text" name="longitude" class="form-control form-control-sm" value="121.1805" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1">Description / Notes</label>
                        <textarea name="description" rows="2" class="form-control form-control-sm" placeholder="Details regarding road closures or alternate routes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4" style="background-color: #0b3954; border: none;">Save Zone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Announcement -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-megaphone text-primary me-2"></i>Create New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-body small">
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1">Announcement Title</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g., Heavy Rainfall Advisory No. 3" required>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Category</label>
                            <select name="category" class="form-select form-select-sm" required>
                                <option value="WEATHER">WEATHER</option>
                                <option value="ROUTE">ROUTE ADVISORY</option>
                                <option value="SUSPENSION">SUSPENSION</option>
                                <option value="GENERAL">GENERAL</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold mb-1">Severity / Color</label>
                            <select name="severity" class="form-select form-select-sm" required>
                                <option value="danger">Danger (Red)</option>
                                <option value="warning" selected>Warning (Orange/Yellow)</option>
                                <option value="info">Info (Blue)</option>
                                <option value="success">Success (Green)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-1">Message Content</label>
                        <textarea name="content" rows="3" class="form-control form-control-sm" placeholder="Detailed message for students and faculty..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4" style="background-color: #0b3954; border: none;">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let adminMap;
    const campusCenter = [13.4116, 121.1803];

    document.addEventListener("DOMContentLoaded", function() {
        adminMap = L.map('admin-map', {
            zoomControl: true
        }).setView(campusCenter, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(adminMap);

        // DWCC Pin
        const dwccIcon = L.divIcon({
            html: '<div style="background-color: #0b3954; color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3);"><i class="bi bi-building"></i></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        L.marker(campusCenter, { icon: dwccIcon })
            .addTo(adminMap)
            .bindPopup('<strong>Divine Word College of Calapan</strong><br><small>DWCC Main Campus</small>')
            .openPopup();

        // Load all zones
        fetch("{{ route('api.mapData') }}")
            .then(res => res.json())
            .then(data => {
                data.zones.forEach(zone => {
                    let color = '#16a34a';
                    if (zone.risk_level === 'HIGH') color = '#dc2626';
                    else if (zone.risk_level === 'MODERATE') color = '#eab308';

                    L.circle([zone.latitude, zone.longitude], {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.35,
                        radius: zone.radius_meters || 150
                    }).addTo(adminMap).bindPopup(`
                        <strong>${zone.name}</strong><br>
                        Risk: <span style="color:${color}; font-weight:bold;">${zone.risk_level}</span><br>
                        Water: ~${zone.water_level_cm || 0} cm<br>
                        ${zone.passable_status}
                    `);
                });

                // Also plot community reports
                data.reports.forEach(rep => {
                    if (rep.latitude && rep.longitude) {
                        L.marker([rep.latitude, rep.longitude])
                            .addTo(adminMap)
                            .bindPopup(`<strong>Report: ${rep.location_name}</strong><br>${rep.reporter_name} (${rep.role})<br><small>${rep.description || ''}</small>`);
                    }
                });
            });
    });
</script>
@endsection
