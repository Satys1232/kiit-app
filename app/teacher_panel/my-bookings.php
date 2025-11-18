<?php include('partials/_header.php') ?>
<?php include('partials/_sidebar.php') ?>
<div class="content">
    <?php include("partials/_navbar.php"); ?>
    <main>
        <div class="header">
            <div class="left">
                <h1>Student Bookings</h1>
            </div>
        </div>
        <div id="bookingsList"></div>
    </main>
</div>
<?php include('partials/_footer.php') ?>
<script>
    function loadBookings() {
        fetch('../assets/fetchTeacherBookings.php')
            .then(res => res.json())
            .then(data => { if (data.success) displayBookings(data.bookings); });
    }
    function displayBookings(bookings) {
        const statusColors = { 'pending': '#FF9800', 'confirmed': '#4CAF50', 'completed': '#2196F3', 'cancelled': '#f44336' };
        const pending = bookings.filter(b => b.status === 'pending');
        const others = bookings.filter(b => b.status !== 'pending');
        let html = '';
        if (pending.length > 0) {
            html += '<h3>Pending Requests</h3>' + pending.map(b => createCard(b, true)).join('');
        }
        if (others.length > 0) {
            html += '<h3 style="margin-top:30px;">Other Bookings</h3>' + others.map(b => createCard(b, false)).join('');
        }
        document.getElementById('bookingsList').innerHTML = html || '<p>No booking requests yet.</p>';
    }
    function createCard(b, showActions) {
        const statusColors = { 'pending': '#FF9800', 'confirmed': '#4CAF50', 'completed': '#2196F3', 'cancelled': '#f44336' };
        return `<div style="border:1px solid #ddd; padding:20px; margin:15px 0; border-radius:8px; background:#f9f9f9;">
            <h4>${b.student_name}</h4>
            <p><strong>Class:</strong> ${b.class} - ${b.section} | <strong>Email:</strong> ${b.student_email}</p>
            <p><strong>Date:</strong> ${b.booking_date} | <strong>Time:</strong> ${b.time_slot}</p>
            <p><strong>Purpose:</strong> ${b.purpose}</p>
            ${b.notes ? `<p><strong>Notes:</strong> ${b.notes}</p>` : ''}
            <p><strong>Status:</strong> <span style="color:${statusColors[b.status]}; font-weight:bold;">${b.status.toUpperCase()}</span></p>
            ${showActions ? `<button onclick="updateStatus(${b.s_no}, 'confirmed')" style="background:#4CAF50; color:white; padding:10px 20px; border:none; cursor:pointer; border-radius:5px; margin-right:10px;">Accept</button>
            <button onclick="updateStatus(${b.s_no}, 'cancelled')" style="background:#f44336; color:white; padding:10px 20px; border:none; cursor:pointer; border-radius:5px;">Reject</button>` : ''}
        </div>`;
    }
    function updateStatus(bookingId, status) {
        const notes = prompt('Add notes (optional):') || '';
        fetch('../assets/updateBookingStatus.php', { method: 'POST', body: new URLSearchParams({booking_id: bookingId, status: status, notes: notes}) })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) loadBookings();
        });
    }
    loadBookings();
</script>