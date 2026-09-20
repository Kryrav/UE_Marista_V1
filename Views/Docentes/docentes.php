<?php headerAdmin($data); ?>
<main class="app-content">
  <div class="app-title"><div><h1><i class="fa fa-address-book-o"></i> <?= $data['page_title'] ?>
  <?php if($_SESSION['permisosMod']['w']){ ?><button class="btn btn-primary" onclick="openModalDocente();"><i class="fas fa-plus-circle"></i> Nuevo</button><?php } ?></h1></div>
  <ul class="app-breadcrumb breadcrumb"><li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li><li class="breadcrumb-item"><a href="<?= base_url(); ?>/Docentes">Docentes</a></li></ul></div>
  <div class="row"><div class="col-md-12"><div class="tile"><div class="tile-body"><div class="table-responsive">
  <table class="table table-hover table-bordered w-100" id="tableDocentes"><thead><tr><th>CI</th><th>Nombre</th><th>Apellido</th><th>Celular</th><th>Email</th><th>Status</th><th>Acciones</th></tr></thead><tbody></tbody></table>
  </div></div></div></div></div>
</main>
<div class="modal fade" id="modalDocente" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header headerRegister"><h5 class="modal-title" id="titleDocente">Nuevo Docente</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
<div class="modal-body"><form id="formDocente">
<input type="hidden" id="idPersona" name="idPersona" value="0">
<div class="form-row"><div class="form-group col-md-4"><label>CI</label><input class="form-control" id="txtCi" name="txtCi" required></div>
<div class="form-group col-md-4"><label>Nombres</label><input class="form-control" id="txtNombre" name="txtNombre" required></div>
<div class="form-group col-md-4"><label>Apellidos</label><input class="form-control" id="txtApellido" name="txtApellido" required></div></div>
<div class="form-row"><div class="form-group col-md-3"><label>Sexo</label><select class="form-control" name="listSexo" id="listSexo"><option value="M">M</option><option value="F">F</option></select></div>
<div class="form-group col-md-3"><label>Celular</label><input class="form-control" name="txtCel" id="txtCel" required></div>
<div class="form-group col-md-6"><label>Email</label><input type="email" class="form-control" name="txtEmail" id="txtEmail" required></div></div>
<div class="form-row"><div class="form-group col-md-8"><label>Dirección</label><input class="form-control" name="txtDireccion" id="txtDireccion"></div>
<div class="form-group col-md-4"><label>Status</label><select class="form-control" name="listStatus" id="listStatus"><option value="1">Activo</option><option value="0">Inactivo</option></select></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Password (vacío=CI)</label><input class="form-control" name="txtPassword" id="txtPassword"></div></div>
<div class="tile-footer"><button class="btn btn-primary" type="submit">Guardar</button> <button class="btn btn-danger" data-dismiss="modal" type="button">Cerrar</button></div>
</form></div></div></div></div>
<?php footerAdmin($data); ?>
