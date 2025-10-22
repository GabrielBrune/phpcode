<?php
// conexao com o banco de dados
require("../banco/conecta_banco.php");
// executar comandos sql
// consulta registros da tabela
$query = $mysqli->query("select * from clientes");
echo $mysqli->error;

//criação de uma variavel para separa o php do html
$listaCli = "";

// carrega consulta de registros
while ($tabela = $query->fetch_assoc()) {
    $id = $tabela['idcli'];
    $nome = htmlspecialchars($tabela['nome']);
    $cpf  = htmlspecialchars($tabela['cpf_cnpj']);
    $tel  = htmlspecialchars($tabela['telefone']);
    $email= htmlspecialchars($tabela['email']);
    $cidade=htmlspecialchars($tabela['cidade']);

    $listaCli .= "
        <tr>
        <td align='center'>$nome</td>
        <td align='center'>$cpf</td>
        <td align='center'>$tel</td>
        <td align='center'>$email</td>
        <td align='center'>$cidade</td>
        <td>
            <button class='button'
                onclick=\"alterarClient('$id','$nome','$cpf','$tel','$email','$cidade')\">
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

//Botao de adicionar clientes
if (isset($_POST["salvar"])) {
    require("../banco/conecta_banco.php");

    $nome = htmlentities($_POST["nome"]);
    $cpf_cnpj = htmlentities($_POST["cpf_cnpj"]);
    $telefone = htmlentities($_POST["telefone"]);
    $email = htmlentities($_POST["email"]);
    $cidade = htmlentities($_POST["cidade"]);

    $mysqli->query("INSERT INTO clientes VALUES (
            '',
            '$nome',
            '$cpf_cnpj',
            '$telefone',
            '$email',
            '$cidade'
        )");

    echo $mysqli->error;

    if ($mysqli->error == "") {
        header("Location: clientes.php?adicionado=1");
    }
}

//Resebendo por methodo post alteração do client->
if (isset($_POST["alterar"])) {
    require("../banco/conecta_banco.php");

    $idcli    = intval($_POST["idcli"]);
    $nome     = $mysqli->real_escape_string($_POST["nome"]);
    $cpf_cnpj = $mysqli->real_escape_string($_POST["cpf_cnpj"]);
    $telefone = $mysqli->real_escape_string($_POST["telefone"]);
    $email    = $mysqli->real_escape_string($_POST["email"]);
    $cidade   = $mysqli->real_escape_string($_POST["cidade"]);

    $sql = "UPDATE clientes SET 
                nome     = '$nome',
                cpf_cnpj = '$cpf_cnpj',
                telefone = '$telefone',
                email    = '$email',
                cidade   = '$cidade'
            WHERE idcli = $idcli";

    $mysqli->query($sql);

    if ($mysqli->error == "") {
        header("Location: clientes.php?alterado=1");
        exit;
    } else {
        echo "Erro ao atualizar: " . $mysqli->error;
    }
}

//Excluindo cliente
if (isset($_POST["confirmar_exclusao"])) {
    $idcli = intval($_POST["idcli"]);
    $mysqli->query("DELETE FROM clientes WHERE idcli = $idcli");

    if ($mysqli->error == "") {
        // redireciona para recarregar a lista
        header("Location: clientes.php?excluido=1");
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
            <th>Cpf/Cnpj</th>
            <th>Telefone</th>
            <th>Email</th>
            <th>Cidade</th>
            <th>Ação</th>
        </tr>
        <?php echo $listaCli; ?>
    </table>

    <!--Div Adicionar Cliente -->
    <div class="flutuante" id="Adicionar">
        <form action="clientes.php" method="POST">
            <label for="nome">Nome</label>
            <input id="nome" name="nome" type="text" maxlength="60" required><br>

            <label for="cpf_cnpj">CPF/CNPJ</label>
            <input id="cpf_cnpj" name="cpf_cnpj" type="text" maxlength="14" placeholder="Somente Numeros" required><br>

            <label for="telefone">Telefone:</label>
            <input id="telefone" name="telefone" type="text" maxlength="11" placeholder="Somente Numeros" required><br>

            <label for="email">Email</label>
            <input id="email" name="email" type="text" maxlength="50" required><br>

            <label for="cidade">Cidade</label>
            <input id="cidade" name="cidade" type="text" maxlength="80" required><br>

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
        <form method="POST" action="clientes.php">
            <input type="hidden" name="idcli" id="alt_idcli">

            <label>Nome:</label>
            <input type="text" name="nome" maxlength="60" id="alt_nome"><br>

            <label>CPF/CNPJ:</label>
            <input type="text" name="cpf_cnpj" maxlength="14" placeholder="Somente Numeros" id="alt_cpf"><br>

            <label>Telefone:</label>
            <input type="text" name="telefone" maxlength="11" placeholder="Somente Numeros" id="alt_tel"><br>

            <label>Email:</label>
            <input type="text" name="email" maxlength="50" id="alt_email"><br>

            <label>Cidade:</label>
            <input type="text" name="cidade" maxlength="80" id="alt_cidade"><br>

            <input class="botao" type="submit" value="Salvar" name="alterar">
        </form>
    </div>
    
    <!-- Div Excluir Cliente -->
    <div class="flutuante" id="Excluir" style="display:none;">
        <form method="POST" action="clientes.php" style="text-align:center; padding:20px;">
            <input type="hidden" name="idcli" id="exc_idcli">

            <p>Deseja realmente excluir ?</p>
            <div>
            <button type="submit" name="confirmar_exclusao" class="botao">Sim</button>
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

    function alterarClient(id, nome, cpf, tel, email, cidade) {
        document.getElementById("alt_idcli").value = id;
        document.getElementById("alt_nome").value = nome;
        document.getElementById("alt_cpf").value = cpf;
        document.getElementById("alt_tel").value = tel;
        document.getElementById("alt_email").value = email;
        document.getElementById("alt_cidade").value = cidade;

       var element = document.getElementById("Alterar");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
    function excluirClient(idcli) {
        document.getElementById("exc_idcli").value = idcli;
        var element = document.getElementById("Excluir");
        element.style.display = (element.style.display == "block") ? "none" : "block";
    }
</script>

</html>