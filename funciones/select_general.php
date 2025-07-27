<?php
function Eliminar_Cliente($vConexion , $vIdConsulta) {


    //soy admin 
        $SQL_MiConsulta="SELECT idCliente FROM clientes 
                        WHERE idCliente = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['idCliente']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "UPDATE clientes SET idActivo = 2 WHERE idCliente = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function InsertarClientes($vConexion){
    
    $SQL_Insert="INSERT INTO clientes (nombre, apellido, telefono, direccion, email, dni)
    VALUES ('".$_POST['Nombre']."' , '".$_POST['Apellido']."' , '".$_POST['Telefono']."', '".$_POST['Direccion']."', '".$_POST['Email']."', '".$_POST['DNI']."')";


    if (!mysqli_query($vConexion, $SQL_Insert)) {
        //si surge un error, finalizo la ejecucion del script con un mensaje
        die('<h4>Error al intentar insertar el registro.</h4>');
    }

    return true;
}

function Eliminar_Turno($vConexion , $vIdConsulta) {


    //soy admin 
        $SQL_MiConsulta="SELECT IdTurno FROM turnos 
                        WHERE IdTurno = $vIdConsulta ";
   
    
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        
    $data = mysqli_fetch_array($rs);

    if (!empty($data['IdTurno']) ) {
        //si se cumple todo, entonces elimino:
        mysqli_query($vConexion, "UPDATE turnos SET idActivo = 2 WHERE IdTurno = $vIdConsulta");
        return true;

    }else {
        return false;
    }
    
}

function Listar_Clientes($vConexion) {

    $Listado=array();

      //1) genero la consulta que deseo
        $SQL = "SELECT * FROM clientes WHERE idActivo = 1";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_CLIENTE'] = $data['idCliente'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['TELEFONO'] = $data['telefono'];
            $Listado[$i]['DIRECCION'] = $data['direccion'];
            $Listado[$i]['EMAIL'] = $data['email'];
            $Listado[$i]['DNI'] = $data['dni'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
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
    $SQL = "SELECT * FROM proveedores WHERE idActivo = 1 ";

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
        $sql = "SELECT * FROM proveedores  WHERE idActivo = 1 ";
        switch ($criterio) { 
        case 'RazonSocial': 
        $sql = "SELECT * FROM proveedores  WHERE idActivo = 1 AND razon_social LIKE '%$parametro%'";
        break;
        case 'CUIT':
        $sql = "SELECT * FROM proveedores WHERE idActivo = 1 AND cuit LIKE '%$parametro%'";
        break;
        case 'Telefono':
        $sql = "SELECT * FROM proveedores WHERE idActivo = 1 AND telefono LIKE '%$parametro%'";
        break;
        case 'Email':
        $sql = "SELECT * FROM proveedores WHERE idActivo = 1 AND email LIKE '%$parametro%'";
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
        mysqli_query($vConexion, "UPDATE proveedores SET idActivo = 2 WHERE idProveedor = $vIdConsulta");
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
    $vIdTurno = (int)$vIdTurno;
    $DatosTurno = array(
        'ID_TURNO' => '',
        'HORARIO' => '',
        'FECHA' => '',
        'ESTILISTA' => '',
        'ESTADO' => '',
        'CLIENTE' => '',
        'servicios_seleccionados' => array()
    );

    // Obtener datos básicos del turno
    $SQL = "SELECT 
                t.IdTurno, 
                t.Horario, 
                t.Fecha, 
                t.IdEstilista, 
                t.IdEstado, 
                t.IdCliente
            FROM turnos t
            WHERE t.IdTurno = $vIdTurno";

    $rs = mysqli_query($vConexion, $SQL);
    
    if (!$rs) {
        die("Error al obtener datos del turno: " . mysqli_error($vConexion));
    }

    $data = mysqli_fetch_assoc($rs);
    
    if (!empty($data)) {
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        // Formatear el horario para eliminar los segundos (de HH:MM:SS a HH:MM)
        $DatosTurno['HORARIO'] = substr($data['Horario'], 0, 5);
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['ESTILISTA'] = $data['IdEstilista'];
        $DatosTurno['ESTADO'] = $data['IdEstado'];
        $DatosTurno['CLIENTE'] = $data['IdCliente'];
        
        // Obtener IDs de servicios asociados al turno
        $SQL_Servicios = "SELECT idTipoServicio 
                          FROM detalle_turno 
                          WHERE idTurno = $vIdTurno";
        
        $rs_servicios = mysqli_query($vConexion, $SQL_Servicios);
        
        if (!$rs_servicios) {
            die("Error al obtener servicios del turno: " . mysqli_error($vConexion));
        }

        $servicios = array();
        while ($servicio = mysqli_fetch_assoc($rs_servicios)) {
            $servicios[] = $servicio['idTipoServicio'];
        }
        
        $DatosTurno['servicios_seleccionados'] = implode(',', $servicios);
    }
    
    return $DatosTurno;
}

function Validar_Turno(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Fecha']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar una fecha. <br />';
    }
    if (empty($_POST['Horario'])) {
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
    $vIdTurno = (int)$vIdTurno;
    $DatosTurno = array('SERVICIOS' => array());

    // Obtener datos básicos del turno
    $SQL = "SELECT 
                t.IdTurno, 
                t.Horario, 
                t.Fecha, 
                CONCAT(e.apellido, ', ', e.nombre) AS ESTILISTA, 
                es.Denominacion AS ESTADO, 
                CONCAT(c.apellido, ', ', c.nombre) AS CLIENTE
            FROM turnos t
            LEFT JOIN usuarios e ON t.IdEstilista = e.id
            LEFT JOIN estado es ON t.IdEstado = es.IdEstado
            LEFT JOIN clientes c ON t.IdCliente = c.idCliente
            WHERE t.IdTurno = $vIdTurno";

    $rs = mysqli_query($vConexion, $SQL);
    $data = mysqli_fetch_array($rs);
    
    if (!empty($data)) {
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        $DatosTurno['HORARIO'] = $data['Horario'];
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['ESTILISTA'] = $data['ESTILISTA'];
        $DatosTurno['ESTADO'] = $data['ESTADO'];
        $DatosTurno['CLIENTE'] = $data['CLIENTE'];
        
        // Obtener servicios del turno
        $SQL_Servicios = "SELECT ts.Denominacion 
                          FROM detalle_turno dt 
                          JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio 
                          WHERE dt.idTurno = $vIdTurno";
        
        $rs_servicios = mysqli_query($vConexion, $SQL_Servicios);
        while ($servicio = mysqli_fetch_array($rs_servicios)) {
            $DatosTurno['SERVICIOS'][] = $servicio['Denominacion'];
        }
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
        WHERE idActivo = 1
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
        $SQL = "SELECT id , apellido , nombre
        FROM usuarios
        WHERE nivel = 2 AND idActivo = 1
        ORDER BY Apellido";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID'] = $data['id'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
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
        WHERE idActivo = 1
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

function Listar_Clientes_Parametro($vConexion,$criterio,$parametro) {
    $Listado=array();

      //1) genero la consulta que deseo segun el parametro
        $sql = "SELECT * FROM clientes";
        switch ($criterio) { 
        case 'Nombre': 
        $sql = "SELECT * FROM clientes WHERE idActivo = 1 AND nombre LIKE '%$parametro%'";
        break;
        case 'Apellido':
        $sql = "SELECT * FROM clientes WHERE idActivo = 1 AND apellido LIKE '%$parametro%'";
        break;
        case 'Telefono':
        $sql = "SELECT * FROM clientes WHERE idActivo = 1 AND telefono LIKE '%$parametro%'";
        break;
        case 'Email':
        $sql = "SELECT * FROM clientes WHERE idActivo = 1 AND email LIKE '%$parametro%'";
        break;
        case 'DNI':
            $sql = "SELECT * FROM clientes WHERE idActivo = 1 AND dni LIKE '%$parametro%'";
            break;
        }    
        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $sql);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_CLIENTE'] = $data['id'];
            $Listado[$i]['NOMBRE'] = $data['nombre'];
            $Listado[$i]['APELLIDO'] = $data['apellido'];
            $Listado[$i]['TELEFONO'] = $data['telefono'];
            $Listado[$i]['DIRECCION'] = $data['direccion'];
            $Listado[$i]['EMAIL'] = $data['email'];
            $Listado[$i]['DNI'] = $data['dni'];
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
    $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                   c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
            FROM turnos t
            INNER JOIN usuarios e ON t.IdEstilista = e.id
            INNER JOIN clientes c ON t.IdCliente = c.idCliente
            INNER JOIN estado es ON t.IdEstado = es.IdEstado
            WHERE t.idActivo = 1
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
                        e.nombre AS ESTILISTA_N,
                        e.apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.IdEstado,
                        es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND CONCAT(c.Apellido, ' ', c.Nombre) LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        case 'Estilista':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.nombre AS ESTILISTA_N,
                        e.apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.IdEstado,
                        es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND CONCAT(e.Apellido, ' ', e.Nombre) LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.nombre AS ESTILISTA_N,
                        e.apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.IdEstado,
                        es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND t.Fecha LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;

        case 'TipoServicio':
            $SQL = "SELECT 
                        t.IdTurno,
                        t.Fecha,
                        t.Horario,
                        e.nombre AS ESTILISTA_N,
                        e.apellido AS ESTILISTA_A,
                        c.Nombre AS CLIENTE_N, 
                        c.Apellido AS CLIENTE_A,
                        es.Denominacion AS ESTADO,
                        es.IdEstado,
                        GROUP_CONCAT(ts.Denominacion SEPARATOR ', ') AS SERVICIOS
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    LEFT JOIN detalle_turno dt ON t.IdTurno = dt.idTurno
                    LEFT JOIN tipo_servicio ts ON dt.idTipoServicio = ts.IdTipoServicio
                    WHERE t.idActivo = 1 AND ts.Denominacion LIKE '%$parametro%'
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

function ColorDeFilaTurnos($vIdTurno, $vFecha, $vEstado, $conexion) { // Corregido el nombre del parámetro
    $Title = '';
    $Color = '';
    
    // Establecer zona horaria de Argentina
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $FechaActual = date("Y-m-d");

    // Debug: Verificar valores recibidos
    // error_log("Turno ID: $vIdTurno, Fecha: $vFecha, Estado: $vEstado");

    // Lógica para marcar turnos vencidos (estado 6)
    if (($vEstado == 1 || $vEstado == 2) && $vFecha < $FechaActual) {
        $Title = 'Turno Vencido';
        $Color = 'table-danger';
        
        // Actualizar estado en la base de datos
        $query = "UPDATE turnos SET IdEstado = 6 WHERE IdTurno = $vIdTurno";
        if (!mysqli_query($conexion, $query)) {
            error_log("Error al actualizar estado del turno: " . mysqli_error($conexion));
        }
        $vEstado = 6; // Actualizamos también la variable local
    }
    
    // Asignar colores y títulos según estado
    switch ($vEstado) { // Usamos la variable que podría haber sido actualizada
        case 1:
            $Title = 'Turno Pendiente';
            $Color = 'table-pendiente';
            break;
        case 2:
            $Title = 'Turno En Proceso';
            $Color = 'table-proceso';
            break;
        case 3:
            $Title = 'Turno Completado';
            $Color = 'table-completo';
            break;
        case 4:
            $Title = 'Turno Pagado';
            $Color = 'table-primaria';
            break;
        case 5:
            $Title = 'Turno Rechazado';
            $Color = 'table-secondary';
            break;
        case 6:
            $Title = 'Turno Vencido';
            $Color = 'table-secondary';
            break;
        default:
            $Title = 'Estado Desconocido';
            $Color = '';
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
//-----------
function Listar_Turnos_Completados($vConexion) {
    $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                   c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
            FROM turnos t
            INNER JOIN usuarios e ON t.IdEstilista = e.id
            INNER JOIN clientes c ON t.IdCliente = c.idCliente
            INNER JOIN estado es ON t.IdEstado = es.IdEstado
            WHERE t.IdEstado = 3 AND t.idActivo = 1
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

function Listar_Turnos_Completados_Parametro($vConexion, $criterio, $parametro) {
    switch ($criterio) {
        case 'Cliente':
            $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                           c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND t.IdEstado = 3 AND CONCAT(c.Apellido, ' ', c.Nombre) LIKE '%$parametro%' 
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;
        case 'Estilista':
            $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                           c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND t.IdEstado = 3 AND CONCAT(e.apellido, ' ', e.nombre) LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;
        case 'Fecha':
            $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                           c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND t.IdEstado = 3 AND t.Fecha LIKE '%$parametro%'
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;
        case 'TipoServicio':
            $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                           c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    INNER JOIN detalle_turno dt ON t.IdTurno = dt.IdTurno
                    INNER JOIN tipo_servicio ts ON dt.IdTipoServicio = ts.IdTipoServicio
                    WHERE t.idActivo = 1 AND t.IdEstado = 3 AND ts.Denominacion LIKE '%$parametro%'
                    GROUP BY t.IdTurno
                    ORDER BY t.Fecha DESC, t.Horario DESC";
            break;
        default:
            $SQL = "SELECT t.IdTurno, t.Fecha, t.Horario, e.nombre AS NOMBRE_E, e.apellido AS APELLIDO_E, 
                           c.Nombre AS NOMBRE_C, c.Apellido AS APELLIDO_C, es.IdEstado, es.Denominacion AS ESTADO
                    FROM turnos t
                    INNER JOIN usuarios e ON t.IdEstilista = e.id
                    INNER JOIN clientes c ON t.IdCliente = c.idCliente
                    INNER JOIN estado es ON t.IdEstado = es.IdEstado
                    WHERE t.idActivo = 1 AND t.IdEstado = 3
                    ORDER BY t.Fecha DESC, t.Horario DESC";
    }
    $resultado = mysqli_query($vConexion, $SQL);
    $Listado = array();
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $Listado[] = $fila;
        }
    }
    return $Listado;
}

function Cobrar_Turno($vConexion, $idTurno) {
    $SQL = "UPDATE turnos SET IdEstado = 4 WHERE IdTurno = $idTurno";
    return mysqli_query($vConexion, $SQL);
}
//------------
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
            FROM productos WHERE idActivo = 1
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
                    WHERE idActivo = 1 AND nombre LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
        case 'Descripcion':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE idActivo = 1 AND descripcion LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
        case 'Precio':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE idActivo = 1 AND precio LIKE '%$parametro%'
                    ORDER BY nombre";
            break;
        case 'Stock':
            $SQL = "SELECT idProducto, nombre, descripcion, precio, stock, fechaRegistro, idActivo
                    FROM productos
                    WHERE idActivo = 1 AND stock LIKE '%$parametro%'
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
        mysqli_query($vConexion, "UPDATE productos SET idActivo = 2 WHERE idProducto = $vIdConsulta");
        return true;
    } else {
        return false;
    }
}

function Listar_Productos_Bajo_Stock($conexion) {
    $sql = "SELECT * FROM productos WHERE idActivo = 1 AND stock <= 10 ORDER BY stock ASC";
    $resultado = mysqli_query($conexion, $sql);
    $productos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $productos[] = $fila;
    }
    return $productos;
}

function Eliminar_Venta($vConexion, $vIdConsulta) {
    // Iniciar transacción
    mysqli_begin_transaction($vConexion);
    
    try {
        // 1. Verificar que la venta existe
        $SQL_MiConsulta = "SELECT idVenta FROM ventas WHERE idVenta = $vIdConsulta";
        $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        $data = mysqli_fetch_array($rs);

        if (empty($data['idVenta'])) {
            return false;
        }

        // 2. Obtener los detalles de la venta
        $SQL_Detalles = "SELECT idProducto, cantidad FROM detalle_venta WHERE idVenta = $vIdConsulta";
        $result_detalles = mysqli_query($vConexion, $SQL_Detalles);

        // 3. Restaurar el stock para cada producto
        while ($detalle = mysqli_fetch_assoc($result_detalles)) {
            $idProducto = $detalle['idProducto'];
            $cantidad = $detalle['cantidad'];
            
            $SQL_Update_Stock = "UPDATE productos 
                                SET stock = stock + $cantidad 
                                WHERE idProducto = $idProducto";
            mysqli_query($vConexion, $SQL_Update_Stock);
        }

        // 4. Eliminar la venta principal
        $SQL_Update_Venta = "UPDATE ventas SET idEstado = 3 WHERE idVenta = $vIdConsulta";
        mysqli_query($vConexion, $SQL_Update_Venta);

        // Confirmar transacción
        mysqli_commit($vConexion);
        return true;

    } catch (Exception $e) {
        // Revertir en caso de error
        mysqli_rollback($vConexion);
        return false;
    }
}

function Listar_Ventas($vConexion) {

    $Listado = array();

    // Consulta que suma el total de cada venta a partir de los detalles
    $SQL = "SELECT 
                v.idVenta, 
                v.idCliente, 
                v.fecha, 
                v.idEstado, 
                IFNULL(SUM(dv.precio_venta * dv.cantidad), 0) AS precioTotal, 
                v.descuento, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM ventas v
            LEFT JOIN clientes c ON v.idCliente = c.idCliente
            LEFT JOIN usuarios u ON v.idUsuario = u.id
            LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
            WHERE v.idActivo = 1
            GROUP BY v.idVenta, v.idCliente, v.fecha, v.descuento, c.nombre, c.apellido, u.nombre, u.apellido
            ORDER BY v.idVenta DESC";

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_VENTA'] = $data['idVenta'];
        $Listado[$i]['ID_ESTADO'] = $data['idEstado'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['precioTotal'];
        $Listado[$i]['DESCUENTO'] = $data['descuento'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $Listado[$i]['VENDEDOR'] = $data['vendedor'];
        $i++;
    }

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
                        v.idEstado, 
                        IFNULL(SUM(dv.precio_venta * dv.cantidad), 0) AS precioTotal, 
                        v.descuento, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM ventas v
                    LEFT JOIN clientes c ON v.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON v.idUsuario = u.id
                    LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                    WHERE v.idActivo = 1 AND c.nombre LIKE '%$parametro%' OR c.apellido LIKE '%$parametro%'
                    GROUP BY v.idVenta, v.idCliente, v.fecha, v.descuento, c.nombre, c.apellido, u.nombre, u.apellido
                    ORDER BY v.fecha DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        v.idVenta, 
                        v.idCliente, 
                        v.fecha, 
                        v.idEstado, 
                        IFNULL(SUM(dv.precio_venta * dv.cantidad), 0) AS precioTotal, 
                        v.descuento, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM ventas v
                    LEFT JOIN clientes c ON v.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON v.idUsuario = u.id
                    LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                    WHERE v.idActivo = 1 AND v.fecha LIKE '%$parametro%'
                    GROUP BY v.idVenta, v.idCliente, v.fecha, v.descuento, c.nombre, c.apellido, u.nombre, u.apellido
                    ORDER BY v.fecha DESC";
            break;

        case 'Id':
            $SQL = "SELECT 
                        v.idVenta, 
                        v.idCliente, 
                        v.fecha, 
                        v.idEstado, 
                        IFNULL(SUM(dv.precio_venta * dv.cantidad), 0) AS precioTotal, 
                        v.descuento, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM ventas v
                    LEFT JOIN clientes c ON v.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON v.idUsuario = u.id
                    LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
                    WHERE v.idActivo = 1 AND v.idVenta = '$parametro'
                    GROUP BY v.idVenta, v.idCliente, v.fecha, v.descuento, c.nombre, c.apellido, u.nombre, u.apellido
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
        $Listado[$i]['ID_ESTADO'] = $data['idEstado'];
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

function Listar_Ventas_Fecha($conexion, $fecha_inicio, $fecha_fin) {
    $ventas = [];
    $sql = "SELECT 
                v.idVenta AS ID_VENTA, 
                v.fecha AS FECHA, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS VENDEDOR, 
                IFNULL(SUM(dv.precio_venta * dv.cantidad), 0) AS PRECIO_TOTAL, 
                v.descuento AS DESCUENTO
            FROM ventas v
            INNER JOIN clientes c ON v.idCliente = c.idCliente
            INNER JOIN usuarios u ON v.idUsuario = u.id
            LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
            WHERE v.idActivo = 1 AND v.fecha BETWEEN ? AND ?
            GROUP BY v.idVenta, v.fecha, c.nombre, c.apellido, u.nombre, u.apellido, v.descuento
            ORDER BY v.fecha DESC";
    
    $stmt = $conexion->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ss', $fecha_inicio, $fecha_fin);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($fila = $result->fetch_assoc()) {
            $ventas[] = $fila;
        }
        $stmt->close();
    }
    return $ventas;
}

function Datos_Venta($vConexion, $vIdVenta) {
    $DatosVenta = array();
    // Consulta que suma el total de la venta a partir de los detalles
    $SQL = "SELECT 
                v.idVenta, 
                v.idCliente, 
                v.fecha, 
                IFNULL(SUM(dv.precio_venta * dv.cantidad), 0) AS precioTotal, 
                v.descuento, 
                v.idEstado, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM ventas v
            LEFT JOIN clientes c ON v.idCliente = c.idCliente
            LEFT JOIN usuarios u ON v.idUsuario = u.id
            LEFT JOIN detalle_venta dv ON v.idVenta = dv.idVenta
            WHERE v.idVenta = $vIdVenta
            GROUP BY v.idVenta, v.idCliente, v.fecha, v.descuento, v.idEstado, c.nombre, c.apellido, u.nombre, u.apellido";

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

function ColorDeFilaVentas($vEstado) { 

    $Title = '';
    $Color = '';
    
    // Asignar colores y títulos según estado
    switch ($vEstado) { // Usamos la variable que podría haber sido actualizada
        case 1:
            $Title = 'En proceso';
            $Color = 'table-proceso';
            break;
        case 2:
            $Title = 'Finalizada';
            $Color = 'table-primaria';
            break;
        case 3:
            $Title = 'Cancelada';
            $Color = 'table-secondary';
            break;
        default:
            $Title = 'Estado Desconocido';
            $Color = '';
    }
    
    return [$Title, $Color];
}

function Listar_Pedidos($vConexion) {

    $Listado = array();

    // Consulta que suma el total de cada pedido a partir de los detalles
    $SQL = "SELECT 
                p.idPedido, 
                p.idCliente, 
                p.fecha,
                p.idEstado, 
                IFNULL(SUM(dp.precio_venta * dp.cantidad), 0) AS precioTotal, 
                p.descuento,
                p.senia, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM pedidos p
            LEFT JOIN clientes c ON p.idCliente = c.idCliente
            LEFT JOIN usuarios u ON p.idUsuario = u.id
            LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
            WHERE p.idActivo = 1
            GROUP BY p.idPedido, p.idCliente, p.fecha, p.descuento, p.senia, c.nombre, c.apellido, u.nombre, u.apellido
            ORDER BY p.idPedido DESC";

    $rs = mysqli_query($vConexion, $SQL);

    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_PEDIDO'] = $data['idPedido'];
        $Listado[$i]['ID_ESTADO'] = $data['idEstado'];
        $Listado[$i]['FECHA'] = $data['fecha'];
        $Listado[$i]['PRECIO_TOTAL'] = $data['precioTotal'];
        $Listado[$i]['DESCUENTO'] = $data['descuento'];
        $Listado[$i]['SENIA'] = $data['senia'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $Listado[$i]['VENDEDOR'] = $data['vendedor'];
        $i++;
    }

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
                        p.idEstado, 
                        IFNULL(SUM(dp.precio_venta * dp.cantidad), 0) AS precioTotal, 
                        p.descuento,
                        p.senia, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON p.idUsuario = u.id
                    LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
                    WHERE p.idActivo = 1 AND c.nombre LIKE '%$parametro%' OR c.apellido LIKE '%$parametro%'
                    GROUP BY p.idPedido, p.idCliente, p.fecha, p.descuento, p.senia, c.nombre, c.apellido, u.nombre, u.apellido
                    ORDER BY p.idPedido DESC";
            break;

        case 'Fecha':
            $SQL = "SELECT 
                        p.idPedido, 
                        p.idCliente, 
                        p.fecha,
                        p.idEstado, 
                        IFNULL(SUM(dp.precio_venta * dp.cantidad), 0) AS precioTotal, 
                        p.descuento,
                        p.senia, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON p.idUsuario = u.id
                    LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
                    WHERE p.idActivo = 1 AND p.fecha LIKE '%$parametro%'
                    GROUP BY p.idPedido, p.idCliente, p.fecha, p.descuento, p.senia, c.nombre, c.apellido, u.nombre, u.apellido
                    ORDER BY p.idPedido DESC";
            break;

        case 'Id':
            $SQL = "SELECT 
                        p.idPedido, 
                        p.idCliente, 
                        p.fecha,
                        p.idEstado, 
                        IFNULL(SUM(dp.precio_venta * dp.cantidad), 0) AS precioTotal, 
                        p.descuento,
                        p.senia, 
                        c.nombre AS CLIENTE_N, 
                        c.apellido AS CLIENTE_A,
                        CONCAT(u.nombre, ' ', u.apellido) AS vendedor
                    FROM pedidos p
                    LEFT JOIN clientes c ON p.idCliente = c.idCliente
                    LEFT JOIN usuarios u ON p.idUsuario = u.id
                    LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
                    WHERE p.idActivo = 1 AND p.idPedido = '$parametro'
                    GROUP BY p.idPedido, p.idCliente, p.fecha, p.descuento, p.senia, c.nombre, c.apellido, u.nombre, u.apellido
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
        $Listado[$i]['ID_ESTADO'] = $data['idEstado'];
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

function Listar_Pedidos_Fecha($conexion, $fecha_inicio, $fecha_fin) {
    $listado = array();

    $sql = "SELECT 
                p.idPedido AS ID_PEDIDO,
                p.fecha AS FECHA,
                IFNULL(SUM(dp.precio_venta * dp.cantidad), 0) AS PRECIO_TOTAL,
                p.descuento AS DESCUENTO,
                p.senia AS SENIA,
                c.nombre AS CLIENTE_N,
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS VENDEDOR
            FROM pedidos p
            INNER JOIN clientes c ON p.idCliente = c.idCliente
            INNER JOIN usuarios u ON p.idUsuario = u.id
            LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
            WHERE p.idActivo = 1 AND p.fecha BETWEEN ? AND ?
            GROUP BY p.idPedido, p.fecha, p.descuento, p.senia, c.nombre, c.apellido, u.nombre, u.apellido
            ORDER BY p.fecha ASC";

    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        error_log("Error preparando consulta Listar_Pedidos_Fecha: " . $conexion->error);
        return [];
    }

    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $listado[] = $fila;
    }

    $stmt->close();
    return $listado;
}

function Eliminar_Pedido($vConexion, $vIdConsulta) {
    // Iniciar transacción
    mysqli_begin_transaction($vConexion);
    
    try {
        // 1. Verificar que el pedido existe
        $SQL_MiConsulta = "SELECT idPedido FROM pedidos WHERE idPedido = $vIdConsulta";
        $rs = mysqli_query($vConexion, $SQL_MiConsulta);
        $data = mysqli_fetch_array($rs);

        if (empty($data['idPedido'])) {
            return false;
        }

        // 2. Obtener los detalles del pedido
        $SQL_Detalles = "SELECT idProducto, cantidad FROM detalle_pedido WHERE idPedido = $vIdConsulta";
        $result_detalles = mysqli_query($vConexion, $SQL_Detalles);

        // 3. Restaurar el stock para cada producto
        while ($detalle = mysqli_fetch_assoc($result_detalles)) {
            $idProducto = $detalle['idProducto'];
            $cantidad = $detalle['cantidad'];
            
            $SQL_Update_Stock = "UPDATE productos 
                                SET stock = stock + $cantidad 
                                WHERE idProducto = $idProducto";
            mysqli_query($vConexion, $SQL_Update_Stock);
        }

        // 4. Eliminar el pedido principal
        $SQL_Update_Pedido = "UPDATE pedidos SET idEstado = 4 WHERE idPedido = $vIdConsulta";
        mysqli_query($vConexion, $SQL_Update_Pedido);

        // Confirmar transacción
        mysqli_commit($vConexion);
        return true;

    } catch (Exception $e) {
        // Revertir en caso de error
        mysqli_rollback($vConexion);
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
    // Consulta que suma el total del pedido a partir de los detalles
    $SQL = "SELECT 
                p.idPedido, 
                p.idCliente, 
                p.fecha,
                ep.denominacion AS estadoPedido, 
                IFNULL(SUM(dp.precio_venta * dp.cantidad), 0) AS precioTotal, 
                p.descuento, 
                p.senia,
                p.idEstado, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A,
                CONCAT(u.nombre, ' ', u.apellido) AS vendedor
            FROM pedidos p
            LEFT JOIN clientes c ON p.idCliente = c.idCliente
            LEFT JOIN estado_pedidos ep ON p.idEstado = ep.idEstadoPedido
            LEFT JOIN usuarios u ON p.idUsuario = u.id
            LEFT JOIN detalle_pedido dp ON p.idPedido = dp.idPedido
            WHERE p.idPedido = $vIdPedido
            GROUP BY p.idPedido, p.idCliente, p.fecha, p.descuento, p.senia, p.idEstado, c.nombre, c.apellido, u.nombre, u.apellido";

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
        $DatosPedido['ESTADO'] = $data['estadoPedido'];
    }
    return $DatosPedido;
}

function ColorDeFilaPedidos($vEstado) { 

    $Title = '';
    $Color = '';
    
    // Asignar colores y títulos según estado
    switch ($vEstado) { // Usamos la variable que podría haber sido actualizada
        case 1:
            $Title = 'En proceso';
            $Color = 'table-proceso';
            break;
        case 2:
            $Title = 'Listo';
            $Color = 'table-completo';
            break;
        case 3:
            $Title = 'Finalizado';
            $Color = 'table-primaria';
            break;
        case 4:
            $Title = 'Cancelado';
            $Color = 'table-secondary';
            break;    
        default:
            $Title = 'Estado Desconocido';
            $Color = '';
    }
    
    return [$Title, $Color];
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
    $sql = "SELECT idProveedor, razon_social FROM proveedores WHERE idActivo = 1";
    return mysqli_query($conexion, $sql);
}

function Listado_Productos($conexion) {
    $sql = "SELECT idProducto, nombre FROM productos WHERE idActivo = 1 ORDER BY nombre";
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
            WHERE c.idActivo = 1
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
                    WHERE c.idActivo = 1 AND p.razon_social LIKE '%$parametro%'
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
                    WHERE c.idActivo = 1 AND c.fecha LIKE '%$parametro%'
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
                    WHERE c.idActivo = 1 AND c.idCompra = '$parametro'
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
        mysqli_query($vConexion, "UPDATE compras SET idActivo = 2 WHERE idCompra = $vIdConsulta");
        
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
                    WHERE idActivo = 1 AND p.razon_social LIKE '%$parametro%'
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
                    WHERE idActivo = 1 AND o.fecha LIKE '%$parametro%'
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
                    WHERE idActivo = 1 AND o.idOrdenCompra = '$parametro'
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
        mysqli_query($vConexion, "UPDATE orden_compra SET idActivo = 2 WHERE idOrdenCompra = $vIdConsulta");
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
            WHERE idActivo = 1 AND o.fecha BETWEEN ? AND ?
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

function Validar_Orden_Compra() {
    $errores = [];
    
    // Validar proveedor
    if (empty($_POST['idProveedor'])) {
        $errores[] = "Debe seleccionar un proveedor";
    }
    
    // Validar artículos
    if (empty($_POST['idArticulo']) || !is_array($_POST['idArticulo'])) {
        $errores[] = "Debe agregar al menos un artículo";
    } else {
        foreach ($_POST['idArticulo'] as $index => $idArticulo) {
            if (empty($idArticulo)) {
                $errores[] = "Debe seleccionar un producto para todos los artículos";
            }
            if (empty($_POST['cantidad'][$index]) || $_POST['cantidad'][$index] < 1) {
                $errores[] = "Cantidad inválida para el artículo " . ($index + 1);
            }
            if (empty($_POST['precio'][$index]) || $_POST['precio'][$index] <= 0) {
                $errores[] = "Precio inválido para el artículo " . ($index + 1);
            }
        }
    }
    
    return empty($errores) ? '' : implode('<br>', $errores);
}

function Insertar_Orden_Compra($conexion) {
    try {
        $conexion->begin_transaction();
        
        // Insertar orden principal
        $sql = "INSERT INTO orden_compra (idProveedor, fecha, idUsuario, descripcion)
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("isis", 
            $_POST['idProveedor'],
            $_POST['fecha'],
            $_SESSION['Usuario_Id'],
            $_POST['descripcion']
        );
        $stmt->execute();
        $idCompra = $conexion->insert_id;
        
        // Insertar detalles
        foreach ($_POST['idArticulo'] as $index => $idArticulo) {
            $sql = "INSERT INTO detalle_orden_compra (idOrdenCompra, idArticulo, cantidad, precio)
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("iiid", 
                $idCompra,
                $idArticulo,
                $_POST['cantidad'][$index],
                $_POST['precio'][$index]
            );
            $stmt->execute();
        }
        
        $conexion->commit();
        return true;
    } catch (Exception $e) {
        $conexion->rollback();
        $GLOBALS['error_compra'] = $e->getMessage();
        return false;
    }
}

function Validar_Usuario() {
    $mensaje = '';
    if (strlen($_POST['Nombre']) < 3) {
        $mensaje .= 'Debes ingresar un nombre con al menos 3 caracteres.<br />';
    }
    if (strlen($_POST['Apellido']) < 3) {
        $mensaje .= 'Debes ingresar un apellido con al menos 3 caracteres.<br />';
    }
    if (strlen($_POST['User']) < 3) {
        $mensaje .= 'Debes ingresar un usuario con al menos 3 caracteres.<br />';
    }
    if (empty($_POST['Nivel']) || !in_array($_POST['Nivel'], ['1','2','3','4','5'])) {
        $mensaje .= 'Debes seleccionar un nivel válido.<br />';
    }
    // Validación de contraseña solo si se está creando o modificando la clave
    if (isset($_POST['Clave']) && $_POST['Clave'] !== '') {
        $clave = $_POST['Clave'];
        if (strlen($clave) < 8) {
            $mensaje .= 'La contraseña debe tener al menos 8 caracteres.<br />';
        }
        if (!preg_match('/[A-Z]/', $clave)) {
            $mensaje .= 'La contraseña debe tener al menos una letra mayúscula.<br />';
        }
        if (!preg_match('/[a-z]/', $clave)) {
            $mensaje .= 'La contraseña debe tener al menos una letra minúscula.<br />';
        }
        if (!preg_match('/[0-9]/', $clave)) {
            $mensaje .= 'La contraseña debe tener al menos un número.<br />';
        }
    }
    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }
    return $mensaje;
}

function InsertarUsuario($vConexion) {
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($vConexion, $_POST['Apellido']);
    $user = mysqli_real_escape_string($vConexion, $_POST['User']);
    $clave = password_hash($_POST['Clave'], PASSWORD_DEFAULT);
    $nivel = (int)$_POST['Nivel'];

    $SQL_Insert = "INSERT INTO usuarios (nombre, apellido, user, clave, nivel) 
                   VALUES ('$nombre', '$apellido', '$user', '$clave', '$nivel')";
    if (mysqli_query($vConexion, $SQL_Insert)) {
        return true;
    } else {
        return false;
    }
}

function Listar_Usuarios($vConexion) {
    $Listado = array();
    $SQL = "SELECT id AS ID_USUARIO, nombre AS NOMBRE, apellido AS APELLIDO, user AS USER, nivel AS NIVEL FROM usuarios WHERE idActivo = 1 ORDER BY apellido, nombre";
    $rs = mysqli_query($vConexion, $SQL);
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_USUARIO'] = $data['ID_USUARIO'];
        $Listado[$i]['NOMBRE'] = $data['NOMBRE'];
        $Listado[$i]['APELLIDO'] = $data['APELLIDO'];
        $Listado[$i]['USER'] = $data['USER'];
        // Puedes mostrar el nombre del nivel si lo prefieres
        switch ($data['NIVEL']) {
            case 1: $Listado[$i]['NIVEL'] = 'Administrador'; break;
            case 2: $Listado[$i]['NIVEL'] = 'Estilista'; break;
            case 3: $Listado[$i]['NIVEL'] = 'Ventas'; break;
            case 4: $Listado[$i]['NIVEL'] = 'Depósito'; break;
            case 5: $Listado[$i]['NIVEL'] = 'Compras'; break;
            default: $Listado[$i]['NIVEL'] = $data['NIVEL'];
        }
        $i++;
    }
    return $Listado;
}

function Listar_Usuarios_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();
    switch ($criterio) {
        case 'Nombre':
            $SQL = "SELECT id AS ID_USUARIO, nombre AS NOMBRE, apellido AS APELLIDO, user AS USER, nivel AS NIVEL FROM usuarios WHERE idActivo = 1 AND nombre LIKE '%$parametro%' ORDER BY apellido, nombre";
            break;
        case 'Apellido':
            $SQL = "SELECT id AS ID_USUARIO, nombre AS NOMBRE, apellido AS APELLIDO, user AS USER, nivel AS NIVEL FROM usuarios WHERE idActivo = 1 AND apellido LIKE '%$parametro%' ORDER BY apellido, nombre";
            break;
        case 'Usuario':
            $SQL = "SELECT id AS ID_USUARIO, nombre AS NOMBRE, apellido AS APELLIDO, user AS USER, nivel AS NIVEL FROM usuarios WHERE idActivo = 1 AND user LIKE '%$parametro%' ORDER BY apellido, nombre";
            break;
        default:
            $SQL = "SELECT id AS ID_USUARIO, nombre AS NOMBRE, apellido AS APELLIDO, user AS USER, nivel AS NIVEL FROM usuarios WHERE idActivo = 1 ORDER BY apellido, nombre";
    }
    $rs = mysqli_query($vConexion, $SQL);
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_USUARIO'] = $data['ID_USUARIO'];
        $Listado[$i]['NOMBRE'] = $data['NOMBRE'];
        $Listado[$i]['APELLIDO'] = $data['APELLIDO'];
        $Listado[$i]['USER'] = $data['USER'];
        switch ($data['NIVEL']) {
            case 1: $Listado[$i]['NIVEL'] = 'Administrador'; break;
            case 2: $Listado[$i]['NIVEL'] = 'Estilista'; break;
            case 3: $Listado[$i]['NIVEL'] = 'Ventas'; break;
            case 4: $Listado[$i]['NIVEL'] = 'Depósito'; break;
            case 5: $Listado[$i]['NIVEL'] = 'Compras'; break;
            default: $Listado[$i]['NIVEL'] = $data['NIVEL'];
        }
        $i++;
    }
    return $Listado;
}

function Datos_Usuario($vConexion, $vIdUsuario) {
    $DatosUsuario = array();
    $SQL = "SELECT id AS ID_USUARIO, nombre AS NOMBRE, apellido AS APELLIDO, user AS USER, nivel AS NIVEL
            FROM usuarios WHERE id = $vIdUsuario";
    $rs = mysqli_query($vConexion, $SQL);
    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosUsuario['ID_USUARIO'] = $data['ID_USUARIO'];
        $DatosUsuario['NOMBRE'] = $data['NOMBRE'];
        $DatosUsuario['APELLIDO'] = $data['APELLIDO'];
        $DatosUsuario['USER'] = $data['USER'];
        $DatosUsuario['NIVEL'] = $data['NIVEL'];
    }
    return $DatosUsuario;
}

function Modificar_Usuario($vConexion) {
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $apellido = mysqli_real_escape_string($vConexion, $_POST['Apellido']);
    $user = mysqli_real_escape_string($vConexion, $_POST['User']);
    $nivel = (int)$_POST['Nivel'];
    $idUsuario = mysqli_real_escape_string($vConexion, $_POST['IdUsuario']);

    $SQL_MiConsulta = "UPDATE usuarios 
        SET nombre = '$nombre',
            apellido = '$apellido',
            user = '$user',
            nivel = '$nivel'";

    // Si se ingresó una nueva clave, la actualiza
    if (!empty($_POST['Clave'])) {
        $clave = password_hash($_POST['Clave'], PASSWORD_DEFAULT);
        $SQL_MiConsulta .= ", clave = '$clave'";
    }

    $SQL_MiConsulta .= " WHERE id = '$idUsuario'";

    if (mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    } else {
        return false;
    }
}

function Eliminar_Usuario($vConexion, $vIdUsuario) {
    // Verifico que el usuario exista
    $SQL_MiConsulta = "SELECT id FROM usuarios WHERE id = $vIdUsuario";
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
    $data = mysqli_fetch_array($rs);

    if (!empty($data['id'])) {
        // Si el usuario existe, lo elimino
        mysqli_query($vConexion, "UPDATE usuarios SET idActivo = 2 WHERE id = $vIdConsulta");
        return true;
    } else {
        return false;
    }
}

function Validar_Servicio() {
    $mensaje = '';
    if (empty($_POST['Denominacion']) || strlen($_POST['Denominacion']) < 3) {
        $mensaje .= 'Debes ingresar una denominación con al menos 3 caracteres.<br />';
    }
    if (!isset($_POST['Precio']) || !is_numeric($_POST['Precio']) || $_POST['Precio'] <= 0) {
        $mensaje .= 'Debes ingresar un precio válido mayor a 0.<br />';
    }
    // Limpiar entradas
    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim(strip_tags($Valor));
    }
    return $mensaje;
}

function InsertarServicio($vConexion) {
    $denominacion = mysqli_real_escape_string($vConexion, $_POST['Denominacion']);
    $precio = (float)$_POST['Precio'];
    $SQL_Insert = "INSERT INTO tipo_servicio (Denominacion, precio) VALUES ('$denominacion', '$precio')";
    if (mysqli_query($vConexion, $SQL_Insert)) {
        return true;
    } else {
        return false;
    }
}

function Listar_Servicios($vConexion) {
    $Listado = array();
    $SQL = "SELECT IdTipoServicio AS ID, Denominacion, precio AS PRECIO FROM tipo_servicio WHERE idActivo = 1 ORDER BY Denominacion";
    $rs = mysqli_query($vConexion, $SQL);
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID'] = $data['ID'];
        $Listado[$i]['DENOMINACION'] = $data['Denominacion'];
        $Listado[$i]['PRECIO'] = $data['PRECIO'];
        $i++;
    }
    return $Listado;
}

function Listar_Servicios_Parametro($vConexion, $criterio, $parametro) {
    $Listado = array();
    switch ($criterio) {
        case 'Denominacion':
            $SQL = "SELECT IdTipoServicio AS ID, Denominacion, precio AS PRECIO FROM tipo_servicio WHERE idActivo = 1 AND Denominacion LIKE '%$parametro%' ORDER BY Denominacion";
            break;
        case 'Precio':
            $SQL = "SELECT IdTipoServicio AS ID, Denominacion, precio AS PRECIO FROM tipo_servicio WHERE idActivo = 1 AND precio LIKE '%$parametro%' ORDER BY Denominacion";
            break;
        default:
            $SQL = "SELECT IdTipoServicio AS ID, Denominacion, precio AS PRECIO FROM tipo_servicio WHERE idActivo = 1 ORDER BY Denominacion";
    }
    $rs = mysqli_query($vConexion, $SQL);
    $i = 0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID'] = $data['ID'];
        $Listado[$i]['DENOMINACION'] = $data['Denominacion'];
        $Listado[$i]['PRECIO'] = $data['PRECIO'];
        $i++;
    }
    return $Listado;
}

function Datos_Servicio($vConexion, $vIdServicio) {
    $DatosServicio = array();
    $SQL = "SELECT IdTipoServicio AS ID, Denominacion, precio AS PRECIO FROM tipo_servicio WHERE IdTipoServicio = $vIdServicio";
    $rs = mysqli_query($vConexion, $SQL);
    $data = mysqli_fetch_array($rs);
    if (!empty($data)) {
        $DatosServicio['ID'] = $data['ID'];
        $DatosServicio['DENOMINACION'] = $data['Denominacion'];
        $DatosServicio['PRECIO'] = $data['PRECIO'];
    }
    return $DatosServicio;
}

function Modificar_Servicio($vConexion) {
    $denominacion = mysqli_real_escape_string($vConexion, $_POST['Denominacion']);
    $precio = (float)$_POST['Precio'];
    $idServicio = (int)$_POST['IdServicio'];

    $SQL_MiConsulta = "UPDATE tipo_servicio 
        SET Denominacion = '$denominacion',
            precio = '$precio'
        WHERE IdTipoServicio = '$idServicio'";

    if (mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    } else {
        return false;
    }
}

function Eliminar_Servicio($vConexion, $vIdServicio) {
    // Verifico que el servicio exista
    $SQL_MiConsulta = "SELECT IdTipoServicio FROM tipo_servicio WHERE IdTipoServicio = $vIdServicio";
    $rs = mysqli_query($vConexion, $SQL_MiConsulta);
    $data = mysqli_fetch_array($rs);

    if (!empty($data['IdTipoServicio'])) {
        // Si el servicio existe, lo elimino
        mysqli_query($vConexion, "UPDATE tipo_servicio SET idActivo = 2 WHERE IdTipoServicio = $vIdConsulta");
        return true;
    } else {
        return false;
    }
}

function Lista_Estados_Pedido($conexion) {
    $query = "SELECT idEstadoPedido as ID_ESTADO, denominacion as ESTADO FROM estado_pedidos";
    $result = $conexion->query($query);
    
    $estados = array();
    while ($row = $result->fetch_assoc()) {
        $estados[] = $row;
    }
    return $estados;
}

function Actualizar_Estado_Pedido($conexion, $id_pedido, $id_estado) {
    $query = "UPDATE pedidos SET idEstado = ? WHERE idPedido = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $id_estado, $id_pedido);
    return $stmt->execute();
}

function Datos_Pedido_Para_Retiro($vConexion, $vIdPedido) {
    // Consulta que incluye el estado actual
    $SQL = "SELECT 
                p.idPedido,
                p.idEstado,
                ep.denominacion AS estadoActual,
                p.descuento,
                p.senia,
                (SELECT SUM(dp.precio_venta * dp.cantidad) 
                 FROM detalle_pedido dp 
                 WHERE dp.idPedido = p.idPedido) AS precioTotal
            FROM pedidos p
            JOIN estado_pedidos ep ON p.idEstado = ep.idEstadoPedido
            WHERE p.idPedido = ?";
    
    $stmt = $vConexion->prepare($SQL);
    $stmt->bind_param("i", $vIdPedido);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

?>