<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <!-- Menú principal siempre visible -->
        <li class="nav-item">
            <a class="nav-link" href="../inicio/index.php">
                <i class="bi bi-grid"></i>
                <span>Menu</span>
            </a>
        </li>

        <?php
        // Nivel 1: Administrador (ve todo)
        if ($_SESSION['Usuario_Nivel'] == 1 || $_SESSION['Usuario_Nivel'] == 2 || $_SESSION['Usuario_Nivel'] == 6) {
        ?>
        <!-- Clientes Nav - Visible para niveles 1, 2 y 6 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-person-bounding-box"></i><span>Clientes</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_clientes.php">
                        <i class="bi bi-circle"></i><span>Agregar</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_clientes.php">
                        <i class="bi bi-circle"></i><span>Listados</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1 || $_SESSION['Usuario_Nivel'] == 5) { ?>
        <!-- Proveedores Nav - Visible para niveles 1 y 5 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#proveedores-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-person-bounding-box"></i><span>Proveedores</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="proveedores-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_proveedores.php">
                        <i class="bi bi-circle"></i><span>Agregar</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_proveedores.php">
                        <i class="bi bi-circle"></i><span>Listados</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1 || $_SESSION['Usuario_Nivel'] == 2 || $_SESSION['Usuario_Nivel'] == 6) { ?>
        <!-- Turnos Nav - Visible para niveles 1, 2 y 6 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-journal-text"></i><span>Turnos</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_turnos.php">
                        <i class="bi bi-circle"></i><span>Agregar</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_turnos.php">
                        <i class="bi bi-circle"></i><span>Listados</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1 || $_SESSION['Usuario_Nivel'] == 4 || $_SESSION['Usuario_Nivel'] == 5) { ?>
        <!-- Deposito Nav - Visible para niveles 1, 4 y 5 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#deposito-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-house"></i><span>Deposito</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="deposito-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_productos.php">
                        <i class="bi bi-circle"></i><span>Agregar Producto</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_productos.php">
                        <i class="bi bi-circle"></i><span>Listados Productos</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1) { ?>
        <!-- Pedidos Nav - Solo nivel 1 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#pedidos-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-tag"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="pedidos-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_pedidos.php">
                        <i class="bi bi-circle"></i><span>Agregar Pedido</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_pedidos.php">
                        <i class="bi bi-circle"></i><span>Listado de Pedidos</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1 || $_SESSION['Usuario_Nivel'] == 3) { ?>
        <!-- Ventas Nav - Visible para niveles 1 y 3 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-cart4"></i><span>Ventas</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="charts-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_ventas.php">
                        <i class="bi bi-circle"></i><span>Agregar venta</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/cobrar_turnos.php">
                        <i class="bi bi-circle"></i><span>Cobrar Turnos</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_ventas.php">
                        <i class="bi bi-circle"></i><span>Listado de Ventas</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1 || $_SESSION['Usuario_Nivel'] == 5) { ?>
        <!-- Compras Nav - Visible para niveles 1 y 5 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#compras-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-bag-plus"></i><span>Compras</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="compras-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_orden_compras.php">
                        <i class="bi bi-circle"></i><span>Generar Orden de Compra</span>
                    </a>
                </li>
                <li>
                    <a href="../agregar/agregar_compras.php">
                        <i class="bi bi-circle"></i><span>Generar Presupuesto</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_compras.php">
                        <i class="bi bi-circle"></i><span>Listado de Presupuestos</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_ordenes_compra.php">
                        <i class="bi bi-circle"></i><span>Listado de Ordenes Compras</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1) { ?>
        <!-- Usuarios Nav - Solo nivel 1 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#usuarios-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-person-bounding-box"></i><span>Usuarios</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="usuarios-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_usuarios.php">
                        <i class="bi bi-circle"></i><span>Agregar</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_usuarios.php">
                        <i class="bi bi-circle"></i><span>Listados</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1) { ?>
        <!-- Servicios Nav - Solo nivel 1 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#servicios-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-scissors"></i><span>Servicios</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="servicios-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../agregar/agregar_servicios.php">
                        <i class="bi bi-circle"></i><span>Agregar</span>
                    </a>
                </li>
                <li>
                    <a href="../listados/listados_servicios.php">
                        <i class="bi bi-circle"></i><span>Listados</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>

        <?php if ($_SESSION['Usuario_Nivel'] == 1) { ?>
        <!-- Panel de control Nav - Solo nivel 1 -->
        <li class="nav-item">
            <a class="nav-link collapsed" data-bs-target="#tables-nav1" data-bs-toggle="collapse" href="#">
                <i class="bi bi-layout-text-window-reverse"></i><span>Panel de control</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tables-nav1" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="../panel_control/panel_turnos.php">
                        <i class="bi bi-circle"></i><span>Turnos</span>
                    </a>
                </li>
                <li>
                    <a href="../panel_control/panel_ventas.php">
                        <i class="bi bi-circle"></i><span>Ventas</span>
                    </a>
                </li>
                <li>
                    <a href="../panel_control/panel_compras.php">
                        <i class="bi bi-circle"></i><span>Compras</span>
                    </a>
                </li>
            </ul>
        </li>
        <?php } ?>
    </ul>
</aside>
