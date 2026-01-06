<?php
// config.php - Configuração do sistema
session_start();

// Inicializar variável do banco
$db = null;

// Configuração do banco de dados SQLite
try {
    $dbPath = __DIR__ . '/database.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Criar tabelas se não existirem
    $db->exec("CREATE TABLE IF NOT EXISTS site_config (
        id INTEGER PRIMARY KEY,
        site_title TEXT,
        contato_email TEXT,
        contato_telefone TEXT
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS carousel_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT,
        texto TEXT,
        imagem TEXT,
        botao_texto TEXT,
        botao_link TEXT,
        ordem INTEGER DEFAULT 0
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS sections (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT,
        slug TEXT,
        conteudo TEXT,
        imagem TEXT,
        ordem INTEGER DEFAULT 0
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT
    )");
    
    // Verificar se existe configuração inicial
    $stmt = $db->query("SELECT COUNT(*) FROM site_config");
    if ($stmt->fetchColumn() == 0) {
        // Inserir configuração padrão
        $db->exec("INSERT INTO site_config (id, site_title, contato_email, contato_telefone) 
                   VALUES (1, 'Minha Empresa', 'contato@minhaempresa.com.br', '(11) 99999-9999')");
        
        // Inserir itens do carousel padrão
        $db->exec("INSERT INTO carousel_items (titulo, texto, imagem, botao_texto, botao_link, ordem) VALUES 
                   ('Bem-vindo ao nosso site', 'Soluções profissionais para seu negócio', 'https://via.placeholder.com/1200x500/4F46E5/ffffff?text=Banner+1', 'Saiba Mais', '#sobre', 1),
                   ('Qualidade e Excelência', 'Compromisso com resultados', 'https://via.placeholder.com/1200x500/7C3AED/ffffff?text=Banner+2', 'Contato', '#contato', 2)");
        
        // Inserir seções padrão
        $db->exec("INSERT INTO sections (titulo, slug, conteudo, imagem, ordem) VALUES 
                   ('Sobre Nós', 'sobre', 'Somos uma empresa dedicada a oferecer as melhores soluções para nossos clientes. Com anos de experiência no mercado, nos destacamos pela qualidade e profissionalismo.', 'https://via.placeholder.com/400x300/10B981/ffffff?text=Sobre', 1),
                   ('Nossos Serviços', 'servicos', 'Oferecemos uma ampla gama de serviços personalizados para atender às necessidades específicas de cada cliente. Nossa equipe está sempre pronta para ajudar.', '', 2),
                   ('Entre em Contato', 'contato', 'Estamos prontos para atender você! Entre em contato conosco através dos nossos canais de comunicação e descubra como podemos ajudar seu negócio a crescer.', '', 3)");
        
        // Criar usuário admin padrão (senha: admin123)
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO admin_users (username, password) VALUES ('admin', '$password')");
    }
    
} catch(PDOException $e) {
    die("❌ Erro na conexão com banco de dados: " . $e->getMessage() . "<br><br>Verifique se o PHP tem permissão de escrita na pasta.");
}

// Função para verificar login
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Função para upload de imagens
function uploadImage($file) {
    $target_dir = "uploads/";
    
    // Criar diretório se não existir
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $imageFileType;
    $target_file = $target_dir . $newFileName;
    
    // Verificar se é uma imagem real
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false;
    }
    
    // Permitir apenas certos formatos
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        return false;
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    }
    
    return false;
}
?>