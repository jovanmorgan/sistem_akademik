<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="slide navbar style.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap?v=<?= time(); ?>" rel="stylesheet">
    <title>Login</title>
    <link href="../css/login_register.css?<?= time(); ?>" rel="stylesheet" />
    <link rel="shortcut icon" href="../assets/img/akademik/3.png" type="" />
    <!-- Link untuk Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css?v=<?= time(); ?>">
</head>

<body>
    <div class="wrapper fadeInDown">
        <div id="formContent">
            <!-- Tabs Titles -->
            <a href="login">
                <h2 class="active">Sign In</h2>
            </a>

            <!-- Icon -->
            <div class="fadeIn first">
                <img src="../assets/img/akademik/3.png" id="icon" alt="User Icon" />
            </div>

            <!-- Login Form -->
            <form id="login">
                <input type="text" id="login" class="fadeIn second" name="username" placeholder="Username" required />
                <input type="text" id="password" class="fadeIn third" name="password" placeholder="Password" required />
                <input type="submit" class="fadeIn fourth" value="Log In" style="cursor: pointer" />
            </form>

            <!-- Remind Passowrd -->
            <!-- <div id="formFooter">
          <a class="underlineHover" href="#">Forgot Password?</a>
        </div> -->
        </div>
    </div>

    </style>
    <!-- End footer -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePasswordVisibility(inputId) {
            var passwordInput = document.getElementById(inputId);
            var passwordIcon = document.querySelector(
                "#" + inputId + " + .show-password"
            );

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordIcon.classList.remove("fa-eye-slash");
                passwordIcon.classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                passwordIcon.classList.remove("fa-eye");
                passwordIcon.classList.add("fa-eye-slash");
            }
        }
        document.getElementById("login").addEventListener("submit", function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../keamanan/proses_login", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = xhr.responseText;
                        var responseArray = response.split(':');
                        if (responseArray[0].trim() === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login berhasil!',
                                text: 'Selamat datang ' + responseArray[1],
                                timer: 2000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            }).then((result) => {
                                switch (responseArray[2].trim()) {
                                    case "admin":
                                        window.location.href = "../pengguna/admin/";
                                        break;
                                    case "pastor":
                                        window.location.href = "../pengguna/pastor/";
                                        break;
                                    case "ketua_kub":
                                        window.location.href = "../pengguna/ketua_kub/";
                                        break;
                                    case "umat":
                                        window.location.href = "../pengguna/umat/";
                                        break;
                                    default:
                                        window.location.href = "login";
                                        break;
                                }
                            });

                            if (rememberMe) {
                                var username = formData.get('username');
                                var password = formData.get('password');
                                document.cookie = "username=" + encodeURIComponent(
                                    username) + "; path=/";
                                document.cookie = "password=" + encodeURIComponent(password) + "; path=/";
                            }
                        } else if (responseArray[0].trim() === "error_password") {
                            Swal.fire("Error", "Password yang dimasukkan salah", "info");
                        } else if (responseArray[0].trim() === "error_username") {
                            Swal.fire("Error", "Username tidak ditemukan", "info");
                        } else if (responseArray[0].trim() === "username_tidak_ada") {
                            Swal.fire("Info", "Username belum diisi", "info");
                        } else if (responseArray[0].trim() === "password_tidak_ada") {
                            Swal.fire("Info", "Password belum diisi", "info");
                        } else if (responseArray[0].trim() === "tidak_ada_data") {
                            Swal.fire("Info", "Username dan Password belum diisi", "info");
                        } else {
                            Swal.fire("Error", "Terjadi kesalahan saat proses login", "error");
                        }
                    } else {
                        Swal.fire("Error", "Gagal", "error");
                    }
                }
            };
            xhr.onerror = function() {
                Swal.fire("Error", "Gagal melakukan request", "error");
            };
            xhr.send(formData);
        });
    </script>
</body>

</html>