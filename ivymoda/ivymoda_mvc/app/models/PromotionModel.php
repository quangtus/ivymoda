<?php

class PromotionModel extends Model {
    protected $table = 'tbl_promotion';

    public function getAllPromotions($limit = 50, $offset = 0) {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT $offset, $limit";
        return parent::getAll($query);
    }

    public function getById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM {$this->table} WHERE promotion_id = $id";
        return $this->getOne($query);
    }

    public function create($data) {
        $title = $this->escape($data['title'] ?? '');
        $description = $this->escape($data['description'] ?? '');
        $content = $this->escape($data['content'] ?? '');
        $image_url = $this->escape($data['image_url'] ?? '');
        $ma_giam_gia_id = isset($data['ma_giam_gia_id']) && $data['ma_giam_gia_id'] !== '' ? (int)$data['ma_giam_gia_id'] : 'NULL';
        $start_date = $this->escape($data['start_date'] ?? date('Y-m-d H:i:s'));
        $end_date = $this->escape($data['end_date'] ?? date('Y-m-d H:i:s'));
        $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
        $priority = isset($data['priority']) ? (int)$data['priority'] : 0;
        $created_by = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 'NULL';

        $query = "INSERT INTO {$this->table} (title, description, content, image_url, ma_giam_gia_id, start_date, end_date, is_active, priority, created_by)
                  VALUES ('$title', '$description', '$content', '$image_url', $ma_giam_gia_id, '$start_date', '$end_date', $is_active, $priority, $created_by)";
        return $this->execute($query);
    }

    public function updatePromotion($id, $data) {
        $id = (int)$id;
        $title = $this->escape($data['title'] ?? '');
        $description = $this->escape($data['description'] ?? '');
        $content = $this->escape($data['content'] ?? '');
        $image_url = isset($data['image_url']) ? $this->escape($data['image_url']) : null;
        $ma_giam_gia_id = isset($data['ma_giam_gia_id']) && $data['ma_giam_gia_id'] !== '' ? (int)$data['ma_giam_gia_id'] : 'NULL';
        $start_date = $this->escape($data['start_date'] ?? date('Y-m-d H:i:s'));
        $end_date = $this->escape($data['end_date'] ?? date('Y-m-d H:i:s'));
        $is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
        $priority = isset($data['priority']) ? (int)$data['priority'] : 0;

        $setImage = $image_url !== null ? ", image_url = '$image_url'" : '';

        $query = "UPDATE {$this->table}
                  SET title = '$title', description = '$description', content = '$content',
                      ma_giam_gia_id = $ma_giam_gia_id, start_date = '$start_date', end_date = '$end_date',
                      is_active = $is_active, priority = $priority $setImage
                  WHERE promotion_id = $id";
        return $this->execute($query);
    }

    public function deletePromotion($id) {
        $id = (int)$id;
        $query = "DELETE FROM {$this->table} WHERE promotion_id = $id";
        return $this->execute($query);
    }

    public function getActivePromotions($limit = 5) {
        $now = date('Y-m-d H:i:s');
        $query = "SELECT * FROM {$this->table}
                  WHERE is_active = 1 AND start_date <= '$now' AND end_date >= '$now'
                  ORDER BY priority DESC, start_date DESC
                  LIMIT $limit";
        return parent::getAll($query);
    }
}


