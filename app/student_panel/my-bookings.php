<?php include("../assets/noSessionRedirect.php"); ?>
<?php include("./verifyRoleRedirect.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="shortcut icon" href="./images/logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo"><img src="./images/logo.png" alt=""><h2>E<span class="danger">R</span>P</h2></div>
        <div class="navbar">
            <a href="index.php"><span class="material-icons-sharp">home</span><h3>Home</h3></a>
            <a href="bookings.php"><span class="material-icons-sharp">calendar_today</span><h3>Book Teacher</h3></a>
            <a href="my-bookings.php" class="active"><span class="material-icons-sharp">history</span><h3>My Bookings</h3></a>
            <a href="logout.php"><span class="material-icons-sharp">logout</span><h3>Logout</h3></a>
        </div>
    </header>
    <div class="container">
        <main>
            <h1>My Bookings</h1>
            <div id="bookingsList" style="margin-top:20px;">
                <div style="text-align:center; padding:20px;">
                    <p>Loading your bookings...</p>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function loadBookings() {
            fetch('../assets/fetchStudentBookings.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayBookings(data.bookings);
                    } else {
                        document.getElementById('bookingsList').innerHTML = `
                            <div style="padding:20px; background:#ffebee; color:#c62828; border:1px solid #ef5350; border-radius:8px;">
                                <h3>Error loading bookings</h3>
                                <p><strong>Error:</strong> ${data.message || 'Unknown error'}</p>
                                <button onclick="location.reload()" style="margin-top:10px; padding:8px 16px; background:#2196F3; color:white; border:none; border-radius:4px; cursor:pointer;">Retry</button>
                            </div>`;
                    }
                })
                .catch(error => {
                    document.getElementById('bookingsList').innerHTML = `
                        <div style="padding:20px; background:#ffebee; color:#c62828; border:1px solid #ef5350; border-radius:8px;">
                            <h3>Error loading bookings</h3>
                            <p><strong>Network error:</strong> ${error.message}</p>
                            <button onclick="location.reload()" style="margin-top:10px; padding:8px 16px; background:#2196F3; color:white; border:none; border-radius:4px; cursor:pointer;">Retry</button>
                        </div>`;
                });
        }
        
        function displayBookings(bookings) {
            if (bookings.length === 0) {
                document.getElementById('bookingsList').innerHTML = `
                    <div style="padding:20px; background:#e3f2fd; color:#1565c0; border:1px solid #2196F3; border-radius:8px; text-align:center;">
                        <h3>No bookings yet</h3>
                        <p>You haven't made any teacher bookings yet.</p>
                        <a href="bookings.php" style="display:inline-block; margin-top:15px; padding:10px 20px; background:#2196F3; color:white; text-decoration:none; border-radius:5px;">Book a Teacher</a>
                    </div>`;
                return;
            }
            
            const statusColors = { 
                'pending': '#FF9800', 
                'confirmed': '#4CAF50', 
                'completed': '#2196F3', 
                'cancelled': '#f44336',
                'rejected': '#f44336'
            };
            
            document.getElementById('bookingsList').innerHTML = bookings.map(b => `
                <div style="border:1px solid #ddd; padding:20px; margin:15px 0; border-radius:8px; background:#f9f9f9;">
                    <div style="display:flex; justify-content:space-between; align-items:start;">
                        <div style="flex:1;">
                            <h3 style="margin:0 0 10px 0;">${b.teacher_name || 'Unknown Teacher'}</h3>
                            <p style="margin:5px 0;"><strong>Subject:</strong> ${b.subject || 'N/A'}</p>
                            <p style="margin:5px 0;"><strong>Date:</strong> ${b.booking_date || 'N/A'}</p>
                            <p style="margin:5px 0;"><strong>Time:</strong> ${b.time_slot || 'N/A'}</p>
                            <p style="margin:5px 0;"><strong>Purpose:</strong> ${b.purpose || 'N/A'}</p>
                            ${b.notes ? `<p style="margin:5px 0;"><strong>Notes:</strong> ${b.notes}</p>` : ''}
                        </div>
                        <div style="text-align:right;">
                            <span style="display:inline-block; padding:5px 15px; background:${statusColors[b.status] || '#999'}; color:white; border-radius:20px; font-weight:bold; font-size:12px;">
                                ${(b.status || 'unknown').toUpperCase()}
                            </span>
                        </div>
                    </div>
                    ${b.status === 'pending' ? `
                        <div style="margin-top:15px; padding-top:15px; border-top:1px solid #ddd;">
                            <button onclick="cancelBooking(${b.s_no})" 
                                style="background:#f44336; color:white; padding:8px 16px; border:none; cursor:pointer; border-radius:5px;">
                                Cancel Booking
                            </button>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        }
        
        function cancelBooking(bookingId) {
            const reason = prompt('Please provide a reason for cancellation:');
            if (!reason || reason.trim() === '') {
                alert('Cancellation reason is required');
                return;
            }
            
            if (!confirm('Are you sure you want to cancel this booking?')) return;
            
            const formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('reason', reason);
            
            fetch('../assets/cancelBooking.php', { 
                method: 'POST', 
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Booking cancelled');
                if (data.success) loadBookings();
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
        }
        
        loadBookings();
    </script>
</body>
</html>