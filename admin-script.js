// admin-script.js - Scripts do painel administrativo

// Funções para Carousel
function showCarouselForm() {
    document.getElementById('carouselForm').classList.add('active');
    document.getElementById('carouselFormTitle').textContent = 'Adicionar Banner';
    document.getElementById('carousel_id').value = '';
    document.getElementById('carousel_titulo').value = '';
    document.getElementById('carousel_texto').value = '';
    document.getElementById('carousel_imagem').value = '';
    document.getElementById('carousel_botao_texto').value = '';
    document.getElementById('carousel_botao_link').value = '';
    document.getElementById('carousel_ordem').value = '1';
    document.getElementById('carouselSubmitBtn').name = 'add_carousel';
    document.getElementById('carouselSubmitBtn').textContent = 'Adicionar';
}

function closeCarouselForm() {
    document.getElementById('carouselForm').classList.remove('active');
}

function editCarousel(item) {
    document.getElementById('carouselForm').classList.add('active');
    document.getElementById('carouselFormTitle').textContent = 'Editar Banner';
    document.getElementById('carousel_id').value = item.id;
    document.getElementById('carousel_titulo').value = item.titulo;
    document.getElementById('carousel_texto').value = item.texto;
    document.getElementById('carousel_imagem').value = item.imagem;
    document.getElementById('carousel_botao_texto').value = item.botao_texto || '';
    document.getElementById('carousel_botao_link').value = item.botao_link || '';
    document.getElementById('carousel_ordem').value = item.ordem;
    document.getElementById('carouselSubmitBtn').name = 'update_carousel';
    document.getElementById('carouselSubmitBtn').textContent = 'Atualizar';
}

// Funções para Seções
function showSectionForm() {
    document.getElementById('sectionForm').classList.add('active');
    document.getElementById('sectionFormTitle').textContent = 'Adicionar Seção';
    document.getElementById('section_id').value = '';
    document.getElementById('section_titulo').value = '';
    document.getElementById('section_slug').value = '';
    document.getElementById('section_conteudo').value = '';
    document.getElementById('section_imagem').value = '';
    document.getElementById('section_ordem').value = '1';
    document.getElementById('sectionSubmitBtn').name = 'add_section';
    document.getElementById('sectionSubmitBtn').textContent = 'Adicionar';
}

function closeSectionForm() {
    document.getElementById('sectionForm').classList.remove('active');
}

function editSection(item) {
    document.getElementById('sectionForm').classList.add('active');
    document.getElementById('sectionFormTitle').textContent = 'Editar Seção';
    document.getElementById('section_id').value = item.id;
    document.getElementById('section_titulo').value = item.titulo;
    document.getElementById('section_slug').value = item.slug;
    document.getElementById('section_conteudo').value = item.conteudo;
    document.getElementById('section_imagem').value = item.imagem || '';
    document.getElementById('section_ordem').value = item.ordem;
    document.getElementById('sectionSubmitBtn').name = 'update_section';
    document.getElementById('sectionSubmitBtn').textContent = 'Atualizar';
}

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const carouselModal = document.getElementById('carouselForm');
    const sectionModal = document.getElementById('sectionForm');
    
    if (event.target === carouselModal) {
        closeCarouselForm();
    }
    if (event.target === sectionModal) {
        closeSectionForm();
    }
}

// Auto-gerar slug a partir do título
document.getElementById('section_titulo').addEventListener('input', function(e) {
    const slugInput = document.getElementById('section_slug');
    if (!slugInput.value || slugInput.value === '') {
        const slug = e.target.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});