<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        $sql = "


DROP PROCEDURE IF EXISTS bsp_activar_usuario ;

CREATE  PROCEDURE bsp_activar_usuario(pIdUsuario smallint)
SALIR:BEGIN
	/*
		Permite cambiar el estado del usuario a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
	*/

    	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar autor no dado de baja
    IF EXISTS(SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario
						AND EstadoUsuario = 'A') THEN
		SELECT 'El usuario ya está Activo.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Usuarios SET EstadoUsuario = 'A' WHERE IdUsuario = pIdUsuario;

    SELECT 'OK' AS Mensaje,'ok' as Response;
END ;

DROP PROCEDURE IF EXISTS bsp_alta_usuario ;

CREATE  PROCEDURE bsp_alta_usuario(pUsername varchar(20), pApellidos varchar(30), pNombres varchar(30), pFechaNacimiento date, pTelefono varchar(15), pEmail varchar(60), pContrasena char(32), pRol char(1)   )
SALIR:BEGIN
/*
	Permite dar de alta un usuario controlando que el username ni el correo electronico esten registrados.
    Lo da de alta con estado A: Activa. Devuelve OK + Id o el mensaje de error en Mensaje.
*/
	DECLARE pIdUsuario int;
    -- Manejo de error en la transacción
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;
    -- Controla parámetros obligatorios
	IF	pUsername = '' OR pUsername IS NULL OR
		pApellidos = '' OR pApellidos IS NULL OR
        pNombres = '' OR pNombres IS NULL OR
        pContrasena = '' OR pContrasena IS NULL OR
        pRol = '' OR pRol IS NULL OR
        pEmail = '' OR pEmail IS NULL THEN
		SELECT 'Faltan datos obligatorios.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla longitud de la contraseña
    IF CHAR_LENGTH(COALESCE(pContrasena,'')) < 6 THEN
		SELECT 'La contraseña tiene que ser mayor a 6.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla que no exista un autor con la misma cuenta
	IF EXISTS(SELECT Username FROM Usuarios WHERE Username = pUsername) THEN
		SELECT 'Ya existe un usuario con ese nombre de cuenta.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla que no exista un autor con la misma dirección de correo electrónico
	IF EXISTS(SELECT Email FROM Usuarios WHERE Email = pEmail) THEN
		SELECT 'Ya existe un usuario con esa dirección de correo electrónico.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;
        INSERT INTO Usuarios
(`IdUsuario`,`Username`,`Apellidos`,`Nombres`,`FechaNacimiento`,`Telefono`,`Email`,`Contrasena`,`FechaCreado`,`Rol`,`EstadoUsuario`) VALUES
(0,pUsername,pApellidos,pNombres,pFechaNacimiento,pTelefono,pEmail,md5(pContrasena),now(),pRol,'A');
		SET pIdUsuario = LAST_INSERT_ID();

        SELECT 'OK' AS Mensaje,'ok' as Response, pIdUsuario AS Id;
    COMMIT;


-- Devuelve OK + Id o el mensaje de error en Mensaje
-- Mensaje varchar(100), Id int
END ;

DROP PROCEDURE IF EXISTS bsp_borra_usuario ;

CREATE  PROCEDURE bsp_borra_usuario(pIdUsuario smallint)
SALIR:BEGIN
	/*
		Permite borrar un usuario, solamente usado para limpiar base de datos y en produccion.
		Devuelve OK o el mensaje de error en Mensaje.
    */
    -- Manejo de error de la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

    START TRANSACTION;
		-- Borra usuario
        DELETE FROM Usuarios WHERE IdUsuario = pIdUsuario;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;
END ;

DROP PROCEDURE IF EXISTS bsp_dame_usuario ;

CREATE  PROCEDURE bsp_dame_usuario(pIdUsuario smallint)
BEGIN
	/*
		Procedimiento que sirve para instanciar un usuario desde la base de datos.
    */
    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Usuarios
    WHERE	IdUsuario = pIdUsuario;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
END ;

DROP PROCEDURE IF EXISTS bsp_darbaja_usuario ;

CREATE  PROCEDURE bsp_darbaja_usuario(pIdUsuario smallint)
SALIR:BEGIN
	/*
		Permite cambiar el estado del usuario a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
	*/
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar autor no dado de baja
    IF EXISTS(SELECT IdUsuario FROM Usuarios WHERE IdUsuario = pIdUsuario
						AND EstadoUsuario = 'B') THEN
		SELECT 'El usuario ya está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Usuarios SET EstadoUsuario = 'B' WHERE IdUsuario = pIdUsuario;

    SELECT 'OK' AS Mensaje,'ok' as Response;
END ;

DROP PROCEDURE IF EXISTS bsp_listar_establecimiento ;

CREATE  PROCEDURE bsp_listar_establecimiento(pIncluyeBajas char(1))
BEGIN
	/*
	Permite listar los establecimiento registrados. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/
	    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Establecimientos
		WHERE
					(pIncluyeBajas = 'S' OR EstadoEstablecimiento = 'A')
		ORDER BY IdEstablecimiento
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Establecimientos}
END ;

DROP PROCEDURE IF EXISTS bsp_listar_usuarios ;

CREATE DEFINER=root@localhost PROCEDURE bsp_listar_usuarios(pIncluyeBajas char(1))
BEGIN
	/*
	Permite listar los usuarios de sistema. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/
	    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		IdUsuario,
Username,
Apellidos,
Nombres,
FechaNacimiento,
Telefono,
Email,
FechaCreado,
Rol,
EstadoUsuario
		FROM		Usuarios
		WHERE
					(pIncluyeBajas = 'S' OR EstadoUsuario = 'A')
		ORDER BY IdUsuario
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Usuarios}
END ;

DROP PROCEDURE IF EXISTS bsp_modificar_perfil ;

CREATE  PROCEDURE bsp_modificar_perfil(pIdUsuario smallint,pUsername varchar(20), pApellidos varchar(30), pNombres varchar(30), pFechaNacimiento date, pTelefono varchar(15), pEmail varchar(60), pContrasena char(32) )
SALIR:BEGIN
/*
	Permite modificar el perfil usuario controlando que el username ni el correo electronico esten registrados.,
    ademas que el mismo no este dado de Baja  Devuelve OK + Id o el mensaje de error en Mensaje.
*/
	DECLARE pIdUsuario int;
    -- Manejo de error en la transacción
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,
				NULL AS Id;
		ROLLBACK;
	END;
    -- Controla parámetros obligatorios
	IF	pUsername = '' OR pUsername IS NULL OR
		pApellidos = '' OR pApellidos IS NULL OR
        pNombres = '' OR pNombres IS NULL OR
        pEmail = '' OR pEmail IS NULL THEN
		SELECT 'Faltan datos obligatorios.' AS Mensaje, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla longitud de la contraseña
    IF CHAR_LENGTH(COALESCE(pContrasena,'')) < 6 THEN
		SELECT 'La contraseña tiene que ser mayor a 6.' AS Mensaje, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla que no exista un autor con la misma cuenta
	IF EXISTS(SELECT Username FROM Usuarios WHERE Username = pUsername) THEN
		SELECT 'Ya existe un usuario con ese nombre de cuenta.' AS Mensaje, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla que no exista un autor con la misma dirección de correo electrónico
	IF EXISTS(SELECT Email FROM Usuarios WHERE Email = pEmail) THEN
		SELECT 'Ya existe un usuario con esa dirección de correo electrónico.' AS Mensaje, NULL AS Id;
		LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

 UPDATE Usuarios
SET
    Username = pUsername,
    Apellidos = pApellidos,
    Nombres = pNombres,
    FechaNacimiento = pFechaNacimiento,
    Telefono = pTelefono,
    Email = pEmail,
    Rol = pRol
WHERE
    IdUsuario = pIdUsuario;
        SELECT 'OK' AS Mensaje, pIdUsuario AS Id,'ok' as Response;

    COMMIT;


-- Devuelve OK + Id o el mensaje de error en Mensaje
-- Mensaje varchar(100), Id int
END ;

DROP PROCEDURE IF EXISTS bsp_modificar_rol_usuario ;

CREATE  PROCEDURE bsp_modificar_rol_usuario(pIdUsuario smallint, pRol char(1))
SALIR:BEGIN
/*
	Permite cambiar el rol del usuario a A: Administrador, O:Organizados, V:Verificador siempre y cuando el usuario este activo.
    Devuelve OK o el mensaje de error en Mensaje.
*/
	DECLARE pIdUsuario int;
    -- Manejo de error en la transacción
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,
				NULL AS Id;
		ROLLBACK;
	END;

        -- Verificar que pRol sea uno de los valores permitidos
    IF pRol NOT IN ('A', 'O', 'V') THEN
        SELECT 'Rol no válido.' AS Mensaje, NULL AS Id;
        LEAVE SALIR; -- Salir del procedimiento
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;
 UPDATE Usuarios SET
		Rol = pRol
        WHERE
		IdUsuario = pIdUsuario;

        SELECT 'OK' AS Mensaje, pIdUsuario AS Id;
    COMMIT;


-- Devuelve OK + Id o el mensaje de error en Mensaje
-- Mensaje varchar(100), Id int
END ;

DROP PROCEDURE IF EXISTS bsp_modifica_perfil ;

CREATE  PROCEDURE bsp_modifica_perfil(pIdUsuario smallint,pUsername varchar(20), pApellidos varchar(30), pNombres varchar(30), pFechaNacimiento date, pTelefono varchar(15), pEmail varchar(60), pContrasena char(32), pRol char(1)   )
SALIR:BEGIN
/*
	Permite modificar el perfil usuario controlando que el username ni el correo electronico esten registrados.,
    ademas que el mismo no este dado de Baja  Devuelve OK + Id o el mensaje de error en Mensaje.
*/
    -- Manejo de error en la transacción
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje, 'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;
    -- Controla parámetros obligatorios
	IF	pUsername = '' OR pUsername IS NULL OR
		pApellidos = '' OR pApellidos IS NULL OR
        pNombres = '' OR pNombres IS NULL OR
        pRol = '' OR pRol IS NULL OR
        pEmail = '' OR pEmail IS NULL THEN
		SELECT 'Faltan datos obligatorios.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

    -- Controla que no exista un autor con la misma cuenta
	IF EXISTS(SELECT Username FROM Usuarios WHERE Username = pUsername AND pIdUsuario !=IdUsuario) THEN
		SELECT 'Ya existe un usuario con ese nombre de cuenta.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla que no exista un autor con la misma dirección de correo electrónico
	IF EXISTS(SELECT Email FROM Usuarios WHERE Email = pEmail AND pIdUsuario !=IdUsuario) THEN
		SELECT 'Ya existe un usuario con esa dirección de correo electrónico.' AS Mensaje, 'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;
 UPDATE Usuarios
SET
    Username = pUsername,
    Apellidos = pApellidos,
    Nombres = pNombres,
    FechaNacimiento = pFechaNacimiento,
    Telefono = pTelefono,
    Email = pEmail,
    Rol = pRol
WHERE
    IdUsuario = pIdUsuario;
        SELECT 'OK' AS Mensaje, 'ok' as Response, pIdUsuario AS Id;

    COMMIT;


-- Devuelve OK + Id o el mensaje de error en Mensaje
-- Mensaje varchar(100), Id int
END ;


DROP PROCEDURE IF EXISTS bsp_listar_establecimiento ;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_establecimiento`(pIncluyeBajas char(1))
BEGIN
	/*
	Permite listar los establecimiento registrados. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/
	    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Establecimientos
		WHERE
					(pIncluyeBajas = 'S' OR EstadoEstablecimiento = 'A')
		ORDER BY IdEstablecimiento
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Establecimientos}
END ;

DROP PROCEDURE IF EXISTS bsp_buscar_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_establecimiento`( pCadena varchar(50), pIncluyeInactivos char(1), pOffset int, pRowCount int)
SALIR:BEGIN
	/*
		Permite buscar los establecimientos a partir del nombre desde el inicio de la cadena solo si la cadena tiene mas de 3 caracteres.
        Puede incluir o no los inactivos (pIncluyeInactivos: S: Si - N: No).
        Para todos, cadena vacía. Incluye paginado.
    */
      DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pCadena)>1 AND CHAR_LENGTH(pCadena) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Establecimientos
	WHERE		Establecimiento LIKE CONCAT('%',pCadena, '%') AND
				(pIncluyeInactivos = 'S' OR EstadoEstablecimiento = 'A')
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Establecimientos
   WHERE	Establecimiento LIKE CONCAT('%',pCadena, '%') AND
			(pIncluyeInactivos = 'S' OR EstadoEstablecimiento = 'A') ORDER BY Establecimiento DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

    -- {Campos de la Tabla Establecimientos}
END ;

DROP PROCEDURE IF EXISTS bsp_alta_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_establecimiento`(pEstablecimiento varchar(60), pUbicacion text, pCapacidad int)
SALIR:BEGIN
/*
Permite dar de alta un establecimiento. Lo da de alta con estado A: Activa. Devuelve OK + Id o el mensaje de error en Mensaje.
*/
    DECLARE pIdEstablecimiento int;
    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pEstablecimiento = '' OR pEstablecimiento IS NULL OR
       pUbicacion IS NULL OR
       pCapacidad IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Controla que no exista un establecimiento con el mismo nombre
    IF EXISTS(SELECT Establecimiento FROM Establecimientos WHERE Establecimiento = pEstablecimiento) THEN
        SELECT 'Ya existe un establecimiento con ese nombre.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

		INSERT INTO Establecimientos
        (`IdEstablecimiento`, `Establecimiento`, `Ubicacion`, `Capacidad`, `EstadoEstablecimiento`) VALUES
        (0, pEstablecimiento, pUbicacion, pCapacidad, 'A');

        SET pIdEstablecimiento = LAST_INSERT_ID();

        SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdEstablecimiento AS Id;

    COMMIT;

-- Mensaje varchar(100), Id int
END ;


DROP PROCEDURE IF EXISTS bsp_modifica_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_establecimiento`(pIdEstablecimiento int, pEstablecimiento varchar(60), pUbicacion text, pCapacidad int)
SALIR:BEGIN
/*
	Permite modificar el establecimiento. Controlando que no este dado de Baja  Devuelve OK + Id o el mensaje de error en Mensaje.
*/
 -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pEstablecimiento = '' OR pEstablecimiento IS NULL OR
       pUbicacion IS NULL OR
       pCapacidad IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Controla que no exista un establecimiento con el mismo nombre
    IF EXISTS(SELECT Establecimiento FROM Establecimientos WHERE Establecimiento = pEstablecimiento AND pIdEstablecimiento != IdEstablecimiento) THEN
        SELECT 'Ya existe un establecimiento con ese nombre.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

        UPDATE Establecimientos SET IdEstablecimiento = pIdEstablecimiento
             , Establecimiento = pEstablecimiento
             , Ubicacion = pUbicacion
             , Capacidad = pCapacidad
             WHERE IdEstablecimiento = pIdEstablecimiento;

        SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdEstablecimiento AS Id;

    COMMIT;
END ;

DROP PROCEDURE IF EXISTS bsp_buscar_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_establecimiento`( pCadena varchar(50), pIncluyeInactivos char(1), pOffset int, pRowCount int)
SALIR:BEGIN
	/*
		Permite buscar los establecimientos a partir del nombre desde el inicio de la cadena solo si la cadena tiene mas de 3 caracteres.
        Puede incluir o no los inactivos (pIncluyeInactivos: S: Si - N: No).
        Para todos, cadena vacía. Incluye paginado.
    */
      DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pCadena)>1 AND CHAR_LENGTH(pCadena) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Establecimientos
	WHERE		Establecimiento LIKE CONCAT('%',pCadena, '%') AND
				(pIncluyeInactivos = 'S' OR EstadoEstablecimiento = 'A')
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Establecimientos
   WHERE	Establecimiento LIKE CONCAT('%',pCadena, '%') AND
			(pIncluyeInactivos = 'S' OR EstadoEstablecimiento = 'A') ORDER BY Establecimiento DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

    -- {Campos de la Tabla Establecimientos}
END ;

DROP PROCEDURE IF EXISTS bsp_dame_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_establecimiento`(pIdEstablecimiento int)
BEGIN
	/*
		Procedimiento que sirve para instanciar un establecimiento desde la base de datos.
    */

    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Establecimientos
    WHERE	IdEstablecimiento = pIdEstablecimiento;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


    -- Campos de la Tabla Establecimiento
END ;


DROP PROCEDURE IF EXISTS bsp_darbaja_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_darbaja_establecimiento`(pIdEstablecimiento int)
SALIR:BEGIN
/*
	Permite cambiar el estado del establecimiento a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar establecimiento no dado de baja
    IF EXISTS(SELECT IdEstablecimiento FROM Establecimientos WHERE IdEstablecimiento = pIdEstablecimiento
						AND EstadoEstablecimiento = 'B') THEN
		SELECT 'El establecimiento ya está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Establecimientos SET EstadoEstablecimiento = 'B' WHERE IdEstablecimiento = pIdEstablecimiento;

    SELECT 'OK' AS Mensaje,'ok' as Response;

-- Mensaje varchar(100)
END ;


DROP PROCEDURE IF EXISTS bsp_activar_establecimiento ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_activar_establecimiento`(pIdEstablecimiento int)
SALIR:BEGIN
/*
	Permite cambiar el estado del establecimiento a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
*/
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar autor no dado de baja
    IF EXISTS(SELECT IdEstablecimiento FROM Establecimientos WHERE IdEstablecimiento = pIdEstablecimiento
						AND EstadoEstablecimiento = 'A') THEN
		SELECT 'El establecimiento ya está Activo.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Establecimientos SET EstadoEstablecimiento = 'A' WHERE IdEstablecimiento = pIdEstablecimiento;

    SELECT 'OK' AS Mensaje,'ok' as Response;


-- Mensaje varchar(100)
END ;


-- EVENTOS

DROP PROCEDURE IF EXISTS bsp_listar_eventos ;


CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_eventos`(pIncluyeBajas char(1))
BEGIN
/*
	Permite listar los eventos registrados. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/
		    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Eventos
		WHERE
					(pIncluyeBajas = 'S' OR (EstadoEvento = 'A' OR EstadoEvento = 'F'))
		ORDER BY IdEvento
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Eventos}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_evento ;


CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_evento`( pCadena varchar(50), pEstado char(1),pIncluyeVotacion char(1), pFechaInicio date, pFechaFinal date, pOffset int, pRowCount int)
SALIR:BEGIN
/*
	Permite buscar los eventos a partir del nombre desde el inicio de la cadena solo si la cadena tiene mas de 3 caracteres,
    a partir de un rango de fecha de inicio y si incluye votacion. (pEstado: A: Activo - B: Baja - F:Finalizada - T:Todos).
    Para todos, cadena vacía. Incluye paginado.
*/
  DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pCadena)>1 AND CHAR_LENGTH(pCadena) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Eventos
	WHERE		Evento LIKE CONCAT('%',pCadena, '%') AND
				( pIncluyeVotacion IS NULL OR pIncluyeVotacion = 'S' OR Votacion = 'N') AND
				 (pEstado = 'T' OR EstadoEvento = pEstado)
AND (pFechaInicio IS NULL OR FechaProbableInicio >= pFechaInicio)
      AND (pFechaFinal IS NULL OR FechaProbableFinal <= pFechaFinal)
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Eventos
   WHERE	Evento LIKE CONCAT('%',pCadena, '%') AND
			( pIncluyeVotacion IS NULL OR pIncluyeVotacion = 'S' OR Votacion = 'N') AND
			(pEstado = 'T' OR EstadoEvento = pEstado)
                AND (pFechaInicio IS NULL OR FechaProbableInicio >= pFechaInicio)
      AND (pFechaFinal IS NULL OR FechaProbableFinal <= pFechaFinal)
			ORDER BY Evento DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;



-- {Campos de la Tabla Eventos}
END ;


DROP PROCEDURE IF EXISTS bsp_alta_evento ;



CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_evento`(pEvento varchar(150), pFechaProbableInicio datetime, pFechaProbableFinal datetime, pVotacion char(1), pIdEstablecimiento  int)
SALIR:BEGIN
/*
	Permite dar de alta un evento. Lo da de alta con estado A: Activa. Devuelve OK + Id o el mensaje de error en Mensaje.
*/

	DECLARE pIdEvento int;
    -- Manejo de error en la transacción
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

	   -- Controla parámetros obligatorios
	IF	pEvento = '' OR pEvento IS NULL OR
		pFechaProbableInicio IS NULL OR
        pFechaProbableFinal IS NULL OR
        pVotacion = '' OR pVotacion IS NULL OR
        pIdEstablecimiento = '' OR pIdEstablecimiento IS NULL THEN
		SELECT 'Faltan datos obligatorios.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;
    -- Controla que el establecimiento exista
	IF NOT EXISTS(SELECT IdEstablecimiento FROM Establecimientos WHERE IdEstablecimiento = pIdEstablecimiento) THEN
		SELECT 'No existe el establecimiento.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

      -- Verificar que pVotacion sea uno de los valores permitidos
    IF pVotacion NOT IN ('S', 'N') THEN
        SELECT 'Votacion no válido.' AS Mensaje, NULL AS Id;
        LEAVE SALIR; -- Salir del procedimiento
    END IF;



    -- COMIENZO TRANSACCION
    START TRANSACTION;

INSERT INTO `Eventos`
(`IdEvento`,
`Evento`,
`FechaProbableInicio`,
`FechaProbableFinal`,
`Votacion`,
`IdEstablecimiento`,
`EstadoEvento`)
VALUES
(0,
pEvento,
pFechaProbableInicio,
pFechaProbableFinal,
pVotacion,
pIdEstablecimiento,'A');


		SET pIdEvento = LAST_INSERT_ID();

        SELECT 'OK' AS Mensaje,'ok' as Response, pIdEvento AS Id;
    COMMIT;
-- Mensaje varchar(100), Id int
END ;


DROP PROCEDURE IF EXISTS bsp_modifica_evento ;



CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_evento`(pIdEvento int, pEvento varchar(150), pFechaProbableInicio date, pFechaProbableFinal date, pVotacion char(1), pFechaInicio date, pFechaFinal date, pIdEstablecimiento  int)
SALIR:BEGIN
/*
	Permite modificar el evento. Controlando que no este dado de Baja o finalizado Devuelve OK + Id o el mensaje de error en Mensaje.
*/

    -- Manejo de error en la transacción
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;



    -- Controla parámetros obligatorios
	IF	pEvento = '' OR pEvento IS NULL OR
		pFechaProbableInicio IS NULL OR
         pFechaProbableFinal IS NULL OR
        pVotacion = '' OR pVotacion IS NULL OR
        pIdEstablecimiento = '' OR pIdEstablecimiento IS NULL THEN
		SELECT 'Faltan datos obligatorios.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

     -- Controlar evento no este finalizado
    IF EXISTS(SELECT IdEvento FROM Eventos WHERE IdEvento = pIdEvento
						AND EstadoEvento = 'F') THEN
		SELECT 'El Evento ya está finalizado. No se puede modificar' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;
        -- Controlar evento no este dado de baja
    IF EXISTS(SELECT IdEvento FROM Eventos WHERE IdEvento = pIdEvento
						AND EstadoEvento = 'B') THEN
		SELECT 'El Evento está dado de baja. No se puede modificar' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

    -- Controla que el establecimiento exista
	IF NOT EXISTS(SELECT IdEstablecimiento FROM Establecimientos WHERE IdEstablecimiento = pIdEstablecimiento) THEN
		SELECT 'No existe el establecimiento.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;


      -- Verificar que pVotacion sea uno de los valores permitidos
    IF pVotacion NOT IN ('S', 'N') THEN
        SELECT 'Votacion no válido.' AS Mensaje, NULL AS Id;
        LEAVE SALIR; -- Salir del procedimiento
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;


	      UPDATE Eventos SET
              Evento = pEvento
             , FechaProbableInicio = pFechaProbableInicio
             , FechaProbableFinal = pFechaProbableFinal
             , Votacion = pVotacion
             , FechaInicio = pFechaInicio
             , FechaFinal = pFechaFinal
             , IdEstablecimiento = pIdEstablecimiento
             WHERE IdEvento = pIdEvento;


        SELECT 'OK' AS Mensaje,'ok' as Response, pIdEvento Id;
    COMMIT;
-- Mensaje varchar(100)
END ;


DROP PROCEDURE IF EXISTS bsp_borra_evento ;


CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_evento`(pIdEvento int)
SALIR:BEGIN
/*
	Permite borrar un evento, solamente usado para limpiar base de datos y en produccion. Devuelve OK o el mensaje de error en Mensaje.
*/
   DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

   -- Controla que el Evento no tenga metricas asociadas
	IF EXISTS(SELECT IdMetrica FROM Metricas WHERE IdEvento = pIdEvento) THEN
		SELECT 'No puede borrar el Evento. Existen metricas asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

	-- Controla que el Evento no tenga zonas asociadas
	IF EXISTS(SELECT IdZona FROM Zonas WHERE IdEvento = pIdEvento) THEN
		SELECT 'No puede borrar el Evento. Existen zonas asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;


	-- Controla que el Evento no tenga patrocinadores asociadas
	IF EXISTS(SELECT IdPatrocinador FROM Patrocinador WHERE IdEvento = pIdEvento) THEN
		SELECT 'No puede borrar el Evento. Existen patrocinadores asociados.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

		-- Controla que el Evento no tenga gastos asociadas
	IF EXISTS(SELECT IdGasto FROM Gastos WHERE IdEvento = pIdEvento) THEN
		SELECT 'No puede borrar el Evento. Existen Gastos asociados.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;



    START TRANSACTION;
		-- Borra usuario
        DELETE FROM Eventos WHERE IdEvento= pIdEvento;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;



-- Mensaje varchar(100)
END  ;


DROP PROCEDURE IF EXISTS bsp_dame_evento ;



CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_evento`(pIdEvento int)
BEGIN
/*
	Procedimiento que sirve para instanciar un evento desde la base de datos.
*/

    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Eventos
    WHERE	IdEvento = pIdEvento;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campo de la Tabla Eventos}
END  ;



DROP PROCEDURE IF EXISTS bsp_darbaja_evento ;



CREATE DEFINER=`root`@`%` PROCEDURE `bsp_darbaja_evento`(pIdEvento int)
SALIR:BEGIN
/*
	Permite cambiar el estado del evento a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
*/

DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar evento no dado de baja
    IF EXISTS(SELECT IdEvento FROM Eventos WHERE IdEvento = pIdEvento
						AND EstadoEvento = 'B') THEN
		SELECT 'El evento ya está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Eventos SET EstadoEvento = 'B' WHERE IdEvento = pIdEvento;

    SELECT 'OK' AS Mensaje,'ok' as Response;


-- Mensaje varchar(100)
END  ;


DROP PROCEDURE IF EXISTS bsp_activar_evento ;



CREATE DEFINER=`root`@`%` PROCEDURE `bsp_activar_evento`(pIdEvento int)
SALIR:BEGIN
/*
	Permite cambiar el estado del evento a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
*/

DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar evento no este activo
    IF EXISTS(SELECT IdEvento FROM Eventos WHERE IdEvento = pIdEvento
						AND EstadoEvento = 'A') THEN
		SELECT 'El Evento ya está Activo.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;



	-- Da de baja
    UPDATE Eventos SET EstadoEvento = 'A' WHERE IdEvento = pIdEvento;

    SELECT 'OK' AS Mensaje,'ok' as Response;


-- Mensaje varchar(100)
END  ;

DROP PROCEDURE IF EXISTS bsp_finalizar_evento ;



CREATE DEFINER=`root`@`%` PROCEDURE `bsp_finalizar_evento`(pIdEvento int, pFechaInicio datetime, pFechaFinal datetime)
SALIR:BEGIN
/*
	Permite cambiar el estado del evento a F: Finalizado siempre y cuando no esté finalizado ni este dado de baja ya.
    Devuelve OK o el mensaje de error en Mensaje.
*/

DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar evento no este finalizado
    IF EXISTS(SELECT IdEvento FROM Eventos WHERE IdEvento = pIdEvento
						AND EstadoEvento = 'F') THEN
		SELECT 'El Evento ya está finalizado.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;
        -- Controlar evento no este dado de baja
    IF EXISTS(SELECT IdEvento FROM Eventos WHERE IdEvento = pIdEvento
						AND EstadoEvento = 'B') THEN
		SELECT 'El Evento está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

   -- Controla parámetros obligatorios
	IF

		pFechaInicio IS NULL OR
        pFechaFinal IS NULL
        THEN
		SELECT 'Faltan datos obligatorios.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;


	-- Da de baja
    UPDATE Eventos SET EstadoEvento = 'F', FechaInicio=pFechaInicio, FechaFinal=pFechaFinal WHERE IdEvento = pIdEvento;

    SELECT 'OK' AS Mensaje,'ok' as Response;



-- Mensaje varchar(100)
END  ;










        ";
        DB::unprepared($sql);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
