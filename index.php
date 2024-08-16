<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Spin Wheel with Arrow on the Right</title>
</head>
<body>
    <div id="participant-count"></div>

    <div class="input-container">
        <button class="button" onclick="fetchParticipants()">Fetch Participants</button>
    </div>
    
    <div id="result"></div>
    <div class="wheel-container">
        <canvas id="wheel" width="400" height="400"></canvas>
        <div class="arrow"></div>
    </div>
    <button id="spin">Spin</button>

    <script>
        const canvas = document.getElementById('wheel');
        const ctx = canvas.getContext('2d');
        let names = [];
        let idleAngle = 0; // Angle for the idle spin
        let idleAnimationId; // To track the idle animation

        function fetchParticipants() {
            fetch('fetch_participants.php')
                .then(response => response.json())
                .then(data => {
                    names = data;
                    drawWheel(); // Redraw the wheel with the new participants
                    displayParticipantCount(); // Display the number of participants
                })
                .catch(error => console.error('Error fetching participants:', error));
        }

        function displayParticipantCount() {
            document.getElementById('participant-count').textContent = `Total Participants: ${names.length}`;
        }

        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = (i * 360 / count) % 360;
                colors.push(`hsl(${hue}, 70%, 50%)`);
            }
            return colors;
        }

        function drawWheel() {
            const segments = names.length;
            const colors = generateColors(segments);
            const angle = (2 * Math.PI) / segments;
            const radius = canvas.width / 2;
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < segments; i++) {
                ctx.beginPath();
                ctx.moveTo(radius, radius);
                ctx.arc(radius, radius, radius, i * angle, (i + 1) * angle);
                ctx.lineTo(radius, radius);
                ctx.fillStyle = colors[i];
                ctx.fill();
                ctx.save();

                ctx.translate(radius, radius);
                ctx.rotate(i * angle + angle / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 18px Arial';
                ctx.fillText(names[i], radius - 10, 5);
                ctx.restore();
            }
        }

        // Idle spinning animation
        function idleSpin() {
            idleAngle += 0.01; // Slow spin
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.save();
            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate(idleAngle);
            ctx.translate(-canvas.width / 2, -canvas.height / 2);
            drawWheel();
            ctx.restore();
            idleAnimationId = requestAnimationFrame(idleSpin);
        }

        function spinWheel() {
            cancelAnimationFrame(idleAnimationId); // Stop the idle spin
            const spinAngle = Math.random() * 360 + 1800;
            const spinDuration = 4000;
            const startTime = performance.now();
            const spin = (time) => {
                const progress = Math.min((time - startTime) / spinDuration, 1);
                const angle = spinAngle * progress;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate((angle * Math.PI) / 180);
                ctx.translate(-canvas.width / 2, -canvas.height / 2);
                drawWheel();
                ctx.restore();
                if (progress < 1) {
                    requestAnimationFrame(spin);
                } else {
                    displayResult(angle);
                    // Do not restart idle spin here
                }
            };
            requestAnimationFrame(spin);
        }

        function displayResult(angle) {
            const segments = names.length;
            const segmentAngle = 360 / segments;
            const normalizedAngle = (angle % 360 + 360) % 360;
            const arrowAngle = 0;
            const winningSegment = Math.floor((arrowAngle - normalizedAngle + 360) % 360 / segmentAngle);
            const winner = names[winningSegment];
            document.getElementById('result').textContent = `The Lucky Winner is: ${winner}`;
        }

        document.getElementById('spin').addEventListener('click', spinWheel);

        // Start the idle spinning animation
        idleSpin();
    </script>
</body>
</html>
