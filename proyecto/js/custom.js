// ===== TASKMIND JS PROFESIONAL =====
document.addEventListener("DOMContentLoaded", function() {

    // 🎯 HEADER SCROLL EFFECT
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.header-pro');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255,255,255,0.98)';
            header.style.boxShadow = '0 5px 25px rgba(0,0,0,0.15)';
        } else {
            header.style.background = 'rgba(255,255,255,0.95)';
            header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
        }
    });

    // 🧭 SMOOTH SCROLL
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });

    // ✨ SCROLL ANIMATIONS
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.funcion-card, .fade-up').forEach(el => {
        observer.observe(el);
    });

    // 📝 FORM CONTACTO
    const formContacto = document.querySelector('.form-contacto');
    if (formContacto) {
        formContacto.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simular envío
            const btn = this.querySelector('button');
            const textoOriginal = btn.textContent;
            
            btn.textContent = 'Enviando...';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.textContent = textoOriginal;
                btn.disabled = false;
                this.reset();
                alert('¡Mensaje enviado correctamente! 🚀 Gracias por tu opinión.');
            }, 2000);
        });
    }

    // 👤 SISTEMA DE SESIÓN
    verificarSesion();

    // 🔄 Función para verificar sesión
    function verificarSesion() {
        const usuario = localStorage.getItem('usuario');
        const userIcon = document.querySelector('.user-icon');
        
        if (usuario && userIcon) {
            userIcon.title = `Hola, ${usuario}`;
            userIcon.style.color = '#28a745';
        }
    }

    // 🚪 Cerrar sesión
    const userIcon = document.querySelector('.user-icon');
    if (userIcon) {
        userIcon.addEventListener('click', function() {
            if (localStorage.getItem('usuario')) {
                if (confirm('¿Cerrar sesión?')) {
                    localStorage.removeItem('usuario');
                    location.reload();
                }
            } else {
                window.location.href = 'auth/login.html';
            }
        });
    }

    // 🎨 HOVER EFFECTS CARDS
    document.querySelectorAll('.funcion-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-15px) scale(1.02)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0) scale(1)';
        });
    });

    // 📱 MOBILE MENU
    const menuToggle = document.querySelector('.menu-tm');
    if (window.innerWidth <= 768 && menuToggle) {
        // Se maneja automáticamente con CSS flex-wrap
    }
});
