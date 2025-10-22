<?php
// conexao com o banco de dados
require("../banco/conecta_banco.php");

// consulta registros da tabela culturas
$query = $mysqli->query("SELECT * FROM culturas");
echo $mysqli->error;

// criação de uma variavel para separar o php do html
$listaculturas = "";

// carrega consulta de registros
while ($tabela = $query->fetch_assoc()) {
    $idcultura = $tabela['idcultura'];
    $cultura   = htmlspecialchars($tabela['cultura']);
    $variedade = htmlspecialchars($tabela['variedade']);
    $ciclo     = htmlspecialchars($tabela['ciclo']);
    $colheita  = htmlspecialchars($tabela['colheita']);

    $listaculturas .= "
        <tr>
            <td align='center'>$cultura</td>
            <td align='center'>$variedade</td>
            <td align='center'>$ciclo</td>
            <td align='center'>$colheita</td>
            <td>
                <button class='button'
                    onclick=\"alterarCultura('$idcultura','$cultura','$variedade','$ciclo','$colheita')\">
                    Alterar
                </button>
                <button class='button'
                    onclick=\"excluirCultura('$idcultura')\">
                    Excluir
                </button>
            </td>
        </tr>
    ";
}

// Botao de adicionar culturas
if (isset($_POST["salvar"])) {
    $cultura   = $mysqli->real_escape_string($_POST['cultura']);
    $variedade = $mysqli->real_escape_string($_POST['variedade']);
    $ciclo     = $mysqli->real_escape_string($_POST['ciclo']);
    $colheita  = $mysqli->real_escape_string($_POST['colheita']);

    $mysqli->query("INSERT INTO culturas VALUES (
        '',
        '$cultura',
        '$variedade',
        '$ciclo',
        '$colheita'
    )");

    if ($mysqli->error == "") {
        header("Location: cultura.php?adicionado=1");
        exit;
    } else {
        echo "Erro ao adicionar: " . $mysqli->error;
    }
}

// Recebendo por metodo post alteração da cultura
if (isset($_POST["alterar"])) {
    $idcultura = intval($_POST["idcultura"]);
    $cultura   = $mysqli->real_escape_string($_POST['cultura']);
    $variedade = $mysqli->real_escape_string($_POST['variedade']);
    $ciclo     = $mysqli->real_escape_string($_POST['ciclo']);
    $colheita  = $mysqli->real_escape_string($_POST['colheita']);

    $sql = "UPDATE culturas SET 
                cultura='$cultura',
                variedade='$variedade',
                ciclo='$ciclo',
                colheita='$colheita'
            WHERE idcultura=$idcultura";

    $mysqli->query($sql);

    if ($mysqli->error == "") {
        header("Location: cultura.php?alterado=1");
        exit;
    } else {
        echo "Erro ao atualizar: " . $mysqli->error;
    }
}

// Excluindo cultura
if (isset($_POST["exclusao"])) {
    $idcultura = intval($_POST["idcultura"]);
    $mysqli->query("DELETE FROM culturas WHERE idcultura=$idcultura");

    if ($mysqli->error == "") {
        header("Location: cultura.php?excluido=1");
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
    <h2>Cadastro de Culturas</h2>
    <div>
        <button class="botao" onclick="addCultura()">Adicionar</button>
        <button class="botao" onclick="buscarCultura()">Pesquisar</button>
    </div>
    <br />
    <table class="tabletitle">
        <tr>
            <th>Cultura</th>
            <th>Variedade</th>
            <th>Ciclo</th>
            <th>Colheita</th>
            <th>Ação</th>
        </tr>
        <?php echo $listaculturas; ?>
    </table>

    
    <!-- Div Adicionar Cultura -->
    <div class="flutuante" id="Adicionar" style="display:none;">
        <form action="cultura.php" method="POST">
            <label>Cultura</label>
            <input name="cultura" type="text" maxlength="60" required><br>
            
            <label>Variedade</label>
            <input name="variedade" type="text" maxlength="60" required><br>
            
            <label>Ciclo</label>
            <input name="ciclo" type="text" maxlength="20" required><br>
            
            <label>Colheita</label>
            <input name="colheita" type="text" maxlength="20" required><br>
            
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
    
    <!-- Div Alterar Cultura -->
    <div class="flutuante" id="Alterar" style="display:none;">
        <form method="POST" action="cultura.php">
            <input type="hidden" name="idcultura" id="alt_idcultura">

            <label>Cultura</label>
            <input id="alt_cultura" name="cultura" type="text" maxlength="60"><br>

            <label>Variedade</label>
            <input id="alt_variedade" name="variedade" type="text" maxlength="60"><br>

            <label>Ciclo</label>
            <input id="alt_ciclo" name="ciclo" type="text" maxlength="20"><br>

            <label>Colheita</label>
            <input id="alt_colheita" name="colheita" type="text" maxlength="20"><br>

            <input class="botao" type="submit" value="Salvar" name="alterar">
        </form>
    </div>
    
    <!-- Div Excluir Cultura -->
    <div class="flutuante" id="Excluir" style="display:none;">
        <form method="POST" action="cultura.php" style="text-align:center; padding:20px;">
            <input type="hidden" name="idcultura" id="exc_idcultura">

            <p>Deseja realmente excluir ?</p>
            <div>
            <button type="submit" name="exclusao" class="botao">Sim</button>
            <button type="button" class="botao" onclick="location.reload()">Não</button>
            </div>
        </form>
    </div>

</body>
<script>
    function addCultura() {
        var element = document.getElementById("Adicionar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function buscarCultura() {
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

    function alterarCultura(id, cultura, variedade, ciclo, colheita) {
        document.getElementById("alt_idcultura").value = id;
        document.getElementById("alt_cultura").value = cultura;
        document.getElementById("alt_variedade").value = variedade;
        document.getElementById("alt_ciclo").value = ciclo;
        document.getElementById("alt_colheita").value = colheita;

        var element = document.getElementById("Alterar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function excluirCultura(idcultura) {
        document.getElementById("exc_idcultura").value = idcultura;
        var element = document.getElementById("Excluir");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
</script>

</html>