-- Active: 1689722680414@@us-cdbr-east-06.cleardb.net@3306@heroku_530aad6597c2bdc
create DATABASE heroku_530aad6597c2bdc;
Use heroku_530aad6597c2bdc;

create table usuarios (
    id_usuario int AUTO_INCREMENT PRIMARY KEY,
    ruc VARCHAR(13) UNIQUE,
    nombre_proveedor VARCHAR(50) NOT null UNIQUE,
    telefono VARCHAR(10) NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

create table clientes (
    id_cliente int AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(10) UNIQUE,
    nombres VARCHAR(50) NOT null UNIQUE,
    telefono VARCHAR(10) NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

create table notificaciones(
    id_notificacion int AUTO_INCREMENT PRIMARY key,
    tipo VARCHAR(50) not null,
    detalles TEXT,
    latitud VARCHAR(50),
    longitud VARCHAR(50),
    created_at DATETIME COMMENT 'fecha q se genero la nofificacion',
    updated_at DATETIME,
    estado VARCHAR(50) DEFAULT 'Reportado' CHECK(estado='Reportado' OR estado='En camino' or estado='Antendiendo' or estado='Finalizado'),
    id_cliente int not null
);

alter TABLE notificaciones add constraint fk_notificaciones_id_cliente foreign key (id_cliente) REFERENCES clientes(id_cliente);

DROP TABLE usuarios;
