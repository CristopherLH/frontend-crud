<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">

    <title>CRUD USERS!</title>

  </head>
  <body>

    <div class="container-fluid">
    <h1>Usuarios</h1>

    <button class="btn btn-info create_update_view btn-sm mb-2"><i class="fa fa-plus mr-2"></i> Crear Usuario</button>
      <div class="form-row content-header">
        <div class="mb-3 col-6 align-self-center">
          <small class="text-muted">Buscar por Nombre o Login</small>
          <div class="input-group">
            <input type="text" class="form-control form-control-sm" id="p_filter" placeholder="Buscar" >
            <div class="input-group-append">
              <button class="btn btn-outline-secondary btn-sm" id="search" type="button">
                <i class="fa fa-search"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-hover table-striped" id="tbl_users">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Estado</th>
                  <th>Login</th>
                  <th>F. Creación</th>
                  <th>F. Actualización</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="modal fade" tabindex="-1" id="modal_frm_user">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script
    src="https://code.jquery.com/jquery-3.5.1.min.js"
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
    crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>

    let urlApi = 'http://localhost/backend/Callback.php'

      $.getJSON(urlApi, {
        action: 'get_states'
      }, res => {
        $('.content-header').append(res.data)
      }).fail(req => {
        toastr.error('No se puedo realizar conexión al servidor')
      })

      function loadUser(p_filter = '', p_state_user = '') {
        $('#tbl_users tbody').html('')
        $.getJSON(urlApi, {
          action: 'get_users',
          p_filter,
          p_state_user
        }, res => {
          let data = ''
          res.data.forEach(r => {
            data += `
              <tr>
                <td>${r.id_user}</td>
                <td>${r.name_user}</td>
                <td>${r.last_name_user}</td>
                <td><strong class="text-${parseInt(r.state_user) === 1 ? 'success' : (parseInt(r.state_user) === 2 ? 'warning' : 'danger')}">
                ${parseInt(r.state_user) === 1 ? 'ACTIVO' : (parseInt(r.state_user) === 2 ? 'BLOQUEADO' : 'ELIMINADO')}
                </strong></td>
                <td>${r.login_user}</td>
                <td>${r.date_created}</td>
                <td>${r.date_updated === null ? '<strong class="text-info">No ha sido actualizado</strong>' : r.date_updated}</td>
                <td>
                  ${parseInt(r.state_user) === 3 ? `
                    <button class="btn btn-success btn-sm active_user" data-toggle="tooltip" title="Activar" data-id-user="${r.id_user}">
                    <i class="fa fa-power-off"></i>
                  </button>` : `  
                  <button class="btn btn-primary btn-sm create_update_view" data-toggle="tooltip" title="Editar" data-id-user="${r.id_user}">
                    <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-sm delete_user" data-toggle="tooltip" title="Eliminar" data-id-user="${r.id_user}">
                    <i class="fa fa-power-off"></i>
                  </button>
                  `}
                </td>
                
              </tr>
            `
          })
          $('#tbl_users tbody').html(data)
        }).fail(req => {
          toastr.error('No se puedo realizar conexión al servidor')
        })
      }

      loadUser()

      $(document).on({
        click: function() {
          $('#modal_frm_user .modal-title').html($(this).data().idUser ? 'Modificar Usuario #' + $(this).data().idUser : 'Registrar Usuario')
         
          $.getJSON(urlApi, {
            action: 'get_users_id',
            id_user: $(this).data().idUser
          }, res => {
            $('#modal_frm_user .modal-body').html(res)
            $('#modal_frm_user').modal('show')
            $('[data-toggle="tooltip"]').tooltip('hide')
          }).fail(req => {
            toastr.error('No se puedo realizar conexión al servidor')
          })
        }
      }, '.create_update_view')

      $(document).on({
        submit: function(e) {
          e.preventDefault()
          
          if (!this.checkValidity()) {
            $(this).addClass('was-validated')
            return
          }

          $(this).removeClass('was-validated')

          $.post(urlApi + '?action=users_insert_update', $(this).serialize(), res => {
            if (res.response === 'danger') {
              toastr.error(res.message)
            } else if (res.response === 'warning') {
              toastr.warning(res.message)
            } else {
              toastr.success(res.message)
              $('#modal_frm_user').modal('hide')
              loadUser()
            }
          }).fail(req => {
            toastr.error('No se puedo realizar conexión al servidor')
          })
        }
      }, '#frm_user')

      $(document).on({
        click: function() {
          $.post(urlApi, {
            action: 'user_delete',
            id_user: $(this).data().idUser
          }, res => {
            toastr.success(res.message)
            loadUser()
          }).fail(req => {
            toastr.error('No se puedo realizar conexión al servidor')
          })
        }
      }, '.delete_user')

      $(document).on({
        click: function() {
         
          $.post(urlApi, {
            action: 'user_active',
            id_user: $(this).data().idUser
          }, res => {
            toastr.success(res.message)
            loadUser()
          }).fail(req => toastr.error('No se puedo realizar conexión al servidor'))
        }
      }, '.active_user')
      
      $(document).on({
        click: function() {
          loadUser($('#p_filter').val())
        }
      }, '#search')

      $(document).on({
        change: function () {
          loadUser('', $(this).val())
        }
      }, '#cbo_state_user')
    </script>
  </body>
</html>