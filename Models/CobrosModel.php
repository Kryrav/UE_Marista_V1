<?php
    class CobrosModel extends Mysql
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function selectCobros()
        {
            $sql = "SELECT id_cobros, nombre, descripcion, tipo, ncuota, valor, status FROM cobro WHERE status != 0 ORDER BY id_cobros DESC";
            return $this->select_all($sql);
        }

        public function selectCobro(int $id)
        {
            $sql = "SELECT * FROM cobro WHERE id_cobros = ?";
            return $this->select($sql, [$id]);
        }

        public function insertCobro(string $nombre, string $descripcion, string $tipo, int $ncuota, float $valor, int $status)
        {
            $sql = "INSERT INTO cobro(nombre, descripcion, tipo, ncuota, valor, status) VALUES(?,?,?,?,?,?)";
            $id = $this->insert($sql, [$nombre, $descripcion, $tipo, $ncuota, $valor, $status]);
            return $id ? "dato_guardado" : false;
        }

        public function updateCobro(int $id, string $nombre, string $descripcion, string $tipo, int $ncuota, float $valor, int $status)
        {
            $sql = "UPDATE cobro SET nombre=?, descripcion=?, tipo=?, ncuota=?, valor=?, status=? WHERE id_cobros=?";
            $ok = $this->update($sql, [$nombre, $descripcion, $tipo, $ncuota, $valor, $status, $id]);
            return $ok ? "dato_guardado" : false;
        }

        public function deleteCobro(int $id)
        {
            $sql = "UPDATE cobro SET status = 0 WHERE id_cobros = ?";
            return $this->update($sql, [$id]);
        }
    }
?>
