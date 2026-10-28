<?php
/**
 * Instalador Guiado e Automático do Banco de Dados
 * Cria o banco e as tabelas com 1 clique
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/session.php';

$mensagem = null;
$tipo = 'info';
$instaladoComSucesso = false;

// Se o formulário de instalação foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['executar_instalacao'])) {
    try {
        $dsnServer = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
        $pdoInit = new PDO($dsnServer, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $sqlSchemaFile = __DIR__ . '/database/schema.sql';
        if (!file_exists($sqlSchemaFile)) {
            throw new Exception("Arquivo de esquema SQL não encontrado em: {$sqlSchemaFile}");
        }

        $sqlContent = file_get_contents($sqlSchemaFile);
        $pdoInit->exec($sqlContent);

        $instaladoComSucesso = true;
        $mensagem = "Banco de dados e tabelas criados com sucesso! Usuários de demonstração cadastrados.";
        $tipo = 'success';

        // Registra log da instalação
        $dsnDb = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdoNovo = new PDO($dsnDb, DB_USER, DB_PASS);
        require_once __DIR__ . '/includes/logger.php';
        registrarLog($pdoNovo, 'INSTALACAO', 'Instalação inicial da base de dados e migrações executadas com sucesso.', 1);

    } catch (Exception $e) {
        $mensagem = "Erro na instalação: " . $e->getMessage();
        $tipo = 'error';
    }
}

// Testa se o banco já está acessível
$bancoJaExiste = false;
try {
    $dsnTeste = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdoTeste = new PDO($dsnTeste, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdoTeste->query("SHOW TABLES LIKE 'usuarios'");
    if ($stmt->rowCount() > 0) {
        $bancoJaExiste = true;
    }
} catch (Exception $e) {
    $bancoJaExiste = false;
}

$pageTitle = "Instalador do Sistema";
require_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xl bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Topo com Gradiente -->
        <div class="p-6 bg-gradient-to-r from-indigo-900/60 via-purple-900/40 to-slate-900 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-2xl shadow-lg">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">Instalação & Configuração</h1>
                    <p class="text-xs text-slate-400">Calculando a Sorte • Configuração do MySQL via PDO</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <?php if ($mensagem): ?>
                <div class="p-4 rounded-xl text-sm border <?= $tipo === 'success' ? 'bg-emerald-950/60 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/60 border-rose-500/40 text-rose-300' ?>">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="fa-solid <?= $tipo === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-rose-400' ?>"></i>
                        <span><?= $tipo === 'success' ? 'Sucesso!' : 'Atenção!' ?></span>
                    </div>
                    <p><?= htmlspecialchars($mensagem) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($bancoJaExiste && !$instaladoComSucesso): ?>
                <div class="p-4 rounded-xl bg-indigo-950/40 border border-indigo-500/30 text-indigo-200 text-sm">
                    <p class="font-bold flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-circle-check text-indigo-400"></i>
                        O banco de dados `<?= DB_NAME ?>` já está configurado e pronto!
                    </p>
                    <p class="text-xs text-indigo-300">Você já pode utilizar todas as ferramentas e jogos da plataforma.</p>
                </div>
            <?php endif; ?>

            <!-- Parâmetros de Conexão -->
            <div class="bg-slate-950/70 p-4 rounded-xl border border-slate-800/80 text-xs font-mono space-y-1.5">
                <div class="text-slate-400 font-sans font-bold text-xs uppercase tracking-wider mb-2">Parâmetros detectados (config/config.php):</div>
                <div class="flex justify-between text-slate-300">
                    <span class="text-slate-500">Host:</span>
                    <span><?= DB_HOST ?>:<?= DB_PORT ?></span>
                </div>
                <div class="flex justify-between text-slate-300">
                    <span class="text-slate-500">Database:</span>
                    <span class="text-amber-400 font-bold"><?= DB_NAME ?></span>
                </div>
                <div class="flex justify-between text-slate-300">
                    <span class="text-slate-500">Usuário:</span>
                    <span><?= DB_USER ?></span>
                </div>
                <div class="flex justify-between text-slate-300">
                    <span class="text-slate-500">Driver:</span>
                    <span class="text-emerald-400">PDO MySQL</span>
                </div>
            </div>

            <!-- Contas de Demonstração Disponíveis -->
            <div class="bg-slate-950/40 p-4 rounded-xl border border-slate-800 text-xs space-y-2">
                <div class="font-bold text-slate-300 flex items-center gap-2">
                    <i class="fa-solid fa-users text-indigo-400"></i>
                    Contas pré-cadastradas para testes:
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                    <div class="p-2.5 rounded-lg bg-slate-900 border border-slate-800">
                        <span class="text-emerald-400 font-bold block">1. Usuário Padrão</span>
                        <span class="text-slate-400 block">E-mail: <code class="text-slate-200">jogador@sorte.com</code></span>
                        <span class="text-slate-400 block">Senha: <code class="text-slate-200">123456</code></span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-900 border border-slate-800">
                        <span class="text-amber-400 font-bold block">2. Membro VIP Premium</span>
                        <span class="text-slate-400 block">E-mail: <code class="text-slate-200">vip@sorte.com</code></span>
                        <span class="text-slate-400 block">Senha: <code class="text-slate-200">123456</code></span>
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <form method="POST" class="pt-2 space-y-3">
                <button type="submit" name="executar_instalacao" value="1" class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 hover:from-indigo-500 hover:to-purple-500 shadow-xl shadow-indigo-600/30 transition transform hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                    <i class="fa-solid fa-database"></i>
                    <span><?= $bancoJaExiste ? 'Recriar / Atualizar Banco de Dados' : 'Criar Banco e Tabelas com 1 Clique' ?></span>
                </button>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a href="login.php" class="text-center py-2.5 px-3 rounded-xl text-xs font-bold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white transition">
                        <i class="fa-solid fa-right-to-bracket mr-1"></i> Ir para Login
                    </a>
                    <a href="calculadora.php" class="text-center py-2.5 px-3 rounded-xl text-xs font-bold text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition">
                        <i class="fa-solid fa-calculator mr-1"></i> Ir para Calculadora
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
