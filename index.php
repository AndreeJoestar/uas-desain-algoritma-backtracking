<?php
session_start();

function solveSchedule($employees, $days, $shifts, $maxSolutions = 10) {
    $schedule = [];
    $solutions = [];
    $usedInShift = [];
    $usageCount = array_fill_keys($employees, 0);

    $totalSlot = count($days) * count($shifts);
    $maxUsage = ceil($totalSlot / count($employees));

    function isSafe($day, $shift, $employee, $schedule, $usedInShift, $usageCount, $maxUsage) {
        if (isset($schedule[$day]) && in_array($employee, $schedule[$day])) return false;
        if (isset($usedInShift[$shift]) && in_array($employee, $usedInShift[$shift])) return false;
        if ($usageCount[$employee] >= $maxUsage) return false;
        return true;
    }

    function backtrack(
        $dayIndex, $shiftIndex,
        $employees, $days, $shifts,
        &$schedule, &$solutions,
        &$usedInShift, &$usageCount,
        $maxSolutions, $maxUsage
    ) {
        if (count($solutions) >= $maxSolutions) return;
        if ($dayIndex === count($days)) {
            $solutions[] = $schedule;
            return;
        }

        $day = $days[$dayIndex];
        $shift = $shifts[$shiftIndex];

        foreach ($employees as $emp) {
            if (isSafe($day, $shift, $emp, $schedule, $usedInShift, $usageCount, $maxUsage)) {

                $schedule[$day][$shift] = $emp;
                $usedInShift[$shift][] = $emp;
                $usageCount[$emp]++;

                $nextShift = $shiftIndex + 1;
                $nextDay = $dayIndex;
                if ($nextShift === count($shifts)) {
                    $nextShift = 0;
                    $nextDay++;
                }

                backtrack(
                    $nextDay, $nextShift,
                    $employees, $days, $shifts,
                    $schedule, $solutions,
                    $usedInShift, $usageCount,
                    $maxSolutions, $maxUsage
                );

                unset($schedule[$day][$shift]);
                array_pop($usedInShift[$shift]);
                $usageCount[$emp]--;
            }
        }
    }

    backtrack(0, 0, $employees, $days, $shifts,
              $schedule, $solutions,
              $usedInShift, $usageCount,
              $maxSolutions, $maxUsage);

    return [count($solutions) >= $maxSolutions, $solutions];
}

/* ================= LOGIN ================= */
if (isset($_POST['login'])) $_SESSION['login'] = true;
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

/* ================= PROCESS ================= */
$status = false;
$solutions = [];
$executionTime = 0;
$error = "";

if (isset($_POST['process'])) {
    $employees = array_values(array_filter($_POST['employees']));
    $days = array_values(array_filter($_POST['days']));
    $jumlahShift = max(1, (int)$_POST['jumlah_shift']);
    $shiftNames = array_values(array_filter($_POST['shift_names'] ?? []));

    $jumlahKaryawan = count($employees);

    $minHari = ($jumlahKaryawan == 15) ? 3 : (($jumlahKaryawan >= 30) ? 6 : 2);
    if (count($days) < $minHari) {
        $error = "Jumlah hari minimal $minHari sesuai jumlah karyawan.";
    } else {
        $shifts = [];
        for ($i = 0; $i < $jumlahShift; $i++) {
            $shifts[] = $shiftNames[$i] ?? "Shift " . ($i + 1);
        }

        $start = microtime(true);
        list($status, $solutions) = solveSchedule($employees, $days, $shifts, 10);
        $executionTime = microtime(true) - $start;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Aplikasi Penjadwalan Shift</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    min-height: 100vh;
}
.card {
    border-radius: 18px;
    border: none;
}
.card-header {
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: white;
    font-weight: bold;
}
.btn {
    border-radius: 12px;
}
.table thead {
    background: #212529;
    color: white;
}
.table tbody tr:nth-child(even) {
    background: #f2f2f2;
}
.badge-custom {
    background: #6f42c1;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.9rem;
    color: white;
}
</style>

<script>
function addEmployeesBulk() {
    let n = prompt("Jumlah karyawan:");
    if (!n || isNaN(n) || n <= 0) return;
    let c = document.getElementById("employees");
    for (let i = 0; i < n; i++) {
        c.innerHTML += `
        <div class="input-group mb-2">
            <input type="text" name="employees[]" class="form-control" required>
            <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">Hapus</button>
        </div>`;
    }
}

function addDaysBulk() {
    let n = prompt("Jumlah hari:");
    if (!n || isNaN(n) || n <= 0) return;
    let c = document.getElementById("days");
    for (let i = 0; i < n; i++) {
        c.innerHTML += `
        <div class="input-group mb-2">
            <input type="text" name="days[]" class="form-control" placeholder="Senin" required>
            <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">Hapus</button>
        </div>`;
    }
}

function generateShiftInputs() {
    let n = document.getElementById("jumlah_shift").value;
    let c = document.getElementById("shiftNames");
    c.innerHTML = "";
    for (let i = 1; i <= n; i++) {
        c.innerHTML += `
        <div class="input-group mb-2">
            <span class="input-group-text">Shift ${i}</span>
            <input type="text" name="shift_names[]" class="form-control"
                   placeholder="Contoh: Shift Pagi (06.00-14.00)" required>
        </div>`;
    }
}
</script>
</head>

<body>
<div class="container py-5">

<?php if (!isset($_SESSION['login'])): ?>

<div class="card shadow text-center">
  <div class="card-header">
    <h4>🔐 Login Sistem</h4>
  </div>
  <div class="card-body">
    <form method="post">
      <button name="login" class="btn btn-primary w-100">Masuk</button>
    </form>
  </div>
</div>

<?php elseif (!isset($_POST['process'])): ?>

<div class="card shadow">
<div class="card-header text-center">
  <h4>🗓️ Penjadwalan Shift Karyawan</h4>
</div>
<div class="card-body">

<form method="post">
<h5>👥 Karyawan</h5>
<div id="employees">
  <div class="input-group mb-2">
    <input type="text" name="employees[]" class="form-control" required>
  </div>
</div>
<button type="button" class="btn btn-success mb-3" onclick="addEmployeesBulk()">➕ Tambah Karyawan</button>

<h5>📆 Hari</h5>
<div id="days">
  <div class="input-group mb-2">
    <input type="text" name="days[]" class="form-control" placeholder="Senin" required>
  </div>
</div>
<button type="button" class="btn btn-success mb-3" onclick="addDaysBulk()">➕ Tambah Hari</button>

<h5>⏱️ Shift Kerja</h5>
<input type="number" name="jumlah_shift" id="jumlah_shift"
       class="form-control mb-2" min="1" required
       oninput="generateShiftInputs()">

<div id="shiftNames"></div>

<button name="process" class="btn btn-primary w-100 mt-3">⚙️ Proses Jadwal</button>
</form>

<a href="?logout=true" class="btn btn-link text-danger mt-3">Logout</a>
</div>
</div>

<?php else: ?>

<div class="card shadow">
<div class="card-header text-center">
  <h4>📊 Hasil Penjadwalan</h4>
</div>
<div class="card-body">

<div class="row text-center mb-4">
  <div class="col-md-3"><span class="badge-custom">👥 <?= $jumlahKaryawan ?> Karyawan</span></div>
  <div class="col-md-3"><span class="badge-custom">📆 <?= count($days) ?> Hari</span></div>
  <div class="col-md-3"><span class="badge-custom">⏱️ <?= count($shifts) ?> Shift</span></div>
  <div class="col-md-3"><span class="badge-custom">⚡ <?= number_format($executionTime,6) ?> dtk</span></div>
</div>

<?php foreach (array_slice($solutions,0,2) as $i=>$sol): ?>
<h5 class="mt-4">📌 Solusi ke-<?= $i+1 ?></h5>
<table class="table table-bordered table-hover">
<tr>
<th>Hari</th>
<?php foreach ($shifts as $s): ?><th><?= $s ?></th><?php endforeach; ?>
</tr>
<?php foreach ($days as $d): ?>
<tr>
<td><b><?= $d ?></b></td>
<?php foreach ($shifts as $s): ?>
<td><?= htmlspecialchars($sol[$d][$s]) ?></td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</table>
<?php endforeach; ?>

<a href="index.php" class="btn btn-secondary">Kembali</a>
<a href="?logout=true" class="btn btn-outline-danger ms-2">Logout</a>

</div>
</div>

<?php endif; ?>

</div>
</body>
</html>
