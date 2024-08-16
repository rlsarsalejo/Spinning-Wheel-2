<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spinning Wheel</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #e9ecef;
            font-family: 'Arial', sans-serif;
        }

        .input-container {
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            font-size: 18px;
            margin-right: 10px;
            color: #333;
        }

        input[type="number"] {
            padding: 12px;
            font-size: 18px;
            border: 2px solid #ced4da;
            border-radius: 8px;
            outline: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus {
            border-color: #495057;
        }

        .wheel-container {
            position: relative;
            width: 80vw; /* Responsive width */
            height: 80vw; /* Maintain aspect ratio */
            max-width: 600px; /* Maximum size */
            max-height: 600px; /* Maximum size */
            margin-top: 20px;
        }

        .wheel {
            border-radius: 50%;
            width: 100%;
            height: 100%;
            background: conic-gradient(
                #ff6f61 0% 25%, 
                #ffcc5c 25% 50%, 
                #88d8b0 50% 75%, 
                #6a5acd 75% 100%
            );
            position: absolute;
            transform: rotate(0deg);
            transition: transform 4s ease-out;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .wheel .label {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 4vw; /* Responsive font size */
            color: #fff;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
            transition: opacity 0.5s ease-in;
        }

        .pointer {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            border-bottom: 50px solid #333;
            transform: translate(-50%, -100%);
            z-index: 10;
        }

        .button {
            padding: 12px 24px;
            font-size: 18px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            outline: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .button:active {
            background-color: #004494;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hidden {
            opacity: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .wheel-container {
                width: 90vw; /* Increase size on smaller screens */
                height: 90vw;
            }

            .wheel .label {
                font-size: 5vw; /* Larger font size on smaller screens */
            }
        }

    </style>
</head>
<body>
    <div class="input-container">
        <label for="segmentCount">Number of Segments:</label>
        <input type="number" id="segmentCount" min="2" value="4" readonly>
        <button class="button" onclick="fetchParticipants()">Fetch Participants</button>
        <button class="button" onclick="updateWheel()">Update Wheel</button>
    </div>
    
    <div class="wheel-container">
        <div id="wheel" class="wheel"></div>
        <div class="pointer"></div>
        <div id="label" class="label hidden">Selected: </div>
    </div>
    
    <button class="button" onclick="spinWheel()">Spin</button>

    <script>
        let participants = [];

        async function fetchParticipants() {
            try {
                const response = await fetch('fetch_participants.php');
                if (!response.ok) {
                    throw new Error('Failed to fetch participants');
                }

                participants = await response.json();
                const participantCount = participants.length;
                document.getElementById('segmentCount').value = participantCount;

                updateWheel();
            } catch (error) {
                console.error('Error fetching participants:', error);
            }
        }

        function updateWheel() {
            const wheel = document.getElementById('wheel');
            const anglePerSegment = 360 / participants.length;
            const gradientStops = [];
            
            const baseColors = ['#ff6f61', '#ffcc5c', '#88d8b0', '#6a5acd'];
            for (let i = 0; i < participants.length; i++) {
                const color = baseColors[i % baseColors.length];
                const startPercent = (i * 100) / participants.length;
                const endPercent = ((i + 1) * 100) / participants.length;
                gradientStops.push(`${color} ${startPercent}% ${endPercent}%`);
            }

            wheel.style.background = `conic-gradient(${gradientStops.join(', ')})`;

            const segmentLabels = participants.map((name, index) => `Segment ${index + 1}: ${name}`);
            const labels = document.querySelectorAll('.wheel .label');
            labels.forEach((label, index) => {
                label.textContent = segmentLabels[index] || '';
            });
        }

        function spinWheel() {
            if (participants.length === 0) {
                alert('No participants to spin!');
                return;
            }

            const wheel = document.getElementById('wheel');
            const label = document.getElementById('label');
            const anglePerSegment = 360 / participants.length;
            const randomRotation = Math.floor(Math.random() * 360);
            
            label.classList.add('hidden');
            wheel.style.transform = `rotate(${randomRotation + 3600}deg)`;
            
            setTimeout(() => {
                const selectedIndex = Math.floor((360 - (randomRotation % 360)) / anglePerSegment) % participants.length;
                label.textContent = `Selected: ${participants[selectedIndex]}`;
                label.classList.remove('hidden');
            }, 4000);
        }

        updateWheel();
    </script>
</body>
</html>
