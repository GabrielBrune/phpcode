<?php
// conexao com o banco de dados
require("../banco/conecta_banco.php");
// executar comandos sql
// consulta registros da tabela
$query = $mysqli->query("select * from funcionarios");
echo $mysqli->error;

//criação de uma variavel para separa o php do html
$listafunc = "";

// carrega consulta de registros
while ($tabela = $query->fetch_assoc()) {
    $id = $tabela['idfunc'];
    $nome = htmlspecialchars($tabela['nome']);
    $cargo  = htmlspecialchars($tabela['cargo']);
    $salario  = number_format(floatval($tabela['salario']), 2, '.', '');

    $listafunc .= "
        <tr>
        <td align='center'>$nome</td>
        <td align='center'>$cargo</td>
        <td align='center'>$salario</td>
        <td>
            <button class='button'
                onclick=\"alterarClient('$id','$nome','$cargo','$salario')\">
                Alterar
            </button>
            <button class='button'
                onclick=\"excluirClient('$id')\">
                Excluir
            </button>
        </td>
    </tr>
    ";
}

//Botao de adicionar funcionarios
if (isset($_POST["salvar"])) {
    require("../banco/conecta_banco.php");

    $nome = $mysqli->real_escape_string($_POST['nome']);
    $cargo = $mysqli->real_escape_string($_POST['cargo']);
    $salario = number_format(floatval($_POST['salario']), 2, '.', '');

    $mysqli->query("INSERT INTO funcionarios (nome, cargo, salario) VALUES (
        '$nome',
        '$cargo',
        '$salario'
    )");

    if ($mysqli->error == "") {
        header("Location: funcionarios.php?adicionado=1");
        exit;
    } else {
        echo "Erro ao adicionar: " . $mysqli->error;
    }
}

//Resebendo por methodo post alteração do client->
if (isset($_POST["alterar"])) {
    require("../banco/conecta_banco.php");

    $idfunc  = intval($_POST["idfunc"]);
    $nome    = $mysqli->real_escape_string($_POST["nome"]);
    $cargo   = $mysqli->real_escape_string($_POST["cargo"]);
    $salario = number_format(floatval($_POST["salario"]), 2, '.', '');

    $sql = "UPDATE funcionarios SET 
                nome = '$nome',
                cargo = '$cargo',
                salario = '$salario'
            WHERE idfunc = $idfunc";

    $mysqli->query($sql);

    if ($mysqli->error == "") {
        header("Location: funcionarios.php?alterado=1");
        exit;
    } else {
        echo "Erro ao atualizar: " . $mysqli->error;
    }
}

//Excluindo cliente
if (isset($_POST["exclusao"])) {
    $idfunc = intval($_POST["idfunc"]);
    $mysqli->query("DELETE FROM funcionarios WHERE idfunc = $idfunc");

    if ($mysqli->error == "") {
        // redireciona para recarregar a lista
        header("Location: funcionarios.php?excluido=1");
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
    <h2>Cadastro de Clientes</h2>
    <div>
        <button class="botao" onclick="addClient()">Adicionar</button>
        <button class="botao" onclick="buscarClient()">Pesquisar</button>
    </div>
    <br />
    <table class="tabletitle">
        <tr>
            <th>Nome</th>
            <th>Cargo</th>
            <th>Salario</th>
            <th>Ação</th>
        </tr>
        <?php echo $listafunc; ?>
    </table>

    <!--Div Adicionar Cliente -->
    <div class="flutuante" id="Adicionar">
        <form action="funcionarios.php" method="POST">
            <label for="funcionario">Nome</label>
            <input id="funcionario" name="nome" type="text" maxlength="60" required><br>

            <label for="cargo">Cargo</label>
            <input id="cargo" name="cargo" type="text" maxlength="50" required><br>

            <label for="salario">Salario</label>
            <input id="salario" name="salario" type="number" step="0.01" required><br>

            <input class="botao" type="submit" value="Salvar" name="salvar"><br>
        </form>
    </div>

    <!-- Div Pesquisar Cliente -->
    <div class="flutuante" id="Pesquisar">
        <label for="busca">Pesquisar:</label>
        <input type="text" id="busca" placeholder="Digite nome, CPF, email..."><br>
        <div>
            <button class="botao" onclick="buscarNaTabela()">Buscar</button>
            <button class="botao" type="button" onclick="location.reload()">Sair</button>
        </div>
    </div>

    <!-- Div Alterar Cliente -->
    <div class="flutuante" id="Alterar" style="display:none;">
        <form method="POST" action="funcionarios.php">
            <input type="hidden" name="idfunc" id="alt_idfunc">

            <label>Nome</label>
            <input id="alt_nome" name="nome" type="text" maxlength="60"><br>

            <label>Cargo</label>
            <input id="alt_cargo" name="cargo" type="text" maxlength="50"><br>

            <label>Salario</label>
            <input id="alt_salario" name="salario" type="number"><br>

            <input class="botao" type="submit" value="Salvar" name="alterar">
        </form>
    </div>
    
    <!-- Div Excluir Cliente -->
    <div class="flutuante" id="Excluir" style="display:none;">
        <form method="POST" action="funcionarios.php" style="text-align:center; padding:20px;">
            <input type="hidden" name="idfunc" id="exc_idfunc">

            <p>Deseja realmente excluir ?</p>
            <div>
            <button type="submit" name="exclusao" class="botao">Sim</button>
            <button type="submit" class="botao">Não</button>
            </div>
        </form>
    </div>

</body>
<script>
    function addClient() {
        var element = document.getElementById("Adicionar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }

    function buscarClient() {
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

    function alterarClient(id, nome, cargo, salario) {
        document.getElementById("alt_idfunc").value = id;
        document.getElementById("alt_nome").value = nome;
        document.getElementById("alt_cargo").value = cargo;
        document.getElementById("alt_salario").value = salario;

        var element = document.getElementById("Alterar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
    function excluirClient(idfunc) {
        document.getElementById("exc_idfunc").value = idfunc;
        var element = document.getElementById("Excluir");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
</script>

</html>