<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Player Contract</title>
    <link rel="icon" type="image/svg+xml" href="/img/logo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    <style>
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            pointer-events: none;
        }

        .thank {
            text-align: center;
            margin-top: 5rem;
        }
    </style>
</head>

<body>
    <div id="page-content">
        <header id="header">
            <img src="https://www.hartlandgirlsacademy.co.uk/wp-content/uploads/2024/08/cropped-HARTLAND-LOGO-2048.png"
                alt="Hartland Logo" height="256" width="256" />
            <h1>25/26 - Player Contract</h1>
        </header>

        <div id="container" class="container">
            <h1 class="thank">Thank you!</h1>
        </div>
    </div>
    <canvas id="fireworks"></canvas>
    <script>
        // Get the canvas element and its drawing context
        const canvas = document.getElementById('fireworks');
        const ctx = canvas.getContext('2d');

        // Set the canvas size to full screen
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        // Define the Particle class for the explosion particles
        class Particle {
            constructor(x, y, color) {
                this.x = x;
                this.y = y;
                this.color = color;
                // Random initial velocity
                this.velocity = {
                    x: (Math.random() - 0.5) * 8,
                    y: (Math.random() - 0.5) * 8
                };
                this.alpha = 1; // Opacity of the particle
                this.friction = 0.99; // Friction to slow down particles
            }

            // Draw a particle on the canvas
            draw() {
                ctx.globalAlpha = this.alpha;
                ctx.beginPath();
                ctx.arc(this.x, this.y, 2, 0, Math.PI * 2, false);
                ctx.fillStyle = this.color;
                ctx.fill();
            }

            // Update the particle position and opacity
            update() {
                this.velocity.x *= this.friction;
                this.velocity.y *= this.friction;
                this.x += this.velocity.x;
                this.y += this.velocity.y;
                this.alpha -= 0.01; // Fade out the particle
            }
        }

        // Define the Firework class for the rockets
        class Firework {
            constructor(x, y, color) {
                this.x = x;
                this.y = y;
                this.color = color;
                // Initial upward velocity
                this.velocity = {
                    x: 0,
                    y: Math.random() * -2.5 - 0.5
                };
                this.particles = []; // Particles for explosion
                this.lifespan = 180; // Time before explosion
                this.hasExploded = false; // Track if the firework has exploded
            }

            // Draw the firework rocket on the canvas
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, 3, 0, Math.PI * 2, false);
                ctx.fillStyle = this.color;
                ctx.fill();
            }

            // Create particles for explosion
            explode() {
                for (let i = 0; i < 50; i++) {
                    this.particles.push(new Particle(this.x, this.y, this.color));
                }
            }

            // Update the firework position, check for explosion
            update() {
                this.lifespan--;

                // Check if the firework should explode
                if (this.lifespan <= 0 && !this.hasExploded) {
                    this.explode();
                    this.velocity = {
                        x: 0,
                        y: 0
                    };
                    this.hasExploded = true;
                } else if (this.lifespan > 0) {
                    this.y += this.velocity.y;
                }

                // Update and draw particles
                for (let i = 0; i < this.particles.length; i++) {
                    this.particles[i].update();
                    this.particles[i].draw();
                }
            }
        }

        // Array to store the fireworks
        let fireworks = [];

        // Function to animate the fireworks
        function animate() {
            // Clear the canvas and create a trailing effect
            ctx.fillStyle = 'rgb(255, 255, 255)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Update and draw each firework
            fireworks.forEach((firework, index) => {
                firework.update();
                firework.draw();

                // Remove fireworks that have finished exploding
                if (firework.lifespan <= 0 && firework.particles.every(p => p.alpha <= 0)) {
                    fireworks.splice(index, 1);
                }
            });

            // Occasionally create a new firework
            if (Math.random() < 0.015) {
                const x = Math.random() * canvas.width;
                const color = `hsl(${Math.random() * 360}, 50%, 50%)`;
                fireworks.push(new Firework(x, canvas.height, color));
            }

            // Request the next animation frame
            requestAnimationFrame(animate);
        }

        // Start the animation
        animate();
    </script>
</body>

</html>
