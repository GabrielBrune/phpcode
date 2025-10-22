<?php
// conexao com o banco de dados
require("../banco/conecta_banco.php");

// consulta registros da tabela propriedade
$query = $mysqli->query("SELECT * FROM propriedade");
echo $mysqli->error;

// criação de uma variavel para separar o php do html
$listaprops = "";

// carrega consulta de registros
while ($tabela = $query->fetch_assoc()) {
    $idprod       = $tabela['idprod'];
    $propriedade  = htmlspecialchars($tabela['propriedade']);
    $proprietario = htmlspecialchars($tabela['proprietário']);
    $area         = htmlspecialchars($tabela['área']);
    $cultura      = htmlspecialchars($tabela['cultura']);

    $listaprops .= "
        <tr>
            <td align='center'>$propriedade</td>
            <td align='center'>$proprietario</td>
            <td align='center'>$area</td>
            <td align='center'>$cultura</td>
            <td>
                <button class='button'
                    onclick=\"alterarPropriedade('$idprod','$propriedade','$proprietario','$area','$cultura')\">
                    Alterar
                </button>
                <button class='button'
                    onclick=\"excluirPropriedade('$idprod')\">
                    Excluir
                </button>
            </td>
        </tr>
    ";
}

// Botao de adicionar propriedade
if (isset($_POST["salvar"])) {
    require("../banco/conecta_banco.php");

    $propriedade  = $mysqli->real_escape_string($_POST['propriedade']);
    $proprietario = $mysqli->real_escape_string($_POST['proprietario']);
    $area         = intval($_POST['area']);
    $cultura      = $mysqli->real_escape_string($_POST['cultura']);

    $mysqli->query("INSERT INTO propriedade VALUES (
            '',
            '$propriedade',
            '$proprietario',
            '$area',
            '$cultura'
        )");

    echo $mysqli->error;

    if ($mysqli->error == "") {
        header("Location: propriedade.php?adicionado=1");
        exit;
    } else {
        echo "Erro ao adicionar: " . $mysqli->error;
    }
}

// Recebendo por metodo post alteração da propriedade
if (isset($_POST["alterar"])) {
    require("../banco/conecta_banco.php");

    $idprod       = intval($_POST["idprod"]);
    $propriedade  = $mysqli->real_escape_string($_POST['propriedade']);
    $proprietario = $mysqli->real_escape_string($_POST['proprietario']);
    $area         = intval($_POST['area']);
    $cultura      = $mysqli->real_escape_string($_POST['cultura']);

    // usar crases nas colunas com acento
    $sql = "UPDATE propriedade SET 
                propriedade = '$propriedade',
                `proprietário` = '$proprietario',
                `área` = '$area',
                cultura = '$cultura'
            WHERE idprod = $idprod";

    $mysqli->query($sql);

    if ($mysqli->error == "") {
        header("Location: propriedade.php?alterado=1");
        exit;
    } else {
        echo "Erro ao atualizar: " . $mysqli->error;
    }
}

// Excluindo propriedade
if (isset($_POST["exclusao"])) {
    $idprod = intval($_POST["idprod"]);
    $mysqli->query("DELETE FROM propriedade WHERE idprod = $idprod");

    if ($mysqli->error == "") {
        header("Location: propriedade.php?excluido=1");
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
    <h2>Cadastro de Propriedades</h2>
    <div>
        <button class="botao" onclick="addPropriedade()">Adicionar</button>
        <button class="botao" onclick="buscarPropriedade()">Pesquisar</button>
    </div>
    <br />
    <table class="tabletitle">
        <tr>
            <th>Propriedade</th>
            <th>Proprietário</th>
            <th>Área</th>
            <th>Cultura</th>
            <th>Ação</th>
        </tr>
        <?php echo $listaprops; ?>
    </table>

    <!-- Div Adicionar Propriedade -->
    <div class="flutuante" id="Adicionar" style="display:none;">
        <form action="propriedade.php" method="POST">
            <label for="propriedade">Propriedade</label>
            <input id="propriedade" name="propriedade" type="text" maxlength="40" required><br>

            <label for="proprietario">Proprietário</label>
            <input id="proprietario" name="proprietario" type="text" maxlength="60" required><br>

            <label for="area">Área</label>
            <input id="area" name="area" type="number" required><br>

            <label for="cultura">Cultura</label>
            <input id="cultura" name="cultura" type="text" maxlength="60"><br>

            <input class="botao" type="submit" value="Salvar" name="salvar"><br>
        </form>
    </div>

    <!-- Div Pesquisar Propriedade -->
    <div class="flutuante" id="Pesquisar" style="display:none;">
        <label for="busca">Pesquisar:</label>
        <input type="text" id="busca" placeholder="Digite propriedade, proprietário, cultura..."><br>
        <div>
            <button class="botao" onclick="buscarNaTabela()">Buscar</button>
            <button class="botao" type="button" onclick="location.reload()">Sair</button>
        </div>
    </div>

    <!-- Div Alterar Propriedade -->
    <div class="flutuante" id="Alterar" style="display:none;">
        <form method="POST" action="propriedade.php">
            <input type="hidden" name="idprod" id="alt_idprod">

            <label>Propriedade</label>
            <input id="alt_propriedade" name="propriedade" type="text" maxlength="40"><br>

            <label>Proprietário</label>
            <input id="alt_proprietario" name="proprietario" type="text" maxlength="60"><br>

            <label>Área</label>
            <input id="alt_area" name="area" type="number"><br>

            <label>Cultura</label>
            <input id="alt_cultura" name="cultura" type="text" maxlength="60"><br>

            <input class="botao" type="submit" value="Salvar" name="alterar">
        </form>
    </div>
    
    <!-- Div Excluir Propriedade -->
    <div class="flutuante" id="Excluir" style="display:none;">
        <form method="POST" action="propriedade.php" style="text-align:center; padding:20px;">
            <input type="hidden" name="idprod" id="exc_idprod">

            <p>Deseja realmente excluir ?</p>
            <div>
            <button type="submit" name="exclusao" class="botao">Sim</button>
            <button type="button" class="botao" onclick="location.reload()">Não</button>
            </div>
        </form>
    </div>

</body>
<script>
    function addPropriedade() {
        var element = document.getElementById("Adicionar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function buscarPropriedade() {
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

    function alterarPropriedade(id, propriedade, proprietario, area, cultura) {
        document.getElementById("alt_idprod").value = id;
        document.getElementById("alt_propriedade").value = propriedade;
        document.getElementById("alt_proprietario").value = proprietario;
        document.getElementById("alt_area").value = area;
        document.getElementById("alt_cultura").value = cultura;

        var element = document.getElementById("Alterar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function excluirPropriedade(idprod) {
        document.getElementById("exc_idprod").value = idprod;
        var element = document.getElementById("Excluir");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
</script>

</html>