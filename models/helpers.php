<?php
function generateId($conn, $table, $prefix, $column) {
    $sql = "SELECT $column FROM $table ORDER BY created_at DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $lastNum = intval(substr($row[$column], strlen($prefix)));
        return $prefix . ($lastNum + 1);
    }
    return $prefix . "1";
}
?>
