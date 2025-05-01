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

function Datos_Turno($vConexion , $vIdTurno) {
    $DatosTurno  =   array();
    //me aseguro que la consulta exista
    $SQL = "SELECT * FROM turnos 
            WHERE IdTurno = $vIdTurno";

    $rs = mysqli_query($vConexion, $SQL);

    $data = mysqli_fetch_array($rs) ;
    if (!empty($data)) {
        $DatosTurno['ID_TURNO'] = $data['IdTurno'];
        $DatosTurno['HORARIO'] = $data['Horario'];
        $DatosTurno['FECHA'] = $data['Fecha'];
        $DatosTurno['TIPO_SERVICIO'] = $data['IdTipoServicio'];
        $DatosTurno['ESTILISTA'] = $data['IdEstilista'];
        $DatosTurno['ESTADO'] = $data['IdEstado'];
        $DatosTurno['CLIENTE'] = $data['IdCliente'];
    }
    return $DatosTurno;

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

function Validar_Turno(){
    $_SESSION['Mensaje']='';
    if (strlen($_POST['Fecha']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar una fecha. <br />';
    }
    if (strlen($_POST['Horario']) < 4) {
        $_SESSION['Mensaje'].='Debes seleccionar un horario. <br />';
    }
    if ($_POST['TipoServicio'] == 'Selecciona una opcion') {
        $_SESSION['Mensaje'].='Debes seleccionar un Tipo de Servicio. <br />';
    }
    if ($_POST['Estilista'] == 'Selecciona una opcion') {
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

function Modificar_Turno($vConexion) {
    //divido el array a una cadena separada por coma para guardar
    $string = implode(',', $_POST['TipoServicio']);

    $fecha = mysqli_real_escape_string($vConexion, $_POST['Fecha']);
    $horario = mysqli_real_escape_string($vConexion, $_POST['Horario']);
    $tipoServicio = mysqli_real_escape_string($vConexion, $string);
    $estilista = mysqli_real_escape_string($vConexion, $_POST['Estilista']);
    $cliente = mysqli_real_escape_string($vConexion, $_POST['Cliente']);
    $estado = mysqli_real_escape_string($vConexion, $_POST['Estado']);
    $idTurno = mysqli_real_escape_string($vConexion, $_POST['IdTurno']);

    $SQL_MiConsulta = "UPDATE turnos 
    SET Fecha = '$fecha',
    Horario = '$horario',
    IdTipoServicio = '$tipoServicio',
    IdEstilista = '$estilista',
    IdCliente = '$cliente',
    IdEstado = '$estado'
    WHERE IdTurno = '$idTurno'";

    if ( mysqli_query($vConexion, $SQL_MiConsulta) != false) {
        return true;
    }else {
        return false;
    }
    
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

    $Listado=array();

      //1) genero la consulta que deseo

        $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.IdEstado as estado, ES.Nombre, ES.Apellido, T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE T.IdCliente=C.idCliente AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista ";
        
        if($_SESSION['Usuario_Nivel'] == '2'){
            //si soy estilista solo veo mis consultas
            if($_SESSION['Usuario_Id'] == 3){
                //Listo lo de Lorena
                $SQL .="AND T.IdEstilista=2 ";
            }elseif($_SESSION['Usuario_Id'] == 4){
                //Listo lo de Natalia
                $SQL .="AND T.IdEstilista=1 ";
            }    

        }

        $SQL .= "ORDER BY T.Fecha DESC, T.Horario";

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            //paso el contenido del tipo de servicio a un array

            $Listado[$i]['ID_TURNO'] = $data['IdTurno'];
            $Listado[$i]['FECHA'] = $data['Fecha'];
            $Listado[$i]['HORARIO'] = $data['Horario'];
            $Listado[$i]['NOMBRE_C'] = $data['nombre'];
            $Listado[$i]['APELLIDO_C'] = $data['apellido'];
            $Listado[$i]['ESTADO'] = $data['estado'];
            $Listado[$i]['NOMBRE_E'] = $data['Nombre'];
            $Listado[$i]['APELLIDO_E'] = $data['Apellido'];
            $Listado[$i]['TIPO_SERVICIO'] = $data['IdTipoServicio'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_Turnos_Parametro($vConexion,$criterio,$parametro) {
    $Listado=array();

      //1) genero la consulta que deseo

        switch ($criterio) { 
        case 'Cliente': 
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.IdEstado as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE (C.nombre LIKE '%$parametro%' OR C.apellido LIKE '%$parametro%') 
        AND T.IdCliente=C.id AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        case 'Estilista':
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.denominacion as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE (ES.Nombre LIKE '%$parametro%' OR ES.Apellido LIKE '%$parametro%') 
        AND T.IdCliente=C.id AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        case 'Fecha':
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.denominacion as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE T.Fecha LIKE '%$parametro%' 
        AND T.IdCliente=C.id AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        case 'TipoServicio':
            $SQL = "SELECT T.IdTurno, T.Fecha, T.Horario, C.nombre, C.apellido, E.denominacion as estado, ES.Nombre, ES.Apellido,T.IdTipoServicio
        FROM clientes C, estado E, estilista ES, turnos T
        WHERE TP.Denominacion LIKE '%$parametro%' 
        AND T.IdCliente=C.id AND T.IdEstado=E.IdEstado
        AND T.IdEstilista=ES.IdEstilista
        ORDER BY T.Fecha, T.Horario";
        break;
        }    

        //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
        $rs = mysqli_query($vConexion, $SQL);
        
        //3) el resultado deberá organizarse en una matriz, entonces lo recorro
        $i=0;
        while ($data = mysqli_fetch_array($rs)) {
            $Listado[$i]['ID_TURNO'] = $data['IdTurno'];
            $Listado[$i]['FECHA'] = $data['Fecha'];
            $Listado[$i]['HORARIO'] = $data['Horario'];
            $Listado[$i]['NOMBRE_C'] = $data['nombre'];
            $Listado[$i]['APELLIDO_C'] = $data['apellido'];
            $Listado[$i]['ESTADO'] = $data['estado'];
            $Listado[$i]['NOMBRE_E'] = $data['Nombre'];
            $Listado[$i]['APELLIDO_E'] = $data['Apellido'];
            $Listado[$i]['TIPO_SERVICIO'] = $data['IdTipoServicio'];
            $i++;
        }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;

}

function InsertarTurnos($vConexion){
    //divido el array a una cadena separada por coma para guardar
    $string = implode(',', $_POST['TipoServicio']);

    $SQL_Insert="INSERT INTO turnos ( Horario, Fecha, IdTipoServicio, IdEstilista, IdEstado, IdCliente)
    VALUES ('".$_POST['Horario']."' , '".$_POST['Fecha']."' , '".$string."', '".$_POST['Estilista']."', '1', '".$_POST['Cliente']."')";


    if (!mysqli_query($vConexion, $SQL_Insert)) {
        //si surge un error, finalizo la ejecucion del script con un mensaje
        die('<h4>Error al intentar insertar el registro.</h4>');
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

function Listar_Ventas($vConexion) {

    $Listado = array();

    // 1) Genero la consulta que deseo
    $SQL = "SELECT 
                v.idVenta, 
                v.idCliente, 
                v.fecha, 
                v.precioTotal, 
                v.descuento, 
                v.senia, 
                c.nombre AS CLIENTE_N, 
                c.apellido AS CLIENTE_A
            FROM ventas v
            LEFT JOIN clientes c ON v.idCliente = c.idCliente
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
        $Listado[$i]['SENIA'] = $data['senia'];
        $Listado[$i]['CLIENTE_N'] = $data['CLIENTE_N'];
        $Listado[$i]['CLIENTE_A'] = $data['CLIENTE_A'];
        $i++;
    }

    // Devuelvo el listado generado en el array $Listado. (Podrá salir vacío o con datos)
    return $Listado;
}

?>