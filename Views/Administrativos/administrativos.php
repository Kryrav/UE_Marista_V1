<?php headerAdmin($data); ?>
<main class="app-content">
  <div class="app-title"><div><h1><i class="fa fa-address-card-o"></i> <?= $data['page_title'] ?>
  <?php if($_SESSION['permisosMod']['w']){ ?><button class="btn btn-primary" onclick="openModalAdmin();"><i class="fas fa-plus-circle"></i> Nuevo</button><?php } ?></h1></div>
  <ul class="app-breadcrumb breadcrumb"><li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li><li class="breadcrumb-item"><a href="<?= base_url(); ?>/Administrativos">Administrativos</a></li></ul></div>
  <div class="row"><div class="col-md-12"><div class="tile"><div class="tile-body"><div class="table-responsive">
  <table class="table table-hover table-bordered w-100" id="tableAdmins"><thead><tr><th>CI</th><th>Nombre</th><th>Apellido</th><th>Rol</th><th>Email</th><th>Status</th><th>Acciones</th></tr></thead><tbody></tbody></table>
  </div></div></div></div></div>
</main>
<div class="modal fade" id="modalAdmin" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header headerRegister"><h5 class="modal-title" id="titleAdmin">Nuevo Administrativo</h5><button class="close" data-dismiss="modal"><span>&times;</span></button></div>
<div class="modal-body"><form id="formAdmin">
<input type="hidden" id="idPersona" name="idPersona" value="0">
<div class="form-row"><div class="form-group col-md-4"><label>CI</label><input class="form-control" name="txtCi" id="txtCiA" required></div>
<div class="form-group col-md-4"><label>Nombres</label><input class="form-control" name="txtNombre" id="txtNombreA" required></div>
<div class="form-group col-md-4"><label>Apellidos</label><input class="form-control" name="txtApellido" id="txtApellidoA" required></div></div>
<div class="form-row"><div class="form-group col-md-3"><label>Sexo</label><select class="form-control" name="listSexo" id="listSexoA"><option value="M">M</option><option value="F">F</option></select></div>
<div class="form-group col-md-3"><label>Celular</label><input class="form-control" name="txtCel" id="txtCelA" required></div>
<div class="form-group col-md-6"><label>Email</label><input type="email" class="form-control" name="txtEmail" id="txtEmailA" required></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Rol</label><select class="form-control" name="listRol" id="listRol"></select></div>
<div class="form-group col-md-6"><label>Dirección</label><input class="form-control" name="txtDireccion" id="txtDireccionA"></div></div>
<div class="form-row"><div class="form-group col-md-6"><label>Password (vacío=CI)</label><input class="form-control" name="txtPassword" id="txtPasswordA"></div>
<div class="form-group col-md-6"><label>Status</label><select class="form-control" name="listStatus" id="listStatusA"><option value="1">Activo</option><option value="0">Inactivo</option></select></div></div>
<div class="tile-footer"><button class="btn btn-primary" type="submit">Guardar</button> <button class="btn btn-danger" data-dismiss="modal" type="button">Cerrar</button></div>
</form></div></div></div></div>
<?php footerAdmin($data); ?>
