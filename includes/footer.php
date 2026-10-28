<?php
/**
 * Rodapé Global HTML5
 * Calculando a Sorte
 */
?>
    <footer class="mt-auto border-t border-slate-900 bg-slate-950/60 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Sistema Auditado & Seguro • PDO & Bcrypt Active</span>
                </div>
                <div>
                    &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong>. Todos os direitos reservados.
                </div>
                <div class="flex items-center gap-4">
                    <a href="historico.php" class="hover:text-slate-300 transition">Auditoria</a>
                    <a href="premium.php" class="hover:text-amber-400 transition">Assinatura Premium</a>
                    <a href="calculadora.php" class="hover:text-indigo-400 transition">Calculadora</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Inclusão dos Toasts e Modais Universais -->
    <?php require_once __DIR__ . '/toast.php'; ?>
    <?php require_once __DIR__ . '/modal.php'; ?>

    <!-- Script Principal da Aplicação -->
    <script src="assets/js/app.js"></script>
</body>
</html>
