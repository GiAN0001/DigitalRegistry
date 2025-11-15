<div class="dashboard-grid">
    <div class="dashboard-card border-l-4 border-blue-500">
        <h3 class="text-lg font-semibold text-gray-700">Total Residents</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalResidents ?? 0 }}</p>
    </div>
    <div class="dashboard-card border-l-4 border-yellow-500">
        <h3 class="text-lg font-semibold text-gray-700">Pending Tickets</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingTickets ?? 0 }}</p>
    </div>
    <div class="dashboard-card border-l-4 border-green-500">
        <h3 class="text-lg font-semibold text-gray-700">Total Staff</h3>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalStaff ?? 0 }}</p>
    </div>
</div>