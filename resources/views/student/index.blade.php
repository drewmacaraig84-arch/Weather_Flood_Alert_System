@extends('layouts.app')

@section('title', 'DWCC Flood & Weather Alert System')

@section('content')
<div class="mobile-shell">

    <!-- Top Header -->
    <div class="px-3 pt-3 pb-2 border-bottom bg-white sticky-top shadow-sm d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px; background-color: #0b3954 !important;">
                <i class="bi bi-shield-fill-check fs-5"></i>
            </div>
            <div>
                <div class="text-uppercase fw-bold text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Divine Word College of Calapan</div>
                <div class="fw-bold text-dark lh-1" style="font-size: 15px;">Weather & Flood Alert</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-light border rounded-circle text-muted" onclick="location.reload()" title="Refresh alerts">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px;">
                <i class="bi bi-person-lock me-1"></i>Admin
            </a>
        </div>
    </div>

    <div class="p-3">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 py-2 px-3 small mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 1. Urgent Class Status Card -->
        <div class="p-3 mb-3 text-white shadow-sm @if($status->status === 'SUSPENDED') status-badge-suspended @elseif($status->status === 'ADVISORY') status-badge-advisory @else status-badge-normal @endif">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-white text-dark fw-bold text-uppercase px-2 py-1" style="font-size: 10px;">
                    <i class="bi bi-megaphone-fill me-1 text-danger"></i> Official Directive
                </span>
                <span class="small opacity-75" style="font-size: 11px;">
                    <i class="bi bi-clock me-1"></i>{{ $status->effective_date ?? now()->format('M d, Y') }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                    @if($status->status === 'SUSPENDED')
                        <i class="bi bi-bell-fill text-danger fs-3"></i>
                    @elseif($status->status === 'ADVISORY')
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    @else
                        <i class="bi bi-check-circle-fill text-success fs-3"></i>
                    @endif
                </div>
                <div>
                    <div class="text-uppercase fw-semibold" style="font-size: 11px; opacity: 0.9;">Class Status</div>
                    <h5 class="fw-bold mb-0">
                        @if($status->status === 'SUSPENDED')
                            CLASSES SUSPENDED
                        @elseif($status->status === 'ADVISORY')
                            CLASS ADVISORY
                        @else
                            NORMAL CLASSES
                        @endif
                    </h5>
                    <div class="small fw-semibold mt-1" style="opacity: 0.95;">
                        Scope: {{ $status->level_scope ?? 'All Levels' }}
                    </div>
                </div>
            </div>

            @if($status->announcement_text)
                <div class="mt-3 pt-2 border-top border-white-50 small" style="font-size: 12px; line-height: 1.4;">
                    {{ $status->announcement_text }}
                </div>
            @endif
        </div>

        <!-- 2. Local Calapan Weather Card -->
        <div class="card card-custom p-3 mb-3 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-1 text-muted small fw-semibold">
                    <i class="bi bi-geo-alt-fill text-danger"></i> Calapan City, Oriental Mindoro
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2" style="font-size: 11px;">
                    {{ $weather['risk_level'] }}
                </span>
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-cloud-rain-heavy-fill text-primary" style="font-size: 42px;"></i>
                    <div>
                        <div class="display-6 fw-bold lh-1 text-dark">{{ $weather['temp'] }}°C</div>
                        <div class="small text-muted fw-medium">{{ $weather['condition'] }}</div>
                    </div>
                </div>
                <div class="text-end small">
                    <div class="text-muted"><i class="bi bi-droplet-half text-info me-1"></i> Rain: <strong class="text-dark">{{ $weather['rainfall'] }}</strong></div>
                    <div class="text-muted"><i class="bi bi-moisture text-primary me-1"></i> Humidity: <strong class="text-dark">{{ $weather['humidity'] }}</strong></div>
                    <div class="text-muted"><i class="bi bi-wind text-secondary me-1"></i> Wind: <strong class="text-dark">{{ $weather['wind'] }}</strong></div>
                </div>
            </div>
        </div>

        <!-- 3. Interactive Flood Hazard Map -->
        <div class="card card-custom p-3 mb-3 shadow-sm" id="map-section">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-map-fill text-primary"></i>
                    <span>Flood Hazard Map (DWCC Routes)</span>
                </div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill py-0 px-2" onclick="recenterMap()" style="font-size: 11px;">
                    <i class="bi bi-crosshair me-1"></i> DWCC
                </button>
            </div>

            <!-- Leaflet Map Container -->
            <div id="map" class="border"></div>

            <!-- Map Legend -->
            <div class="d-flex align-items-center justify-content-around mt-2 pt-2 border-top bg-light rounded-3 px-2 py-1 small" style="font-size: 11px;">
                <div class="d-flex align-items-center gap-1">
                    <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #16a34a;"></span>
                    <span class="text-muted">Safe</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #eab308;"></span>
                    <span class="text-muted">Moderate</span>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #dc2626;"></span>
                    <span class="text-muted">Impassable</span>
                </div>
            </div>
        </div>

        <!-- 4. Submit Flood Report Callout -->
        <div class="card card-custom p-3 mb-3 shadow-sm bg-primary-subtle border-primary-subtle d-flex flex-row align-items-center justify-content-between">
            <div>
                <h6 class="fw-bold text-primary mb-1">Flooding in your street?</h6>
                <p class="small text-muted mb-0" style="font-size: 12px;">Help fellow DWCC students travel safely by submitting a hazard report.</p>
            </div>
            <button class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap ms-2" data-bs-toggle="modal" data-bs-target="#reportModal" style="background-color: #0b3954; border: none;">
                <i class="bi bi-plus-circle me-1"></i> Report
            </button>
        </div>

        <!-- 5. Official School Announcements -->
        <div class="card card-custom p-3 mb-3 shadow-sm" id="alerts-section">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill text-danger"></i>
                    <span>Official Announcements</span>
                </div>
                <span class="badge bg-secondary-subtle text-secondary small">{{ $announcements->count() }} Updates</span>
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse($announcements as $announcement)
                    <div class="p-2 rounded-3 border-start border-4 bg-light small" style="border-left-color: @if($announcement->severity === 'danger') #dc2626 @elseif($announcement->severity === 'warning') #d97706 @elseif($announcement->severity === 'success') #16a34a @else #0b3954 @endif !important;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge @if($announcement->severity === 'danger') bg-danger @elseif($announcement->severity === 'warning') bg-warning text-dark @elseif($announcement->severity === 'success') bg-success @else bg-primary @endif" style="font-size: 9px;">
                                {{ $announcement->category }}
                            </span>
                            <span class="text-muted" style="font-size: 10px;">
                                <i class="bi bi-clock me-1"></i>{{ $announcement->created_at ? $announcement->created_at->diffForHumans() : 'Just now' }}
                            </span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;">{{ $announcement->title }}</h6>
                        <p class="text-muted mb-0" style="font-size: 12px; line-height: 1.4;">{{ $announcement->content }}</p>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No active announcements at this time.</div>
                @endforelse
            </div>
        </div>

        <!-- 6. Verified Student & Barangay Reports -->
        <div class="card card-custom p-3 mb-3 shadow-sm">
            <div class="fw-bold text-dark d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-chat-square-quote-fill text-primary"></i>
                <span>Community Flood Reports</span>
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse($recentReports as $report)
                    <div class="p-2 border rounded-3 bg-white small">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark" style="font-size: 12px;">{{ $report->location_name }}</strong>
                            <span class="badge @if($report->severity === 'HIGH') bg-danger @elseif($report->severity === 'MODERATE') bg-warning text-dark @else bg-success @endif" style="font-size: 9px;">
                                {{ $report->severity }} RISK
                            </span>
                        </div>
                        <div class="text-muted mb-1" style="font-size: 11px;">{{ $report->description }}</div>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 10px;">
                            <span>By: {{ $report->reporter_name }} ({{ $report->role }})</span>
                            <span class="text-primary fw-semibold">{{ $report->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No community reports submitted today.</div>
                @endforelse
            </div>
        </div>

        <!-- Emergency Hotlines Footer -->
        <div class="text-center py-3 text-muted small border-top mt-4" style="font-size: 11px;">
            <div class="fw-bold text-dark mb-1">DWCC Emergency Contacts</div>
            <div>Campus Security: (043) 288-XXXX | CDRRMO Calapan: 911</div>
            <div class="mt-2 opacity-75">Weather and Flood Alert System • Divine Word College of Calapan</div>
        </div>

    </div>

    <!-- Bottom Navigation Bar (Mobile Friendly) -->
    <div class="bottom-nav">
        <a href="{{ route('student.index') }}" class="bottom-nav-item active">
            <i class="bi bi-house-door-fill"></i>
            <span>Home</span>
        </a>
        <a href="#map-section" class="bottom-nav-item">
            <i class="bi bi-map"></i>
            <span>Map</span>
        </a>
        <a href="#alerts-section" class="bottom-nav-item">
            <i class="bi bi-bell"></i>
            <span>Alerts</span>
        </a>
        <a href="javascript:void(0)" class="bottom-nav-item" data-bs-toggle="modal" data-bs-target="#reportModal">
            <i class="bi bi-plus-circle text-danger"></i>
            <span>Report</span>
        </a>
    </div>

</div>

<!-- Student Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="reportModalLabel"><i class="bi bi-water text-primary me-2"></i>Report Flooding</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('student.report') }}" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <p class="small text-muted mb-3">Provide quick details about flooded streets along your way to or from DWCC.</p>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Your Name</label>
                        <input type="text" name="reporter_name" class="form-control form-control-sm rounded-3" placeholder="e.g., Juan Dela Cruz" required>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Role / Department</label>
                            <select name="role" class="form-select form-select-sm rounded-3">
                                <option value="Student">Student (College)</option>
                                <option value="SHS Student">Senior High School</option>
                                <option value="JHS Student">Junior High School</option>
                                <option value="Faculty">Faculty Member</option>
                                <option value="Staff">School Staff</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold mb-1">Contact Number (Optional)</label>
                            <input type="text" name="contact_number" class="form-control form-control-sm rounded-3" placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Location / Street in Calapan</label>
                        <input type="text" name="location_name" class="form-control form-control-sm rounded-3" placeholder="e.g., J.P. Rizal Ave near Gate 1" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Flood Severity</label>
                        <select name="severity" class="form-select form-select-sm rounded-3" required>
                            <option value="LOW">Low (Ankle deep, passable to all vehicles)</option>
                            <option value="MODERATE" selected>Moderate (Gutter/Knee deep, caution for motorcycles)</option>
                            <option value="HIGH">High (Above knee, impassable)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Description / Observations</label>
                        <textarea name="description" rows="2" class="form-control form-control-sm rounded-3" placeholder="Describe the water current, traffic, or passable alternate roads..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold" style="background-color: #0b3954; border: none;">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let map;
    const dwccCampusCoords = [13.4116, 121.1803];

    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Leaflet map centered at DWCC Calapan
        map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView(dwccCampusCoords, 15);

        // OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // DWCC Campus Main Marker
        const campusIcon = L.divIcon({
            className: 'custom-campus-pin',
            html: '<div style="background-color: #0b3954; color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 16px; border: 2px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3);"><i class="bi bi-building"></i></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        L.marker(dwccCampusCoords, { icon: campusIcon })
            .addTo(map)
            .bindPopup('<strong>Divine Word College of Calapan</strong><br><small>Main Campus Center</small>')
            .openPopup();

        // Fetch live flood zones from backend
        fetch("{{ route('api.mapData') }}")
            .then(res => res.json())
            .then(data => {
                // Plot Flood Hazard Zones
                data.zones.forEach(zone => {
                    let color = '#16a34a';
                    if (zone.risk_level === 'HIGH') color = '#dc2626';
                    else if (zone.risk_level === 'MODERATE') color = '#eab308';

                    // Circle overlay
                    L.circle([zone.latitude, zone.longitude], {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.35,
                        radius: zone.radius_meters || 150
                    }).addTo(map).bindPopup(`
                        <div style="font-size: 12px;">
                            <strong style="color: ${color};">${zone.risk_level} RISK FLOOD</strong><br>
                            <strong>${zone.name}</strong><br>
                            <span class="text-muted">Water Level: ~${zone.water_level_cm || 0} cm</span><br>
                            <small>${zone.passable_status}</small>
                        </div>
                    `);
                });
            })
            .catch(err => console.error("Map load error:", err));
    });

    function recenterMap() {
        if (map) {
            map.setView(dwccCampusCoords, 16);
        }
    }
</script>
@endsection
