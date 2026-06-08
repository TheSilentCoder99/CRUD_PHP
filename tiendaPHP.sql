USE tienda;

CREATE TABLE `fabricante` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `producto` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `precio` double NOT NULL,
  `id_fabricante` int unsigned NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `imagen` varchar(350) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_fabricante` (`id_fabricante`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_fabricante`) REFERENCES `fabricante` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE usuario(
id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nombre VARCHAR(250) NOT NULL,
apellido1 VARCHAR(250) NOT NULL,
apellido2 VARCHAR(250),
email VARCHAR(250) NOT NULL,
login VARCHAR(250) NOT NULL,
password VARCHAR(255) NOT NULL
);

/*RELLENANDO LAS TABLAS*/
INSERT INTO producto (nombre, descripcion, precio, imagen, id_fabricante) VALUES
('PlayStation 5', 'Consola de última generación con gráficos 4K y SSD ultrarrápido', 499.99, 'ps5.jpg', 1),
('Smart TV 55" 4K', 'Televisor Samsung 55 pulgadas con resolución 4K y HDR', 699.99, 'tv_samsung.jpg', 2),
('iPhone 15 Pro', 'Smartphone con chip A17 Pro y cámara de 48MP', 1099.99, 'iphone15.jpg', 3),
('Micrófono Inalámbrico', 'Micrófono profesional para videoconferencias y grabación', 79.99, 'micro_lg.jpg', 4),
('Lavavajillas', 'Lavavajillas Bosch con sistema Silence y 12 cubiertos', 549.00, 'bosch_lava.jpg', 5),
('Auriculares Sony WH-1000XM5', 'Auriculares con cancelación de ruido líder en el mercado', 349.99, 'sony_auriculares.jpg', 1),
('Tablet Galaxy Tab S9', 'Tablet Samsung con pantalla AMOLED y S Pen incluido', 799.99, 'galaxy_tab.jpg', 2),
('MacBook Air M2', 'Portátil ultradelgado con chip M2 y 8GB RAM', 1199.99, 'macbook_m2.jpg', 3),
('Monitor UltraWide 34"', 'Monitor LG 34 pulgadas curvo con resolución 3440x1440', 449.99, 'monitor_lg.jpg', 4),
('Robot Aspirador', 'Robot aspirador Serie 8 con mapeo inteligente', 699.00, 'robot_bosch.jpg', 5);