<?php
// conexao com o banco de dados
require("../banco/conecta_banco.php");

// consulta registros da tabela equipamentos
$query = $mysqli->query("SELECT * FROM equipamentos");
echo $mysqli->error;

// criação de uma variavel para separar o php do html
$listaequip = "";

// carrega consulta de registros
while ($tabela = $query->fetch_assoc()) {
    $idequipe    = $tabela['idequipe'];
    $equipamento = htmlspecialchars($tabela['equipamento']);
    $localizacao = htmlspecialchars($tabela['localização']);
    $custo       = htmlspecialchars($tabela['custo']);

    $listaequip .= "
        <tr>
            <td align='center'>$equipamento</td>
            <td align='center'>$localizacao</td>
            <td align='center'>$custo</td>
            <td>
                <button class='button'
                    onclick=\"alterarEquip('$idequipe','$equipamento','$localizacao','$custo')\">
                    Alterar
                </button>
                <button class='button'
                    onclick=\"excluirEquip('$idequipe')\">
                    Excluir
                </button>
            </td>
        </tr>
    ";
}

// Botao de adicionar equipamentos
if (isset($_POST["salvar"])) {
    $equipamento = $mysqli->real_escape_string($_POST['equipamento']);
    $localizacao = $mysqli->real_escape_string($_POST['localizacao']);
    $custo       = number_format(floatval($_POST['custo']), 2, '.', '');

    $mysqli->query("INSERT INTO equipamentos VALUES (
        '',
        '$equipamento',
        '$localizacao',
        '$custo'
    )");

    if ($mysqli->error == "") {
        header("Location: equip.php?adicionado=1");
        exit;
    } else {
        echo "Erro ao adicionar: " . $mysqli->error;
    }
}

// Recebendo por metodo post alteração do equipamento
if (isset($_POST["alterar"])) {
    $idequipe    = intval($_POST["idequipe"]);
    $equipamento = $mysqli->real_escape_string($_POST['equipamento']);
    $localizacao = $mysqli->real_escape_string($_POST['localizacao']);
    $custo       = number_format(floatval($_POST['custo']), 2, '.', '');

    $sql = "UPDATE equipamentos SET 
                equipamento='$equipamento',
                localização='$localizacao',
                custo='$custo'
            WHERE idequipe=$idequipe";

    $mysqli->query($sql);

    if ($mysqli->error == "") {
        header("Location: equip.php?alterado=1");
        exit;
    } else {
        echo "Erro ao atualizar: " . $mysqli->error;
    }
}

// Excluindo equipamento
if (isset($_POST["exclusao"])) {
    $idequipe = intval($_POST["idequipe"]);
    $mysqli->query("DELETE FROM equipamentos WHERE idequipe=$idequipe");

    if ($mysqli->error == "") {
        header("Location: equip.php?excluido=1");
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
    <a class="botao" href="../index.php">Voltar</a>
    <h2>Cadastro de Equipamentos</h2>
    <div>
        <button class="botao" onclick="addEquip()">Adicionar</button>
        <button class="botao" onclick="buscarEquip()">Pesquisar</button>
    </div>
    <br />
    <table class="tabletitle">
        <tr>
            <th>Equipamento</th>
            <th>Localização</th>
            <th>Custo</th>
            <th>Ação</th>
        </tr>
        <?php echo $listaequip; ?>
    </table>

    <!-- Div Adicionar Equipamento -->
    <div class="flutuante" id="Adicionar" style="display:none;">
        <form action="equip.php" method="POST">
            <label for="equipamento">Equipamento</label>
            <input id="equipamento" name="equipamento" type="text" maxlength="60" required><br>

            <label for="localizacao">Localização</label>
            <input id="localizacao" name="localizacao" type="text" maxlength="50" required><br>

            <label for="custo">Custo</label>
            <input id="custo" name="custo" type="number" step="0.01" required><br>

            <input class="botao" type="submit" value="Salvar" name="salvar"><br>
        </form>
    </div>

    <!-- Div Pesquisar Cliente -->
    <div class="flutuante" id="Pesquisar">
        <label for="busca">Pesquisar:</label>
        <input type="text" id="busca" placeholder="Digite nome, data, ..."><br>
        <div>
            <button class="botao" onclick="buscarNaTabela()">Buscar</button>
            <button class="botao" type="button" onclick="location.reload()">Sair</button>
        </div>
    </div>

    <!-- Div Alterar Equipamento -->
    <div class="flutuante" id="Alterar" style="display:none;">
        <form method="POST" action="equip.php">
            <input type="hidden" name="idequipe" id="alt_idequipe">

            <label>Equipamento</label>
            <input id="alt_equipamento" name="equipamento" type="text" maxlength="60"><br>

            <label>Localização</label>
            <input id="alt_localizacao" name="localizacao" type="text" maxlength="50"><br>

            <label>Custo</label>
            <input id="alt_custo" name="custo" type="number" step="0.01"><br>

            <input class="botao" type="submit" value="Salvar" name="alterar">
        </form>
    </div>
    
    <!-- Div Excluir Equipamento -->
    <div class="flutuante" id="Excluir" style="display:none;">
        <form method="POST" action="equip.php" style="text-align:center; padding:20px;">
            <input type="hidden" name="idequipe" id="exc_idequipe">

            <p>Deseja realmente excluir ?</p>
            <div>
            <button type="submit" name="exclusao" class="botao">Sim</button>
            <button type="button" class="botao" onclick="location.reload()">Não</button>
            </div>
        </form>
    </div>

</body>
<script>
    function addEquip() {
        var element = document.getElementById("Adicionar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function buscarEquip() {
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

    function alterarEquip(id, equipamento, localizacao, custo) {
        document.getElementById("alt_idequipe").value = id;
        document.getElementById("alt_equipamento").value = equipamento;
        document.getElementById("alt_localizacao").value = localizacao;
        document.getElementById("alt_custo").value = custo;

        var element = document.getElementById("Alterar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function excluirEquip(id) {
        document.getElementById("exc_idequipe").value = id;
        var element = document.getElementById("Excluir");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
</script>

</html>