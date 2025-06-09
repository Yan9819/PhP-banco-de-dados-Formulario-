<?php
$db = new PDO("sqlite:usuarios.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS usuarios (id INTEGER PRIMARY KEY, nome TEXT, email TEXT)");

$usuario = ['id'=>'', 'nome'=>'', 'email'=>''];
$editando = false;

if (isset($_GET['deletar'])) {
    $db->prepare("DELETE FROM usuarios WHERE id=?")->execute([$_GET['deletar']]);
    header("Location: index.php"); exit;
}

if (isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE id=?");
    $stmt->execute([$_GET['editar']]);
    $usuario = $stmt->fetch(); $editando = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['id']) {
        $db->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?")
           ->execute([$_POST['nome'], $_POST['email'], $_POST['id']]);
    } else {
        $db->prepare("INSERT INTO usuarios (nome, email) VALUES (?, ?)")
           ->execute([$_POST['nome'], $_POST['email']]);
    }
    header("Location: index.php"); exit;
}

$usuarios = $db->query("SELECT * FROM usuarios")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Usuários</title></head>
<body>

<h2><?= $editando ? "Editar Usuário" : "Cadastrar Usuário" ?></h2>
<form method="POST">
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
    <label>
        Nome:<br>
        <input type="text" name="nome" required value="<?= htmlspecialchars($usuario['nome']) ?>">
    </label><br><br>
    <label>
        Email:<br>
        <input type="email" name="email" required value="<?= htmlspecialchars($usuario['email']) ?>">
    </label><br><br>
    <input type="submit" value="<?= $editando ? "Atualizar" : "Cadastrar" ?>">
    <?php if ($editando): ?> <a href="index.php">Cancelar</a> <?php endif; ?>
</form>

<h2>Lista de Usuários</h2>
<?php if ($usuarios): ?>
<table border="1" cellpadding="5">
    <tr><th>ID</th><th>Nome</th><th>Email</th><th>Ações</th></tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['nome']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td>
            <a href="?editar=<?= $u['id'] ?>">Editar</a> |
            <a href="?deletar=<?= $u['id'] ?>" onclick="return confirm('Tem certeza?')">Deletar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?><p>Nenhum usuário cadastrado.</p><?php endif; ?>

</body>
</html>
