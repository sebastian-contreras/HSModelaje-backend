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
        $sql = "
--
-- ER/Studio 8.0 SQL Code Generation
-- Company :      SC
-- Project :      HSModelaje-Relacional.DM1
-- Author :       Sebastian Contreras
--
-- Date Created : Saturday, March 08, 2025 13:18:16
-- Target DBMS : MySQL 5.x
--

--
-- TABLE: Entradas
--

CREATE TABLE Entradas(
    IdEntrada            BIGINT          AUTO_INCREMENT,
    IdZona               INT             NOT NULL,
    IdEstablecimiento    INT             NOT NULL,
    IdEvento             INT             NOT NULL,
    ApelName             VARCHAR(100)    NOT NULL,
    DNI                  VARCHAR(11)     NOT NULL,
    Correo               VARCHAR(100)    NOT NULL,
    Telefono             VARCHAR(15)     NOT NULL,
    FechaAlta            DATETIME        NOT NULL,
    Comprobante          VARCHAR(400),
    EstadoEnt            CHAR(1),
    PRIMARY KEY (IdEntrada, IdZona, IdEstablecimiento, IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Establecimientos
--

CREATE TABLE Establecimientos(
    IdEstablecimiento        INT            AUTO_INCREMENT,
    Establecimiento          VARCHAR(60)    NOT NULL,
    Ubicacion                TEXT           NOT NULL,
    Capacidad                INT            NOT NULL,
    EstadoEstablecimiento    CHAR(1)        NOT NULL,
    PRIMARY KEY (IdEstablecimiento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Eventos
--

CREATE TABLE Eventos(
    IdEvento                INT             AUTO_INCREMENT,
    Evento                  VARCHAR(150)    NOT NULL,
    FechaInicio             DATETIME,
    FechaFinal              DATETIME,
    FechaProbableInicio    DATETIME        NOT NULL,
    FechaProbableFinal      DATETIME        NOT NULL,
    Votacion                CHAR(1)         NOT NULL,
    EstadoEvento            CHAR(1)         NOT NULL,
    IdEstablecimiento       INT             NOT NULL,
    PRIMARY KEY (IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Gastos
--

CREATE TABLE Gastos(
    IdGasto       INT               NOT NULL        AUTO_INCREMENT,
    IdEvento       INT               NOT NULL,
    Gasto          VARCHAR(100)      NOT NULL,
    Personal       VARCHAR(100)      NOT NULL,
    Monto          DECIMAL(15, 2)    NOT NULL,
    Comprobante    VARCHAR(400),
    FechaCreado    DATETIME        NOT NULL,
    PRIMARY KEY (IdGasto, IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Jueces
--

CREATE TABLE Jueces(
    IdJuez        INT            AUTO_INCREMENT,
    IdEvento      INT            NOT NULL,
    DNI           CHAR(11)       NOT NULL,
    ApelName      VARCHAR(80)    NOT NULL,
    Correo        VARCHAR(60)    NOT NULL,
    Telefono      VARCHAR(15)    NOT NULL,
    EstadoJuez    CHAR(1)        NOT NULL,
    PRIMARY KEY (IdJuez, IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Metricas
--

CREATE TABLE Metricas(
    IdMetrica        INT             AUTO_INCREMENT,
    IdEvento         INT             NOT NULL,
    Metrica          VARCHAR(150)    NOT NULL,
    EstadoMetrica    CHAR(1)         NOT NULL,
    PRIMARY KEY (IdMetrica, IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Modelos
--

CREATE TABLE Modelos(
    IdModelo     INT            AUTO_INCREMENT,
    DNI          CHAR(11),
    ApelName     VARCHAR(80),
    FechaNacimiento         DATE,
    Sexo         CHAR(1),
    Telefono     VARCHAR(15),
    Correo       VARCHAR(60),
    EstadoMod    CHAR(1),
    PRIMARY KEY (IdModelo)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Participantes
--

CREATE TABLE Participantes(
    IdParticipante        INT             NOT NULL,
    IdEvento              INT             NOT NULL,
    IdModelo              INT             NOT NULL,
    Promotor              VARCHAR(100),
    EstadoParticipante    CHAR(1)         NOT NULL,
    PRIMARY KEY (IdParticipante, IdEvento, IdModelo)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Patrocinador
--

CREATE TABLE Patrocinador(
    IdPatrocinador    SMALLINT        AUTO_INCREMENT,
    IdEvento          INT             NOT NULL,
    Patrocinador      VARCHAR(100)    NOT NULL,
    Correo            VARCHAR(100)    NOT NULL,
    Telefono          VARCHAR(10)     NOT NULL,
    Descripcion       TEXT,
    PRIMARY KEY (IdPatrocinador, IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Usuarios
--

CREATE TABLE Usuarios(
    IdUsuario          SMALLINT       AUTO_INCREMENT,
    Username           VARCHAR(20)    NOT NULL,
    Apellidos          VARCHAR(30)    NOT NULL,
    Nombres            VARCHAR(30)    NOT NULL,
    FechaNacimiento    DATE           NOT NULL,
    Telefono           VARCHAR(15)    NOT NULL,
    Email              VARCHAR(60)    NOT NULL,
    Contrasena         CHAR(32)       NOT NULL,
    FechaCreado        DATETIME       NOT NULL,
    Rol                CHAR(1)        NOT NULL,
    EstadoUsuario      CHAR(1)        NOT NULL,
    PRIMARY KEY (IdUsuario)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Votacion
--

CREATE TABLE Votacion(
    IdVoto            CHAR(10)    NOT NULL,
    IdMetrica         INT         NOT NULL,
    IdEvento          INT         NOT NULL,
    IdJuez            INT         NOT NULL,
    IdParticipante    INT         NOT NULL,
    IdModelo          INT         NOT NULL,
    Nota              INT         NOT NULL,
    Devolucion        TEXT,
    EstadoVoto        CHAR(1)     NOT NULL,
    PRIMARY KEY (IdVoto, IdMetrica, IdEvento, IdJuez, IdParticipante, IdModelo)
)ENGINE=INNODB
COMMENT=''
;

--
-- TABLE: Zonas
--

CREATE TABLE Zonas(
    IdZona               INT               NOT NULL,
    IdEstablecimiento    INT               NOT NULL,
    IdEvento             INT               NOT NULL,
    Zona                 VARCHAR(100)      NOT NULL,
    Capacidad            INT               NOT NULL,
    Ocupacion            INT               NOT NULL,
    AccesoDisc           CHAR(1)           NOT NULL,
    Precio               DECIMAL(15, 2)    NOT NULL,
    Detalle              TEXT              NOT NULL,
    EstadoZona           CHAR(1)           NOT NULL,
    PRIMARY KEY (IdZona, IdEstablecimiento, IdEvento)
)ENGINE=INNODB
COMMENT=''
;

--
-- INDEX: Ref46
--

CREATE INDEX Ref46 ON Entradas(IdEvento, IdEstablecimiento, IdZona)
;
--
-- INDEX: Ref320
--

CREATE INDEX Ref320 ON Eventos(IdEstablecimiento)
;
--
-- INDEX: Ref65
--

CREATE INDEX Ref65 ON Gastos(IdEvento)
;
--
-- INDEX: Ref624
--

CREATE INDEX Ref624 ON Jueces(IdEvento)
;
--
-- INDEX: Ref68
--

CREATE INDEX Ref68 ON Metricas(IdEvento)
;
--
-- INDEX: Ref621
--

CREATE INDEX Ref621 ON Participantes(IdEvento)
;
--
-- INDEX: Ref1222
--

CREATE INDEX Ref1222 ON Participantes(IdModelo)
;
--
-- INDEX: Ref64
--

CREATE INDEX Ref64 ON Patrocinador(IdEvento)
;
--
-- INDEX: UI_Username
--

CREATE UNIQUE INDEX UI_Username ON Usuarios(Username)
;
--
-- INDEX: Ref1113
--

CREATE INDEX Ref1113 ON Votacion(IdMetrica, IdEvento)
;
--
-- INDEX: Ref1416
--

CREATE INDEX Ref1416 ON Votacion(IdJuez, IdEvento)
;
--
-- INDEX: Ref1523
--

CREATE INDEX Ref1523 ON Votacion(IdParticipante, IdEvento, IdModelo)
;
--
-- INDEX: Ref31
--

CREATE INDEX Ref31 ON Zonas(IdEstablecimiento)
;
--
-- INDEX: Ref62
--

CREATE INDEX Ref62 ON Zonas(IdEvento)
;
--
-- TABLE: Entradas
--

ALTER TABLE Entradas ADD CONSTRAINT RefZonas62
    FOREIGN KEY (IdZona, IdEstablecimiento, IdEvento)
    REFERENCES Zonas(IdZona, IdEstablecimiento, IdEvento)
;


--
-- TABLE: Eventos
--

ALTER TABLE Eventos ADD CONSTRAINT RefEstablecimientos202
    FOREIGN KEY (IdEstablecimiento)
    REFERENCES Establecimientos(IdEstablecimiento)
;


--
-- TABLE: Gastos
--

ALTER TABLE Gastos ADD CONSTRAINT RefEventos52
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Jueces
--

ALTER TABLE Jueces ADD CONSTRAINT RefEventos242
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Metricas
--

ALTER TABLE Metricas ADD CONSTRAINT RefEventos82
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Participantes
--

ALTER TABLE Participantes ADD CONSTRAINT RefEventos212
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;

ALTER TABLE Participantes ADD CONSTRAINT RefModelos222
    FOREIGN KEY (IdModelo)
    REFERENCES Modelos(IdModelo)
;


--
-- TABLE: Patrocinador
--

ALTER TABLE Patrocinador ADD CONSTRAINT RefEventos42
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Votacion
--

ALTER TABLE Votacion ADD CONSTRAINT RefMetricas132
    FOREIGN KEY (IdMetrica, IdEvento)
    REFERENCES Metricas(IdMetrica, IdEvento)
;

ALTER TABLE Votacion ADD CONSTRAINT RefJueces162
    FOREIGN KEY (IdEvento, IdJuez)
    REFERENCES Jueces(IdJuez, IdEvento)
;

ALTER TABLE Votacion ADD CONSTRAINT RefParticipantes232
    FOREIGN KEY (IdEvento, IdParticipante, IdModelo)
    REFERENCES Participantes(IdParticipante, IdEvento, IdModelo)
;


--
-- TABLE: Zonas
--

ALTER TABLE Zonas ADD CONSTRAINT RefEstablecimientos12
    FOREIGN KEY (IdEstablecimiento)
    REFERENCES Establecimientos(IdEstablecimiento)
;

ALTER TABLE Zonas ADD CONSTRAINT RefEventos23
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;

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
