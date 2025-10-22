<?php
// conexao com o banco de dados
require("../banco/conecta_banco.php");

// executar comandos sql
// consulta registros da tabela vendas
$query = $mysqli->query("select * from vendas");
echo $mysqli->error;

// criação de uma variavel para separar o php do html
$listavendas = "";

// carrega consulta de registros
while ($tabela = $query->fetch_assoc()) {
    $idvenda    = $tabela['idvenda'];
    $data_venda = htmlspecialchars($tabela['data_venda']);
    $idcli      = htmlspecialchars($tabela['idcli']);
    $produto    = htmlspecialchars($tabela['produto']);
    $quantidade = htmlspecialchars($tabela['quantidade']);
    $valortotal = htmlspecialchars($tabela['valortotal']);

    $listavendas .= "
        <tr>
        <td align='center'>$data_venda</td>
        <td align='center'>$idcli</td>
        <td align='center'>$produto</td>
        <td align='center'>$quantidade</td>
        <td align='center'>$valortotal</td>
        <td>
            <button class='button'
                onclick=\"alterarVenda('$idvenda','$data_venda','$idcli','$produto','$quantidade','$valortotal')\">
                Alterar
            </button>
            <button class='button'
                onclick=\"excluirVenda('$idvenda')\">
                Excluir
            </button>
        </td>
    </tr>
    ";
}

// Botao de adicionar vendas
if (isset($_POST["salvar"])) {
    require("../banco/conecta_banco.php");

    $data_venda = $mysqli->real_escape_string($_POST['data_venda']);
    $idcli      = intval($_POST['idcli']);
    $produto    = $mysqli->real_escape_string($_POST['produto']);
    $quantidade = intval($_POST['quantidade']);
    $valortotal = number_format(floatval($_POST['valortotal']), 2, '.', '');

    $mysqli->query("INSERT INTO vendas VALUES (
            '',
            '$data_venda',
            '$idcli',
            '$produto',
            '$quantidade',
            '$valortotal'
        )");

    echo $mysqli->error;

    if ($mysqli->error == "") {
        header("Location: vendas.php?adicionado=1");
    }
}

// Recebendo por metodo post alteração da venda
if (isset($_POST["alterar"])) {
    require("../banco/conecta_banco.php");

    $idvenda    = intval($_POST["idvenda"]);
    $data_venda = $mysqli->real_escape_string($_POST["data_venda"]);
    $idcli      = intval($_POST["idcli"]);
    $produto    = $mysqli->real_escape_string($_POST["produto"]);
    $quantidade = intval($_POST["quantidade"]);
    $valortotal = number_format(floatval($_POST["valortotal"]), 2, '.', '');

    $sql = "UPDATE vendas SET 
                data_venda = '$data_venda',
                idcli = '$idcli',
                produto = '$produto',
                quantidade = '$quantidade',
                valortotal = '$valortotal'
            WHERE idvenda = $idvenda";

    $mysqli->query($sql);

    if ($mysqli->error == "") {
        header("Location: vendas.php?alterado=1");
        exit;
    } else {
        echo "Erro ao atualizar: " . $mysqli->error;
    }
}

// Excluindo venda
if (isset($_POST["exclusao"])) {
    $idvenda = intval($_POST["idvenda"]);
    $mysqli->query("DELETE FROM vendas WHERE idvenda = $idvenda");

    if ($mysqli->error == "") {
        header("Location: vendas.php?excluido=1");
        exit;
    } else {
        echo "Erro ao excluir: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <button class="botao"><a href="../index.php">Voltar</a></button>
    <h2>Cadastro de Vendas</h2>
    <div>
        <button class="botao" onclick="addVenda()">Adicionar</button>
        <button class="botao" onclick="buscarVenda()">Pesquisar</button>
    </div>
    <br />
    <table class="tabletitle">
        <tr>
            <th>Data</th>
            <th>ID Cliente</th>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Valor Total</th>
            <th>Ação</th>
        </tr>
        <?php echo $listavendas; ?>
    </table>

    <!-- Div Adicionar Venda -->
    <div class="flutuante" id="Adicionar">
        <form action="vendas.php" method="POST">
            <label for="data_venda">Data</label>
            <input id="data_venda" name="data_venda" type="date" required><br>

            <label for="idcli">ID Cliente</label>
            <input id="idcli" name="idcli" type="number" required><br>

            <label for="produto">Produto</label>
            <input id="produto" name="produto" type="text" maxlength="100" required><br>

            <label for="quantidade">Quantidade</label>
            <input id="quantidade" name="quantidade" type="number" required><br>

            <label for="valortotal">Valor Total</label>
            <input id="valortotal" name="valortotal" type="number" step="0.01" required><br>

            <input class="botao" type="submit" value="Salvar" name="salvar"><br>
        </form>
    </div>

    <!-- Div Pesquisar Venda -->
    <div class="flutuante" id="Pesquisar">
        <label for="busca">Pesquisar:</label>
        <input type="text" id="busca" placeholder="Digite nome, Cliente,..."><br>
        <div>
            <button class="botao" onclick="buscarNaTabela()">Buscar</button>
            <button class="botao" type="button" onclick="location.reload()">Sair</button>
        </div>
    </div>

    <!-- Div Alterar Venda -->
    <div class="flutuante" id="Alterar" style="display:none;">
        <form method="POST" action="vendas.php">
            <input type="hidden" name="idvenda" id="alt_idvenda">

            <label>Data</label>
            <input id="alt_data" name="data_venda" type="date"><br>

            <label>ID Cliente</label>
            <input id="alt_idcli" name="idcli" type="number"><br>

            <label>Produto</label>
            <input id="alt_produto" name="produto" type="text" maxlength="100"><br>

            <label>Quantidade</label>
            <input id="alt_quantidade" name="quantidade" type="number"><br>

            <label>Valor Total</label>
            <input id="alt_valor" name="valortotal" type="number" step="0.01"><br>

            <input class="botao" type="submit" value="Salvar" name="alterar">
        </form>
    </div>
    
    <!-- Div Excluir Venda -->
    <div class="flutuante" id="Excluir">
        <form method="POST" action="vendas.php" style="text-align:center; padding:20px;">
            <input type="hidden" name="idvenda" id="exc_idvenda">

            <p>Deseja realmente excluir ?</p>
            <div>
            <button type="submit" name="exclusao" class="botao">Sim</button>
            <button type="button" class="botao" onclick="location.reload()">Não</button>
            </div>
        </form>
    </div>

</body>
<script>
    function addVenda() {
        var element = document.getElementById("Adicionar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function buscarVenda() {
        var element = document.getElementById("Pesquisar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function buscarNaTabela() {
        var input = document.getElementById("busca").value.toLowerCase();
        var tabela = document.querySelector(".tabletitle");
        var linhas = tabela.getElementsByTagName("tr");

        for (var i = 1; i < linhas.length; i++) { // começa em 1 para pular o cabeçalho
            var colunas = linhas[i].getElementsByTagName("td");
            var achou = false;

            for (var j = 0; j < colunas.length - 1; j++) { // ignora a coluna de ações
                if (colunas[j].innerText.toLowerCase().indexOf(input) > -1) {
                    achou = true;
                    break;
                }
            }
            linhas[i].style.display = achou ? "" : "none";
        }
    }

    function alterarVenda(id, data, idcli, produto, quantidade, valor) {
        document.getElementById("alt_idvenda").value = id;
        document.getElementById("alt_data").value = data;
        document.getElementById("alt_idcli").value = idcli;
        document.getElementById("alt_produto").value = produto;
        document.getElementById("alt_quantidade").value = quantidade;
        document.getElementById("alt_valor").value = valor;

        var element = document.getElementById("Alterar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function excluirVenda(idvenda){
        document.getElementById("exc_idvenda").value = idvenda;
        var element = document.getElementById("Excluir");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
</script>

</html>