
<div class="modal fade" id="modalFormCurso" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModalCurso">Nuevo Curso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <!-- Altura del formulario  -->
            <form id="formCurso" name="formCurso" class="form-horizontal">
              <input type="hidden" id="idParalelo" name="idParalelo" value="0">
              <p class="text-primary">Todos los campos son obligatorios.</p>

              <div class="form-row">
                
                <div class="form-group col-md-4">
                    <label for="listGrado">Nivel:</label>
                    <select class="form-control selectpicker" id="listGrado" name="listGrado" required >
                        <option value="Inicial">Inicial</option>
                        <option value="1">Primero</option>
                        <option value="2">Segundo</option>
                        <option value="3">Tercero</option>
                        <option value="4">Cuarto</option>
                        <option value="5">Quinto</option>
                        <option value="6">Sexto</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="listSigla">Paralelo:</label>
                    <select class="form-control selectpicker" id="listSigla" name="listSigla" required >
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="listTurno">Turno:</label>
                    <select class="form-control selectpicker" id="listTurno" name="listTurno" required >
                        <option value="M">Mañana</option>
                        <option value="T">Tarde</option>
                    </select>
                </div>

              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="txtDescripcion">Descripción del Curso: </label>
                    <textarea class="form-control" id="txtDescripcion" name="txtDescripcion"rows="2"></textarea>
                </div>                
              </div>
           
              <div class="form-row">
                <div class="form-group col-md-8">
                  <label for="listTutorDocente">Tutor (docente de la unidad):</label>
                  <select class="form-control" id="listTutorDocente" name="listTutorDocente" required="">
                    <option value="0">Sin asignación</option>
                  </select>
                  <small class="form-text text-muted">Solo docentes activos registrados en el sistema.</small>
                </div>
                

                <div class="form-group col-md-4">
                    <label for="TipoEstudiante">Status:</label>
                    <select class="form-control selectpicker" id="TipoEstudiante" name="TipoEstudiante" required >
                        <option value="1">Activo</option>
                        <option value="2">Inactivo</option>
                    </select>
                </div>
             </div>
             
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Registrar Curso</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>




<!-- --------------------------------------------------------------------------- -->
<div class="modal fade" id="modalFormMateria" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nueva Materia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="tile">
            <div class="tile-body">
            <form id="formMatricula" name="formMatricula" class="form-horizontal">
              <input type="hidden" id="idEstudiante" name="idEstudiante" value="">
              <p class="text-primary">Todos los campos son obligatorios.</p>

              <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="txtAreaMateria">Área: </label>
                    <select class="form-control selectpicker" id="txtAreaMateria" name="txtAreaMateria" required >
                        <option value="1">Lenguaje y Comunicación</option>
                        <option value="2">Matemáticas</option>
                        <option value="3">Ciencias Naturales	</option>
                        <option value="4">Ciencias Sociales</option>
                        <option value="5">Educación Artística</option>
                        <option value="5">Educación Física y Deportes</option>
                        <option value="5">Religión</option>
                        <option value="5">Tecnología</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="txtCampoMateria">Campo: </label>
                    <select class="form-control selectpicker" id="txtEstudiante" name="txtEstudiante" required >
                        <option value="1">Comunicación y Lenguajes</option>
                        <option value="2">Matemáticas</option>
                        <option value="3">Ciencia, Tecnología y Producción</option>
                        <option value="4">Comunidad y Sociedad</option>
                        <option value="5">Comunicación y Expresión Cultural</option>
                        <option value="5">Educación Física</option>
                        <option value="5">Religiosos</option>
                    </select>
                </div>
               
              </div>
              <div class="form-row">
                    <div class="form-group col-md-12">
                        <label for="txtDesMAteria">Descripción de Materia: </label>
                        <textarea class="form-control" id="txtDesMAteria" name="txtDesMAteria"rows="1"></textarea>
                    </div>                
              </div>    
                

              <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="txtCampoMateria">Asignación de Docente: </label>
                    <select class="form-control selectpicker" id="txtEstudiante" name="txtEstudiante" required >
                        <option value="1">Jose Enrique Terceros</option>
                        <option value="2">Ricardo Antonio Miño</option>
                        <option value="3">Pedro Pascal</option>
                        <option value="4">Josh Daniel Bauroro</option>
                        <option value="5">Jose Fernando Marquez</option>
                        <option value="6">Mikaela Torrez Cayo</option>
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label for="listCursoAsign">Curso: </label>
                    <select class="form-control selectpicker" id="listCursoAsign" name="listCursoAsign" required >
                        <option value="1">Primero - A</option>
                        <option value="2">Segundo - A</option>
                        <option value="3">Tercero - A</option>
                        <option value="4">Cuerto - A</option>
                        <option value="5">Quinto - A</option>
                        <option value="6">Sexto - A</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="StatusMAteria">Status: </label>
                    <select class="form-control selectpicker" id="StatusMAteria" name="StatusMAteria" required >
                        <option value="1">Activa</option>
                        <option value="2">Inactiva</option>
                    </select>
                </div>
              </div>
              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Registrar</span></button>&nbsp;&nbsp;&nbsp;
                <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar</button>
              </div>
            </form>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>





<!-- --------------------------------------------------------------------------- -->
<div class="modal fade" id="modalViewCurso" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header headerRegister">
        <input type="hidden" id="listarEstudiantes" value="">
        <h5 class="modal-title">Curso</h5>
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="text-rigth">
          <button id="btnImprimirLista" class="btn btn-secondary" type="button" onclick="fntImprimirListaCurso();" ><i class="fa fa-print"></i> Imprimir lista</button>
        </div>
        <div class="row">
            <div class="col-md-12 text-align-center">
                <div class="tile">
                    <div class="tile-body">
                        <h4>Datos generales del curso</h4>
                        <form class="form-horizontal">
                            <div class="form-group row">
                                <label class="control-label col-md-2">Curso: </label>
                                <div class="col-md-4">
                                    <input class="form-control"  name="datoCurso" id="datoCurso" type="text" placeholder="" value="" disabled>
                                </div>
                                
                                <label class="control-label col-md-2">Estado:</label>
                                <div class="col-md-4">
                                    <input class="form-control col-md-8" name="datoEstado" id="datoEstado"  type="text" placeholder=" "value="" disabled>
                                </div>
                                
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-md-2">Turno:</label>
                                <div class="col-md-4">
                                    <input class="form-control col-md-8" name="datoTurno" id="datoTurno"  type="text" placeholder="" Value="" disabled>
                                </div>
                                <label class="control-label col-md-2">Tutor:</label>
                                <div class="col-md-4">
                                    <input class="form-control col-md-8" name="datoTutor" id="datoTutor"  type="text" placeholder="" value="" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label col-md-2">Cantidad de Estudiantes:</label>
                                <div class="col-md-4">
                                    <input class="form-control col-md-8" name="datoInscritos" id="datoInscritos"  type="text" placeholder="" value="" disabled>
                                </div>

                                <label class="control-label col-md-2">Cupo: </label>

                                <div class="col-md-4">
                                    <input class="form-control" name="datoParalelo" id="datoParalelo"  type="text" placeholder="" value="" disabled>
                                </div>
                            </div>

                            
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <h4>Estudiantes Matriculados</h4>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tableEstudiantes">
                                <thead>
                                    <tr>
                                    <th>CI</th>
                                    <th>Matricula</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acceso al Sistema</th>
                                    <th>Ver</th>
                                    </tr>
                                </thead>
                                <tbody id="listCursoEstudiantes">
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Datos Estudiante-->


<!-- Modal View other -->
<div class="modal fade" id="modalViewEstudianteOther" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Datos del Estudiante</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Datos Generales -->
        <h6 class="border-bottom pb-2 mb-3 text-primary">Datos Generales</h6>
        <table class="table table-bordered">
          
          <tbody>
            <tr>
              <td><strong>C.I.:</strong></td>
              <td id="celStudentCi">*</td>
            </tr>
            <tr>
              <td><strong>Nombres:</strong></td>
              <td id="celStudentNombre">*</td>
            </tr>
            <tr>
              <td><strong>Apellidos:</strong></td>
              <td id="celStudentApellido">*</td>
            </tr>
            <tr>
              <td><strong>Sexo:</strong></td>
              <td id="celStudentSexo">*</td>
            </tr>

            <tr>
              <td><strong>Fecha de Nacimiento</strong></td>
              <td id="celStudentFechaNacimiento">*</td>
            </tr>

            <tr>
              <td><strong>Ciudad:</strong></td>
              <td id="celStudentCiudad">*</td>
            </tr>

            <tr>
              <td><strong>Pais:</strong></td>
              <td id="celStudentPais">*</td>
            </tr>
            <tr>
              <td><strong>Provincia:</strong></td>
              <td id="celStudentProvincia">*</td>
            </tr>
          </tbody>
        </table>
        
        <!-- Contacto -->
        <h6 class="border-bottom pb-2 mb-3 text-primary">Contacto</h6>
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td><strong>Nº Celular:</strong></td>
              <td id="celStudentCelular">*</td>
            </tr>
            <tr>
              <td><strong>Domicilio:</strong></td>
              <td id="celStudentDomicilio">*</td>
            </tr>
            <tr>
              <td><strong>Correo:</strong></td>
              <td id="celStudentEmail">*</td>
            </tr>
            <tr>
              <td><strong>Usuario de Acceso:</strong></td>
              <td id="celStudentUsuario">*</td>
            </tr>
            <tr>
              <td><strong>En caso de emergencia:</strong></td>
              <td id="celStudentEmergencia">*</td>
            </tr>
            
          </tbody>
        </table>
        
        <!-- Estado -->
        <h6 class="border-bottom pb-2 mb-3 text-primary">Datos Estudiante</h6>
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td><strong>RUDE:</strong></td>
              <td id="celStudentRude">*</td>
            </tr>
            <tr>
              <td><strong>Colegio Procedencia:</strong></td>
              <td id="celStudentColPRocedencia">*</td>
            </tr>
            <tr>
              <td><strong>Estudiante:</strong></td>
              <td id="celStudentEstadoRegEst">*</td>
            </tr>
            <tr>
              <td><strong>Fecha de Registro:</strong></td>
              <td id="celStudentFechaRegEst">*</td>
            </tr>
            <tr>
              <td><strong>Estado:</strong></td>
              <td id="celStudentStatus">*</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>




