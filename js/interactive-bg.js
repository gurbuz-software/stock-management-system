// Interactive Background Effect
class InteractiveBackground {
    constructor() {
        this.container = document.body;
        this.mouseX = 0;
        this.mouseY = 0;
        this.init();
    }

    init() {
        // Create gradient overlay
        this.createGradientOverlay();
        
        // Add event listeners
        this.container.addEventListener('mousemove', this.handleMouseMove.bind(this));
        this.container.addEventListener('mouseleave', this.resetGradient.bind(this));
        this.container.addEventListener('mouseenter', this.startGradient.bind(this));
        
        // Start animation
        this.startGradient();
    }

    createGradientOverlay() {
        // Remove existing overlay if any
        const existingOverlay = document.getElementById('interactive-bg-overlay');
        if (existingOverlay) {
            existingOverlay.remove();
        }

        // Create new overlay
        this.overlay = document.createElement('div');
        this.overlay.id = 'interactive-bg-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.5s ease;
            background: radial-gradient(
                circle at 50% 50%,
                rgba(99, 102, 241, 0.1) 0%,
                rgba(139, 92, 246, 0.05) 30%,
                rgba(30, 41, 59, 0) 70%
            );
        `;
        
        this.container.appendChild(this.overlay);
    }

    handleMouseMove(e) {
        this.mouseX = e.clientX;
        this.mouseY = e.clientY;
        
        this.updateGradientPosition();
    }

    updateGradientPosition() {
        if (!this.overlay) return;

        const x = (this.mouseX / window.innerWidth) * 100;
        const y = (this.mouseY / window.innerHeight) * 100;

        this.overlay.style.background = `
            radial-gradient(
                circle at ${x}% ${y}%,
                rgba(99, 102, 241, 0.15) 0%,
                rgba(139, 92, 246, 0.08) 25%,
                rgba(30, 41, 59, 0) 60%
            )
        `;
        
        // Add subtle glow effect
        this.overlay.style.boxShadow = `
            inset 0 0 100px rgba(99, 102, 241, 0.05),
            inset 0 0 200px rgba(139, 92, 246, 0.03)
        `;
    }

    startGradient() {
        if (this.overlay) {
            this.overlay.style.opacity = '1';
        }
    }

    resetGradient() {
        if (this.overlay) {
            this.overlay.style.opacity = '0';
            
            // Reset to center position
            setTimeout(() => {
                this.overlay.style.background = `
                    radial-gradient(
                        circle at 50% 50%,
                        rgba(99, 102, 241, 0.1) 0%,
                        rgba(139, 92, 246, 0.05) 30%,
                        rgba(30, 41, 59, 0) 70%
                    )
                `;
            }, 500);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new InteractiveBackground();
});

// Add floating particles effect
class FloatingParticles {
    constructor() {
        this.container = document.body;
        this.particles = [];
        this.particleCount = 15;
        this.init();
    }

    init() {
        this.createParticles();
        this.animateParticles();
    }

    createParticles() {
        // Remove existing particles
        const existingParticles = document.querySelectorAll('.floating-particle');
        existingParticles.forEach(particle => particle.remove());

        // Create new particles
        for (let i = 0; i < this.particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'floating-particle';
            
            // Random properties
            const size = Math.random() * 4 + 1;
            const posX = Math.random() * 100;
            const posY = Math.random() * 100;
            const duration = Math.random() * 20 + 10;
            const delay = Math.random() * 5;
            const opacity = Math.random() * 0.3 + 0.1;
            
            particle.style.cssText = `
                position: fixed;
                width: ${size}px;
                height: ${size}px;
                background: rgba(99, 102, 241, ${opacity});
                border-radius: 50%;
                pointer-events: none;
                z-index: -1;
                left: ${posX}%;
                top: ${posY}%;
                animation: float ${duration}s ease-in-out ${delay}s infinite;
                box-shadow: 0 0 ${size * 2}px rgba(99, 102, 241, ${opacity * 0.5});
            `;
            
            this.container.appendChild(particle);
            this.particles.push(particle);
        }
    }

    animateParticles() {
        // CSS animation handles the floating
    }
}

// Add CSS for floating animation
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
        }
        25% {
            transform: translate(20px, -20px) rotate(90deg);
        }
        50% {
            transform: translate(-15px, -30px) rotate(180deg);
        }
        75% {
            transform: translate(-20px, 10px) rotate(270deg);
        }
    }
    
    .floating-particle {
        will-change: transform;
    }
`;
document.head.appendChild(style);

// Initialize particles
document.addEventListener('DOMContentLoaded', () => {
    new FloatingParticles();
});