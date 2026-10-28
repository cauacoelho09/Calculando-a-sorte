<?php
/**
 * Página de Cadastro de Usuários
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

if (isLoggedIn()) {
    header('Location: calculadora.php');
    exit;
}

$pageTitle = "Criar Conta";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        
        <!-- Cartão de Cadastro -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl p-6 sm:p-8 backdrop-blur-xl relative overflow-hidden">
            <!-- Detalhes decorativos -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Cabeçalho -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 p-0.5 shadow-xl shadow-purple-600/20">
                    <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-2xl text-purple-400">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-black text-white tracking-tight">Crie sua Conta</h1>
                <p class="text-xs text-slate-400 mt-1">Ganhe 50 moedas da sorte grátis logo na entrada!</p>
            </div>

            <!-- Formulário -->
            <form action="actions/auth_register.php" method="POST" class="space-y-4">
                <div>
                    <label for="nome" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Nome Completo
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" id="nome" name="nome" required placeholder="Seu nome"
                            class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        E-mail
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <input type="email" id="email" name="email" required placeholder="seu@email.com"
                            class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Senha (mínimo 6 caracteres)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" id="senha" name="senha" required minlength="6" placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div>
                    <label for="confirmar_senha" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Confirmar Senha
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6" placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-950/50 border border-slate-800 text-[11px] text-slate-400 flex items-start gap-2">
                    <i class="fa-solid fa-shield-check text-emerald-400 mt-0.5"></i>
                    <span>Sua senha será protegida com hash seguro <strong>Bcrypt</strong> e todas as ações no sistema são registradas em logs de auditoria.</span>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-500 shadow-xl shadow-indigo-600/30 transition transform hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 mt-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Cadastrar e Começar com 50 Moedas</span>
                </button>
            </form>

            <!-- Link para Login -->
            <div class="text-center mt-6">
                <p class="text-xs text-slate-400">
                    Já possui conta registrada?
                    <a href="login.php" class="font-bold text-indigo-400 hover:text-indigo-300 ml-1 transition">
                        Entrar agora
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
