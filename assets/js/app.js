/**
 * Interações do CineGuia.
 * - Menu mobile
 * - Minha lista (localStorage)
 * - Filtros do catálogo via API PHP (com fallback no submit normal)
 */
(function () {
    "use strict";

    var CHAVE_LISTA = "cineguia-lista";

    inicializarMenu();
    atualizarBotoesLista();
    document.addEventListener("click", aoClicarLista);
    inicializarFiltrosCatalogo();
    renderizarMinhaLista();

    function inicializarMenu() {
        var botao = document.querySelector("[data-menu-botao]");
        var menu = document.querySelector("[data-menu]");
        if (!botao || !menu) {
            return;
        }

        botao.addEventListener("click", function () {
            var aberto = menu.classList.toggle("aberto");
            botao.setAttribute("aria-expanded", aberto ? "true" : "false");
        });
    }

    function lerLista() {
        try {
            var bruto = localStorage.getItem(CHAVE_LISTA);
            var dados = bruto ? JSON.parse(bruto) : [];
            return Array.isArray(dados) ? dados : [];
        } catch (erro) {
            return [];
        }
    }

    function gravarLista(ids) {
        localStorage.setItem(CHAVE_LISTA, JSON.stringify(ids));
    }

    function alternarLista(id) {
        var ids = lerLista();
        var indice = ids.indexOf(id);
        if (indice >= 0) {
            ids.splice(indice, 1);
        } else {
            ids.push(id);
        }
        gravarLista(ids);
        atualizarBotoesLista();
        renderizarMinhaLista();
    }

    function atualizarBotoesLista() {
        var ids = lerLista();
        document.querySelectorAll("[data-lista-id]").forEach(function (botao) {
            var ativo = ids.indexOf(botao.getAttribute("data-lista-id")) >= 0;
            botao.setAttribute("aria-pressed", ativo ? "true" : "false");
            botao.classList.toggle("ativo", ativo);
            var noDetalhe = botao.classList.contains("botao-secundario");
            botao.textContent = ativo
                ? "Na lista"
                : (noDetalhe ? "+ Minha lista" : "+ Lista");
        });
    }

    function aoClicarLista(evento) {
        var botao = evento.target.closest("[data-lista-id]");
        if (!botao) {
            return;
        }
        evento.preventDefault();
        alternarLista(botao.getAttribute("data-lista-id"));
    }

    function inicializarFiltrosCatalogo() {
        var form = document.querySelector("[data-filtros]");
        var grade = document.querySelector("[data-grade-catalogo]");
        if (!form || !grade) {
            return;
        }

        var tempo;
        form.addEventListener("input", function () {
            clearTimeout(tempo);
            tempo = setTimeout(buscarCatalogo, 280);
        });
        form.addEventListener("change", buscarCatalogo);
        form.addEventListener("submit", function (evento) {
            evento.preventDefault();
            buscarCatalogo();
        });
    }

    function buscarCatalogo() {
        var form = document.querySelector("[data-filtros]");
        var grade = document.querySelector("[data-grade-catalogo]");
        var contador = document.querySelector("[data-contador]");
        if (!form || !grade) {
            return;
        }

        var dados = new FormData(form);
        var params = new URLSearchParams(dados);
        var url = "api/titulos.php?" + params.toString();

        fetch(url)
            .then(function (resposta) {
                if (!resposta.ok) {
                    throw new Error("Falha na API");
                }
                return resposta.json();
            })
            .then(function (json) {
                grade.innerHTML = montarCartoes(json.resultados || []);
                if (contador) {
                    var total = json.total || 0;
                    contador.textContent = total + (total === 1 ? " resultado" : " resultados");
                }
                atualizarBotoesLista();
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, "", "catalogo.php?" + params.toString());
                }
            })
            .catch(function () {
                form.submit();
            });
    }

    function montarCartoes(itens) {
        if (!itens.length) {
            return '<p class="vazio">Nenhum título encontrado. Tente outro termo ou limpe os filtros.</p>';
        }

        return itens.map(function (item) {
            var generos = (item.generos || []).join(" · ");
            var nota = Number(item.nota).toFixed(1).replace(".", ",");
            return (
                '<article class="cartao">' +
                    '<a class="cartao-link" href="' + escapar(item.url) + '">' +
                        '<div class="cartao-poster">' +
                            '<img src="' + escapar(item.poster) + '" alt="Pôster de ' + escapar(item.titulo) + '" width="300" height="450">' +
                            '<span class="selo-nota">★ ' + escapar(nota) + "</span>" +
                        "</div>" +
                        '<div class="cartao-corpo">' +
                            "<h3>" + escapar(item.titulo) + "</h3>" +
                            '<p class="cartao-meta">' + escapar(item.tipo_rotulo) + " · " + escapar(String(item.ano)) + "</p>" +
                            '<p class="cartao-generos">' + escapar(generos) + "</p>" +
                        "</div>" +
                    "</a>" +
                    '<button type="button" class="botao-lista" data-lista-id="' + escapar(item.id) + '" aria-pressed="false">+ Lista</button>' +
                "</article>"
            );
        }).join("");
    }

    function renderizarMinhaLista() {
        var grade = document.querySelector("[data-minha-lista]");
        var contador = document.querySelector("[data-lista-contador]");
        var blocoJson = document.getElementById("catalogo-json");
        if (!grade || !blocoJson) {
            return;
        }

        var catalogo;
        try {
            catalogo = JSON.parse(blocoJson.textContent);
        } catch (erro) {
            catalogo = [];
        }

        var ids = lerLista();
        var itens = catalogo.filter(function (item) {
            return ids.indexOf(item.id) >= 0;
        }).map(function (item) {
            return {
                id: item.id,
                titulo: item.titulo,
                tipo: item.tipo,
                tipo_rotulo: item.tipo === "serie" ? "Série" : "Filme",
                ano: item.ano,
                nota: item.nota,
                generos: item.generos,
                poster: "poster.php?id=" + encodeURIComponent(item.id),
                url: "titulo.php?id=" + encodeURIComponent(item.id)
            };
        });

        grade.innerHTML = itens.length
            ? montarCartoes(itens)
            : '<p class="vazio">Sua lista está vazia. Abra um título e toque em “+ Lista”.</p>';

        if (contador) {
            contador.textContent = itens.length + (itens.length === 1 ? " título salvo" : " títulos salvos");
        }
        atualizarBotoesLista();
    }

    function escapar(texto) {
        return String(texto)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }
})();
