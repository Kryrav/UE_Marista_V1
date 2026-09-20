    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media();?>/images/avatar.png" alt="User Image">
        <div>
          <p class="app-sidebar__user-name"><?= $_SESSION['userData']['nombre']; ?></p>
          <p class="app-sidebar__user-designation"><?= $_SESSION['userData']['nombrerol']; ?></p>
        </div>
      </div>
      <ul class="app-menu">
        
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->
        <?php if(!empty($_SESSION['permisos'][1]['r'])){ ?>
        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/dashboard">
                <i class="app-menu__icon fa fa-dashboard"></i>
                <span class="app-menu__label">Dashboard</span>
            </a>
        </li>
        <?php } ?>
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->

        <?php if(!empty($_SESSION['permisos'][2]['r'])){ ?>
        <li class="treeview">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-users" aria-hidden="true"></i>
                <span class="app-menu__label">Usuarios</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>/usuarios"><i class="icon fa fa-circle-o"></i> Usuarios</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>/roles"><i class="icon fa fa-circle-o"></i> Roles</a></li>
            </ul>
        </li>
        <?php } ?>
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->
       
        <?php if(!empty($_SESSION['permisos'][3]['r']) || !empty($_SESSION['permisos'][4]['r']) || !empty($_SESSION['permisos'][12]['r'])){ ?>
        <li class="treeview">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-archive" aria-hidden="true"></i>
                <span class="app-menu__label">Matrícula y Registro </span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <?php if(!empty($_SESSION['permisos'][3]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Estudiantes"><i class="icon fa fa-circle-o"></i>Estudiantes</a></li>
                <?php } ?>
                <?php if(!empty($_SESSION['permisos'][12]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Tutores"><i class="icon fa fa-circle-o"></i>Tutores / Padres</a></li>
                <?php } ?>
                <?php if(!empty($_SESSION['permisos'][4]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Matricula"><i class="icon fa fa-circle-o"></i>Matrícula</a></li>
                <?php } ?>
            </ul>
        </li>
        <?php } ?>
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->

      <?php if(!empty($_SESSION['permisos'][5]['r']) || !empty($_SESSION['permisos'][6]['r'])){ ?>
        <li class="treeview">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-credit-card-alt" aria-hidden="true"></i>
                <span class="app-menu__label">Mensualidad y cobros</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <?php if(!empty($_SESSION['permisos'][5]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Pensiones"><i class="icon fa fa-circle-o"></i>Mensualidades</a></li>
                <?php } ?>
                <?php if(!empty($_SESSION['permisos'][6]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Cobros"><i class="icon fa fa-circle-o"></i>Cobros</a></li>
                <?php } ?>
            </ul>
        </li>
    
        <?php } ?>

        <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->

        <?php if(!empty($_SESSION['permisos'][7]['r']) || !empty($_SESSION['permisos'][10]['r']) || !empty($_SESSION['permisos'][11]['r'])){ ?>
        <li class="treeview">
            <a class="app-menu__item" href="#" data-toggle="treeview">
                <i class="app-menu__icon fa fa-table" aria-hidden="true"></i>
                <span class="app-menu__label">Curso</span>
                <i class="treeview-indicator fa fa-angle-right"></i>
            </a>
            <ul class="treeview-menu">
                <?php if(!empty($_SESSION['permisos'][7]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Cursos"><i class="icon fa fa-circle-o"></i>Cursos</a></li>
                <?php } ?>
                <?php if(!empty($_SESSION['permisos'][10]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Materias"><i class="icon fa fa-circle-o"></i>Materias</a></li>
                <?php } ?>
                <?php if(!empty($_SESSION['permisos'][11]['r'])){ ?>
                <li><a class="treeview-item" href="<?= base_url(); ?>/Gestion"><i class="icon fa fa-circle-o"></i>Gestión Lectiva</a></li>
                <?php } ?>
            </ul>
        </li>
    
        <?php } ?>

      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->
      <?php if(!empty($_SESSION['permisos'][8]['r'])){ ?>
        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/Docentes">
                <i class="app-menu__icon fa fa-address-book-o"></i>
                <span class="app-menu__label">Docentes</span>
            </a>
        </li>
        <?php } ?>
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->
      <?php if(!empty($_SESSION['permisos'][9]['r'])){ ?>
        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/Administrativos">
                <i class="app-menu__icon fa fa-address-card-o"></i>
                <span class="app-menu__label">Administrativos</span>
            </a>
        </li>
        <?php } ?>
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->
      <?php if(!empty($_SESSION['permisos'][13]['r'])){ ?>
        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/Reportes">
                <i class="app-menu__icon fa fa-file-text-o"></i>
                <span class="app-menu__label">Reportes</span>
            </a>
        </li>
        <?php } ?>
      <!-- -------------------------------------------------------------------------------------------------------------------------------------- -->

        <li>
            <a class="app-menu__item" href="<?= base_url(); ?>/logout">
                <i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i>
                <span class="app-menu__label">Logout</span>
            </a>
        </li>
      </ul>
    </aside>