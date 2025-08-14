<?php
// File: admin/buat_admin.php

// 1. Tentukan password yang Anda inginkan
$passwordPolos = 'admin12345'; // Ganti dengan password yang kuat

// 2. Gunakan password_hash() untuk membuat hash yang aman
$hashPassword = password_hash($passwordPolos, PASSWORD_BCRYPT);

// 3. Tampilkan hasilnya
echo "Password Polos: " . $passwordPolos . "<br>";
echo "Password Hash (simpan ini di database):<br>";
echo $hashPassword;
