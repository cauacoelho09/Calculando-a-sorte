<?php
/**
 * Página de Login de Usuários
 * Calculando a Sorte
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/session.php';

// Se já estiver logado, redireciona para a calculadora
if (isLoggedIn()) {
    header('Location: calculadora.php');
    exit;
}

$pageTitle = "Entrar";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        
        <!-- Cartão de Login -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl p-6 sm:p-8 backdrop-blur-xl relative overflow-hidden">
            <!-- Detalhe decorativo de fundo -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Cabeçalho -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 p-0.5 shadow-xl shadow-indigo-600/20">
                    <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center text-2xl text-indigo-400">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-black text-white tracking-tight">Bem-vindo de volta!</h1>
                <p class="text-xs text-slate-400 mt-1">Acesse sua conta para testar sua sorte matemática</p>
            </div>

            <!-- Formulário -->
            <form action="actions/auth_login.php" method="POST" class="space-y-4">
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
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Senha
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <input type="password" id="senha" name="senha" required placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-3 bg-slate-950/80 border border-slate-700/80 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                        <button type="button" onclick="togglePasswordVisibility('senha', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-500 shadow-xl shadow-indigo-600/30 transition transform hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2 mt-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Entrar na Plataforma</span>
                </button>
            </form>

            <!-- Acesso Rápido para Testes -->
            <div class="mt-6 pt-6 border-t border-slate-800">
                <p class="text-[11px] font-semibold text-slate-400 text-center uppercase tracking-wider mb-2">
                    Acesso Rápido de Demonstração
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="preencherLogin('jogador@sorte.com', '123456')" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-user text-emerald-400"></i>
                        <span>Jogador Free</span>
                    </button>
                    <button type="button" onclick="preencherLogin('vip@sorte.com', '123456')" class="py-2 px-3 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30 transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-crown text-amber-400"></i>
                        <span>Membro VIP</span>
                    </button>
                </div>
            </div>

            <!-- Link para Cadastro -->
            <div class="text-center mt-6">
                <p class="text-xs text-slate-400">
                    Ainda não possui uma conta?
                    <a href="register.php" class="font-bold text-indigo-400 hover:text-indigo-300 ml-1 transition">
                        Cadastre-se grátis
                    </a>
                </p>
            </div>

        </div>

    </div>
</div>

<script>
function togglePasswordVisibility(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function preencherLogin(email, senha) {
    document.getElementById('email').value = email;
    document.getElementById('senha').value = senha;
    showToast('info', `Credenciais de teste preenchidas: ${email}`);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
