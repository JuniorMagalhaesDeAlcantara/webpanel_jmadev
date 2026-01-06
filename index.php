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
} catch(PDOException $e) {
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
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
                    <li><a href="admin.php" class="admin-link" title="Acesso Administrativo">🔒</a></li>
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
            <p class="section-subtitle"><?= htmlspecialchars(substr($servicesSection['conteudo'], 0, 150)) ?><?= strlen($servicesSection['conteudo']) > 150 ? '...' : '' ?></p>
            
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
            <p style="font-size: 1.1rem; margin-bottom: 1rem;">&copy; <?= date('Y') ?> <?= htmlspecialchars($config['site_title'] ?? '') ?>. Todos os direitos reservados.</p>
            <p><?= htmlspecialchars($config['contato_email'] ?? '') ?> | <?= htmlspecialchars($config['contato_telefone'] ?? '') ?></p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>