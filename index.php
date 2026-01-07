<?php
// index.php - Página principal do site com conteúdo totalmente dinâmico
require_once 'config.php';

// Verificar se o banco está funcionando
if ($db === null) {
    die("Erro: Banco de dados não inicializado. Verifique o arquivo config.php");
}

// Buscar dados do site
try {
    $stmt = $db->query("SELECT * FROM site_config WHERE id = 1");
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$config) {
        $config = [
            'site_title' => 'Meu Site',
            'contato_email' => '',
            'contato_telefone' => '',
            'service_cards' => '[]',
            'about_stats' => '[]'
        ];
    }

    // Decodificar JSON
    $serviceCards = json_decode($config['service_cards'] ?? '[]', true) ?: [];
    $aboutStats = json_decode($config['about_stats'] ?? '[]', true) ?: [];

    $stmt = $db->query("SELECT * FROM carousel_items ORDER BY ordem");
    $carousel = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->query("SELECT * FROM sections ORDER BY ordem");
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['site_title'] ?? 'Meu Site') ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="container">
                <div class="logo"><?= htmlspecialchars($config['site_title'] ?? 'Logo') ?></div>
                <ul class="nav-menu">
                    <li><a href="#home">Início</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#servicos">Serviços</a></li>
                    <li><a href="#contato">Contato</a></li>
                    <li><a href="admin.php" class="admin-link" title="Painel Admin">🔒</a></li>
                </ul>
                <div class="burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Carousel -->
    <?php if (count($carousel) > 0): ?>
        <section id="home" class="carousel-section">
            <div class="carousel">
                <?php foreach ($carousel as $index => $item): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="Banner">
                        <div class="carousel-caption">
                            <h1><?= htmlspecialchars($item['titulo']) ?></h1>
                            <p><?= htmlspecialchars($item['texto']) ?></p>
                            <?php if ($item['botao_texto']): ?>
                                <a href="<?= htmlspecialchars($item['botao_link']) ?>" class="btn"><?= htmlspecialchars($item['botao_texto']) ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($carousel) > 1): ?>
                <button class="carousel-control prev" onclick="moveSlide(-1)">&#10094;</button>
                <button class="carousel-control next" onclick="moveSlide(1)">&#10095;</button>
                <div class="carousel-dots">
                    <?php foreach ($carousel as $index => $item): ?>
                        <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index ?>)"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <!-- Seção Sobre Estilizada -->
    <?php
    $aboutSection = null;
    foreach ($sections as $section) {
        if ($section['slug'] === 'sobre') {
            $aboutSection = $section;
            break;
        }
    }
    if ($aboutSection):
    ?>
        <section id="sobre" class="content-section about-section">
            <div class="container">
                <h2><?= htmlspecialchars($aboutSection['titulo']) ?></h2>
                <p class="section-subtitle">Conheça nossa história e valores</p>

                <div class="about-content">
                    <div>
                        <p class="about-text"><?= nl2br(htmlspecialchars($aboutSection['conteudo'])) ?></p>

                        <?php if (count($aboutStats) > 0): ?>
                            <div class="about-stats">
                                <?php foreach ($aboutStats as $stat): ?>
                                    <div class="stat-card">
                                        <span class="stat-number"><?= htmlspecialchars($stat['number']) ?></span>
                                        <span class="stat-label"><?= htmlspecialchars($stat['label']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($aboutSection['imagem']): ?>
                        <div class="about-image">
                            <img src="<?= htmlspecialchars($aboutSection['imagem']) ?>" alt="Sobre nós">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Seção de Serviços com Cards Dinâmicos -->
    <?php
    $servicesSection = null;
    foreach ($sections as $section) {
        if ($section['slug'] === 'servicos') {
            $servicesSection = $section;
            break;
        }
    }
    if ($servicesSection):
    ?>
        <section id="servicos" class="content-section services-section">
            <div class="container">
                <h2><?= htmlspecialchars($servicesSection['titulo']) ?></h2>

                <?php if (!empty($servicesSection['conteudo'])): ?>
                    <p class="section-subtitle"><?= nl2br(htmlspecialchars($servicesSection['conteudo'])) ?></p>
                <?php endif; ?>

                <?php if (count($serviceCards) > 0): ?>
                    <div class="services-grid">
                        <?php foreach ($serviceCards as $card): ?>
                            <div class="service-card">
                                <div class="service-icon"><?= $card['icon'] ?></div>
                                <h3><?= htmlspecialchars($card['title']) ?></h3>
                                <p><?= htmlspecialchars($card['description']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">Nenhum serviço cadastrado ainda.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Outras Seções Dinâmicas -->
    <?php foreach ($sections as $section):
        if ($section['slug'] === 'sobre' || $section['slug'] === 'servicos') continue;
    ?>
        <section id="<?= htmlspecialchars($section['slug']) ?>" class="content-section <?= $section['slug'] === 'contato' ? 'contact-section' : '' ?>">
            <div class="container">
                <h2><?= htmlspecialchars($section['titulo']) ?></h2>

                <?php if ($section['slug'] === 'contato'): ?>
                    <p class="section-subtitle">Entre em contato conosco</p>
                    <div class="contact-info">
                        <div class="contact-card">
                            <div class="contact-icon">📧</div>
                            <h3>Email</h3>
                            <p><?= htmlspecialchars($config['contato_email'] ?? 'contato@empresa.com') ?></p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📱</div>
                            <h3>Telefone</h3>
                            <p><?= htmlspecialchars($config['contato_telefone'] ?? '(11) 99999-9999') ?></p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <h3>Localização</h3>
                            <p>São Paulo, SP</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($section['imagem']): ?>
                        <div class="section-with-image">
                            <div class="section-text">
                                <div class="section-content"><?= nl2br(htmlspecialchars($section['conteudo'])) ?></div>
                            </div>
                            <div class="section-image">
                                <img src="<?= htmlspecialchars($section['imagem']) ?>" alt="<?= htmlspecialchars($section['titulo']) ?>">
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="section-content" style="text-align: center; max-width: 800px; margin: 0 auto;">
                            <?= nl2br(htmlspecialchars($section['conteudo'])) ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><?= htmlspecialchars($config['site_title'] ?? 'Logo') ?></h3>
                    <p>Transformando ideias em soluções inovadoras. Comprometidos com a excelência e satisfação dos nossos clientes.</p>
                </div>

                <div class="footer-links">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="#home">Início</a></li>
                        <li><a href="#sobre">Sobre</a></li>
                        <li><a href="#servicos">Serviços</a></li>
                        <li><a href="#contato">Contato</a></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Contato</h4>
                    <ul>
                        <li><a href="mailto:<?= htmlspecialchars($config['contato_email'] ?? 'contato@empresa.com') ?>"><?= htmlspecialchars($config['contato_email'] ?? 'contato@empresa.com') ?></a></li>
                        <li><a href="tel:<?= htmlspecialchars($config['contato_telefone'] ?? '') ?>"><?= htmlspecialchars($config['contato_telefone'] ?? '(11) 99999-9999') ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($config['site_title'] ?? '') ?>. Todos os direitos reservados.</p>
                <p>Desenvolvido com dedicação e tecnologia de ponta</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Flutuante -->
    <a href="https://wa.me/5511951552819" target="_blank" class="whatsapp-float" title="Fale conosco pelo WhatsApp">
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 0c-8.837 0-16 7.163-16 16 0 2.825 0.737 5.607 2.137 8.048l-2.137 7.952 7.933-2.127c2.42 1.37 5.173 2.127 8.067 2.127 8.837 0 16-7.163 16-16s-7.163-16-16-16zM16 29.467c-2.482 0-4.908-0.646-7.07-1.87l-0.507-0.292-4.713 1.262 1.262-4.669-0.292-0.508c-1.207-2.100-1.847-4.507-1.847-6.924 0-7.435 6.050-13.485 13.485-13.485s13.485 6.050 13.485 13.485c0 7.436-6.050 13.486-13.485 13.486zM21.960 18.981c-0.305-0.153-1.801-0.889-2.081-0.99s-0.483-0.153-0.686 0.153c-0.203 0.305-0.787 0.99-0.964 1.194s-0.356 0.229-0.661 0.076c-0.305-0.153-1.289-0.475-2.454-1.513-0.907-0.809-1.519-1.807-1.696-2.112s-0.018-0.469 0.134-0.621c0.138-0.137 0.305-0.356 0.458-0.534s0.203-0.305 0.305-0.509c0.102-0.203 0.051-0.381-0.025-0.534s-0.686-1.653-0.940-2.263c-0.248-0.594-0.499-0.513-0.686-0.522-0.178-0.008-0.381-0.010-0.584-0.010s-0.533 0.076-0.812 0.381c-0.279 0.305-1.065 1.041-1.065 2.539s1.090 2.945 1.243 3.148c0.153 0.203 2.156 3.289 5.223 4.611 0.729 0.314 1.299 0.502 1.743 0.643 0.732 0.229 1.398 0.197 1.925 0.120 0.587-0.088 1.801-0.736 2.055-1.447s0.254-1.320 0.178-1.447c-0.076-0.127-0.279-0.203-0.584-0.356z" />
        </svg>
    </a>

    <!-- Elementos animados de fundo -->
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <script src="script.js"></script>
</body>

</html>