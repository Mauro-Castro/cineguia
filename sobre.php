<?php
declare(strict_types=1);

$paginaAtual = 'sobre';
$tituloPagina = 'Sobre o projeto';
$descricaoPagina = 'Documentação do CineGuia para apresentação acadêmica.';

require_once __DIR__ . '/includes/header.php';
?>

<article class="sobre">
    <header class="pagina-cabecalho">
        <h1>Sobre o CineGuia</h1>
        <p>Site de filmes e séries inspirado no IMDb, desenvolvido para apresentação acadêmica com HTML, CSS, JavaScript e PHP.</p>
    </header>

    <section>
        <h2>Objetivo</h2>
        <p>
            Demonstrar, de forma clara, como um front-end estático se conecta a um back-end em PHP:
            listagem, busca, filtros, página de detalhes, persistência de avaliações em JSON
            e uma lista pessoal no navegador.
        </p>
    </section>

    <section>
        <h2>Tecnologias</h2>
        <ul>
            <li><strong>HTML:</strong> páginas semânticas (<code>header</code>, <code>nav</code>, <code>main</code>, <code>article</code>).</li>
            <li><strong>CSS:</strong> layout responsivo, tema escuro e identidade visual próxima ao IMDb.</li>
            <li><strong>JavaScript:</strong> menu mobile, lista pessoal, filtros ao vivo no catálogo.</li>
            <li><strong>PHP:</strong> leitura do catálogo, filtros no servidor, API JSON e gravação de avaliações.</li>
        </ul>
    </section>

    <section>
        <h2>Como os dados circulam</h2>
        <ol>
            <li>O arquivo <code>data/titulos.json</code> é o “banco” do catálogo.</li>
            <li><code>includes/funcoes.php</code> lê, filtra e formata esses dados.</li>
            <li>As páginas PHP montam o HTML no servidor (SSR).</li>
            <li><code>api/titulos.php</code> devolve JSON para o JavaScript atualizar o catálogo sem recarregar.</li>
            <li>O formulário de avaliação envia POST para <code>api/avaliacoes.php</code>, que grava em <code>data/avaliacoes.json</code>.</li>
            <li>A “Minha lista” usa <code>localStorage</code> — cada visitante vê só o que salvou no próprio navegador.</li>
        </ol>
    </section>

    <section>
        <h2>Funcionalidades para demonstrar</h2>
        <ul>
            <li>Home com destaque, filmes, séries e ordenação por nota.</li>
            <li>Catálogo com busca, tipo, gênero e ordem (funciona com e sem JS).</li>
            <li>Ficha do título com elenco, gêneros, semelhantes e avaliações.</li>
            <li>Pôster gerado em SVG pelo PHP (<code>poster.php</code>), sem imagens externas.</li>
            <li>Página de documentação (esta) e arquivo <code>DOCUMENTACAO.md</code> na raiz.</li>
        </ul>
    </section>

    <section>
        <h2>Limitações conscientes</h2>
        <p>
            Não há login, banco SQL nem API de filmes reais. As notas editoriais são fictícias/didáticas.
            O objetivo é explicar a arquitetura, não competir com um produto comercial.
        </p>
    </section>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
