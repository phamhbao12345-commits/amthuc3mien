<?php
require_once __DIR__ . '/Model.php';

class MonAnModel extends Model {
    protected $table = "mon_an";

    public function getAll() {
        $query = "SELECT m.*, v.ten_vung FROM " . $this->table . " m LEFT JOIN vung_mien v ON m.id_vung_mien = v.id";
        return $this->fetchAll($query);
    }

    public function getById($id) {
        $query = "SELECT m.*, v.ten_vung FROM " . $this->table . " m LEFT JOIN vung_mien v ON m.id_vung_mien = v.id WHERE m.id = ? LIMIT 1";
        return $this->fetchOne($query, [$id]);
    }

    public function getBySlug($slug) {
        $query = "SELECT m.*, v.ten_vung FROM " . $this->table . " m LEFT JOIN vung_mien v ON m.id_vung_mien = v.id WHERE m.slug = ? LIMIT 1";
        return $this->fetchOne($query, [$slug]);
    }

    public function getAllFiltered($vung_mien = null) {
        $sql = "SELECT m.*, v.ten_vung FROM " . $this->table . " m LEFT JOIN vung_mien v ON m.id_vung_mien = v.id";
        $params = [];

        if (!empty($vung_mien)) {
            $sql .= " WHERE m.id_vung_mien = ?";
            $params[] = $vung_mien;
        }

        $sql .= " ORDER BY m.id DESC";
        return $this->fetchAll($sql, $params);
    }

    public function countAll() {
        return $this->count('mon_an');
    }

    public function create($data) {
        $sql = "INSERT INTO " . $this->table . " (ten_mon, slug, hinh_anh, mo_ta_ngan, id_vung_mien, gia_tien, nguyen_lieu, cach_lam, danh_gia) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->query($sql, [
            $data['ten_mon'] ?? '',
            $data['slug'] ?? '',
            $data['hinh_anh'] ?? '',
            $data['mo_ta_ngan'] ?? '',
            $data['id_vung_mien'] ?? 1,
            $data['gia_tien'] ?? 0,
            $data['nguyen_lieu'] ?? '',
            $data['cach_lam'] ?? '',
            $data['danh_gia'] ?? 4.8
        ]);
        return $this->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE " . $this->table . " SET ten_mon = ?, hinh_anh = ?, mo_ta_ngan = ?, id_vung_mien = ?, gia_tien = ? WHERE id = ?";
        return $this->query($sql, [
            $data['ten_mon'],
            $data['hinh_anh'],
            $data['mo_ta_ngan'],
            $data['id_vung_mien'],
            $data['gia_tien'],
            $id
        ]);
    }

    public function delete($id) {
        return $this->query("DELETE FROM " . $this->table . " WHERE id = ?", [$id]);
    }

    // Hàm tìm kiếm theo keyword và location
    public function search($keyword = '', $location = '') {
        $keyword = trim($keyword);
        $location = trim($location);

        $query = "SELECT m.*, v.ten_vung FROM " . $this->table . " m 
                  LEFT JOIN vung_mien v ON m.id_vung_mien = v.id 
                  WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $query .= " AND (m.ten_mon LIKE :keyword OR m.mo_ta_ngan LIKE :keyword OR m.nguyen_lieu LIKE :keyword)";
            $params[':keyword'] = "%" . $keyword . "%";
        }

        if (!empty($location) && $location !== 'Tất cả' && $location !== 'Tất cả khu vực') {
            if (in_array($location, ['Hồ Chí Minh', 'Cần Thơ', 'Tây Ninh', 'Phú Quốc'])) {
                $query .= " AND (m.ten_mon LIKE :loc OR m.mo_ta_ngan LIKE :loc OR v.ten_vung LIKE '%Nam%' OR m.id_vung_mien = 3)";
                $params[':loc'] = "%" . $location . "%";
            } elseif ($location === 'Hà Nội') {
                $query .= " AND (m.ten_mon LIKE :loc OR m.mo_ta_ngan LIKE :loc OR v.ten_vung LIKE '%Bắc%' OR m.id_vung_mien = 1)";
                $params[':loc'] = "%" . $location . "%";
            } elseif ($location === 'Đà Nẵng') {
                $query .= " AND (m.ten_mon LIKE :loc OR m.mo_ta_ngan LIKE :loc OR v.ten_vung LIKE '%Trung%' OR m.id_vung_mien = 2)";
                $params[':loc'] = "%" . $location . "%";
            } else {
                $query .= " AND (m.ten_mon LIKE :loc OR m.mo_ta_ngan LIKE :loc)";
                $params[':loc'] = "%" . $location . "%";
            }
        }

        if (!empty($keyword)) {
            $query .= " ORDER BY (CASE WHEN m.ten_mon LIKE :keyword THEN 1 ELSE 2 END), m.danh_gia DESC";
        } else {
            $query .= " ORDER BY m.danh_gia DESC";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Hàm lấy các món ăn nổi bật (đánh giá cao nhất)
    public function getTopRated($limit = 6) {
        $query = "SELECT m.*, v.ten_vung FROM " . $this->table . " m LEFT JOIN vung_mien v ON m.id_vung_mien = v.id ORDER BY m.danh_gia DESC LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách món ăn gợi ý CHỈ theo đúng tỉnh thành của món đang xem
     */
    public function getRelatedDishes($tinhThanh = '', $idVungMien = null, $excludeId = 0, $limit = 4) {
        $tinhThanh = trim($tinhThanh);
        $results = [];
        $collectedIds = [(int)$excludeId];

        // 1. Ưu tiên lấy các món thuộc cùng chính tỉnh thành đó trước
        if (!empty($tinhThanh)) {
            $shortName = str_ireplace(['TP.', 'Tỉnh', 'Thành phố'], '', $tinhThanh);
            $shortName = trim($shortName);

            $sql = "SELECT m.*, v.ten_vung FROM " . $this->table . " m 
                    LEFT JOIN vung_mien v ON m.id_vung_mien = v.id 
                    WHERE m.id != ? AND (
                        m.tinh_thanh = ? 
                        OR m.tinh_thanh LIKE ? 
                        OR m.ten_mon LIKE ?
                    ) 
                    ORDER BY m.danh_gia DESC LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, (int)$excludeId, PDO::PARAM_INT);
            $stmt->bindValue(2, $tinhThanh, PDO::PARAM_STR);
            $stmt->bindValue(3, '%' . $shortName . '%', PDO::PARAM_STR);
            $stmt->bindValue(4, '%' . $shortName . '%', PDO::PARAM_STR);
            $stmt->bindValue(5, (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $r) {
                $collectedIds[] = (int)$r['id'];
            }
        }

        // 2. Nếu tỉnh thành đó có ít món (dưới limit món), tự động bổ sung thêm các món đặc sắc cùng miền
        $remaining = $limit - count($results);
        if ($remaining > 0 && !empty($idVungMien)) {
            $placeholders = implode(',', array_fill(0, count($collectedIds), '?'));
            $sql = "SELECT m.*, v.ten_vung FROM " . $this->table . " m 
                    LEFT JOIN vung_mien v ON m.id_vung_mien = v.id 
                    WHERE m.id NOT IN ($placeholders) AND m.id_vung_mien = ? 
                    ORDER BY m.danh_gia DESC LIMIT ?";
            $stmt = $this->conn->prepare($sql);
            $idx = 1;
            foreach ($collectedIds as $id) {
                $stmt->bindValue($idx++, $id, PDO::PARAM_INT);
            }
            $stmt->bindValue($idx++, (int)$idVungMien, PDO::PARAM_INT);
            $stmt->bindValue($idx++, (int)$remaining, PDO::PARAM_INT);
            $stmt->execute();
            $moreDishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $results = array_merge($results, $moreDishes);
        }

        return $results;
    }
}

/**
 * VungMienModel quản lý danh mục vùng miền ẩm thực (Bắc - Trung - Nam)
 * Được gộp chung trong file MonAnModel do liên quan trực tiếp đến món ăn
 */
class VungMienModel extends Model {
    protected $table = 'vung_mien';

    public function getAll() {
        return $this->fetchAll("SELECT * FROM vung_mien ORDER BY id ASC");
    }

    public function getById($id) {
        return $this->fetchOne("SELECT * FROM vung_mien WHERE id = ?", [$id]);
    }
}

