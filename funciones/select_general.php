<?php
function Eliminar_Cliente($vConexion , $vIdConsulta) {


    //soy admin 
        $SQL_MiConsulta="SELECT idCliente FROM clientes 
                        WHERE idCliente = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idCliente']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM clientes WHERE idCliente = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Eliminar_Turno($vConexion , $vIdConsulta) {


    //soy admin 
        $SQL_MiConsulta="SELECT IdTurno FROM turnos 
                        WHERE IdTurno = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['IdTurno']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM turnos WHERE IdTurno = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Datos_Cliente($vConexion , $vIdCliente) {
    $DatosCliente  =   array();
    //me aseguro que la consulta exista
    $SQL = "SELECT * FROM clientes 
            WHERE idCliente = $vIdCliente";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs) ;
    if (!empty($data)) {
        $DatosCliente['ID_CLIENTE'] = $data['idCliente'];
        $DatosCliente['NOMBRE'] = $data['nombre'];
        $DatosCliente['APELLIDO'] = $data['apellido'];
        $DatosCliente['TELEFONO'] = $data['telefono'];
        $DatosCliente['DIRECCION'] = $data['direccion'];
        $DatosCliente['EMAIL'] = $data['email'];
        $DatosCliente['DNI'] = $data['dni'];
    }
    return $DatosCliente;

}

function Validar_Cliente(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Nombre']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un nombre con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Apellido']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un apellido con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Direccion']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar una direccion con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Telefono']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un telefono con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Email']) < 5) {
        $_SESSION['Mensaje'].='Debes ingresar un correo con al menos 5 caracteres. <br />';
    }
    if (strlen($_POST['DNI']) < 8) {
        $_SESSION['Mensaje'].='Debes ingresar un DNI con al menos 8 caracteres. <br />';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    foreach($_POST as $Id=>$Valor){
        $_POST[$Id] = trim($_POST[$Id]);
        $_POST[$Id] = strip_tags($_POST[$Id]);
    }

    return $_SESSION['Mensaje'];
}

function Modificar_Cliente($vConexion) {
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($vConexion, $_POST['Apellido']);
    $telefono = mysqli_real_escape_string($vConexion, $_POST['Telefono']);
    $direccion = mysqli_real_escape_string($vConexion, $_POST['Direccion']);
    $email = mysqli_real_escape_string($vConexion, $_POST['Email']);
    $dni = mysqli_real_escape_string($vConexion, $_POST['DNI']);
    $idCliente = mysqli_real_escape_string($vConexion, $_POST['IdCliente']);

    $SQL_MiConsulta = "UPDATE clientes 
    SET nombre = '$nombre',
    apellido = '$apellido',
    telefono = '$telefono',
    direccion = '$direccion',
    email = '$email',
    dni = '$dni'
    WHERE idCliente = '$idCliente'";

    if ( mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    }else {
        return false;
    }
    
}

function InsertarProveedor($vConexion) {
    // Prevenir SQL Injection usando consultas preparadas
    $SQL_Insert = "INSERT INTO proveedores (razon_social, cuit, telefono, email) 
                   VALUES (?, ?, ?, ?)";
    
    $stmt = $vConexion->prepare($SQL_Insert);
    
    if (!$stmt) {
        die('<h4>Error al preparar la consulta: ' . $vConexion->error . '</h4>');
    }

    // Vincular parámetros (s=string, i=entero)
    $stmt->bind_param(
        "siis", 
        $_POST['RazonSocial'], 
        $_POST['CUIT'], 
        $_POST['Telefono'], 
        $_POST['Email']
    );

    if (!$stmt->execute()) {
        die('<h4>Error al insertar el registro: ' . $stmt->error . '</h4>');
    }

    return true;
}

function Validar_Proveedor(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['RazonSocial']) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar una razon social con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['CUIT']) < 8) {
        $_SESSION['Mensaje'].='Debes ingresar una CUIT con al menos 8 caracteres. <br />';
    }
    if (strlen($_POST['Telefono']) < 8) {
        $_SESSION['Mensaje'].='Debes ingresar un telefono con al menos 8 caracteres. <br />';
    }
    if (strlen($_POST['Email']) < 8) {
        $_SESSION['Mensaje'].='Debes ingresar un correo con al menos 8 caracteres. <br />';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    foreach($_POST as $Id=>$Valor){
        $_POST[$Id] = trim($_POST[$Id]);
        $_POST[$Id] = strip_tags($_POST[$Id]);
    }

    return $_SESSION['Mensaje'];
}

function Listar_Proveedores($vConexion) {

    $Listado = array();

    // 1) Genero la consulta que deseo
    $SQL = "SELECT * FROM proveedores";

    // 2) A la conexión actual le brindo mi consulta, y el resultado lo entrego a variable $rs
    $rs = mysqli_query($vConexion, $SQL);

    // 3) El resultado deberá organizarse en una matriz, entonces lo recorro
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_PROVEEDOR'] = $data['idProveedor'];
        $Listado[$i]['RAZON_SOCIAL'] = $data['razon_social'];
        $Listado[$i]['CUIT'] = $data['cuit'];
        $Listado[$i]['TELEFONO'] = $data['telefono'];
        $Listado[$i]['EMAIL'] = $data['email'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Listar_Proveedores_Parametro($vConexion,$criterio,$parametro) {
    $Listado=array();

      //1) genero la consulta que deseo segun el parametro
        $sql = "SELECT * FROM proveedores";
        switch ($criterio) { 
        case 'RazonSocial': 
        $sql = "SELECT * FROM proveedores WHERE razon_social LIKE '%$parametro%'";
        break;
        case 'CUIT':
        $sql = "SELECT * FROM proveedores WHERE cuit LIKE '%$parametro%'";
        break;
        case 'Telefono':
        $sql = "SELECT * FROM proveedores WHERE telefono LIKE '%$parametro%'";
        break;
        case 'Email':
        $sql = "SELECT * FROM proveedores WHERE email LIKE '%$parametro%'";
        break;
        }    
        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $sql);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_PROVEEDOR'] = $data['idProveedor'];
            $Listado[$i]['RAZON_SOCIAL'] = $data['razon_social'];
            $Listado[$i]['CUIT'] = $data['cuit'];
            $Listado[$i]['TELEFONO'] = $data['telefono'];
            $Listado[$i]['EMAIL'] = $data['email'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Eliminar_Proveedor($vConexion , $vIdConsulta) {

    //soy admin 
        $SQL_MiConsulta="SELECT idProveedor FROM proveedores 
                        WHERE idProveedor = $vIdConsulta "; 
     
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idProveedor']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM proveedores WHERE idProveedor = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Datos_Proveedor($vConexion, $vIdProveedor) {
    $DatosProveedor = array();
    // Me aseguro que la consulta exista
    $SQL = "SELECT * FROM proveedores 
            WHERE idProveedor = $vIdProveedor";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosProveedor['ID_PROVEEDOR'] = $data['idProveedor'];
        $DatosProveedor['RAZON_SOCIAL'] = $data['razon_social'];
        $DatosProveedor['CUIT'] = $data['cuit'];
        $DatosProveedor['TELEFONO'] = $data['telefono'];
        $DatosProveedor['EMAIL'] = $data['email'];
    }
    return $DatosProveedor;
}

function Modificar_Proveedor($vConexion) {
    $razon_social = mysqli_real_escape_string($vConexion, $_POST['RazonSocial']);
    $cuit = mysqli_real_escape_string($vConexion, $_POST['CUIT']);
    $telefono = mysqli_real_escape_string($vConexion, $_POST['Telefono']);
    $email = mysqli_real_escape_string($vConexion, $_POST['Email']);
    $idProveedor = mysqli_real_escape_string($vConexion, $_POST['IdProveedor']);

    $SQL_MiConsulta = "UPDATE proveedores 
    SET razon_social = '$razon_social',
        cuit = '$cuit',
        telefono = '$telefono',
        email = '$email'
    WHERE idProveedor = '$idProveedor'";

    if (mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    } else {
        return false;
    }
}

function Datos_Turno($vConexion, $vIdTurno) {
    $DatosTurno = array();
    
    $SQL = "SELECT t.*, 
                   GROUP_CONCAT(dt.idTipoServicio) AS servicios_seleccionados
            FROM turnos t
            LEFT JOIN detalle_turno dt ON t.IdTurno = dt.idTurno
            WHERE t.IdTurno = $vIdTurno
            GROUP BY t.IdTurno";

    $rs = mysqli_query($vConexion, $SQL);
    
    if ($rs && mysqli_num_rows($rs) > 0) {
        $data = mysqli_fetch_assoc($rs);
        
        // Mantener la estructura original del array
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        $DatosTurno['HORARIO'] = $data['Horario'];
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['servicios_seleccionados'] = $data['servicios_seleccionados']; // Ahora viene de detalle_turno
        $DatosTurno['ESTILISTA'] = $data['IdEstilista'];
        $DatosTurno['ESTADO'] = $data['IdEstado'];
        $DatosTurno['CLIENTE'] = $data['IdCliente'];
    }
    
    return $DatosTurno;
}

function Validar_Turno(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Fecha']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar una fecha. <br />';
    }
    if (strlen($_POST['Horario']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar un horario. <br />';
    }
    if (empty($_POST['TipoServicio']) || in_array('', $_POST['TipoServicio'])) {
        $_SESSION['Mensaje'] .= 'Debes seleccionar al menos un Tipo de Servicio válido.<br />';
    }
    if ($_POST['Estilista'] === 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Estilista. <br />';
    }
    if ($_POST['Cliente'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Cliente. <br />';
    }

    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    //foreach($_POST as $Id=>$Valor){
    //    $_POST[$Id] = trim($_POST[$Id]);
    //    $_POST[$Id] = strip_tags($_POST[$Id]);
    //}

    return $_SESSION['Mensaje'];
}

function Datos_Turno_Comprobante($vConexion, $vIdTurno) {
    $DatosTurno = array();

    // Consulta para obtener los datos del turno junto con los valores de las tablas relacionadas
    $SQL = "SELECT 
                t.IdTurno, 
                t.Horario, 
                t.Fecha, 
                ts.Denominacion AS TIPO_SERVICIO, 
                CONCAT(e.Apellido, ', ', e.Nombre) AS ESTILISTA, 
                es.Denominacion AS ESTADO, 
                CONCAT(c.apellido, ', ', c.nombre) AS CLIENTE
            FROM turnos t
            LEFT JOIN tipo_servicio ts ON t.IdTipoServicio = ts.IdTipoServicio
            LEFT JOIN estilista e ON t.IdEstilista = e.IdEstilista
            LEFT JOIN estado es ON t.IdEstado = es.IdEstado
            LEFT JOIN clientes c ON t.IdCliente = c.idCliente
            WHERE t.IdTurno = $vIdTurno";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        $DatosTurno['HORARIO'] = $data['Horario'];
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['TIPO_SERVICIO'] = $data['TIPO_SERVICIO'];
        $DatosTurno['ESTILISTA'] = $data['ESTILISTA'];
        $DatosTurno['ESTADO'] = $data['ESTADO'];
        $DatosTurno['CLIENTE'] = $data['CLIENTE'];
    }

    return $DatosTurno;
}

function Modificar_Turno($vConexion) {
    // 1. Actualizar datos principales del turno
    $SQL_Update = "UPDATE turnos SET
                    Fecha = '".$_POST['Fecha']."',
                    Horario = '".$_POST['Horario']."',
                    IdEstilista = '".$_POST['Estilista']."',
                    IdCliente = '".$_POST['Cliente']."',
                    IdEstado = '".$_POST['Estado']."'
                   WHERE IdTurno = ".$_POST['IdTurno'];

    if (!mysqli_query($vConexion, $SQL_Update)) {
        die('<h4>Error al actualizar el turno.</h4>');
    }

    // 2. Actualizar servicios
    // Eliminar servicios anteriores
    $SQL_Delete = "DELETE FROM detalle_turno WHERE idTurno = ".$_POST['IdTurno'];
    mysqli_query($vConexion, $SQL_Delete);

    // Insertar nuevos servicios
    if (!empty($_POST['TipoServicio'])) {
        foreach ($_POST['TipoServicio'] as $idTipoServicio) {
            $SQL_Insert = "INSERT INTO detalle_turno (idTurno, idTipoServicio)
                           VALUES (".$_POST['IdTurno'].", $idTipoServicio)";
            mysqli_query($vConexion, $SQL_Insert);
        }
    }
    
    return true;
}

function Listar_Tipos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT IdTipoServicio , Denominacion
        FROM tipo_servicio
        ORDER BY Denominacion";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['IdTipoServicio'];
            $Listado[$i]['DENOMINACION'] = $data['Denominacion'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Estilistas($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT IdEstilista , Apellido , Nombre
        FROM estilista
        ORDER BY Apellido";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['IdEstilista'];
            $Listado[$i]['APELLIDO'] = $data['Apellido'];
            $Listado[$i]['NOMBRE'] = $data['Nombre'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Clientes_Turnos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT idCliente , apellido , nombre
        FROM clientes
        ORDER BY Apellido";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['idCliente'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Estados_Turnos($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT IdEstado , Denominacion
        FROM estado
        ORDER BY IdEstado";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['IdEstado'];
            $Listado[$i]['DENOMINACION'] = $data['Denominacion'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Turnos($vConexion) {
    $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.Nombre AS NOMBRE_E, e.Apellido AS APELLIDO_E, 
                   c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
            FROM turnos t
            INNER JOIN estilista e ON t.IdEstilista = e.IdEstilista
            INNER JOIN clientes c ON t.IdCliente = c.idCliente
            INNER JOIN estado es ON t.IdEstado = es.IdEstado
            ORDER BY t.Fecha DESC, t.Horario DESC";

    $resultado = mysqli_query($vConexion, $SQL);
    
    $Listado = array();
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $Listado[] = $fila;
        }
    }
    return $Listado;
}

function Listar_Servicios_Por_Turno($vConexion, $idTurno) {
    $SQL = "SELECT ts.Denominacion 
            FROM detalle_turno dt
            INNER JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
            WHERE dt.idTurno = $idTurno";

    $resultado = mysqli_query($vConexion, $SQL);
    $servicios = array();
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $servicios[] = $fila['Denominacion'];
        }
    }
    return $servicios;
}

function Listar_Turnos_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    switch ($criterio) {
        case 'Cliente':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.Nombre AS ESTILISTA_N,
                        e.Apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.IdEstado,
                        es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estilista e ON t.IdEstilista = e.idEstilista
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE CONCAT(c.Apellido, ' ', c.Nombre) LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        case 'Estilista':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.Nombre AS ESTILISTA_N,
                        e.Apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.IdEstado,
                        es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN estilista e ON t.IdEstilista = e.idEstilista
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE CONCAT(e.Apellido, ' ', e.Nombre) LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.Nombre AS ESTILISTA_N,
                        e.Apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.IdEstado,
                        es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN estilista e ON t.IdEstilista = e.idEstilista
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.Fecha LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        case 'TipoServicio':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.Nombre AS ESTILISTA_N,
                        e.Apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.Denominacion AS ESTADO,
                        es.IdEstado,
                        GROUP_CONCAT(ts.Denominacion SEPARATOR ', ') AS SERVICIOS
                    FROM turnos t
                    INNER JOIN estilista e ON t.IdEstilista = e.idEstilista
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    LEFT JOIN detalle_turno dt ON t.IdTurno = dt.idTurno
                    LEFT JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
                    WHERE ts.Denominacion LIKE '%$parametro%'
                    GROUP BY t.IdTurno
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        default:
            return $Listado;
    }

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['IdTurno'] = $data['IdTurno'];
        $Listado[$i]['IdEstado'] = $data['IdEstado'];
        $Listado[$i]['Fecha'] = $data['Fecha'];
        $Listado[$i]['Horario'] = $data['Horario'];
        $Listado[$i]['NOMBRE_E'] = $data['ESTILISTA_N'];
        $Listado[$i]['APELLIDO_E'] = $data['ESTILISTA_A'];
        $Listado[$i]['NOMBRE_C'] = $data['CLIENTE_N'];
        $Listado[$i]['APELLIDO_C'] = $data['CLIENTE_A'];
        $Listado[$i]['ESTADO'] = $data['ESTADO'];
        $i++;
    }

    return $Listado;
}

function InsertarTurnos($vConexion) {
    // Insertar el turno en la tabla TURNOS (sin IdTipoServicio)
    $SQL_Insert = "INSERT INTO turnos (Horario, Fecha, IdEstilista, IdEstado, IdCliente)
                   VALUES (
                       '" . $_POST['Horario'] . "',
                       '" . $_POST['Fecha'] . "',
                       '" . $_POST['Estilista'] . "',
                       '1',
                       '" . $_POST['Cliente'] . "'
                   )";

    if (!mysqli_query($vConexion, $SQL_Insert)) {
        die('<h4>Error al insertar el turno.</h4>');
    }

    // Obtener el ID del turno recién creado
    $idTurno = mysqli_insert_id($vConexion);

    // Insertar cada tipo de servicio en DETALLE_TURNO
    foreach ($_POST['TipoServicio'] as $idTipoServicio) {
        $SQL_Detalle = "INSERT INTO detalle_turno (idTurno, idTipoServicio)
                        VALUES ('$idTurno', '$idTipoServicio')";

        if (!mysqli_query($vConexion, $SQL_Detalle)) {
            die('<h4>Error al insertar el detalle del turno.</h4>');
        }
    }

    return true;
}

function ColorDeFila($vFecha,$vEstado) {
    $Title='';
    $Color=''; 
    $FechaActual = date("Y-m-d");

    if ($vFecha < $FechaActual && $vEstado!=3){
        //la fecha del viaje es mayor a mañana?
        $Title='Turno Vencido';
        $Color='table-danger'; 
    
    } else if ($vEstado == 2){
        //Turno en Curso
        $Title='Turno en Curso';
        $Color='table-warning'; 
    } else if ($vEstado==3){
        //Turno Completado
        $Title='Turno Completado';
        $Color='table-success'; 
    } else if ($vEstado == 1){
        //Turno pendiente
        $Title='Turno Pendiente';
        $Color='table-primary';
    }
        
    
    return [$Title, $Color];

}

function Listar_Horarios_Ocupados($MiConexion, $fecha) {
    $query = "SELECT Horario FROM turnos WHERE Fecha = ?";
    $stmt = $MiConexion->prepare($query);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    $horariosOcupados = [];
    while ($row = $result->fetch_assoc()) {
        $horariosOcupados[] = $row['Horario'];
    }

    return $horariosOcupados;
}

function Validar_Producto(){
    $vMensaje='';
        
    if (strlen($_POST['Nombre']) < 3) {
        $vMensaje.='Debes ingresar un nombre con al menos 3 caracteres. <br />';
    }
    if (strlen($_POST['Precio']) < 1) {
        $vMensaje.='Debes ingresar un precio con al menos 1 caracter. <br />';
    }
    if (strlen($_POST['Stock']) < 1) {
        $vMensaje.='Debes ingresar un stock con al menos 1 caracter. <br />';
    }
           
    //con esto aseguramos que limpiamos espacios y limpiamos de caracteres de codigo ingresados
    foreach($_POST as $Id=>$Valor){
        $_POST[$Id] = trim($_POST[$Id]);
        $_POST[$Id] = strip_tags($_POST[$Id]);
    }
    
    
    return $vMensaje;
    
}

function InsertarProductos($vConexion) {
    // Obtengo la fecha actual en formato 'YYYY-MM-DD'
    $fechaActual = date('Y-m-d');

    $SQL_Insert = "INSERT INTO productos (nombre, descripcion, precio, stock, fechaRegistro, idActivo)
    VALUES ('" . $_POST['Nombre'] . "' , '" . $_POST['Descripcion'] . "' , '" . $_POST['Precio'] . "', '" . $_POST['Stock'] . "', '$fechaActual', '1')";

    if (!mysqli_query($vConexion, $SQL_Insert)) {
        // Si surge un error, finalizo la ejecución del script con un mensaje
        die('<h4>Error al intentar insertar el registro.</h4>');
    }

    return true;
}

function Listar_Productos($vConexion) {

    $Listado = array();

    // 1) Genero la consulta que deseo
    $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
            FROM productos
            ORDER BY nombre";

    // 2) A la conexión actual le brindo mi consulta, y el resultado lo entrego a la variable $rs
    $rs = mysqli_query($vConexion, $SQL);

    // 3) El resultado deberá organizarse en una matriz, entonces lo recorro
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_PRODUCTO'] = $data['idProducto'];
        $Listado[$i]['NOMBRE'] = $data['nombre'];
        $Listado[$i]['DESCRIPCION'] = $data['descripcion'];
        $Listado[$i]['PRECIO'] = $data['precio'];
        $Listado[$i]['STOCK'] = $data['stock'];
        $Listado[$i]['FECHA_REGISTRO'] = $data['fechaRegistro'];
        $Listado[$i]['ACTIVO'] = $data['idActivo'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Listar_Productos_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    // 1) Genero la consulta que deseo
    switch ($criterio) {
        case 'Nombre':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE nombre LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
        case 'Descripcion':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE descripcion LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
        case 'Precio':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE precio LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
        case 'Stock':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE stock LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
    }

    // 2) A la conexión actual le brindo mi consulta, y el resultado lo entrego a la variable $rs
    $rs = mysqli_query($vConexion, $SQL);

    // 3) El resultado deberá organizarse en una matriz, entonces lo recorro
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_PRODUCTO'] = $data['idProducto'];
        $Listado[$i]['NOMBRE'] = $data['nombre'];
        $Listado[$i]['DESCRIPCION'] = $data['descripcion'];
        $Listado[$i]['PRECIO'] = $data['precio'];
        $Listado[$i]['STOCK'] = $data['stock'];
        $Listado[$i]['FECHA_REGISTRO'] = $data['fechaRegistro'];
        $Listado[$i]['ACTIVO'] = $data['idActivo'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Datos_Producto($vConexion, $vIdProducto) {
    $DatosProducto = array();
    // Me aseguro que la consulta exista
    $SQL = "SELECT * FROM productos 
            WHERE idProducto = $vIdProducto";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosProducto['ID_PRODUCTO'] = $data['idProducto'];
        $DatosProducto['NOMBRE'] = $data['nombre'];
        $DatosProducto['DESCRIPCION'] = $data['descripcion'];
        $DatosProducto['PRECIO'] = $data['precio'];
        $DatosProducto['STOCK'] = $data['stock'];
        $DatosProducto['FECHA_REGISTRO'] = $data['fechaRegistro'];
        $DatosProducto['ACTIVO'] = $data['idActivo'];
    }
    return $DatosProducto;
}

function Modificar_Producto($vConexion) {
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $descripcion = mysqli_real_escape_string($vConexion, $_POST['Descripcion']);
    $precio = mysqli_real_escape_string($vConexion, $_POST['Precio']);
    $stock = mysqli_real_escape_string($vConexion, $_POST['Stock']);
    $activo = mysqli_real_escape_string($vConexion, $_POST['Activo']);
    $idProducto = mysqli_real_escape_string($vConexion, $_POST['IdProducto']);

    $SQL_MiConsulta = "UPDATE productos 
    SET nombre = '$nombre',
    descripcion = '$descripcion',
    precio = '$precio',
    stock = '$stock',
    idActivo = '$activo'
    WHERE idProducto = '$idProducto'";

    if (mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    } else {
        return false;
    }
}

function Eliminar_Producto($vConexion, $vIdProducto) {

    // Verifico que el producto exista
    $SQL_MiConsulta = "SELECT idProducto FROM productos 
                        WHERE idProducto = $vIdProducto";

    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idProducto'])) {
        // Si el producto existe, lo elimino
        mysqli_query($vConexion, "DELETE FROM productos WHERE idProducto = $vIdProducto");
        return true;
    } else {
        return false;
    }
}

function Listar_Productos_Bajo_Stock($conexion) {
    $sql = "SELECT * FROM productos WHERE stock <= 10 ORDER BY stock ASC";
    $resultado = mysqli_query($conexion, $sql);
    $productos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $productos[] = $fila;
    }
    return $productos;
}

function Eliminar_Venta($vConexion , $vIdConsulta) {


    //soy admin 
        $SQL_MiConsulta="SELECT idVenta FROM ventas 
                        WHERE idVenta = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idVenta']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM ventas WHERE idVenta = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Listar_Ventas($vConexion) {

    $Listado = array();

    // 1) Genero la consulta que deseo
    $SQL = "SELECT 
                v.idVenta, 
                v.idCliente, 
                v.fecha, 
                v.precioTotal, 
                v.descuento, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM ventas v
            LEFT JOIN clientes c ON v.idCliente = c.idCliente
            LEFT JOIN usuarios u ON v.idUsuario = u.id
            ORDER BY v.fecha DESC";

    // 2) A la conexión actual le brindo mi consulta, y el resultado lo entrego a la variable $rs
    $rs = mysqli_query($vConexion, $SQL);

    // 3) El resultado deberá organizarse en una matriz, entonces lo recorro
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_VENTA'] = $data['idVenta'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['precioTotal'];
        $Listado[$i]['DESCUENTO'] = $data['descuento'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $Listado[$i]['VENDEDOR'] = $data['vendedor'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Listar_Ventas_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    // Genero la consulta según el criterio
    switch ($criterio) {
        case 'Cliente':
            $SQL = "SELECT 
                        v.idVenta, 
                        v.idCliente, 
                        v.fecha, 
                        v.precioTotal, 
                        v.descuento, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM ventas v
                    LEFT JOIN clientes c ON v.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON v.idUsuario = u.id
                    WHERE c.nombre LIKE '%$parametro%' OR c.apellido LIKE '%$parametro%'
                    ORDER BY v.fecha DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        v.idVenta, 
                        v.idCliente, 
                        v.fecha, 
                        v.precioTotal, 
                        v.descuento, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM ventas v
                    LEFT JOIN clientes c ON v.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON v.idUsuario = u.id
                    WHERE v.fecha LIKE '%$parametro%'
                    ORDER BY v.fecha DESC";
            break;

        case 'Id':
            $SQL = "SELECT 
                        v.idVenta, 
                        v.idCliente, 
                        v.fecha, 
                        v.precioTotal, 
                        v.descuento, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM ventas v
                    LEFT JOIN clientes c ON v.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON v.idUsuario = u.id
                    WHERE v.idVenta = '$parametro'
                    ORDER BY v.fecha DESC";
            break;

        default:
            return $Listado; // Si no hay un criterio válido, devuelvo un listado vacío
    }

    // Ejecuto la consulta
    $rs = mysqli_query($vConexion, $SQL);

    // Organizo el resultado en un array
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_VENTA'] = $data['idVenta'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['precioTotal'];
        $Listado[$i]['DESCUENTO'] = $data['descuento'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $Listado[$i]['VENDEDOR'] = $data['vendedor'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Datos_Venta($vConexion, $vIdVenta) {
    $DatosVenta = array();
    // Me aseguro que la consulta exista
    $SQL = "SELECT 
                v.idVenta, 
                v.idCliente, 
                v.fecha, 
                v.precioTotal, 
                v.descuento, 
                v.idEstado, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM ventas v
            LEFT JOIN clientes c ON v.idCliente = c.idCliente
            LEFT JOIN usuarios u ON v.idUsuario = u.id
            WHERE v.idVenta = $vIdVenta";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosVenta['ID_VENTA'] = $data['idVenta'];
        $DatosVenta['ID_CLIENTE'] = $data['idCliente'];
        $DatosVenta['FECHA'] = $data['fecha'];
        $DatosVenta['PRECIO_TOTAL'] = $data['precioTotal'];
        $DatosVenta['DESCUENTO'] = $data['descuento'];
        $DatosVenta['ID_ESTADO'] = $data['idEstado'];
        $DatosVenta['VENDEDOR'] = $data['vendedor'];
        $DatosVenta['CLIENTE_N'] = $data['CLIENTE_N'];
        $DatosVenta['CLIENTE_A'] = $data['CLIENTE_A'];
    }
    return $DatosVenta;
}

function Detalles_Venta($vConexion, $vIdVenta) {
    $DetallesVenta = array();
    // Me aseguro que la consulta exista
    $SQL = "SELECT 
                dv.idDetalleVenta, 
                dv.idVenta, 
                dv.idProducto, 
                p.nombre AS PRODUCTO, 
                dv.precio_venta AS PRECIO_VENTA, 
                dv.cantidad AS CANTIDAD, 
                dv.idEstado AS ID_ESTADO, 
                e.Denominacion AS ESTADO
            FROM detalle_venta dv
            LEFT JOIN productos p ON dv.idProducto = p.idProducto
            LEFT JOIN estado e ON dv.idEstado = e.IdEstado
            WHERE dv.idVenta = $vIdVenta";

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $DetallesVenta[$i]['ID_DETALLE'] = $data['idDetalleVenta'];
        $DetallesVenta[$i]['ID_VENTA'] = $data['idVenta'];
        $DetallesVenta[$i]['ID_PRODUCTO'] = $data['idProducto'];
        $DetallesVenta[$i]['PRODUCTO'] = $data['PRODUCTO'];
        $DetallesVenta[$i]['PRECIO_VENTA'] = $data['PRECIO_VENTA'];
        $DetallesVenta[$i]['CANTIDAD'] = $data['CANTIDAD'];
        $DetallesVenta[$i]['ID_ESTADO'] = $data['ID_ESTADO'];
        $DetallesVenta[$i]['ESTADO'] = $data['ESTADO'];
        $i++;
    }
    return $DetallesVenta;
}

function Listar_Pedidos($vConexion) {

    $Listado = array();

    // 1) Genero la consulta que deseo
    $SQL = "SELECT 
                p.idPedido, 
                p.idCliente, 
                p.fecha, 
                p.precioTotal, 
                p.descuento,
                p.senia, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM pedidos p
            LEFT JOIN clientes c ON p.idCliente = c.idCliente
            LEFT JOIN usuarios u ON p.idUsuario = u.id
            ORDER BY p.idPedido DESC";

    // 2) A la conexión actual le brindo mi consulta, y el resultado lo entrego a la variable $rs
    $rs = mysqli_query($vConexion, $SQL);

    // 3) El resultado deberá organizarse en una matriz, entonces lo recorro
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_PEDIDO'] = $data['idPedido'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['precioTotal'];
        $Listado[$i]['DESCUENTO'] = $data['descuento'];
        $Listado[$i]['SENIA'] = $data['senia'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $Listado[$i]['VENDEDOR'] = $data['vendedor'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Listar_Pedidos_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    // Genero la consulta según el criterio
    switch ($criterio) {
        case 'Cliente':
            $SQL = "SELECT 
                        p.idPedido, 
                        p.idCliente, 
                        p.fecha, 
                        p.precioTotal, 
                        p.descuento,
                        p.senia, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON p.idUsuario = u.id
                    WHERE c.nombre LIKE '%$parametro%' OR c.apellido LIKE '%$parametro%'
                    ORDER BY p.idPedido DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        p.idPedido, 
                        p.idCliente, 
                        p.fecha, 
                        p.precioTotal, 
                        p.descuento,
                        p.senia, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON p.idUsuario = u.id
                    WHERE p.fecha LIKE '%$parametro%'
                    ORDER BY p.idPedido DESC";
            break;

        case 'Id':
            $SQL = "SELECT 
                        p.idPedido, 
                        p.idCliente, 
                        p.fecha, 
                        p.precioTotal, 
                        p.descuento,
                        p.senia, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON p.idUsuario = u.id
                    WHERE p.idPedido = '$parametro'
                    ORDER BY p.idPedido DESC";
            break;

        default:
            return $Listado; // Si no hay un criterio válido, devuelvo un listado vacío
    }

    // Ejecuto la consulta
    $rs = mysqli_query($vConexion, $SQL);

    // Organizo el resultado en un array
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_PEDIDO'] = $data['idPedido'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['precioTotal'];
        $Listado[$i]['DESCUENTO'] = $data['descuento'];
        $Listado[$i]['SENIA'] = $data['senia'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $Listado[$i]['VENDEDOR'] = $data['vendedor'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Eliminar_Pedido($vConexion , $vIdConsulta) {

        $SQL_MiConsulta="SELECT idPedido FROM pedidos 
                        WHERE idPedido = $vIdConsulta "; 
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idPedido']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM pedidos WHERE idPedido = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Detalles_Pedido($vConexion, $vIdPedido) {
    $DetallesPedido = array();
    // Me aseguro que la consulta exista
    $SQL = "SELECT 
                dp.idDetallePedido, 
                dp.idPedido, 
                dp.idProducto, 
                p.nombre AS PRODUCTO, 
                dp.precio_venta AS PRECIO_VENTA, 
                dp.cantidad AS CANTIDAD, 
                dp.IdEstado AS ID_ESTADO, 
                e.Denominacion AS ESTADO
            FROM detalle_pedido dp
            LEFT JOIN productos p ON dp.idProducto = p.idProducto
            LEFT JOIN estado e ON dp.IdEstado = e.IdEstado
            WHERE dp.idPedido = $vIdPedido";

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $DetallesPedido[$i]['ID_DETALLE'] = $data['idDetallePedido'];
        $DetallesPedido[$i]['ID_PEDIDO'] = $data['idPedido'];
        $DetallesPedido[$i]['ID_PRODUCTO'] = $data['idProducto'];
        $DetallesPedido[$i]['PRODUCTO'] = $data['PRODUCTO'];
        $DetallesPedido[$i]['PRECIO_VENTA'] = $data['PRECIO_VENTA'];
        $DetallesPedido[$i]['CANTIDAD'] = $data['CANTIDAD'];
        $DetallesPedido[$i]['ID_ESTADO'] = $data['ID_ESTADO'];
        $DetallesPedido[$i]['ESTADO'] = $data['ESTADO'];
        $i++;
    }
    return $DetallesPedido;
}

function Datos_Pedido($vConexion, $vIdPedido) {
    $DatosPedido = array();
    // Me aseguro que la consulta exista
    $SQL = "SELECT 
                p.idPedido, 
                p.idCliente, 
                p.fecha, 
                p.precioTotal, 
                p.descuento, 
                p.senia,
                p.idEstado, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM pedidos p
            LEFT JOIN clientes c ON p.idCliente = c.idCliente
            LEFT JOIN usuarios u ON p.idUsuario = u.id
            WHERE p.idPedido = $vIdPedido";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosPedido['ID_PEDIDO'] = $data['idPedido'];
        $DatosPedido['ID_CLIENTE'] = $data['idCliente'];
        $DatosPedido['FECHA'] = $data['fecha'];
        $DatosPedido['PRECIO_TOTAL'] = $data['precioTotal'];
        $DatosPedido['DESCUENTO'] = $data['descuento'];
        $DatosPedido['SENIA'] = $data['senia'];
        $DatosPedido['ID_ESTADO'] = $data['idEstado'];
        $DatosPedido['VENDEDOR'] = $data['vendedor'];
        $DatosPedido['CLIENTE_N'] = $data['CLIENTE_N'];
        $DatosPedido['CLIENTE_A'] = $data['CLIENTE_A'];
    }
    return $DatosPedido;
}

function Eliminar_Detalle_Pedido($conexion, $idDetalle) {
    // Desactivar autocommit para iniciar transacción
    $conexion->autocommit(false);

    try {
        // Verificar que el ID recibido sea numérico
        if (!is_numeric($idDetalle)) {
            throw new Exception("ID de detalle inválido");
        }

        // 1. Obtener información del detalle con JOIN al pedido
        $sql_select = "SELECT dp.idPedido, dp.precio_venta, dp.cantidad, p.precioTotal as precio_actual 
                       FROM detalle_pedido dp
                       LEFT JOIN pedidos p ON dp.idPedido = p.idPedido
                       WHERE dp.idDetallePedido = ?";

        $stmt_select = $conexion->prepare($sql_select);
        if (!$stmt_select) {
            throw new Exception("Error preparando SELECT: " . $conexion->error);
        }

        if (!$stmt_select->bind_param("i", $idDetalle)) {
            throw new Exception("Error en bind_param SELECT: " . $stmt_select->error);
        }

        if (!$stmt_select->execute()) {
            throw new Exception("Error ejecutando SELECT: " . $stmt_select->error);
        }

        $result = $stmt_select->get_result();
        $detalle = $result->fetch_assoc();

        if (!$detalle) {
            throw new Exception("No se encontró el detalle con ID: $idDetalle");
        }

        // Si no hay pedido asociado
        if (is_null($detalle['precio_actual'])) {
            $sql_delete = "DELETE FROM detalle_pedido WHERE idDetallePedido = ?";
            $stmt_delete = $conexion->prepare($sql_delete);

            if (!$stmt_delete) {
                throw new Exception("Error preparando DELETE huérfano: " . $conexion->error);
            }

            if (!$stmt_delete->bind_param("i", $idDetalle) || !$stmt_delete->execute()) {
                throw new Exception("Error ejecutando DELETE huérfano: " . $stmt_delete->error);
            }

            $conexion->commit();
            $_SESSION['Mensaje'] = "Detalle eliminado (no tenía pedido asociado)";
            $_SESSION['Estilo'] = 'warning';
            return true;
        }

        $idPedido = $detalle['idPedido'];
        $montoDetalle = $detalle['precio_venta'] * $detalle['cantidad'];
        $nuevoTotal = max(0, $detalle['precio_actual'] - $montoDetalle);

        // 2. Eliminar el detalle
        $sql_delete = "DELETE FROM detalle_pedido WHERE idDetallePedido = ?";
        $stmt_delete = $conexion->prepare($sql_delete);

        if (!$stmt_delete) {
            throw new Exception("Error preparando DELETE: " . $conexion->error);
        }

        if (!$stmt_delete->bind_param("i", $idDetalle) || !$stmt_delete->execute()) {
            throw new Exception("Error ejecutando DELETE: " . $stmt_delete->error);
        }

        // 3. Actualizar el total del pedido
        $sql_update = "UPDATE pedidos SET precioTotal = ? WHERE idPedido = ?";
        $stmt_update = $conexion->prepare($sql_update);

        if (!$stmt_update) {
            throw new Exception("Error preparando UPDATE pedido: " . $conexion->error);
        }

        if (!$stmt_update->bind_param("di", $nuevoTotal, $idPedido) || !$stmt_update->execute()) {
            throw new Exception("Error ejecutando UPDATE pedido: " . $stmt_update->error);
        }

        // Todo correcto, confirmar
        $conexion->commit();
        $_SESSION['Mensaje'] = "Detalle eliminado y total actualizado";
        $_SESSION['Estilo'] = 'success';
        return true;

    } catch (Exception $e) {
        $conexion->rollback();
        $_SESSION['Mensaje'] = "Error al eliminar: " . $e->getMessage();
        $_SESSION['Estilo'] = 'danger';

        // MOSTRAR error en pantalla para debug (solo en desarrollo)
        echo "<div class='alert alert-danger'><strong>Error técnico:</strong> " . $e->getMessage() . "</div>";

        return false;
    } finally {
        $conexion->autocommit(true);
    }
}

function Actualizar_Senia_Pedido($conexion, $idPedido, $nuevaSenia) {
    try {
        // Validar entrada
        if (!is_numeric($nuevaSenia)) {
            throw new Exception("La seña debe ser un valor numérico");
        }
        
        // Convertir a float
        $nuevaSenia = (float)$nuevaSenia;
        
        $sql = "UPDATE pedidos SET senia = ? WHERE idPedido = ?";
        $stmt = $conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $conexion->error);
        }
        
        // Usar "di" (double, integer)
        if (!$stmt->bind_param("di", $nuevaSenia, $idPedido)) {
            throw new Exception("Error vinculando parámetros: " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando consulta: " . $stmt->error);
        }
        
        // Considerar que puede actualizarse a mismo valor
        return true;
        
    } catch (Exception $e) {
        error_log("Error en Actualizar_Senia_Pedido: " . $e->getMessage());
        return false;
    }
}

function Actualizar_Cantidad_Detalle($conexion, $idDetalle, $nuevaCantidad) {
    $conexion->autocommit(false);

    try {
        // Cast to integer at the very beginning for consistent handling
        $nuevaCantidad = (int)$nuevaCantidad; // Ensures $nuevaCantidad is an actual integer type

        // Validation improved: Now checking if the integer value is valid
        if ($nuevaCantidad < 1) { // is_numeric and is_int are implicitly handled by the cast and check
            throw new Exception("La cantidad debe ser un número entero mayor a 0");
        }

        $sql_select = "SELECT idPedido, precio_venta, cantidad FROM detalle_pedido WHERE idDetallePedido = ?";
        $stmt_select = $conexion->prepare($sql_select);
        if (!$stmt_select) throw new Exception("Error preparando SELECT: " . $conexion->error);

        $stmt_select->bind_param("i", $idDetalle);
        if (!$stmt_select->execute()) throw new Exception("Error ejecutando SELECT: " . $stmt_select->error);

        $result = $stmt_select->get_result();
        $detalle = $result->fetch_assoc();

        if (!$detalle) throw new Exception("No se encontró el detalle con ID: $idDetalle");

        $idPedido = $detalle['idPedido'];
        $precioVenta = $detalle['precio_venta'];
        $cantidadAnterior = $detalle['cantidad'];

        $diferencia = ($nuevaCantidad - $cantidadAnterior) * $precioVenta;

        // Actualizar cantidad
        $sql_update = "UPDATE detalle_pedido SET cantidad = ? WHERE idDetallePedido = ?";
        $stmt_update = $conexion->prepare($sql_update);
        if (!$stmt_update) throw new Exception("Error preparando UPDATE: " . $conexion->error);
        if (!$stmt_update->bind_param("ii", $nuevaCantidad, $idDetalle) || !$stmt_update->execute()) {
            throw new Exception("Error ejecutando UPDATE: " . $stmt_update->error);
        }

        // Actualizar total del pedido
        $sql_update_pedido = "UPDATE pedidos SET precioTotal = precioTotal + ? WHERE idPedido = ?";
        $stmt_update_pedido = $conexion->prepare($sql_update_pedido);
        if (!$stmt_update_pedido) throw new Exception("Error preparando UPDATE pedido: " . $conexion->error);
        if (!$stmt_update_pedido->bind_param("di", $diferencia, $idPedido) || !$stmt_update_pedido->execute()) {
            throw new Exception("Error ejecutando UPDATE pedido: " . $stmt_update_pedido->error);
        }

        $conexion->commit();
        return true;

    } catch (Exception $e) {
        $conexion->rollback();
        error_log("Error en Actualizar_Cantidad_Detalle: " . $e->getMessage());
        $_SESSION['Mensaje'] = $e->getMessage();
        $_SESSION['Estilo'] = 'danger';
        return false;
    } finally {
        $conexion->autocommit(true);
    }
}

function Listado_Proveedores($conexion) {
    $sql = "SELECT idProveedor, razon_social FROM proveedores";
    return mysqli_query($conexion, $sql);
}

function Listado_Productos($conexion) {
    $sql = "SELECT idProducto, nombre FROM productos ORDER BY nombre";
    return mysqli_query($conexion, $sql);
}

function Validar_Compra() {
    $errores = [];
    
    if (empty($_POST['idProveedor'])) {
        $errores[] = "Seleccione un proveedor";
    }
    
    if (empty($_POST['idArticulo']) || !is_array($_POST['idArticulo'])) {
        $errores[] = "Debe agregar al menos un artículo";
    } else {
        foreach($_POST['idArticulo'] as $index => $idArticulo) {
            if (empty($idArticulo)) {
                $errores[] = "Artículo no seleccionado en la fila " . ($index + 1);
            }
            if (empty($_POST['cantidad'][$index]) || $_POST['cantidad'][$index] < 1) {
                $errores[] = "Cantidad inválida en la fila " . ($index + 1);
            }
        }
    }
    
    return implode("<br>", $errores);
}

function Insertar_Compra($conexion) {
    try {
        $conexion->autocommit(false);

        // Insertar cabecera
        $sql_cabecera = "INSERT INTO compras (idProveedor, fecha, idUsuario, descripcion) 
                        VALUES (?, ?, ?, ?)";
        $stmt_cabecera = $conexion->prepare($sql_cabecera);
        
        if (!$stmt_cabecera) {
            throw new Exception("Error preparando cabecera: " . $conexion->error);
        }

        $idProveedor = (int)$_POST['idProveedor'];
        $fecha = $_POST['fecha'];
        $idUsuario = (int)$_SESSION['Usuario_Id'];
        $descripcion = $_POST['descripcion'] ?? '';

        $stmt_cabecera->bind_param("isis", $idProveedor, $fecha, $idUsuario, $descripcion);
        
        if (!$stmt_cabecera->execute()) {
            throw new Exception("Error ejecutando cabecera: " . $stmt_cabecera->error);
        }

        $idCompra = $conexion->insert_id;

        // Insertar detalles
        $sql_detalle = "INSERT INTO detalle_compra (idCompra, idArticulo, cantidad) 
                        VALUES (?, ?, ?)";
        $stmt_detalle = $conexion->prepare($sql_detalle);
        
        if (!$stmt_detalle) {
            throw new Exception("Error preparando detalle: " . $conexion->error);
        }

        foreach ($_POST['idArticulo'] as $index => $idArticulo) {
            $cantidad = (int)$_POST['cantidad'][$index];
            
            $stmt_detalle->bind_param("iii", $idCompra, $idArticulo, $cantidad);
            
            if (!$stmt_detalle->execute()) {
                throw new Exception("Error ejecutando detalle: " . $stmt_detalle->error);
            }
        }

        $conexion->commit();
        return true;

    } catch (Exception $e) {
        $conexion->rollback();
        error_log("Error en compra: " . $e->getMessage());
        $GLOBALS['error_compra'] = $e->getMessage(); // Almacenar error en variable global
        return false; // Retornar false explícitamente
    } finally {
        $conexion->autocommit(true);
    }
}

function Listar_Compras($vConexion) {
    $Listado = array();

    // 1) Genero la consulta que deseo
    $SQL = "SELECT 
                c.idCompra, 
                c.fecha, 
                c.descripcion,
                p.razon_social AS PROVEEDOR,
                CONCAT(u.nombre, ' ', u.apellido) AS USUARIO
            FROM compras c
            LEFT JOIN proveedores p ON c.idProveedor = p.idProveedor
            LEFT JOIN usuarios u ON c.idUsuario = u.id
            ORDER BY c.idCompra DESC";

    // 2) Ejecuto la consulta
    $rs = mysqli_query($vConexion, $SQL);

    // 3) Organizo el resultado en un array
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_COMPRA'] = $data['idCompra'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['DESCRIPCION'] = $data['descripcion'];
        $Listado[$i]['PROVEEDOR'] = $data['PROVEEDOR'];
        $Listado[$i]['USUARIO'] = $data['USUARIO'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

function Listar_Compras_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();

    switch ($criterio) {
        case 'Proveedor':
            $SQL = "SELECT 
                        c.idCompra, 
                        c.fecha, 
                        c.descripcion,
                        p.razon_social AS PROVEEDOR,
                        CONCAT(u.nombre, ' ', u.apellido) AS USUARIO
                    FROM compras c
                    LEFT JOIN proveedores p ON c.idProveedor = p.idProveedor
                    LEFT JOIN usuarios u ON c.idUsuario = u.id
                    WHERE p.razon_social LIKE '%$parametro%'
                    ORDER BY c.idCompra DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        c.idCompra, 
                        c.fecha, 
                        c.descripcion,
                        p.razon_social AS PROVEEDOR,
                        CONCAT(u.nombre, ' ', u.apellido) AS USUARIO
                    FROM compras c
                    LEFT JOIN proveedores p ON c.idProveedor = p.idProveedor
                    LEFT JOIN usuarios u ON c.idUsuario = u.id
                    WHERE c.fecha LIKE '%$parametro%'
                    ORDER BY c.idCompra DESC";
            break;

        case 'Id':
            $SQL = "SELECT 
                        c.idCompra, 
                        c.fecha, 
                        c.descripcion,
                        p.razon_social AS PROVEEDOR,
                        CONCAT(u.nombre, ' ', u.apellido) AS USUARIO
                    FROM compras c
                    LEFT JOIN proveedores p ON c.idProveedor = p.idProveedor
                    LEFT JOIN usuarios u ON c.idUsuario = u.id
                    WHERE c.idCompra = '$parametro'
                    ORDER BY c.idCompra DESC";
            break;

        default:
            return $Listado;
    }

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_COMPRA'] = $data['idCompra'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['DESCRIPCION'] = $data['descripcion'];
        $Listado[$i]['PROVEEDOR'] = $data['PROVEEDOR'];
        $Listado[$i]['USUARIO'] = $data['USUARIO'];
        $i++;
    }

    return $Listado;
}

function Eliminar_Compra($vConexion , $vIdConsulta) {

        $SQL_MiConsulta="SELECT idCompra FROM compras 
                        WHERE idCompra = $vIdConsulta "; 
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idCompra']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM compras WHERE idCompra = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Datos_Compra($vConexion, $idCompra) {
    $SQL = "SELECT c.*, 
                   p.razon_social AS PROVEEDOR,
                   CONCAT(u.nombre, ' ', u.apellido) AS USUARIO
            FROM compras c
            INNER JOIN proveedores p ON c.idProveedor = p.idProveedor
            INNER JOIN usuarios u ON c.idUsuario = u.id
            WHERE c.idCompra = $idCompra";
    
    $rs = mysqli_query($vConexion, $SQL);
    return mysqli_fetch_assoc($rs);
}

function Detalles_Compra($vConexion, $idCompra) {
    $SQL = "SELECT dc.*, p.nombre AS ARTICULO
            FROM detalle_compra dc
            INNER JOIN productos p ON dc.idArticulo = p.idProducto
            WHERE dc.idCompra = $idCompra";
    
    $rs = mysqli_query($vConexion, $SQL);
    $detalles = array();
    while ($fila = mysqli_fetch_assoc($rs)) {
        $detalles[] = $fila;
    }
    return $detalles;
}

function Eliminar_Detalle_Compra($vConexion, $idDetalle) {
    $SQL = "DELETE FROM detalle_compra WHERE idDetalleCompra = $idDetalle";
    return mysqli_query($vConexion, $SQL);
}

function Actualizar_Cantidad_Detalle_Compra($vConexion, $idDetalle, $cantidad) {
    $SQL = "UPDATE detalle_compra 
            SET cantidad = $cantidad 
            WHERE idDetalleCompra = $idDetalle";
    return mysqli_query($vConexion, $SQL);
}

function Listar_Ordenes_Compra($MiConexion) {
    $Listado = array();

    $SQL = "SELECT 
                o.idOrdenCompra, 
                o.fecha, 
                p.razon_social AS PROVEEDOR,
                CONCAT(u.nombre, ' ', u.apellido) AS USUARIO,
                IFNULL(SUM(d.cantidad * d.precio), 0) AS PRECIO_TOTAL
            FROM orden_compra o
            LEFT JOIN proveedores p ON o.idProveedor = p.idProveedor
            LEFT JOIN usuarios u ON o.idUsuario = u.id
            LEFT JOIN detalle_orden_compra d ON o.idOrdenCompra = d.idOrdenCompra
            GROUP BY o.idOrdenCompra, o.fecha, p.razon_social, u.nombre, u.apellido
            ORDER BY o.idOrdenCompra DESC";

    $rs = mysqli_query($MiConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_ORDEN'] = $data['idOrdenCompra'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PROVEEDOR'] = $data['PROVEEDOR'];
        $Listado[$i]['USUARIO'] = $data['USUARIO'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['PRECIO_TOTAL'];
        $i++;
    }

    return $Listado;
}

function Listar_Ordenes_Compra_Parametro($MiConexion, $criterio, $parametro) {
    $Listado = array();

    switch ($criterio) {
        case 'Proveedor':
            $SQL = "SELECT 
                        o.idOrdenCompra, 
                        o.fecha, 
                        p.razon_social AS PROVEEDOR,
                        CONCAT(u.nombre, ' ', u.apellido) AS USUARIO,
                        IFNULL(SUM(d.cantidad * d.precio), 0) AS PRECIO_TOTAL
                    FROM orden_compra o
                    LEFT JOIN proveedores p ON o.idProveedor = p.idProveedor
                    LEFT JOIN usuarios u ON o.idUsuario = u.id
                    LEFT JOIN detalle_orden_compra d ON o.idOrdenCompra = d.idOrdenCompra
                    WHERE p.razon_social LIKE '%$parametro%'
                    GROUP BY o.idOrdenCompra, o.fecha, p.razon_social, u.nombre, u.apellido
                    ORDER BY o.idOrdenCompra DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        o.idOrdenCompra, 
                        o.fecha, 
                        p.razon_social AS PROVEEDOR,
                        CONCAT(u.nombre, ' ', u.apellido) AS USUARIO,
                        IFNULL(SUM(d.cantidad * d.precio), 0) AS PRECIO_TOTAL
                    FROM orden_compra o
                    LEFT JOIN proveedores p ON o.idProveedor = p.idProveedor
                    LEFT JOIN usuarios u ON o.idUsuario = u.id
                    LEFT JOIN detalle_orden_compra d ON o.idOrdenCompra = d.idOrdenCompra
                    WHERE o.fecha LIKE '%$parametro%'
                    GROUP BY o.idOrdenCompra, o.fecha, p.razon_social, u.nombre, u.apellido
                   
                    ORDER BY o.idOrdenCompra DESC";
            break;

        case 'Id':
            $SQL = "SELECT 
                        o.idOrdenCompra, 
                        o.fecha, 
                        p.razon_social AS PROVEEDOR,
                        CONCAT(u.nombre, ' ', u.apellido) AS USUARIO,
                        IFNULL(SUM(d.cantidad * d.precio), 0) AS PRECIO_TOTAL
                    FROM orden_compra o
                    LEFT JOIN proveedores p ON o.idProveedor = p.idProveedor
                    LEFT JOIN usuarios u ON o.idUsuario = u.id
                    LEFT JOIN detalle_orden_compra d ON o.idOrdenCompra = d.idOrdenCompra
                    WHERE o.idOrdenCompra = '$parametro'
                    GROUP BY o.idOrdenCompra, o.fecha, p.razon_social, u.nombre, u.apellido
                    ORDER BY o.idOrdenCompra DESC";
            break;

        default:
            return $Listado;
    }

    $rs = mysqli_query($MiConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_ORDEN'] = $data['idOrdenCompra'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PROVEEDOR'] = $data['PROVEEDOR'];
        $Listado[$i]['USUARIO'] = $data['USUARIO'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['PRECIO_TOTAL'];
        $i++;
    }

    return $Listado;
}

function Eliminar_Orden_Compra($vConexion , $vIdConsulta) {

        $SQL_MiConsulta="SELECT idOrdenCompra FROM orden_compra 
                        WHERE idOrdenCompra = $vIdConsulta "; 
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idOrdenCompra']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "DELETE FROM orden_compra WHERE idOrdenCompra = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Datos_Orden_Compra($conexion, $id_orden) {
    $sql = "SELECT 
                o.idOrdenCompra, 
                o.fecha, 
                p.razon_social AS PROVEEDOR,
                p.telefono,
                CONCAT(u.nombre, ' ', u.apellido) AS USUARIO,
                IFNULL(SUM(d.cantidad * d.precio), 0) AS PRECIO_TOTAL
            FROM orden_compra o
            LEFT JOIN proveedores p ON o.idProveedor = p.idProveedor
            LEFT JOIN usuarios u ON o.idUsuario = u.id
            LEFT JOIN detalle_orden_compra d ON o.idOrdenCompra = d.idOrdenCompra
            WHERE o.idOrdenCompra = ?
            GROUP BY o.idOrdenCompra";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_orden);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function Detalles_Orden_Compra($conexion, $id_orden) {
    $sql = "SELECT 
                d.*, 
                p.nombre AS ARTICULO 
            FROM detalle_orden_compra d
            JOIN productos p ON d.idArticulo = p.idProducto
            WHERE d.idOrdenCompra = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_orden);
    $stmt->execute();
    $result = $stmt->get_result();

    $detalles = array();
    while ($fila = $result->fetch_assoc()) {
        $detalles[] = $fila;
    }
    return $detalles;
}

function Eliminar_Detalle_Orden($conexion, $id_detalle) {
    $sql = "DELETE FROM detalle_orden_compra WHERE idDetalleOrdenCompra = ?";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([$id_detalle]);
}

function Actualizar_Detalle_Orden($conexion, $id_detalle, $cantidad, $precio) {
    $sql = "UPDATE detalle_orden_compra 
            SET cantidad = ?, precio = ?
            WHERE idDetalleOrdenCompra = ?";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([$cantidad, $precio, $id_detalle]);
}

function Listar_Ordenes_Compra_Rango($conexion, $fecha_inicio, $fecha_fin) {
    $sql = "SELECT 
                o.idOrdenCompra AS ID_ORDEN,
                o.fecha AS FECHA,
                p.razon_social AS PROVEEDOR,
                CONCAT(u.nombre, ' ', u.apellido) AS USUARIO,
                IFNULL(SUM(d.cantidad * d.precio), 0) AS PRECIO_TOTAL
            FROM orden_compra o
            LEFT JOIN proveedores p ON o.idProveedor = p.idProveedor
            LEFT JOIN usuarios u ON o.idUsuario = u.id
            LEFT JOIN detalle_orden_compra d ON o.idOrdenCompra = d.idOrdenCompra
            WHERE o.fecha BETWEEN ? AND ?
            GROUP BY o.idOrdenCompra
            ORDER BY o.fecha DESC";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ordenes = array();
    while ($fila = $result->fetch_assoc()) {
        $ordenes[] = $fila;
    }
    return $ordenes;
}

?>