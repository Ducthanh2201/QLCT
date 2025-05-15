<?php
/**
 * Chuyển hướng đến URL chỉ định
 * @param string $path Đường dẫn tương đối
 */
function redirect($path) {
    header('Location: ' . BASEURL . $path);
    exit;
}

/**
 * Lấy giá trị thuộc tính của đối tượng một cách an toàn
 * @param mixed $object Đối tượng cần lấy thuộc tính
 * @param string $property Tên thuộc tính cần lấy
 * @param mixed $default Giá trị mặc định nếu thuộc tính không tồn tại
 * @return mixed
 */
function get_property_safe($object, $property, $default = '') {
    if(is_object($object) && isset($object->$property)) {
        return $object->$property;
    }
    return $default;
}
?>