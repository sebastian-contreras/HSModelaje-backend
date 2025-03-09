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
    (`IdJuez`, `IdEvento`, `DNI`, `ApelName`, `Correo`, `Telefono`,`EstadoJuez`) VALUES
    (pIdJuez, pIdEvento, pDNI, pApelName, pCorreo, pTelefono,'A');

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
