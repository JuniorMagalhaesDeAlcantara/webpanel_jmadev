<?php
// index.php - Página principal do site
require_once 'config.php';

// Buscar dados do site
$stmt = $db->query("SELECT * FROM site_config WHERE id = 1");
$config = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT * FROM carousel_items ORDER BY ordem");
$carousel = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query("SELECT * FROM sections ORDER BY ordem");
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['site_title'] ?? 'Meu Site') ?></title>
    <link rel="stylesheet" href="style.css">
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
        <button class="carousel-control prev" onclick="moveSlide(-1)">&#10094;</button>
        <button class="carousel-control next" onclick="moveSlide(1)">&#10095;</button>
        <div class="carousel-dots">
            <?php foreach ($carousel as $index => $item): ?>
            <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index ?>)"></span>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Seções Dinâmicas -->
    <?php foreach ($sections as $section): ?>
    <section id="<?= htmlspecialchars($section['slug']) ?>" class="content-section">
        <div class="container">
            <?php if ($section['imagem']): ?>
            <div class="section-with-image">
                <div class="section-text">
                    <h2><?= htmlspecialchars($section['titulo']) ?></h2>
                    <div class="section-content"><?= nl2br(htmlspecialchars($section['conteudo'])) ?></div>
                </div>
                <div class="section-image">
                    <img src="<?= htmlspecialchars($section['imagem']) ?>" alt="<?= htmlspecialchars($section['titulo']) ?>">
                </div>
            </div>
            <?php else: ?>
            <h2><?= htmlspecialchars($section['titulo']) ?></h2>
            <div class="section-content"><?= nl2br(htmlspecialchars($section['conteudo'])) ?></div>
            <?php endif; ?>
        </div>
    </section>
    <?php endforeach; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($config['site_title'] ?? '') ?>. Todos os direitos reservados.</p>
            <p><?= htmlspecialchars($config['contato_email'] ?? '') ?> | <?= htmlspecialchars($config['contato_telefone'] ?? '') ?></p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>