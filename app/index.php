<?php
// 1. กำหนดค่าการเชื่อมต่อฐานข้อมูลโดยดึงชื่อจาก .env [5]
// ใน Docker เราจะใช้ชื่อ Service (db-server) เป็น Hostname แทน localhost
$host = 'db-server'; 
$user = 'root';      // ใช้ root ตามที่กำหนดไว้ใน .env
$db   = 'it464_db';  // ชื่อฐานข้อมูลที่ตั้งไว้ใน DB_NAME

// 2. กฎเหล็กด้านความปลอดภัย: อ่านรหัสผ่านจาก Docker Secret [4, 7]
$secret_path = '/run/secrets/db_root_pass';
$pass = trim(file_get_contents($secret_path));

// 3. เริ่มการเชื่อมต่อด้วย mysqli [8]
$conn = new mysqli($host, $user, $pass, $db);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("<h2 style='color:red;'>❌ Connection Failed: " . $conn->connect_error . "</h2>");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Project Tracker - <?php echo $user; ?></title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .status-done { color: #28a745; font-weight: bold; }
        .header-info { background: #f4f4f4; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header-info">
        <h1>📋 ระบบติดตามสถานะโครงงานนักศึกษา</h1>
        <p><strong>ผู้ดูแลระบบ:</strong> Yochuwa (ID: 1660705235)</p>
        <p>Status: <span style="color:green;">Connected to MariaDB successfully!</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Project Title</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // 4. ดึงข้อมูลจากตาราง students มาแสดงผล [2]
            $sql = "SELECT * FROM students";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["id"]) . "</td>
                            <td>" . htmlspecialchars($row["student_id"]) . "</td>
                            <td>" . htmlspecialchars($row["full_name"]) . "</td>
                            <td>" . htmlspecialchars($row["project_name"]) . "</td>
                            <td class='status-done'>" . htmlspecialchars($row["status"]) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>ไม่พบข้อมูลในระบบ (ตรวจสอบไฟล์ script-data.sql)</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>