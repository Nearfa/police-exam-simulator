<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html"); 
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการข้อสอบ - Police Exam Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --p-blue: #002244; --white: #ffffff; --bg: #f4f7f9; --success: #28a745; --danger: #dc3545; }
        body { font-family: 'Sarabun', sans-serif; background: var(--bg); margin: 0; display: flex; }
        .sidebar { width: 250px; background: var(--p-blue); color: white; height: 100vh; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 290px); }
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-submit { background: var(--success); color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; width: 100%; }
        .menu-item { display: block; color: white; text-decoration: none; padding: 12px; border-radius: 8px; margin-bottom: 10px; }
        .menu-item:hover { background: rgba(255,255,255,0.1); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; color: var(--p-blue); }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .btn-edit { color: #007bff; cursor: pointer; border: none; background: none; font-weight: 600; }
        .btn-delete { color: var(--danger); cursor: pointer; border: none; background: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>👮 Admin Panel</h2>
        <a href="admin_dashboard.php" class="menu-item">📊 หน้าแรก</a>
        <a href="manage_questions.php" class="menu-item" style="background: rgba(255,255,255,0.2);">📝 จัดการข้อสอบ</a>
        <a href="../index.html" class="menu-item" style="margin-top: 50px; color: #ffcc00;">🏠 กลับหน้าหลัก</a>
    </div>

    <div class="main-content">
        <h1>📝 จัดการคลังข้อสอบ</h1>

        <div class="form-card">
            <h3>➕ เพิ่มข้อสอบใหม่</h3>
            <form id="add-question-form">
                <div class="form-group">
                    <label>วิชา</label>
                    <select name="subject" required>
                        <option value="Thai">ภาษาไทย</option>
                        <option value="Math">ความสามารถทั่วไป (คณิต)</option>
                        <option value="English">ภาษาอังกฤษ</option>
                        <option value="Law">กฎหมายที่ประชาชนควรรู้</option>
                        <option value="IT">คอมพิวเตอร์และสารสนเทศ</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>โจทย์คำถาม</label>
                    <textarea name="question" rows="3" required></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group"><label>ตัวเลือก 1</label><input type="text" name="choice_1" required></div>
                    <div class="form-group"><label>ตัวเลือก 2</label><input type="text" name="choice_2" required></div>
                    <div class="form-group"><label>ตัวเลือก 3</label><input type="text" name="choice_3" required></div>
                    <div class="form-group"><label>ตัวเลือก 4</label><input type="text" name="choice_4" required></div>
                </div>
                <div class="form-group">
                    <label>คำตอบที่ถูกต้อง (เฉลย)</label>
                    <select name="answer" required>
                        <option value="1">ข้อ 1</option>
                        <option value="2">ข้อ 2</option>
                        <option value="3">ข้อ 3</option>
                        <option value="4">ข้อ 4</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">➕ บันทึกข้อสอบลงคลัง</button>
            </form>
        </div>

        <div class="form-card">
            <h3>📋 รายการข้อสอบทั้งหมดในระบบ</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">วิชา</th>
                        <th>คำถาม</th>
                        <th style="width: 15%; text-align: center;">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="questions-list">
                    <tr><td colspan="3" style="text-align:center;">กำลังโหลดข้อมูล...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        window.onload = function() {
            const user = JSON.parse(localStorage.getItem('user'));
            if (!user || user.role !== 'admin') {
                alert('⛔ คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
                window.location.href = '../login.html';
                return;
            }
            fetchAllQuestions();
        };

        async function fetchAllQuestions() {
            try {
                const response = await fetch('api/admin_get_questions.php');
                const data = await response.json();
                const tbody = document.getElementById('questions-list');
                tbody.innerHTML = '';

                data.forEach(q => {
                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${q.subject}</strong></td>
                            <td>${q.question}</td>
                            <td style="text-align: center;">
                                <button class="btn-delete" onclick="deleteQuestion(${q.id})">ลบ</button>
                            </td>
                        </tr>
                    `;
                });
            } catch (err) { console.error("Fetch error:", err); }
        }

        async function deleteQuestion(id) {
            if (confirm('⚠️ คุณแน่ใจหรือไม่ว่าต้องการลบข้อสอบข้อนี้?')) {
                try {
                    const response = await fetch(`api/admin_delete_question.php?id=${id}`);
                    const result = await response.json();
                    if (result.success) {
                        alert('✅ ลบเรียบร้อย');
                        fetchAllQuestions();
                    } else {
                        alert('❌ ไม่สามารถลบได้');
                    }
                } catch (err) { alert('🚨 เกิดข้อผิดพลาด'); }
            }
        }

        function editQuestion(id) {
            window.location.href = `edit_question.php?id=${id}`;
        }

        document.getElementById('add-question-form').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('api/admin_add_question.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if(result.success) {
                    alert('✅ บันทึกข้อสอบสำเร็จ!');
                    e.target.reset();
                    fetchAllQuestions();
                } else {
                    alert('❌ ผิดพลาด: ' + result.message);
                }
            } catch (err) { alert('🚨 ติดต่อเซิร์ฟเวอร์ไม่ได้'); }
        };
    </script>
</body>
</html>