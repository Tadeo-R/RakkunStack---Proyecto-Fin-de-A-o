<?php
$pageTitle = 'Quiénes Somos';
$pageCss   = ['pages.css', 'special-pages.css'];
require VIEW_PATH . '/include/page_start.php';
?>

    <main class="about-container">
        <section class="about-hero">
            <h1>Sobre Nuestra Plataforma</h1>
            <p>Brindamos soluciones integrales para la organización, gestión y seguimiento de torneos deportivos y digitales de forma ágil e intuitiva.</p>
        </section>

        <section class="about-grid">
            <div class="about-card">
                <div class="about-icon-wrapper icon-blue">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3>Seguro</h3>
                <p>Protegemos tus datos y la información de cada torneo con altos estándares de seguridad.</p>
            </div>

            <div class="about-card">
                <div class="about-icon-wrapper icon-clock">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <h3>Simple</h3>
                <p>Administrá torneos, horarios y resultados de forma fácil, rápida y sin complicaciones.</p>
            </div>

            <div class="about-card">
                <div class="about-icon-wrapper icon-support">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h3>Soporte 24/7</h3>
                <p>Atención personalizada para ayudarte a gestionar la logística de tus eventos.</p>
            </div>
        </section>
    </main>

<?php require VIEW_PATH . '/include/page_end.php'; ?>
