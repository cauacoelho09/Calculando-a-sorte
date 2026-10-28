<?php
/**
 * Página de Perfil do Usuário
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/includes/auth_check.php';

$user = getLoggedUserData($pdo);
if (!$user) {
    header('Location: login.php');
    exit;
}

// Estatísticas
$totalCalculos = $pdo->prepare("SELECT COUNT(*) FROM calculos WHERE user_id = ?");
$totalCalculos->execute([$user['id']]);
$qtdCalculos = $totalCalculos->fetchColumn();

$totalJogos = $pdo->prepare("SELECT COUNT(*), COALESCE(SUM(pontos), 0) FROM jogos_historico WHERE user_id = ?");
$totalJogos->execute([$user['id']]);
$dadosJogos = $totalJogos->fetch(PDO::FETCH_NUM);
$qtdPartidas = $dadosJogos[0];
$pontosTotais = $dadosJogos[1];

$pageTitle = "Meu Perfil";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="max-w-4xl mx-auto px-4 py-6 sm:py-10">
    
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Meu Perfil & Configurações</h1>
        <p class="text-xs sm:text-sm text-slate-400">Gerencie seus dados e confira suas conquistas matemáticas.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        
        <!-- Cartão Lateral: Avatar e Conquistas (4 colunas) -->
        <div class="md:col-span-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 text-center space-y-4 backdrop-blur-xl">
            <div class="w-24 h-24 mx-auto rounded-3xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-amber-400 p-1 shadow-2xl">
                <div class="w-full h-full bg-slate-950 rounded-[20px] flex items-center justify-center text-4xl text-indigo-300">
                    <i class="fa-solid fa-user-astronaut"></i>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-bold text-white"><?= htmlspecialchars($user['nome']) ?></h2>
                <p class="text-xs text-slate-400 font-mono"><?= htmlspecialchars($user['email']) ?></p>
            </div>

            <!-- Badges -->
            <div class="flex items-center justify-center gap-2 pt-1">
                <?php if ($user['is_premium']): ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        <i class="fa-solid fa-crown mr-1"></i> VIP Premium
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                        Jogador Free
                    </span>
                <?php endif; ?>
            </div>

            <!-- Estatísticas Resumo -->
            <div class="grid grid-cols-2 gap-2 pt-4 border-t border-slate-800 text-left">
                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-[10px] text-slate-500 font-bold uppercase block">Moedas</span>
                    <span class="text-sm font-black text-amber-400 font-mono"><?= number_format($user['moedas']) ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-[10px] text-slate-500 font-bold uppercase block">Cálculos</span>
                    <span class="text-sm font-black text-emerald-400 font-mono"><?= number_format($qtdCalculos) ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-[10px] text-slate-500 font-bold uppercase block">Partidas</span>
                    <span class="text-sm font-black text-purple-400 font-mono"><?= number_format($qtdPartidas) ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-[10px] text-slate-500 font-bold uppercase block">Pontos</span>
                    <span class="text-sm font-black text-indigo-400 font-mono"><?= number_format($pontosTotais) ?></span>
                </div>
            </div>

            <div class="pt-2">
                <button type="button" onclick="confirmarLogout()" class="w-full py-2.5 rounded-xl text-xs font-semibold text-rose-400 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Encerrar Sessão</span>
                </button>
            </div>
        </div>

        <!-- Formulário de Alteração de Dados (8 colunas) -->
        <div class="md:col-span-8 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 backdrop-blur-xl">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-indigo-400"></i>
                <span>Editar Dados Pessoais</span>
            </h3>

            <form action="actions/profile_update.php" method="POST" class="space-y-4">
                <div>
                    <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Nome Completo
                    </label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($user['nome']) ?>" required
                        class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                        E-mail Cadastrado (Não alterável)
                    </label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                        class="w-full px-4 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-sm text-slate-500 cursor-not-allowed">
                </div>

                <div class="pt-4 border-t border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-key"></i>
                        <span>Alterar Senha (Opcional)</span>
                    </h4>

                    <div class="space-y-3">
                        <div>
                            <label for="senha_atual" class="block text-xs font-medium text-slate-400 mb-1">
                                Senha Atual
                            </label>
                            <input type="password" id="senha_atual" name="senha_atual" placeholder="Necessário apenas para alterar senha"
                                class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="nova_senha" class="block text-xs font-medium text-slate-400 mb-1">
                                    Nova Senha
                                </label>
                                <input type="password" id="nova_senha" name="nova_senha" minlength="6" placeholder="Mínimo 6 caracteres"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="confirmar_nova_senha" class="block text-xs font-medium text-slate-400 mb-1">
                                    Confirmar Nova Senha
                                </label>
                                <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" minlength="6" placeholder="Repita a nova senha"
                                    class="w-full px-4 py-2.5 bg-slate-950 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-600 focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="py-3 px-6 rounded-xl font-bold text-xs text-white bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/30 transition">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
