<?php include("../assets/noSessionRedirect.php"); ?>
<?php include("./verifyRoleRedirect.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Map</title>
    <link rel="shortcut icon" href="./images/logo.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .map-container {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .map-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .map-header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .map-header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .map-image-wrapper {
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.15);
            background: #f5f5f5;
            position: relative;
        }
        
        .map-image-wrapper img {
            width: 100%;
            height: auto;
            display: block;
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }
        
        .map-image-wrapper img.zoomed {
            cursor: zoom-out;
            transform: scale(1.5);
            transform-origin: center;
        }
        
        .zoom-controls {
            text-align: center;
            margin-top: 20px;
        }
        
        .zoom-controls button {
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px 25px;
            margin: 0 10px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .zoom-controls button:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .zoom-controls button:active {
            transform: translateY(0);
        }
        
        .map-info {
            margin-top: 30px;
            padding: 20px;
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            border-radius: 8px;
        }
        
        .map-info h3 {
            color: #1976D2;
            margin-bottom: 10px;
        }
        
        .map-info ul {
            list-style: none;
            padding-left: 0;
        }
        
        .map-info ul li {
            padding: 8px 0;
            color: #555;
        }
        
        .map-info ul li:before {
            content: "📍";
            margin-right: 10px;
        }
        
        .fullscreen-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.95);
            z-index: 9999;
            overflow: auto;
        }
        
        .fullscreen-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .fullscreen-overlay img {
            max-width: 95%;
            max-height: 95%;
            object-fit: contain;
            cursor: zoom-out;
        }
        
        .close-fullscreen {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            cursor: pointer;
            z-index: 10000;
        }
        
        @media screen and (max-width: 768px) {
            .map-container {
                padding: 15px;
            }
            
            .map-header h1 {
                font-size: 1.8em;
            }
            
            .zoom-controls button {
                padding: 10px 20px;
                font-size: 14px;
                margin: 5px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><img src="./images/logo.png" alt=""><h2>E<span class="danger">R</span>P</h2></div>
        <div class="navbar">
            <a href="index.php"><span class="material-icons-sharp">home</span><h3>Home</h3></a>
            <a href="bookings.php"><span class="material-icons-sharp">calendar_today</span><h3>Book Teacher</h3></a>
            <a href="my-bookings.php"><span class="material-icons-sharp">history</span><h3>My Bookings</h3></a>
            <a href="campus-map.php" class="active"><span class="material-icons-sharp">map</span><h3>Campus Map</h3></a>
            <a href="logout.php"><span class="material-icons-sharp">logout</span><h3>Logout</h3></a>
        </div>
    </header>
    
    <div class="container">
        <main>
            <div class="map-container">
                <div class="map-header">
                    <h1>🗺️ Campus Map</h1>
                    <p>Navigate KIIT Campus with ease - Find buildings, departments, and facilities</p>
                </div>
                
                <div class="map-image-wrapper" id="mapWrapper">
                    <img src="./images/campus-map.png" alt="KIIT Campus Map" id="campusMap">
                </div>
                
                <div class="zoom-controls">
                    <button onclick="viewFullscreen()">
                        <span class="material-icons-sharp" style="vertical-align: middle;">fullscreen</span>
                        View Fullscreen
                    </button>
                    <button onclick="downloadMap()">
                        <span class="material-icons-sharp" style="vertical-align: middle;">download</span>
                        Download Map
                    </button>
                </div>
                
                <div class="map-info">
                    <h3>Key Locations</h3>
                    <ul>
                        <li><strong>KIIT Campus 14</strong> - Main Academic Building</li>
                        <li><strong>Kalinga Institute of Medical Sciences (KIMS)</strong> - Medical Campus</li>
                        <li><strong>Infocity Square</strong> - Central Hub</li>
                        <li><strong>KIIT Square 1 & 2</strong> - Student Facilities</li>
                        <li><strong>Multiple Campuses</strong> - Connected via campus transport</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
    
    <div class="fullscreen-overlay" id="fullscreenOverlay" onclick="closeFullscreen()">
        <span class="close-fullscreen">&times;</span>
        <img src="./images/campus-map.png" alt="KIIT Campus Map Fullscreen">
    </div>
    
    <script>
        function viewFullscreen() {
            document.getElementById('fullscreenOverlay').classList.add('active');
        }
        
        function closeFullscreen() {
            document.getElementById('fullscreenOverlay').classList.remove('active');
        }
        
        function downloadMap() {
            const link = document.createElement('a');
            link.href = './images/campus-map.png';
            link.download = 'KIIT-Campus-Map.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Escape key to close fullscreen
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFullscreen();
            }
        });
    </script>
</body>
</html>