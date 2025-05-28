<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="../inicio/index.php">
          <i class="bi bi-grid"></i>
          <span>Menu</span>
        </a>
      </li><!-- End Menu Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-bounding-box"></i><span>Clientes</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
      </li><!-- End Clientes Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#proveedores-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-person-bounding-box"></i><span>Proveedores</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="proveedores-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
      </li><!-- End Proveedores Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Turnos</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
      </li><!-- End Turnos Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#deposito-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-house"></i><span>Deposito</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="deposito-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
      </li><!-- End Deposito Nav -->

            <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#pedidos-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-tag"></i><span>Pedidos</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="pedidos-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
      </li><!-- End Pedidos Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#charts-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-cart4"></i><span>Ventas</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="charts-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="../agregar/agregar_ventas.php">
              <i class="bi bi-circle"></i><span>Agregar venta</span>
            </a>
          </li>
          <li>
            <a href="../listados/listados_ventas.php">
              <i class="bi bi-circle"></i><span>Listado de Ventas</span>
            </a>
          </li>
        </ul>
      </li><!-- End Ventas Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#compras-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-bag-plus"></i><span>Compras</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="compras-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
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
      </li><!-- End Compras Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav1" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Panel de control</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav1" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="../panel_control/panel_turnos.php">
              <i class="bi bi-circle"></i><span>Turnos</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Ventas</span>
            </a>
          </li>
          <li>
            <a href="tables-data.html">
              <i class="bi bi-circle"></i><span>Compras</span>
            </a>
          </li>
        </ul>
      </li><!-- End Panel de control Nav -->

    </ul>

  </aside><!-- End Sidebar-->
