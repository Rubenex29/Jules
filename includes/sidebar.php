<nav id="sidebar">
    <div class="sidebar-header">
        <h3><?php echo APP_NAME; ?></h3>
    </div>

    <ul class="list-unstyled components">
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
            <a href="index.php"><i class="bi bi-speedometer2"></i> Painel</a>
        </li>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'companies.php' || basename($_SERVER['PHP_SELF']) == 'company_add.php') ? 'active' : ''; ?>">
            <a href="companies.php"><i class="bi bi-building"></i> Empresas</a>
        </li>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contacts.php' || basename($_SERVER['PHP_SELF']) == 'contact_add.php') ? 'active' : ''; ?>">
            <a href="contacts.php"><i class="bi bi-people"></i> Contactos</a>
        </li>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'leads.php' || basename($_SERVER['PHP_SELF']) == 'lead_add.php') ? 'active' : ''; ?>">
            <a href="leads.php"><i class="bi bi-funnel"></i> Leads</a>
        </li>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'opportunities.php' || basename($_SERVER['PHP_SELF']) == 'opportunity_add.php') ? 'active' : ''; ?>">
            <a href="opportunities.php"><i class="bi bi-cash-coin"></i> Oportunidades</a>
        </li>
         <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'tasks.php') ? 'active' : ''; ?>">
            <a href="tasks.php"><i class="bi bi-check2-square"></i> Tarefas</a>
        </li>
        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li class="<?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
            <a href="users.php"><i class="bi bi-gear"></i> Utilizadores</a>
        </li>
        <?php endif; ?>
        <li>
            <a href="#pageSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="bi bi-download"></i> Exportar Dados</a>
            <ul class="collapse list-unstyled" id="pageSubmenu">
                <li>
                    <a href="export.php?type=contacts">Exportar Contactos (CSV)</a>
                </li>
                 <li>
                    <a href="export.php?type=leads">Exportar Leads (CSV)</a>
                </li>
                 <li>
                    <a href="export.php?type=companies">Exportar Empresas (CSV)</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
