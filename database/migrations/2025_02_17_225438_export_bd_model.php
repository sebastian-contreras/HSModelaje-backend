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
-- Date Created : Tuesday, February 25, 2025 18:32:19
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
;



--
-- TABLE: Eventos
--

CREATE TABLE Eventos(
    IdEvento                INT             AUTO_INCREMENT,
    Evento                  VARCHAR(150)    NOT NULL,
    FechaInicio             DATETIME,
    FechaFinal              DATETIME,
    FechaProblableInicio    DATETIME        NOT NULL,
    FechaProbableFinal      DATETIME        NOT NULL,
    Votacion                CHAR(1)         NOT NULL,
    EstadoEvento            CHAR(1)         NOT NULL,
    IdEstablecimiento       INT             NOT NULL,
    PRIMARY KEY (IdEvento)
)ENGINE=INNODB
;



--
-- TABLE: Gastos
--

CREATE TABLE Gastos(
    IdGastos       INT               NOT NULL,
    IdEvento       INT               NOT NULL,
    Gasto          VARCHAR(100)      NOT NULL,
    Personal       VARCHAR(100)      NOT NULL,
    Monto          DECIMAL(15, 2)    NOT NULL,
    Comprobante    VARCHAR(400),
    PRIMARY KEY (IdGastos, IdEvento)
)ENGINE=INNODB
;



--
-- TABLE: Jueces
--

CREATE TABLE Jueces(
    IdJuez        INT            AUTO_INCREMENT,
    DNI           CHAR(11)       NOT NULL,
    ApelName      VARCHAR(80)    NOT NULL,
    Correo        VARCHAR(60)    NOT NULL,
    Telefono      VARCHAR(15)    NOT NULL,
    EstadoJuez    CHAR(1)        NOT NULL,
    PRIMARY KEY (IdJuez)
)ENGINE=INNODB
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
;



--
-- TABLE: Modelos
--

CREATE TABLE Modelos(
    IdModelo     INT            AUTO_INCREMENT,
    DNI          CHAR(11),
    ApelName     VARCHAR(80),
    Edad         DATE,
    Sexo         CHAR(1),
    Telefono     VARCHAR(15),
    Correo       VARCHAR(60),
    EstadoMod    CHAR(1),
    PRIMARY KEY (IdModelo)
)ENGINE=INNODB
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
;



--
-- TABLE: Votacion
--

CREATE TABLE Votacion(
    IdVoto        CHAR(10)    NOT NULL,
    IdMetrica     INT         NOT NULL,
    IdEvento      INT         NOT NULL,
    IdModelo      INT         NOT NULL,
    IdJuez        INT         NOT NULL,
    Nota          INT         NOT NULL,
    Devolucion    TEXT,
    EstadoVoto    CHAR(1)     NOT NULL,
    PRIMARY KEY (IdVoto, IdMetrica, IdEvento, IdModelo, IdJuez)
)ENGINE=INNODB
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
-- INDEX: Ref68
--

CREATE INDEX Ref68 ON Metricas(IdEvento)
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
-- INDEX: Ref1215
--

CREATE INDEX Ref1215 ON Votacion(IdModelo)
;
--
-- INDEX: Ref1416
--

CREATE INDEX Ref1416 ON Votacion(IdJuez)
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

ALTER TABLE Entradas ADD CONSTRAINT RefZonas6
    FOREIGN KEY (IdZona, IdEstablecimiento, IdEvento)
    REFERENCES Zonas(IdZona, IdEstablecimiento, IdEvento)
;


--
-- TABLE: Eventos
--

ALTER TABLE Eventos ADD CONSTRAINT RefEstablecimientos20
    FOREIGN KEY (IdEstablecimiento)
    REFERENCES Establecimientos(IdEstablecimiento)
;


--
-- TABLE: Gastos
--

ALTER TABLE Gastos ADD CONSTRAINT RefEventos5
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Metricas
--

ALTER TABLE Metricas ADD CONSTRAINT RefEventos8
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Patrocinador
--

ALTER TABLE Patrocinador ADD CONSTRAINT RefEventos4
    FOREIGN KEY (IdEvento)
    REFERENCES Eventos(IdEvento)
;


--
-- TABLE: Votacion
--

ALTER TABLE Votacion ADD CONSTRAINT RefMetricas13
    FOREIGN KEY (IdMetrica, IdEvento)
    REFERENCES Metricas(IdMetrica, IdEvento)
;

ALTER TABLE Votacion ADD CONSTRAINT RefModelos15
    FOREIGN KEY (IdModelo)
    REFERENCES Modelos(IdModelo)
;

ALTER TABLE Votacion ADD CONSTRAINT RefJueces16
    FOREIGN KEY (IdJuez)
    REFERENCES Jueces(IdJuez)
;


--
-- TABLE: Zonas
--

ALTER TABLE Zonas ADD CONSTRAINT RefEstablecimientos1
    FOREIGN KEY (IdEstablecimiento)
    REFERENCES Establecimientos(IdEstablecimiento)
;

ALTER TABLE Zonas ADD CONSTRAINT RefEventos2
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
