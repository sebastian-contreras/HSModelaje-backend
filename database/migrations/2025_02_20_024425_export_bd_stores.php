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
	WHERE		(pCadena is NULL OR Evento LIKE CONCAT('%',pCadena, '%')) AND
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


DROP PROCEDURE IF EXISTS bsp_listar_gastos ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_gastos`(pIdEvento int)
SALIR:BEGIN
/*
	Permite listar los gastos registrados en un evento.
*/
   SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Gastos
		WHERE
					IdEvento = pIdEvento
		ORDER BY IdGasto
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Gastos}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_gastos ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_gastos`(pIdEvento int, pGasto varchar(100) , pOffset int, pRowCount int)
SALIR:BEGIN
	/*
		Permite listar los gastos registrados en un evento, y filtrarlos por nombre. Incluye Paginado
    */
      DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pGasto)>1 AND CHAR_LENGTH(pGasto) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Gastos
	WHERE		(pGasto IS NULL OR Gasto LIKE CONCAT('%',pGasto, '%')) AND IdEvento = pIdEvento
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Gastos
   WHERE	(pGasto IS NULL OR Gasto LIKE CONCAT('%',pGasto, '%'))	AND IdEvento = pIdEvento
   ORDER BY IdGasto DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

    -- {Campos de la Tabla Gastos}
END;


DROP PROCEDURE IF EXISTS bsp_alta_gasto ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_gasto`(
    pIdEvento int,
    pGasto varchar(100),
    pPersonal varchar(100),
    pMonto decimal(10,2),
    pComprobante varchar(400)
)
SALIR:BEGIN
/*
    Permite dar de alta un gasto. Devuelve OK + Id o el mensaje de error en Mensaje.
*/
    DECLARE pIdGasto int;
    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdEvento IS NULL OR
       pGasto = '' OR pGasto IS NULL OR
       pPersonal = '' OR pPersonal IS NULL OR
       pMonto IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Controla que el monto sea mayor a 0
    IF pMonto <= 0 THEN
        SELECT 'El monto debe ser mayor a 0.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;
    		SET pIdGasto = 1 + (SELECT COALESCE(MAX(IdGasto),0)
								FROM Gastos);
        INSERT INTO Gastos
        (`IdGasto`, `IdEvento`, `Gasto`, `Personal`, `Monto`, `Comprobante`, `FechaCreado`) VALUES
        (pIdGasto, pIdEvento, pGasto, pPersonal, pMonto, pComprobante, NOW());

        SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdGasto AS Id;
    COMMIT;
-- Mensaje varchar(100), Id int
END;


DROP PROCEDURE IF EXISTS bsp_modifica_gasto ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_gasto`(pIdGasto int,pGasto varchar(100), pPersonal varchar(100), pMonto decimal(10,2), pComprobante varchar(400))
SALIR:BEGIN
/*
	Permite modificar el gasto.  Devuelve OK + Id o el mensaje de error en Mensaje.
*/

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdGasto IS NULL OR
       pGasto = '' OR pGasto IS NULL OR
       pPersonal = '' OR pPersonal IS NULL OR
       pMonto IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Controla que el monto sea mayor a 0
    IF pMonto <= 0 THEN
        SELECT 'El monto debe ser mayor a 0.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

        UPDATE Gastos SET
            IdGasto=pIdGasto,
            Gasto=pGasto,
            Personal=pPersonal,
            Monto=pMonto,
            Comprobante=pComprobante
            WHERE IdGasto = pIdGasto;

        SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdGasto AS Id;
    COMMIT;
-- Mensaje varchar(100), Id int
END;


DROP PROCEDURE IF EXISTS bsp_borra_gasto ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_gasto`(pIdGasto int)
SALIR:BEGIN
	/*
		Permite borrar un gasto, solamente usado para limpiar base de datos y en produccion.
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
        DELETE FROM Gastos WHERE IdGasto = pIdGasto;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;
END;

DROP PROCEDURE IF EXISTS bsp_dame_gasto ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_gasto`(pIdGasto int)
SALIR:BEGIN
/*
	Procedimiento que sirve para instanciar un gasto desde la base de datos.
*/
    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Gastos
    WHERE	IdGasto = pIdGasto;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Gastos}
END;


-- MODELOS


DROP PROCEDURE IF EXISTS bsp_listar_modelos;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_modelos`(pIncluyeBajas char(1))
BEGIN
/*
	Permite listar los modelos registrados. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/

		    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Modelos
		WHERE
					(pIncluyeBajas = 'S' OR EstadoMod = 'A')
		ORDER BY IdModelo
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Modelos}
END;


DROP PROCEDURE IF EXISTS bsp_buscar_modelo;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_modelo`(pDNI char(11), pApelName varchar(80), pFechaNacimientoMin date,pFechaNacimientoMax date, pSexo char(1), pEstado char(1), pOffset int, pRowCount int)
SALIR:BEGIN
/*
	Permite buscar los modelos registrados, por eventos en los que participo, DNI, apellido y nombre, fecha de nacimiento, sexo y estado.
    Incluye paginado.
*/
DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pApelName)>1 AND CHAR_LENGTH(pApelName) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;


	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Modelos
	WHERE
		(pDNI IS NULL OR DNI LIKE CONCAT('%',pDNI, '%')) AND
        (pApelName IS NULL OR ApelName LIKE CONCAT('%',pApelName, '%')) AND
		(pFechaNacimientoMin IS NULL OR FechaNacimiento >= pFechaNacimientoMin) AND
        (pFechaNacimientoMax IS NULL OR FechaNacimiento <= pFechaNacimientoMax) AND
        (pSexo IS NULL OR Sexo = pSexo) AND
		(pEstado IS NULL OR EstadoMod = pEstado)
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Modelos
   WHERE
		(pDNI IS NULL OR DNI LIKE CONCAT('%',pDNI, '%')) AND
        (pApelName IS NULL OR ApelName LIKE CONCAT('%',pApelName, '%')) AND
		(pFechaNacimientoMin IS NULL OR FechaNacimiento >= pFechaNacimientoMin) AND
        (pFechaNacimientoMax IS NULL OR FechaNacimiento <= pFechaNacimientoMax) AND
        (pSexo IS NULL OR Sexo = pSexo) AND
		(pEstado IS NULL OR EstadoMod = pEstado)
		ORDER BY IdModelo DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;



-- {Campos de la Tabla Modelos}
END;

DROP PROCEDURE IF EXISTS bsp_alta_modelo;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_modelo`(
    pDNI char(11),
    pApelName varchar(80),
    pFechaNacimiento date,
    pSexo char(1),
    pTelefono varchar(15),
    pCorreo varchar(60)
)
SALIR:BEGIN
/*
    Permite dar de alta un modelo. Lo da de alta con estado A: Activa. Devuelve OK + Id o el mensaje de error en Mensaje.
*/

    DECLARE pIdModelo int;
    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pDNI = '' OR pDNI IS NULL OR
       pApelName IS NULL OR
       pFechaNacimiento IS NULL OR
       pSexo IS NULL OR
       pTelefono IS NULL OR
       pCorreo IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Controla que no exista un modelo con el mismo DNI
    IF EXISTS(SELECT DNI FROM Modelos WHERE DNI = pDNI) THEN
        SELECT 'Ya existe un modelo con ese DNI.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    INSERT INTO Modelos
    (`IdModelo`, `DNI`, `ApelName`, `FechaNacimiento`, `Sexo`, `Telefono`, `Correo`, `EstadoMod`) VALUES
    (0, pDNI, pApelName, pFechaNacimiento,pSexo, pTelefono, pCorreo, 'A');

    SET pIdModelo = LAST_INSERT_ID();

    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdModelo AS Id;

    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_modifica_modelo ;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_modelo`(pIdModelo int,pDNI char(11), pApelName varchar(80), pFechaNacimiento date, pSexo char(1), pTelefono varchar(15), pCorreo varchar(60))
SALIR:BEGIN
/*
	Permite modificar el modelo. Controlando que no este dado de Baja  Devuelve OK + Id o el mensaje de error en Mensaje.
*/
 -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pDNI = '' OR pDNI IS NULL OR
       pApelName IS NULL OR
       pIdModelo IS NULL OR
       pFechaNacimiento IS NULL OR
       pSexo IS NULL OR
       pTelefono IS NULL OR
       pCorreo IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Controla que no exista un modelo con el mismo DNI
    IF EXISTS(SELECT DNI FROM Modelos WHERE DNI = pDNI AND IdModelo != pIdModelo) THEN
        SELECT 'Ya existe un modelo con ese DNI.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

        UPDATE Modelos SET
     DNI = pDNI,
     ApelName = pApelName,
     FechaNacimiento = pFechaNacimiento,
     Sexo = pSexo,
     Telefono = pTelefono,
     Correo = pCorreo
	WHERE IdModelo = pIdModelo;



    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdModelo AS Id;

    COMMIT;

-- Mensaje varchar(100)
END;


DROP PROCEDURE IF EXISTS bsp_borra_modelo;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_modelo`(pIdModelo int)
SALIR:BEGIN
/*
	Permite borrar un modelo, solamente usado para limpiar base de datos y en produccion. Devuelve OK o el mensaje de error en Mensaje.
*/
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;
   -- Controla que el Modelo no haya participado nunca
	IF EXISTS(SELECT IdModelo FROM Participantes WHERE IdModelo  = pIdModelo ) THEN
		SELECT 'No puede borrar el Modelo. Existen participaciones asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

    START TRANSACTION;
		-- Borra
        DELETE FROM Modelos WHERE IdModelo = pIdModelo;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;


-- Mensaje varchar(100)
END;


DROP PROCEDURE IF EXISTS bsp_dame_modelo;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_modelo`(pIdModelo int)
SALIR:BEGIN
/*
	Procedimiento que sirve para instanciar un modelo desde la base de datos.
*/


    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Modelos
    WHERE	IdModelo = pIdModelo ;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campo de la Tabla Modelos}
END;

DROP PROCEDURE IF EXISTS bsp_darbaja_modelo;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_darbaja_modelo`(pIdModelo int)
SALIR:BEGIN
/*
	Permite cambiar el estado de un modelo a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar autor no dado de baja
    IF EXISTS(SELECT IdModelo FROM Modelos WHERE IdModelo = pIdModelo
						AND EstadoMod = 'B') THEN
		SELECT 'El modelo ya está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Modelos SET EstadoMod = 'B' WHERE IdModelo = pIdModelo;

    SELECT 'OK' AS Mensaje,'ok' as Response;
-- Mensaje varchar(100)
END;


DROP PROCEDURE IF EXISTS bsp_activar_modelo;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_activar_modelo`(pIdModelo int)
SALIR:BEGIN
/*
	Permite cambiar el estado de un modelo a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar autor no dado de baja
    IF EXISTS(SELECT IdModelo FROM Modelos WHERE IdModelo = pIdModelo
						AND EstadoMod = 'A') THEN
		SELECT 'El modelo ya está activo.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- ACTIVAR
    UPDATE Modelos SET EstadoMod = 'A' WHERE IdModelo = pIdModelo;

    SELECT 'OK' AS Mensaje,'ok' as Response;
-- Mensaje varchar(100)
END;




DROP PROCEDURE IF EXISTS bsp_dame_juez;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_juez`(pIdJuez int)
SALIR:BEGIN
/*
	Procedimiento que sirve para instanciar un juez desde la base de datos.
*/


    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Jueces
    WHERE	IdJuez = pIdJuez;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campo de la Tabla Modelos}
END;




DROP PROCEDURE IF EXISTS bsp_dame_juez_token;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_juez_token`(pToken char(36))
SALIR:BEGIN
/*
	Procedimiento que sirve para instanciar un juez desde la base de datos por token.
*/


    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Jueces
    WHERE	Token = pToken;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campo de la Tabla Modelos}
END;




DROP PROCEDURE IF EXISTS bsp_listar_jueces;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_jueces`(pIdEvento int, pIncluyeBajas char(1))
SALIR:BEGIN
/*
	Permite listar los modelos jueces  de un evento. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/
	 SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Jueces
		WHERE
					IdEvento = pIdEvento
                    AND
                    (pIncluyeBajas = 'S' OR EstadoJuez = 'A')
		ORDER BY IdJuez
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Jueces}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_juez`(pIdEvento int, pDNI char(11), pApelName varchar(80), pEstado char(1), pOffset int, pRowCount int )
SALIR:BEGIN
/*
	Permite buscar los jueces registrados, por eventos en los que participo, DNI, apellido y nombre y estado. Incluye paginado.
*/

 DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pApelName)>1 AND CHAR_LENGTH(pApelName) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Jueces
	WHERE
    	(pDNI IS NULL OR DNI LIKE CONCAT('%',pDNI, '%')) AND
        (pApelName IS NULL OR ApelName LIKE CONCAT('%',pApelName, '%')) AND
        (pEstado IS NULL OR EstadoJuez = pEstado) AND
        IdEvento = pIdEvento
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Jueces
   WHERE
       	(pDNI IS NULL OR DNI LIKE CONCAT('%',pDNI, '%')) AND
        (pApelName IS NULL OR ApelName LIKE CONCAT('%',pApelName, '%')) AND
        (pEstado IS NULL OR EstadoJuez = pEstado) AND
        IdEvento = pIdEvento
   ORDER BY IdJuez DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;




-- {Campos de la Tabla Jueces}
END;

DROP PROCEDURE IF EXISTS bsp_alta_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_juez`(
    pIdEvento INT,
    pDNI CHAR(11),
    pApelName VARCHAR(80),
    pCorreo VARCHAR(60),
    pTelefono VARCHAR(15)
)
SALIR:BEGIN
    /*
        Permite dar de alta un juez. Devuelve OK + Id o el mensaje de error en Mensaje.
    */

    DECLARE pIdJuez INT;

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdEvento IS NULL OR
       pDNI = '' OR pDNI IS NULL OR
       pApelName = '' OR pApelName IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    -- Generar un nuevo ID para el juez
    SET pIdJuez = 1 + (SELECT COALESCE(MAX(IdJuez), 0) FROM Jueces);

    -- Insertar el nuevo juez
    INSERT INTO Jueces
    (`IdJuez`, `IdEvento`, `DNI`, `ApelName`, `Correo`, `Telefono`,`EstadoJuez`,`Token`) VALUES
    (pIdJuez, pIdEvento, pDNI, pApelName, pCorreo, pTelefono,'A',UUID());

    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdJuez AS Id;

    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_modifica_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_juez`(pIdJuez int, pDNI char(11) , pApelName varchar(80), pTelefono varchar(15), pCorreo varchar(60))
SALIR:BEGIN
    /*
            Permite modificar un juez. Controlando que no este dado de Baja  Devuelve OK + Id o el mensaje de error en Mensaje.
    */


    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdJuez IS NULL OR
       pDNI = '' OR pDNI IS NULL OR
       pApelName = '' OR pApelName IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    UPDATE Jueces SET
    DNI = pDNI,
    ApelName = pApelName,
    Correo = pCorreo,
    Telefono = pTelefono
    WHERE IdJuez = pIdJuez;

    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdJuez AS Id;

    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_borra_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_juez`(pIdJuez int)
SALIR:BEGIN
/*
	Permite borrar un juez, solamente usado para limpiar base de datos y en produccion. Devuelve OK o el mensaje de error en Mensaje.
*/

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

	   -- Controla que el juez no haya participado en una votacion
	IF EXISTS(SELECT IdJuez FROM Votacion WHERE IdJuez = pIdJuez ) THEN
		SELECT 'No puede borrar el Juez. Existen Votaciones asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

    START TRANSACTION;
		-- Borra usuario
        DELETE FROM Jueces WHERE IdJuez = pIdJuez;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;

-- Mensaje varchar(100)
END;
DROP PROCEDURE IF EXISTS bsp_dame_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_juez`(pIdJuez int)
BEGIN
/*
	Procedimiento que sirve para instanciar un juez desde la base de datos.
*/

   SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Jueces
    WHERE	IdJuez = pIdJuez;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- pIdJuez int
END;

DROP PROCEDURE IF EXISTS bsp_darbaja_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_darbaja_juez`(pIdJuez int)
SALIR:BEGIN
/*
	Permite cambiar el estado de un juez a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


    IF EXISTS(SELECT IdJuez FROM Jueces WHERE IdJuez = pIdJuez
						AND EstadoJuez = 'B') THEN
		SELECT 'El juez ya está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Jueces SET EstadoJuez = 'B' WHERE IdJuez = pIdJuez;

    SELECT 'OK' AS Mensaje,'ok' as Response;

-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_activar_juez;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_activar_juez`(pIdJuez int)
SALIR:BEGIN
/*
	Permite cambiar el estado de un juez a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


    IF EXISTS(SELECT IdJuez FROM Jueces WHERE IdJuez = pIdJuez
						AND EstadoJuez = 'A') THEN
		SELECT 'El juez ya está activo.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Jueces SET EstadoJuez = 'A' WHERE IdJuez = pIdJuez;

    SELECT 'OK' AS Mensaje,'ok' as Response;

-- Mensaje varchar(100)
END;


DROP PROCEDURE IF EXISTS bsp_listar_zonas;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_zonas`(pIdEvento int, pIncluyeBajas char(1))
BEGIN
/*
	Permite listar las zonas registradas en un evento. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No)
*/
		SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Zonas
		WHERE
					(pIncluyeBajas = 'S' OR EstadoZona = 'A') AND IdEvento = pIdEvento
		ORDER BY IdZona
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Eventos}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_zonas;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_zonas`(pIdEvento int, pZona varchar(100), pAccesoDisc char(1),pEstado char(1), pOffset int, pRowCount int)
SALIR:BEGIN
/*
	Permite listar las zonas registradas en un evento. Puede mostrar o no las inactivas (pIncluyeBajas: S: Si - N: No). Incluye Paginado
*/
  DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pZona)>1 AND CHAR_LENGTH(pZona) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Zonas
	WHERE		(pZona IS NULL OR Zona LIKE CONCAT('%',pZona, '%')) AND
				(pEstado IS NULL OR EstadoZona = pEstado) AND
                (pAccesoDisc IS NULL OR AccesoDisc = pAccesoDisc) AND
				 IdEvento = pIdEvento
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Zonas
   WHERE
				(pZona IS NULL OR Zona LIKE CONCAT('%',pZona, '%')) AND
				(pEstado IS NULL OR EstadoZona = pEstado) AND
                (pAccesoDisc IS NULL OR AccesoDisc = pAccesoDisc) AND
				 IdEvento = pIdEvento
   ORDER BY IdZona DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campos de la Tabla Zonas}
END;

DROP PROCEDURE IF EXISTS bsp_alta_zona;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_zona`(
    pIdEvento int,
    pZona varchar(100),
    pCapacidad int,
    pAccesoDisc char(1),
    pPrecio decimal(15, 2),
    pDetalle text
)
SALIR:BEGIN
/*
 Permite dar de alta una zona en un evento. Lo da de alta con estado A: Activa. Devuelve OK + Id o el mensaje de error en Mensaje.
 */
DECLARE pIdZona int;

DECLARE pIdEstablecimiento int;

-- Manejo de error en la transacción
DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN SHOW ERRORS;

SELECT
    'Error en la transacción. Contáctese con el administrador.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

ROLLBACK;

END;

-- Controla parámetros obligatorios
IF pIdEvento IS NULL
OR pZona = ''
OR pZona IS NULL
OR pCapacidad IS NULL
OR pAccesoDisc IS NULL
OR pPrecio IS NULL THEN
SELECT
    'Faltan datos obligatorios.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

LEAVE SALIR;

END IF;

-- Controla que el evento exista
IF NOT EXISTS (
    SELECT
        IdEvento
    FROM
        Eventos
    WHERE
        IdEvento = pIdEvento
) THEN
SELECT
    'No existe el evento.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

LEAVE SALIR;

END IF;

-- Verificar que pAccesoDisc sea uno de los valores permitidos
IF pAccesoDisc NOT IN ('S', 'N') THEN
SELECT
    'Acceso para discapacitados no válido.' AS Mensaje,
    NULL AS Id;

LEAVE SALIR;

-- Salir del procedimiento
END IF;

-- COMIENZO TRANSACCION
START TRANSACTION;

SET
    pIdZona = 1 + (
        SELECT
            COALESCE(MAX(IdZona), 0)
        FROM
            Zonas
    );

SET
    pIdEstablecimiento = (
        SELECT
            IdEstablecimiento
        from
            Eventos
        WHERE
            IdEvento = pIdEvento
    );

INSERT INTO
    `Zonas` (
        `IdZona`,
        `IdEstablecimiento`,
        `IdEvento`,
        `Zona`,
        `Ocupacion`,
        `Capacidad`,
        `AccesoDisc`,
        `Precio`,
        `Detalle`,
        `EstadoZona`
    )
VALUES
    (
        pIdZona,
		pIdEstablecimiento,
        pIdEvento,
        pZona,
        0,
        pCapacidad,
        pAccesoDisc,
        pPrecio,
        pDetalle,
        'A'
    );

SELECT
    'OK' AS Mensaje,
    'ok' AS Response,
    pIdZona AS Id;

COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_modifica_zona;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_zona`(
    pIdZona int,
    pZona varchar(100),
    pCapacidad int,
    pAccesoDisc char(1),
    pPrecio decimal(15, 2),
    pDetalle text
)
SALIR:BEGIN
/*
 Permite modificar el evento. Controlando que no este dado de Baja  Devuelve OK + Id o el mensaje de error en Mensaje.
 */
-- Manejo de error en la transacción
DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN SHOW ERRORS;

SELECT
    'Error en la transacción. Contáctese con el administrador.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

ROLLBACK;

END;

-- Controla parámetros obligatorios
IF pIdZona IS NULL
OR pZona = ''
OR pZona IS NULL
OR pCapacidad IS NULL
OR pAccesoDisc IS NULL
OR pPrecio IS NULL THEN
SELECT
    'Faltan datos obligatorios.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

LEAVE SALIR;

END IF;

-- Verificar que pAccesoDisc sea uno de los valores permitidos
IF pAccesoDisc NOT IN ('S', 'N') THEN
SELECT
    'Acceso para discapacitados no válido.' AS Mensaje,
    NULL AS Id;

LEAVE SALIR;

-- Salir del procedimiento
END IF;

-- COMIENZO TRANSACCION
START TRANSACTION;

UPDATE
    Zonas
SET
    Zona = pZona,
    Capacidad = pCapacidad,
    AccesoDisc = pAccesoDisc,
    Precio = pPrecio,
    Detalle = pDetalle
WHERE
    IdZona = pIdZona;

SELECT
    'OK' AS Mensaje,
    'ok' AS Response,
    pIdZona AS Id;

COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_borra_zona;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_zona`(pIdZona int)
SALIR:BEGIN
/*
	Permite borrar una zona, solamente usado para limpiar base de datos y en produccion, debe verificarse que no hayan entradas vendidas en esa zona.
    Devuelve OK o el mensaje de error en Mensaje.
*/
   DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

   -- Controla que el Evento no tenga metricas asociadas
	IF EXISTS(SELECT IdZona FROM Entradas WHERE IdZona = pIdZona) THEN
		SELECT 'No puede borrar la zona. Existen entradas asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;


    START TRANSACTION;
		-- Borra usuario
        DELETE FROM Zonas WHERE IdZona= pIdZona;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;



-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_dame_zona;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_zona`(pIdZona int)
BEGIN
/*
	Procedimiento que sirve para instanciar una zona desde la base de datos.
*/

    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Zonas
    WHERE	IdZona = pIdZona;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campo de la Tabla Zonas}
END;

DROP PROCEDURE IF EXISTS bsp_darbaja_zona;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_darbaja_zona`(pIdZona int)
SALIR:BEGIN
/*
	Permite cambiar el estado de una zona a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
*/

DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar zona no dado de baja
    IF EXISTS(SELECT IdZona FROM Zonas WHERE IdZona = pIdZona
						AND EstadoZona = 'B') THEN
		SELECT 'La zona ya está dado de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Zonas SET EstadoZona = 'B' WHERE IdZona = pIdZona;

    SELECT 'OK' AS Mensaje,'ok' as Response;


-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_activar_zona;


CREATE DEFINER=`root`@`%` PROCEDURE `bsp_activar_zona`(pIdZona int)
SALIR:BEGIN
/*
	Permite cambiar el estado de una zona a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
*/

DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;

    -- Controlar zona no dado de baja
    IF EXISTS(SELECT IdZona FROM Zonas WHERE IdZona = pIdZona
						AND EstadoZona = 'A') THEN
		SELECT 'La zona ya está activa.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Zonas SET EstadoZona = 'A' WHERE IdZona = pIdZona;

    SELECT 'OK' AS Mensaje,'ok' as Response;


-- Mensaje varchar(100)
END;


DROP PROCEDURE IF EXISTS bsp_listar_metricas;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_metricas`(pIdEvento int, pIncluyeBajas char(1))
BEGIN
/*
		Permite listar las metricas registrados en un evento.
*/
		SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Metricas
		WHERE
					(pIncluyeBajas = 'S' OR EstadoMetrica = 'A') AND IdEvento = pIdEvento
		ORDER BY IdMetrica
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Metricas}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_metricas;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_metricas`(pIdEvento int, pMetrica varchar(150) ,pEstado char(1), pOffset int, pRowCount int )
SALIR:BEGIN
/*
	Permite buscar las metricas registrados en un evento.
*/

 DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pMetrica)>1 AND CHAR_LENGTH(pMetrica) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Metricas
	WHERE
        (pMetrica IS NULL OR Metrica LIKE CONCAT('%',pMetrica, '%')) AND
        (pEstado IS NULL OR EstadoMetrica = pEstado) AND
        IdEvento = pIdEvento
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Metricas
   WHERE
        (pMetrica IS NULL OR Metrica LIKE CONCAT('%',pMetrica, '%')) AND
        (pEstado IS NULL OR EstadoMetrica = pEstado) AND
        IdEvento = pIdEvento
   ORDER BY IdMetrica DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Metricas}
END;

DROP PROCEDURE IF EXISTS bsp_alta_metrica;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_metrica`(pIdEvento int, pMetrica varchar(150))
SALIR :BEGIN
/*
 Permite dar de alta un metrica. Devuelve OK + Id o el mensaje de error en Mensaje.
 */
DECLARE pIdMetrica INT;

-- Manejo de error en la transacción
DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN SHOW ERRORS;

SELECT
    'Error en la transacción. Contáctese con el administrador.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

ROLLBACK;

END;

-- Controla parámetros obligatorios
IF pIdEvento IS NULL
OR pMetrica = ''
OR pMetrica IS NULL THEN
SELECT
    'Faltan datos obligatorios.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

LEAVE SALIR;

END IF;

-- COMIENZO TRANSACCION
START TRANSACTION;

-- Generar un nuevo ID para el metrica
SET
    pIdMetrica = 1 + (
        SELECT
            COALESCE(MAX(IdMetrica), 0)
        FROM
            Metricas
    );

-- Insertar el nuevo metrica
INSERT INTO
    Metricas (
        `IdMetrica`,
        `IdEvento`,
        `Metrica`,
        `EstadoMetrica`
    )
VALUES
    (pIdMetrica, pIdEvento, pMetrica, 'A');

-- Mensaje de éxito
SELECT
    'OK' AS Mensaje,
    'ok' AS Response,
    pIdMetrica AS Id;

COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_modifica_metrica;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_metrica`(pIdMetrica int, pMetrica varchar(150))
SALIR :BEGIN
/*
    Permite modificar una metrica.  Devuelve OK + Id o el mensaje de error en Mensaje.
 */

-- Manejo de error en la transacción
DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN SHOW ERRORS;

SELECT
    'Error en la transacción. Contáctese con el administrador.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

ROLLBACK;

END;

-- Controla parámetros obligatorios
IF pIdMetrica IS NULL
OR pMetrica = ''
OR pMetrica IS NULL THEN
SELECT
    'Faltan datos obligatorios.' AS Mensaje,
    'error' AS Response,
    NULL AS Id;

LEAVE SALIR;

END IF;

-- COMIENZO TRANSACCION
START TRANSACTION;

UPDATE Metricas SET
    Metrica=pMetrica
    WHERE IdMetrica=pIdMetrica;

-- Mensaje de éxito
SELECT
    'OK' AS Mensaje,
    'ok' AS Response,
    pIdMetrica AS Id;

COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_borra_metrica;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_metrica`(pIdMetrica int)
SALIR:BEGIN
/*
	Permite borrar una metrica. Devuelve OK o el mensaje de error en Mensaje.
*/

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

	   -- Controla que el juez no haya participado en una votacion
	IF EXISTS(SELECT IdMetrica FROM Votacion WHERE IdMetrica = pIdMetrica ) THEN
		SELECT 'No puede borrar el Juez. Existen Votaciones asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

    START TRANSACTION;
		-- Borra usuario
        DELETE FROM Metricas WHERE IdMetrica = pIdMetrica;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;

-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_dame_metrica;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_metrica`(pIdMetrica int)
BEGIN
/*
	Procedimiento que sirve para instanciar una metrica desde la base de datos.
*/

   SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Metricas
    WHERE	IdMetrica = pIdMetrica;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Metricas}
END;

DROP PROCEDURE IF EXISTS bsp_darbaja_metrica;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_darbaja_metrica`(pIdMetrica int)
SALIR:BEGIN
/*
	Permite cambiar el estado de una metrica a B: Baja siempre y cuando no esté dada de baja. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


    IF EXISTS(SELECT IdMetrica FROM Metricas WHERE IdMetrica = pIdMetrica
						AND EstadoMetrica = 'B') THEN
		SELECT 'La metrica ya está dada de baja.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Metricas SET EstadoMetrica = 'B' WHERE IdMetrica = pIdMetrica;

    SELECT 'OK' AS Mensaje,'ok' as Response;

-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_activar_metrica;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_activar_metrica`(pIdMetrica int)
SALIR:BEGIN
/*
	Permite cambiar el estado de una metrica a A: Activo siempre y cuando no esté activo ya. Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


    IF EXISTS(SELECT IdMetrica FROM Metricas WHERE IdMetrica = pIdMetrica
						AND EstadoMetrica = 'A') THEN
		SELECT 'La metrica ya está activa.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

    UPDATE Metricas SET EstadoMetrica = 'A' WHERE IdMetrica = pIdMetrica;

    SELECT 'OK' AS Mensaje,'ok' as Response;

-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_listar_patrocinadores;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_patrocinadores`(pIdEvento int)
BEGIN
/*
	Permite listar los patrocinadores registrados en un evento.
*/
	 SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Patrocinadores
		WHERE
					IdEvento = pIdEvento
		ORDER BY IdPatrocinador
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Patrocinadores}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_patrocinadores;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_patrocinadores`(pIdEvento int, pPatrocinador varchar(100), pOffset int, pRowCount int )
SALIR:BEGIN
/*
	Permite listar los patrocinadores registrados en un evento.
*/
  DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	IF CHAR_LENGTH(pPatrocinador)>1 AND CHAR_LENGTH(pPatrocinador) < 3 THEN
		SELECT 'Sea más específico en la búsqueda' AS Mensaje;
        LEAVE SALIR;
	END IF;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Patrocinadores
	WHERE		(pPatrocinador IS NULL OR Patrocinador LIKE CONCAT('%',pPatrocinador, '%')) AND IdEvento = pIdEvento
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Patrocinadores
   WHERE	(pPatrocinador IS NULL OR Patrocinador LIKE CONCAT('%',pPatrocinador, '%'))	AND IdEvento = pIdEvento
   ORDER BY IdPatrocinador DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campos de la Tabla Patrocinadores}
END;

DROP PROCEDURE IF EXISTS bsp_alta_patrocinador;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_patrocinador`(pIdEvento int, pPatrocinador varchar(100), pCorreo varchar(100), pTelefono varchar(10), pDomicilioRef varchar(150),pDescripcion text)
SALIR:BEGIN
/*
	Permite dar de alta un patrocinador. Devuelve OK + Id o el mensaje de error en Mensaje.
*/

    DECLARE pIdPatrocinador INT;

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdEvento IS NULL OR
       pPatrocinador = '' OR pPatrocinador IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    -- Generar un nuevo ID para el patrocinador
    SET pIdPatrocinador = 1 + (SELECT COALESCE(MAX(IdPatrocinador), 0) FROM Patrocinadores);

    -- Insertar el nuevo patrocinador
    INSERT INTO Patrocinadores
    (`IdPatrocinador`, `IdEvento`, `Patrocinador`, `Correo`, `Telefono`, `DomicilioRef`, `Descripcion`, `FechaCreado`) VALUES
    (pIdPatrocinador, pIdEvento, pPatrocinador, pCorreo, pTelefono, pDomicilioRef ,pDescripcion, NOW());

    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdPatrocinador AS Id;

    COMMIT;


-- Mensaje varchar(100), Id int
END;

DROP PROCEDURE IF EXISTS bsp_modifica_patrocinador;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_patrocinador`(pIdPatrocinador int, pPatrocinador varchar(100), pCorreo varchar(100), pTelefono varchar(10), pDomicilioRef varchar(150), pDescripcion text)
SALIR:BEGIN
/*
	Permite modificar el patrocinador.  Devuelve OK + Id o el mensaje de error en Mensaje.
*/

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF
       pPatrocinador = '' OR pPatrocinador IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;
    UPDATE Patrocinadores SET
     Patrocinador=pPatrocinador,
     Correo=pCorreo,
     Telefono=pTelefono,
     Descripcion=pDescripcion,
     DomicilioRef=pDomicilioRef
     WHERE IdPatrocinador = pIdPatrocinador;

    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdPatrocinador AS Id;

    COMMIT;

-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_borra_patrocinador;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_patrocinador`(pIdPatrocinador int)
SALIR:BEGIN
/*
	Permite borrar un patrocinador de un evento.
*/
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

    START TRANSACTION;

        DELETE FROM Patrocinadores WHERE IdPatrocinador = pIdPatrocinador;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;
-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_dame_patrocinador;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_patrocinador`(pIdPatrocinador int)
SALIR:BEGIN
/*
	Procedimiento que sirve para instanciar un patrocinador desde la base de datos.
*/

      SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Patrocinadores
    WHERE	IdPatrocinador = pIdPatrocinador;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campo de la Tabla Patrocinadores}
END;



-- ENTRADAS

DROP PROCEDURE IF EXISTS bsp_listar_entradas;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_entradas`(pIdEvento int)
BEGIN
/*
	Permite listar las entradas registradas de un evento.
*/
  SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		*
		FROM		Entradas
		WHERE
					IdEvento = pIdEvento
		ORDER BY IdEntrada
		;

		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;


-- {Campos de la Tabla Entradas}
END;

DROP PROCEDURE IF EXISTS bsp_buscar_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_buscar_entrada`(
	pCadena varchar(50),
	pDNI varchar(11),
	pEstado char(1),
	pIdZona int,
	pIdEvento int,
	pOffset int,
	pRowCount int
)
SALIR :BEGIN
/*
 Permite buscar las entradas de un evento, filtrando por zona, apellido, nombre , Correo y DNI del comprador, y por estado de entrada.
 Incluye paginado.
 */
DECLARE pTotalRows int;

SET
	SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

IF CHAR_LENGTH(pCadena) > 1
AND CHAR_LENGTH(pCadena) < 3 THEN
SELECT
	'Sea más específico en la búsqueda' AS Mensaje;

LEAVE SALIR;

END IF;

SET
	pTotalRows = (
		SELECT
			COUNT(*)
		FROM
			Entradas
		WHERE
			(
				pDNI IS NULL
				OR DNI = pDNI
			)
			AND (
				pCadena IS NULL
				OR ApelName LIKE CONCAT('%', pCadena, '%')
				OR Correo LIKE CONCAT('%', pCadena, '%')
				OR Telefono LIKE CONCAT('%', pCadena, '%')
				OR DNI LIKE CONCAT('%', pCadena, '%')
			)
			AND (
				pIdZona IS NULL
				OR IdZona = pIdZona
			)
			AND (
				pIdEvento IS NULL
				OR IdEvento = pIdEvento
			)
			AND (
				pEstado IS NULL
				OR EstadoEnt = pEstado
			)
	);

-- Consulta final
SELECT
	*,
	pTotalRows as TotalRows
FROM
	Entradas
WHERE
	(
		pDNI IS NULL
		OR DNI = pDNI
	)
	AND (
		pCadena IS NULL
		OR ApelName LIKE CONCAT('%', pCadena, '%')
		OR Correo LIKE CONCAT('%', pCadena, '%')
		OR Telefono LIKE CONCAT('%', pCadena, '%')
		OR DNI LIKE CONCAT('%', pCadena, '%')
	)
	AND (
		pIdZona IS NULL
		OR IdZona = pIdZona
	)
	AND (
		pIdEvento IS NULL
		OR IdEvento = pIdEvento
	)
	AND (
		pEstado IS NULL
		OR EstadoEnt = pEstado
	)
ORDER BY
	IdEntrada DESC
LIMIT
	pOffset, pRowCount;

SET
	SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campos de la Tabla Entradas}
END;

DROP PROCEDURE IF EXISTS bsp_alta_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_entrada`(
    pIdZona INT,
    pApelname VARCHAR(100),
    pDNI VARCHAR(11),
    pCorreo VARCHAR(100),
    pTelefono VARCHAR(15),
    pComprobante VARCHAR(400),
    pCantidad INT
)
SALIR:BEGIN
    /*
        Permite dar de alta una entrada. Lo da de alta con estado P: Pendiente y con la fecha actual como fecha de alta.
        Devuelve OK + Id o el mensaje de error en Mensaje.
    */

    DECLARE pIdEntrada INT;
    DECLARE pIdEvento INT;
    DECLARE pIdEstablecimiento INT;

    DECLARE pOcupacionZona INT;
    DECLARE pCapacidadZona INT;
    DECLARE pImporte decimal(15, 2);

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdZona IS NULL OR
       pApelname = '' OR pApelname IS NULL OR
       pDNI = '' OR pDNI IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL OR
       pCantidad = '' OR pCantidad IS NULL OR
       pComprobante IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;
	IF (pCantidad<1) THEN
    		SELECT 'La cantidad de entrada debe ser por lo menos una.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;
        -- Controla que el establecimiento exista
	IF NOT EXISTS(SELECT IdZona FROM Zonas WHERE IdZona = pIdZona) THEN
		SELECT 'No existe la zona.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

  SET pIdEvento = (SELECT IdEvento FROM Zonas WHERE IdZona = pIdZona);
  SET pIdEstablecimiento = (SELECT IdEstablecimiento FROM Zonas WHERE IdZona = pIdZona);

  SET pImporte = (SELECT Precio FROM Zonas WHERE IdZona = pIdZona) * pCantidad;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    -- Insertar la nueva entrada con estado P (Pendiente)
    INSERT INTO Entradas
    (`IdEntrada`, `IdEvento`, `IdZona`,`IdEstablecimiento`, `Apelname`, `DNI`, `Correo`, `Telefono`, `Comprobante`, `EstadoEnt`, `FechaAlta`,`Cantidad`,`Importe`) VALUES
    (0,  pIdEvento,pIdZona,pIdEstablecimiento, pApelname, pDNI, pCorreo, pTelefono, pComprobante, 'P', NOW(),pCantidad,pImporte);

    SET pIdEntrada = LAST_INSERT_ID();


    -- Actualizar la cantidad de entradas vendidas en la zona
    SET pOcupacionZona = (SELECT Ocupacion FROM Zonas WHERE IdZona = pIdZona);
    SET pCapacidadZona = (SELECT Capacidad FROM Zonas WHERE IdZona = pIdZona);
    SET pOcupacionZona = pOcupacionZona + pCantidad;

    IF pOcupacionZona > pCapacidadZona THEN
        SELECT 'No hay capacidad suficiente en la zona.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
        LEAVE SALIR;
    END IF;
    UPDATE Zonas SET Ocupacion = pOcupacionZona WHERE IdZona = pIdZona;



    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdEntrada AS Id;

    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_alta_entrada_vendedor;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_entrada_vendedor`(
    pIdZona INT,
    pApelname VARCHAR(100),
    pDNI VARCHAR(11),
    pCorreo VARCHAR(100),
    pTelefono VARCHAR(15),
    pComprobante VARCHAR(400),
    pCantidad INT
)
SALIR:BEGIN
    /*
        Permite dar de alta una entrada desde sistema. Lo da de alta con estado P: Pendiente y con la fecha actual como fecha de alta, el comprobante puede exister o no.
        Devuelve OK + Id o el mensaje de error en Mensaje.
    */

    DECLARE pIdEntrada INT;
    DECLARE pIdEvento INT;
    DECLARE pIdEstablecimiento INT;


	DECLARE pOcupacionZona INT;
    DECLARE pCapacidadZona INT;
    DECLARE pImporte decimal(15, 2);

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;


    -- Controla parámetros obligatorios
    IF pIdZona IS NULL OR
       pApelname = '' OR pApelname IS NULL OR
       pDNI = '' OR pDNI IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL
        THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

	IF (pCantidad<1) THEN
    		SELECT 'La cantidad de entrada debe ser por lo menos una.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

        -- Controla que el establecimiento exista
	IF NOT EXISTS(SELECT IdZona FROM Zonas WHERE IdZona = pIdZona) THEN
		SELECT 'No existe la zona.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;



  SET pIdEvento = (SELECT IdEvento FROM Zonas WHERE IdZona = pIdZona);
  SET pIdEstablecimiento = (SELECT IdEstablecimiento FROM Zonas WHERE IdZona = pIdZona);
  SET pImporte = (SELECT Precio FROM Zonas WHERE IdZona = pIdZona) * pCantidad;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    -- Insertar la nueva entrada con estado P (Pendiente)
    INSERT INTO Entradas
    (`IdEntrada`, `IdEvento`, `IdZona`,`IdEstablecimiento`, `Apelname`, `DNI`, `Correo`, `Telefono`, `Comprobante`, `EstadoEnt`, `FechaAlta`, `Cantidad`,`Importe`) VALUES
    (0,  pIdEvento,pIdZona,pIdEstablecimiento, pApelname, pDNI, pCorreo, pTelefono, pComprobante, 'P', NOW(),pCantidad,pImporte);

    SET pIdEntrada = LAST_INSERT_ID();

        -- Actualizar la cantidad de entradas vendidas en la zona
    SET pOcupacionZona = (SELECT Ocupacion FROM Zonas WHERE IdZona = pIdZona);
    SET pCapacidadZona = (SELECT Capacidad FROM Zonas WHERE IdZona = pIdZona);
    SET pOcupacionZona = pOcupacionZona + pCantidad;

    IF pOcupacionZona > pCapacidadZona THEN
        SELECT 'No hay capacidad suficiente en la zona.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
        LEAVE SALIR;
    END IF;

    UPDATE Zonas SET Ocupacion = pOcupacionZona WHERE IdZona = pIdZona;

    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdEntrada AS Id;


    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_modifica_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_modifica_entrada`(pIdEntrada bigint, pIdZona int, pApelname varchar(100), pDNI varchar(11), pCorreo varchar(100), pTelefono varchar(15), pComprobante varchar(400))
SALIR:BEGIN
    /*
      Permite modificar una entrada. Controlando que no este haya sido usada(U) o rechazada(R).
      Devuelve OK + Id o el mensaje de error en Mensaje.
    */

    DECLARE pIdEvento INT;
    DECLARE pIdEstablecimiento INT;

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;


  	IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada AND (EstadoEnt='U' OR EstadoEnt='R')) THEN
		SELECT 'La entrada esta usada o rechazada, no puede ser modificada.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;


    -- Controla parámetros obligatorios
    IF pIdZona IS NULL OR
       pApelname = '' OR pApelname IS NULL OR
       pDNI = '' OR pDNI IS NULL OR
       pCorreo = '' OR pCorreo IS NULL OR
       pTelefono = '' OR pTelefono IS NULL
        THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

        -- Controla que el establecimiento exista
	IF NOT EXISTS(SELECT IdZona FROM Zonas WHERE IdZona = pIdZona) THEN
		SELECT 'No existe la zona.' AS Mensaje,'error' as Response, NULL AS Id;
		LEAVE SALIR;
    END IF;

  SET pIdEvento = (SELECT IdEvento FROM Zonas WHERE IdZona = pIdZona);
  SET pIdEstablecimiento = (SELECT IdEstablecimiento FROM Zonas WHERE IdZona = pIdZona);

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    -- Insertar la nueva entrada con estado P (Pendiente)
    UPDATE Entradas SET
    IdEvento=pIdEvento,
    IdZona=pIdZona,
    IdEstablecimiento=pIdEstablecimiento,
    Apelname=pApelname,
    DNI=pDNI,
    Correo=pCorreo,
    Telefono=pTelefono,
    Comprobante=pComprobante
    WHERE IdEntrada = pIdEntrada;


    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdEntrada AS Id;

    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_borra_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_entrada`(pIdEntrada bigint)
SALIR:BEGIN
/*
	Permite borrar una entrada, solamente usado para limpiar base de datos y en produccion. Devuelve OK o el mensaje de error en Mensaje.
*/
  DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;
   -- Controla que el Modelo no haya participado nunca
	IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada=pIdEntrada AND (EstadoEnt  = 'A'  OR EstadoEnt  = 'U')) THEN
		SELECT 'No puede borrar la entrada. Esta entrada esta pagada o usada.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

    START TRANSACTION;
		-- Borra
        DELETE FROM Entradas WHERE IdEntrada = pIdEntrada;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;


-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_dame_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_entrada`(pIdEntrada bigint)
SALIR:BEGIN
/*
	Procedimiento que sirve para instanciar una entrada desde la base de datos.
*/
    SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Entradas
    WHERE	IdEntrada = pIdEntrada;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;

-- {Campo de la Tabla Entradas}
END;

DROP PROCEDURE IF EXISTS bsp_abonar_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_abonar_entrada`(pIdEntrada bigint)
SALIR:BEGIN
/*
	Permite cambiar el estado de la entrada a A: Abonado y asignar un comprobante, ademas esta no debe estar usada, abonada ni rechazada.
  Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


  IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada
						AND EstadoEnt = 'A') THEN
		SELECT 'La entrada ya esta abonada.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

 IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada
						AND (EstadoEnt = 'U' OR EstadoEnt = 'R')) THEN
		SELECT 'La entrada esta Usada o rechazada. No se puede abonar' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Entradas SET EstadoEnt = 'A',
    Token = uuid()
    WHERE IdEntrada = pIdEntrada;

    SELECT 'OK' AS Mensaje,'ok' as Response;
-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_usar_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_usar_entrada`(pIdEntrada bigint)
SALIR:BEGIN
/*
	Permite cambiar el estado de la entrada a U: Usada siempre y cuando no esté Pendiente, Usada o Rechazada.
  Devuelve OK o el mensaje de error en Mensaje.
*/
DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


  IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada
						AND EstadoEnt = 'U') THEN
		SELECT 'La entrada ya esta usada.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

 IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada
						AND (EstadoEnt = 'P' OR EstadoEnt = 'R')) THEN
		SELECT 'La entrada esta rechazada o pendiente. No se puede usar' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Entradas SET EstadoEnt = 'U' WHERE IdEntrada = pIdEntrada;

    SELECT 'OK' AS Mensaje,'ok' as Response;
-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_rechazar_entrada;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_rechazar_entrada`(pIdEntrada bigint)
SALIR:BEGIN
/*
	Permite cambiar el estado de la entrada a R: Rechazada siempre y cuando no esté Rechazada, Abonada o Usada.
  Devuelve OK o el mensaje de error en Mensaje.
*/

    DECLARE pOcupacionZona INT;
    DECLARE pCapacidadZona INT;


DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador.' Mensaje,'error' as Response,
				NULL AS Id;
		ROLLBACK;
	END;


  IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada
						AND EstadoEnt = 'R') THEN
		SELECT 'La entrada ya esta rechazada.' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

 IF EXISTS(SELECT IdEntrada FROM Entradas WHERE IdEntrada = pIdEntrada
						AND (EstadoEnt = 'A' OR EstadoEnt = 'U')) THEN
		SELECT 'La entrada ya esta abonada o usada. No se puede rechazar' AS Mensaje,'error' as Response;
        LEAVE SALIR;
	END IF;

	-- Da de baja
    UPDATE Entradas SET EstadoEnt = 'R' WHERE IdEntrada = pIdEntrada;

	    SET pOcupacionZona = (SELECT Ocupacion FROM Zonas WHERE IdZona = pIdZona);
    SET pCapacidadZona = (SELECT Capacidad FROM Zonas WHERE IdZona = pIdZona);
    SET pOcupacionZona = pOcupacionZona - pCantidad;
    UPDATE Zonas SET Ocupacion = pOcupacionZona WHERE IdZona = pIdZona;



    SELECT 'OK' AS Mensaje,'ok' as Response;
-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_listar_participantes;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_participantes`(pIdEvento int, pOffset int, pRowCount int)
SALIR:BEGIN
/*
	Permite listar los participantes  de un evento. Tiene paginado.
*/

 DECLARE pTotalRows int;

       	SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

	SET pTotalRows =  (SELECT COUNT(*)
	FROM		Participantes
	WHERE
        IdEvento = pIdEvento
				);

   -- Consulta final
   SELECT * , pTotalRows as TotalRows
   FROM		Participantes JOIN Modelos USING(IdModelo)
   WHERE
        IdEvento = pIdEvento
   ORDER BY IdParticipante DESC LIMIT pOffset, pRowCount;

	SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;




-- {Campos de la Tabla Participantes}
END;

DROP PROCEDURE IF EXISTS bsp_alta_participante;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_participante`(pIdEvento int, pIdModelo int, pPromotor varchar(100))
SALIR:BEGIN
    /*
        Permite dar de alta un participante en un evento. Devuelve OK + Id o el mensaje de error en Mensaje.
    */
    DECLARE pIdParticipante INT;

    -- Manejo de error en la transacción
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SHOW ERRORS;
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Controla parámetros obligatorios
    IF pIdEvento IS NULL OR
       pIdModelo = '' OR pIdModelo IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;


    IF NOT EXISTS (SELECT IdModelo FROM Modelos WHERE IdModelo = pIdModelo) THEN
              SELECT 'El modelo no existe.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- COMIENZO TRANSACCION
    START TRANSACTION;

    -- Generar un nuevo ID para el juez
    SET pIdParticipante = 1 + (SELECT COALESCE(MAX(IdParticipante), 0) FROM Participantes);

    -- Insertar el nuevo juez
    INSERT INTO Participantes
    (`IdParticipante`,
`IdEvento`,
`IdModelo`,
`Promotor`) VALUES
    (pIdParticipante, pIdEvento, pIdModelo, pPromotor);

    -- Mensaje de éxito
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdParticipante AS Id;

    COMMIT;

END;

DROP PROCEDURE IF EXISTS bsp_borra_participante;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_borra_participante`(pIdParticipante int)
SALIR:BEGIN
/*
	Permite borrar un participante. Devuelve OK o el mensaje de error en Mensaje.
*/

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		-- SHOW ERRORS;
		SELECT 'Error en la transacción. Contáctese con el administrador' Mensaje,'error' as Response;
        ROLLBACK;
    END;

	   -- Controla que el participante no haya participado en una votacion
	IF EXISTS(SELECT IdParticipante FROM Votacion WHERE IdParticipante = pIdParticipante ) THEN
		SELECT 'No puede borrar el participante. Existen Votaciones asociadas.' AS Mensaje,'error' as Response;
		LEAVE SALIR;
    END IF;

    START TRANSACTION;
		-- Borra usuario
        DELETE FROM Participantes WHERE IdParticipante = pIdParticipante;

        SELECT 'OK' Mensaje,'ok' as Response;
    COMMIT;

-- Mensaje varchar(100)
END;

DROP PROCEDURE IF EXISTS bsp_dame_participante;

CREATE DEFINER=`root`@`%` PROCEDURE `bsp_dame_participante`(pIdParticipante int)
BEGIN
/*
	Procedimiento que sirve para instanciar un participante desde la base de datos.
*/

   SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

    SELECT	*, 'ok' as Response
    FROM	Participantes JOIN Modelos USING(IdModelo)
    WHERE	IdParticipante = pIdParticipante;

    SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Participantes}
END;



DROP PROCEDURE IF EXISTS bsp_alta_voto;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_alta_voto`(
    pIdParticipante INT,
    pIdJuez INT,
    pIdMetrica INT,
    pNota INT,
    pDevolucion TEXT
)
SALIR:BEGIN
    DECLARE pIdEvento INT;
    DECLARE pIdModelo INT;
    DECLARE pIdVoto BIGINT;

    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Validación de parámetros obligatorios
    IF pIdParticipante IS NULL OR
       pIdJuez IS NULL OR
       pIdMetrica IS NULL OR
       pNota IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Obtener IdEvento e IdModelo desde Participantes
    SELECT IdEvento, IdModelo
    INTO pIdEvento, pIdModelo
    FROM Participantes
    WHERE IdParticipante = pIdParticipante;

    -- Validar que se haya encontrado el participante
    IF pIdEvento IS NULL OR pIdModelo IS NULL THEN
        SELECT 'Participante inválido o no registrado.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Comenzar transacción
    START TRANSACTION;

    -- Eliminar votos anteriores del mismo juez para el mismo participante, métrica y evento
    DELETE FROM Votacion
    WHERE IdMetrica = pIdMetrica
      AND IdEvento = pIdEvento
      AND IdJuez = pIdJuez
      AND IdParticipante = pIdParticipante;

    -- Insertar el nuevo voto
    INSERT INTO Votacion (
        IdMetrica, IdEvento, IdJuez, IdParticipante, IdModelo, Nota, Devolucion, EstadoVoto
    ) VALUES (
        pIdMetrica, pIdEvento, pIdJuez, pIdParticipante, pIdModelo, pNota, pDevolucion, 'A'
    );

    -- Obtener ID autogenerado
    SET pIdVoto = LAST_INSERT_ID();

    -- Confirmar
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdVoto AS Id;

    COMMIT;
END;

DROP PROCEDURE IF EXISTS bsp_listar_votos;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_listar_votos`(pIdEvento int)
SALIR:BEGIN
/*
	Permite listar los votos  de un evento.
*/
	 SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

		SELECT		V.IdParticipante,M.DNI AS DNIModelo,M.ApelName AS ApelNameModelo,J.IdJuez,J.DNI AS DNIJuez,J.ApelName AS ApelNameJuez,P.IdMetrica,P.Metrica, V.Nota
		FROM Votacion V
        JOIN Modelos M USING (IdModelo)
        JOIN Jueces J USING (IdJuez)
        JOIN Metricas P USING (IdMetrica)
		WHERE
					V.IdEvento = pIdEvento
		ORDER BY J.IdJuez
		;


		SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
-- {Campos de la Tabla Votacion,Modelos,Jueces}
END;



DROP PROCEDURE IF EXISTS bsp_reiniciar_votacion_participante;
CREATE DEFINER=`root`@`%` PROCEDURE `bsp_reiniciar_votacion_participante`(pIdParticipante INT)
SALIR:BEGIN
/*
	Procedimiento para reinicar la votacion de un participante, los votos al participante se quedan es estado inicial. 
    Devuelve OK o el mensaje de error en Mensaje.
*/
    -- Manejo de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'Error en la transacción. Contáctese con el administrador.' AS Mensaje, 'error' AS Response, NULL AS Id;
        ROLLBACK;
    END;

    -- Validación de parámetros obligatorios
    IF pIdParticipante IS NULL THEN
        SELECT 'Faltan datos obligatorios.' AS Mensaje, 'error' AS Response, NULL AS Id;
        LEAVE SALIR;
    END IF;

    -- Comenzar transacción
    START TRANSACTION;

    -- Eliminar votos anteriores del mismo juez para el mismo participante, métrica y evento
    DELETE FROM Votacion
    WHERE IdParticipante = pIdParticipante;
    -- Confirmar
    SELECT 'OK' AS Mensaje, 'ok' AS Response, pIdVoto AS Id;
    COMMIT;
    
END;


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
