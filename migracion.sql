-- Ejecuta esto UNA vez en phpMyAdmin sobre la base d_o_axel_502

-- 1. XP ganado por día (alimenta racha, meta diaria, gráfica semanal y días practicados)
CREATE TABLE IF NOT EXISTS `actividad` (
  `id_usuario` smallint(4) NOT NULL,
  `fecha` date NOT NULL,
  `xp` int(5) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_usuario`, `fecha`),
  CONSTRAINT `actividad_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Crea fila de progreso para los usuarios que no tengan (si no, el UPDATE no afecta a nadie)
INSERT INTO `progreso` (`porcentaje`, `experiencia`, `racha`, `fecha_registro`, `id_usuario`, `id_curso`)
SELECT 0, 0, 0, CURDATE(), u.`id_usuario`, 1
FROM `usuario` u
WHERE NOT EXISTS (SELECT 1 FROM `progreso` p WHERE p.`id_usuario` = u.`id_usuario`);
