<?php
require_once __DIR__ . '/Model.php';

class TinTucModel extends Model {
    protected $table = "tin_tuc";
    private $table_name = "tin_tuc";

    /**
     * Lấy danh sách bài viết (có hỗ trợ lọc theo danh mục, từ khóa và phân trang)
     */
    public function getAll($danh_muc = '', $keyword = '', $limit = 0, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($danh_muc) && $danh_muc !== 'Tất cả') {
            $query .= " AND danh_muc = :danh_muc";
            $params[':danh_muc'] = $danh_muc;
        }

        if (!empty($keyword)) {
            $query .= " AND (tieu_de LIKE :keyword OR tom_tat LIKE :keyword OR tac_gia LIKE :keyword)";
            $params[':keyword'] = "%" . $keyword . "%";
        }

        $query .= " ORDER BY id DESC";

        if ($limit > 0) {
            $query .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số bài viết
     */
    public function countAll($danh_muc = '', $keyword = '') {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($danh_muc) && $danh_muc !== 'Tất cả') {
            $query .= " AND danh_muc = :danh_muc";
            $params[':danh_muc'] = $danh_muc;
        }

        if (!empty($keyword)) {
            $query .= " AND (tieu_de LIKE :keyword OR tom_tat LIKE :keyword OR tac_gia LIKE :keyword)";
            $params[':keyword'] = "%" . $keyword . "%";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy bài viết theo ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bài viết theo slug
     */
    public function getBySlug($slug) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách bài viết nổi bật
     */
    public function getFeatured($limit = 4) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE noi_bat = 1 ORDER BY id DESC LIMIT " . (int)$limit);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách bài viết được xem nhiều nhất
     */
    public function getMostViewed($limit = 4) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " ORDER BY luot_xem DESC, id DESC LIMIT " . (int)$limit);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bài viết liên quan theo cùng danh mục
     */
    public function getRelated($danh_muc, $excludeId = 0, $limit = 3) {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " WHERE danh_muc = ? AND id != ? ORDER BY id DESC LIMIT " . (int)$limit);
        $stmt->execute([$danh_muc, $excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tăng lượt xem bài viết
     */
    public function incrementViews($id) {
        $stmt = $this->conn->prepare("UPDATE " . $this->table_name . " SET luot_xem = luot_xem + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Lấy danh sách danh mục hiện có
     */
    public function getCategories() {
        return [
            'Kinh doanh ăn uống',
            'Địa Điểm Ăn Uống',
            'Món ngon & Công thức',
            'Tiệc & Tổ chức tiệc',
            'Khám phá',
            'Mẹo ẩm thực'
        ];
    }

    /**
     * Thêm bài viết mới
     */
    public function create($data) {
        $sql = "INSERT INTO " . $this->table_name . " 
                (tieu_de, slug, tom_tat, noi_dung, hinh_anh, danh_muc, tac_gia, noi_bat, trang_thai) 
                VALUES (:tieu_de, :slug, :tom_tat, :noi_dung, :hinh_anh, :danh_muc, :tac_gia, :noi_bat, :trang_thai)";
        
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute([
            ':tieu_de' => $data['tieu_de'],
            ':slug' => $data['slug'],
            ':tom_tat' => $data['tom_tat'] ?? '',
            ':noi_dung' => $data['noi_dung'] ?? '',
            ':hinh_anh' => $data['hinh_anh'] ?? '',
            ':danh_muc' => $data['danh_muc'] ?? 'Khám phá',
            ':tac_gia' => $data['tac_gia'] ?? 'Ban biên tập',
            ':noi_bat' => !empty($data['noi_bat']) ? 1 : 0,
            ':trang_thai' => isset($data['trang_thai']) ? (int)$data['trang_thai'] : 1
        ]);

        return $result ? $this->conn->lastInsertId() : false;
    }

    /**
     * Cập nhật bài viết
     */
    public function update($id, $data) {
        $sql = "UPDATE " . $this->table_name . " SET 
                tieu_de = :tieu_de,
                tom_tat = :tom_tat,
                noi_dung = :noi_dung,
                hinh_anh = :hinh_anh,
                danh_muc = :danh_muc,
                tac_gia = :tac_gia,
                noi_bat = :noi_bat,
                trang_thai = :trang_thai";
        
        $params = [
            ':tieu_de' => $data['tieu_de'],
            ':tom_tat' => $data['tom_tat'] ?? '',
            ':noi_dung' => $data['noi_dung'] ?? '',
            ':hinh_anh' => $data['hinh_anh'] ?? '',
            ':danh_muc' => $data['danh_muc'] ?? 'Khám phá',
            ':tac_gia' => $data['tac_gia'] ?? 'Ban biên tập',
            ':noi_bat' => !empty($data['noi_bat']) ? 1 : 0,
            ':trang_thai' => isset($data['trang_thai']) ? (int)$data['trang_thai'] : 1,
            ':id' => $id
        ];

        if (!empty($data['slug'])) {
            $sql .= ", slug = :slug";
            $params[':slug'] = $data['slug'];
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa bài viết
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

