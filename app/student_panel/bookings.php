<?php include("../assets/noSessionRedirect.php"); ?>
<?php include("./verifyRoleRedirect.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Teacher</title>
    <link rel="shortcut icon" href="./images/logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo"><img src="./images/logo.png" alt=""><h2>E<span class="danger">R</span>P</h2></div>
        <div class="navbar">
            <a href="index.php"><span class="material-icons-sharp">home</span><h3>Home</h3></a>
            <a href="bookings.php" class="active"><span class="material-icons-sharp">calendar_today</span><h3>Book Teacher</h3></a>
<a href="campus-map.php"><span class="material-icons-sharp">map</span><h3>Campus Map</h3></a>
            <a href="my-bookings.php"><span class="material-icons-sharp">history</span><h3>My Bookings</h3></a>
            <a href="logout.php"><span class="material-icons-sharp">logout</span><h3>Logout</h3></a>
        </div>
    </header>
    <div class="container">
        <main>
            <h1>Book a Teacher</h1>
            
            <!-- Search Box -->
            <div style="margin: 20px 0;">
                <input type="text" 
                       id="searchBox" 
                       placeholder="Search teachers by name or subject..." 
                       style="width: 100%; max-width: 500px; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
            </div>
            
            <div id="teachersList" style="margin-top:20px;"></div>
        </main>
    </div>

    <div id="bookingModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000;">
        <div style="background:white; margin:50px auto; padding:30px; width:500px; max-width:90%; border-radius:10px;">
            <h2>Book Appointment</h2>
            <form id="bookingForm">
                <input type="hidden" id="teacher_id" name="teacher_id">
                <p><strong>Teacher:</strong> <span id="teacher_name"></span></p>
                <label>Date:</label>
                <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" style="width:100%;padding:8px;margin-bottom:10px;">
                <label>Time Slot:</label>
                <select id="time_slot" name="time_slot" required style="width:100%;padding:8px;margin-bottom:10px;">
                    <option value="">-- Select a slot --</option>
                </select>
                <label>Purpose:</label>
                <textarea id="purpose" name="purpose" required rows="4" style="width:100%;padding:8px;margin-bottom:10px;" placeholder="Please describe the purpose of your meeting..."></textarea>
                <button type="submit" style="background:#4CAF50; color:white; padding:10px 20px; border:none; cursor:pointer; border-radius:5px;">Confirm Booking</button>
                <button type="button" onclick="closeModal()" style="background:#f44336; color:white; padding:10px 20px; border:none; cursor:pointer; margin-left:10px; border-radius:5px;">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        let teachersData = [];
        let searchTimeout;
        
        // Search functionality
        document.getElementById('searchBox').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadTeachers(e.target.value);
            }, 300);
        });
        
        function loadTeachers(searchTerm = '') {
            const url = searchTerm 
                ? `../assets/fetchTeachersForBooking.php?search=${encodeURIComponent(searchTerm)}`
                : '../assets/fetchTeachersForBooking.php';
                
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        teachersData = data.teachers;
                        displayTeachers(data.teachers);
                    } else {
                        document.getElementById('teachersList').innerHTML = '<p style="color:red;">Error loading teachers</p>';
                    }
                })
                .catch(err => {
                    document.getElementById('teachersList').innerHTML = '<p style="color:red;">Error: ' + err.message + '</p>';
                });
        }
        
        function displayTeachers(teachers) {
            if (teachers.length === 0) {
                document.getElementById('teachersList').innerHTML = '<p>No teachers found.</p>';
                return;
            }
            
            document.getElementById('teachersList').innerHTML = teachers.map(t => `
                <div style="border:1px solid #ddd; padding:20px; margin:15px 0; border-radius:8px; background:#f9f9f9; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h3 style="margin-top:0; color:#333;">${t.fname} ${t.lname}</h3>
                    <p><strong>Subject:</strong> ${t.subject || 'Not specified'}</p>
                    <p><strong>Chamber:</strong> ${t.chamber_no}</p>
                    <p><strong>Email:</strong> ${t.email}</p>
                    ${t.bio ? `<p style="font-style:italic; color:#666;">${t.bio}</p>` : ''}
                    <button onclick="openBookingModal('${t.id}', '${t.fname} ${t.lname}')" 
                            style="background:#2196F3; color:white; padding:10px 20px; border:none; cursor:pointer; border-radius:5px; margin-top:10px;">
                        Book Appointment
                    </button>
                </div>
            `).join('');
        }
        
        function openBookingModal(teacherId, teacherName) {
            document.getElementById('teacher_id').value = teacherId;
            document.getElementById('teacher_name').textContent = teacherName;
            document.getElementById('bookingModal').style.display = 'block';
            
            const teacher = teachersData.find(t => t.id === teacherId);
            if (teacher) {
                let slots = {};
                try {
                    slots = JSON.parse(teacher.available_slots || '{}');
                    console.log('Available slots:', slots);
                } catch(e) {
                    console.error('Error parsing slots:', e);
                }
                
                document.getElementById('booking_date').onchange = function() {
                    const selectedDate = new Date(this.value + 'T00:00:00');
                    const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    const dayName = days[selectedDate.getDay()];
                    const timeSlots = slots[dayName] || [];
                    
                    console.log('Selected date:', this.value, 'Day:', dayName, 'Slots:', timeSlots);
                    
                    if (timeSlots.length === 0) {
                        document.getElementById('time_slot').innerHTML = '<option value="">No slots available for this day</option>';
                    } else {
                        document.getElementById('time_slot').innerHTML = '<option value="">-- Select a slot --</option>' +
                            timeSlots.map(slot => `<option value="${slot}">${slot}</option>`).join('');
                    }
                };
                
                // Trigger change if date is already selected
                const dateInput = document.getElementById('booking_date');
                if (dateInput.value) {
                    dateInput.onchange();
                }
            }
        }
        
        function closeModal() {
            document.getElementById('bookingModal').style.display = 'none';
            document.getElementById('bookingForm').reset();
        }
        
        document.getElementById('bookingForm').onsubmit = function(e) {
            e.preventDefault();
            
            fetch('../assets/createBooking.php', { 
                method: 'POST', 
                body: new FormData(this) 
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    closeModal();
                }
            })
            .catch(err => {
                alert('Error: ' + err.message);
            });
        };
        
        loadTeachers();
    </script>
</body>
</html>