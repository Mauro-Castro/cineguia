# CineGuia

Catálogo de filmes e séries no estilo IMDb, feito **somente** com HTML, CSS, JavaScript e PHP.

Projeto acadêmico: o funcionamento detalhado, o roteiro de apresentação e os diagramas estão em [`DOCUMENTACAO.md`](DOCUMENTACAO.md).

## Como executar (Windows)

### Opção 1 — XAMPP (mais comum em faculdade)

1. Instale o [XAMPP](https://www.apachefriends.org/).
2. Copie esta pasta para `C:\xampp\htdocs\filme`.
3. Abra o painel do XAMPP e inicie o **Apache**.
4. No navegador: [http://localhost/filme/index.php](http://localhost/filme/index.php).

A pasta `data/` precisa ter permissão de escrita para as avaliações serem gravadas.

### Opção 2 — servidor embutido do PHP

Se o `php` estiver no PATH:

```bat
iniciar.bat
```

Ou no terminal, dentro desta pasta:

```bat
php -S localhost:8000
```

Abra [http://localhost:8000/index.php](http://localhost:8000/index.php).

## Páginas

| Arquivo | Função |
| --- | --- |
| `index.php` | Home com destaques |
| `catalogo.php` | Busca e filtros |
| `titulo.php` | Ficha + avaliações |
| `lista.php` | Minha lista (navegador) |
| `sobre.php` | Resumo do projeto |
